<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 10:55
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\Authorization;
use AppBundle\Entity\Student;
use AppBundle\Entity\Centre;
use AppBundle\Entity\Message;
use AppBundle\Normalizers\AuthorizationNormalizer;
use AppBundle\Services\Facades\AttachmentFacade;
use AppBundle\Services\Facades\AuthorizationFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use DateTimeZone;

/**
 * @Route("/authorizations")
 */
class AuthorizationsController extends Controller
{
    private $studentFacade;
    private $authorizationFacade;
    private $progenitorFacade;
    private $attachmentFacade;
    private $centreFacade;
    private $responseFactory;
    private $utils;

    public function __construct(StudentFacade $studentFacade,
                                AuthorizationFacade $authorizationFacade,
                                ProgenitorFacade $progenitorFacade,
                                AttachmentFacade $attachmentFacade,
                                CentreFacade $centreFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->studentFacade = $studentFacade;
        $this->authorizationFacade = $authorizationFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->attachmentFacade = $attachmentFacade;
        $this->centreFacade = $centreFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("", name="crearAutorizacion")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $centre = $this->centreFacade->find($request->get('centre'));
        // TODO: enviar date actual cuando se clica en boton desde front_end
        $sendingDate = date_create_from_format('Y-m-d G:i:s', $request->request->get('sendingDate'), new DateTimeZone('Atlantic/Canary'));
        $limitDate = date_create_from_format('Y-m-d G:i:s', $request->request->get('limitDate') . '23:59:59', new DateTimeZone('UTC'));
        $authorization = new Authorization(
            $request->request->get('subject'),
            $request->request->get('message'),
            $sendingDate,
            $centre,
            $limitDate
        );
        $this->authorizationFacade->create($authorization);

        // TODO: renombrar el fichero que se guarda con id unico
            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
        if ($tempFile != null) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] .'/hermerest_backend/src/AppBundle/Uploads/Authorizations/'. $fileName;
            move_uploaded_file($tempFile, $filePath);
            $attachment = new Attachment(
                $fileName,
                $authorization
            );
            $this->attachmentFacade->create($attachment);
        }

        $this->sendAuthorization($request->request->get('studentsIds'), $authorization, $this->authorizationFacade);

        return $this->responseFactory->successfulJsonResponse([
            'id' => $authorization->getId(),
            'limitDate' => $authorization->getLimitDate(),
            'subject' => $authorization->getSubject(),
        ]);
    }


    private function sendAuthorization($studentsIds, $authorization, $authorizationFacade)
    {
        foreach ($studentsIds as $studentId) {
            $student = $this->studentFacade->find($studentId);
            $authorization->addStudent($student);
            $authorizationFacade->edit();
        }
    }

}