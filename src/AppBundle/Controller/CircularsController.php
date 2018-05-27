<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 13:45
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\Circular;
use AppBundle\Normalizers\CircularNormalizer;
use AppBundle\Services\Facades\AttachmentFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\CircularFacade;
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
 * @Route("/circulars")
 */
class CircularsController extends Controller
{
    private $studentFacade;
    private $circularFacade;
    private $attachmentFacade;
    private $centreFacade;
    private $responseFactory;
    private $utils;

    public function __construct(StudentFacade $studentFacade,
                                CircularFacade $circularFacade,
                                CentreFacade $centreFacade,
                                AttachmentFacade $attachmentFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->studentFacade = $studentFacade;
        $this->circularFacade = $circularFacade;
        $this->centreFacade = $centreFacade;
        $this->attachmentFacade = $attachmentFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/{id}", name="listarCircularesDelCentro")
     * @Method("GET")
     */
    public function getAction(Request $request, $id)
    {
        $centre = $this->centreFacade->find($id);

        return $this->responseFactory->successfulJsonResponse(
            ['circulars' =>
                $this->utils->serializeArray(
                    $centre->getMessagesOfType('Circular'), new CircularNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("", name="crearCircular")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $centre = $this->centreFacade->find($request->get('centre'));
        // TODO: enviar date actual cuando se clica en boton desde front_end
        $sendingDate = new DateTime('2000-01-01', new DateTimeZone('Atlantic/Canary'));
        $circular = new Circular(
            $request->request->get('subject'),
            $request->request->get('message'),
            $sendingDate,
            $centre
        );
        $this->circularFacade->create($circular);

        // TODO: renombrar el fichero que se guarda con id unico
        $tempFile = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        if ($tempFile != null) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] .'/hermerest_backend/src/AppBundle/Uploads/Circulars/'. $fileName;
            move_uploaded_file($tempFile, $filePath);
            $attachment = new Attachment(
                $fileName,
                $circular
            );
            $this->attachmentFacade->create($attachment);
        }

        $this->sendCircular($request->request->get('studentsIds'), $circular, $this->circularFacade);

        return $this->responseFactory->successfulJsonResponse([
            'id' => $circular->getId(),
            'subject' => $circular->getSubject(),
        ]);
    }

    private function sendCircular($studentsIds, $circular, $circularFacade)
    {
        foreach ($studentsIds as $studentId) {
            $student = $this->studentFacade->find($studentId);
            $circular->addStudent($student);
            $circularFacade->edit();
        }
    }
}