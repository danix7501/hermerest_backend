<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 15:18
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\Poll;
use AppBundle\Entity\PollOption;
use AppBundle\Normalizers\PollNormalizer;
use AppBundle\Normalizers\PollOptionNormalizer;
use AppBundle\Normalizers\PollReplyNormalizer;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\PollFacade;
use AppBundle\Services\Facades\AttachmentFacade;
use AppBundle\Services\Facades\PollOptionFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/polls")
 */
class PollsController extends Controller
{
    private $studentFacade;
    private $pollFacade;
    private $pollOptionFacade;
    private $centreFacade;
    private $attachmentFacade;
    private $responseFactory;
    private $progenitorFacade;
    private $utils;

    public function __construct(StudentFacade $studentFacade,
                                PollFacade $pollFacade,
                                PollOptionFacade $pollOptionFacade,
                                CentreFacade $centreFacade,
                                AttachmentFacade $attachmentFacade,
                                ResponseFactory $responseFactory,
                                ProgenitorFacade $progenitorFacade,
                                Utils $utils)
    {
        $this->studentFacade = $studentFacade;
        $this->pollFacade = $pollFacade;
        $this->pollOptionFacade = $pollOptionFacade;
        $this->centreFacade = $centreFacade;
        $this->attachmentFacade = $attachmentFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/replies", name="listarRespuestasEncuestas")
     * @Method("GET")
     */
    public function getReplyPollAction(Request $request)
    {
        $poll = $this->pollFacade->find($request->query->get('poll'));
        if ($poll == null) return $this->responseFactory->unsuccessfulJsonResponse('La encuesta no exite');
        return $this->responseFactory->successfulJsonResponse(
            [
                'pollsReplies' => $this->getPollResults($poll)
            ]
        );
    }

    /**
     * @Route("", name="crearEncuesta")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $centre = $this->centreFacade->find($request->request->get('centre'));
        // TODO: enviar date actual cuando se clica en boton desde front_end
        $sendingDate = new DateTime($request->request->get('sendingDate'), new DateTimeZone('Atlantic/Canary'));
        $limitDate = new DateTime($request->request->get('limitDate') . '23:59:59', new DateTimeZone('UTC'));
        $poll = new Poll(
            $request->request->get('subject'),
            $request->request->get('message'),
            $sendingDate,
            $centre,
            $limitDate,
            $request->request->get('multipleChoice')
        );
        $this->pollFacade->create($poll);

        // TODO: renombrar el fichero que se guarda con id unico
        $tempFile = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        if ($tempFile != null) {
            $filePath = substr($_SERVER['DOCUMENT_ROOT'],0,-4) .'/src/AppBundle/Uploads/Polls/'. $fileName;
            move_uploaded_file($tempFile, $filePath);
            $attachment = new Attachment(
                $fileName,
                $poll
            );
            $this->attachmentFacade->create($attachment);
        }

        $this->sendPoll($request->request->get('studentsIds'), $poll, $this->pollFacade);
        $this->addOptionsToPoll($request->request->get('pollOptions'), $poll, $this->pollOptionFacade);

        return $this->responseFactory->successfulJsonResponse(
            [ 'poll' => [
                'id' => $poll->getId(),
                'sendingDate' => $poll->getSendingDate(),
                'limitDate' => $poll->getLimitDate(),
                'subject' => $poll->getSubject(),
                'message' => $poll->getMessage()
            ]
        ]);
    }
    /**
     * @Route("/donwload", name="descargarEncuesta")
     * @Method("GET")
     */
    public function donwloadAction(Request $request)
    {
        $nameFile = $request->query->get('attachment');
        $filePath = substr($_SERVER['DOCUMENT_ROOT'],0,-4) . '/src/AppBundle/Uploads/Polls/' . $nameFile;
        header ("Content-Disposition: attachment; filename=$nameFile ");
        header ("Content-Type: application/force-download");
        header ("Content-Length: ".filesize($filePath));
        readfile($filePath);
    }


    /**
     * @Route("/{id}/editLimitDate", name="editarFechaLimiteEncuesta")
     * @Method("PUT")
     */
    public function editLimitDatePollAction(Request $request, $id)
    {
        $poll = $this->pollFacade->find($id);
        if ($poll == null) return $this->responseFactory->unsuccessfulJsonResponse('La encuesta no existe');

        $poll->setLimitDate(new DateTime($request->request->get('newLimitDate') . '23:59:59', new DateTimeZone('Atlantic/Canary')));
        $this->pollFacade->edit();

        return $this->responseFactory->successfulJsonResponse(
            [ 'poll' =>
                [
                    'id' => $poll->getId(),
                    'limitDate' => $poll->getLimitDate()
                ]
            ]);
    }

    /**
     * @Route("/{id}", name="verEncuesta")
     * @Method("GET")
     */
    public function seeAction(Request $request, $id)
    {
        $poll = $this->pollFacade->find($id);
        $parent = $this->progenitorFacade->find($request->query->get("parent"));
        return $this->responseFactory->successfulJsonResponse([
            'subject' => $poll->getSubject(),
            'message' => $poll->getMessage(),
            'sendingDate' => $poll->getSendingDate()->format('Y-m-d H:i:s'),
            'limitDate' => $poll->getLimitDate()->format('Y-m-d H:i:s'),
            'options' => $this->utils->serializeArray($poll->getPollOptions(), new PollOptionNormalizer()),
            'attachmentId' =>  $poll->getAttachments()[0] == null ? null : $poll->getAttachments()[0]->getId(),
            'attachmentName' => $poll->getAttachments()[0] == null ? null : $poll->getAttachments()[0]->getName(),
            'multiple' => $poll->getMultipleChoice(),
            'replied' => $poll->isRepliedBy($parent)
        ]);
    }


    private function sendPoll($studentsIds, $authorization, $authorizationFacade)
    {
        $studentsIds = explode(',', $studentsIds);

        foreach ($studentsIds as $studentId) {
            $student = $this->studentFacade->find($studentId);
            $authorization->addStudent($student);
            $authorizationFacade->edit();
        }
    }

    private function addOptionsToPoll($pollOptions, $poll, $pollOptionFacade)
    {
        $pollOptions = explode(',', $pollOptions);
        foreach ($pollOptions as $pollOptionText) {
            $polls = new PollOption(
                $pollOptionText,
                $poll
            );
            $pollOptionFacade->create($polls);
        }
    }

    private function getPollResults($poll): array
    {
        $pollResults = array();
        foreach ($poll->getPollOptions() as $pollOption)
            array_push($pollResults, ['optionText' => $pollOption->getText(), 'countReplies' => count($pollOption->getReplies())]);
        return $pollResults;
    }
}