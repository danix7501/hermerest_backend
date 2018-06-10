<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 15:58
 */

namespace AppBundle\Controller;

use AppBundle\Normalizers\AuthorizationNormalizer;
use AppBundle\Normalizers\CentreNormalizer;
use AppBundle\Normalizers\CircularNormalizer;
use AppBundle\Normalizers\PollNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\AttachmentFacade;
use AppBundle\Services\Facades\AuthorizationFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/parents")
 */
class ProgenitoresController extends Controller
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
     * @Route("/{id}/centres", name="listarCentrosDelPadre")
     * @Method("GET")
     */
    public function getCentresAction(Request $request, $id)
    {
        $parent = $this->progenitorFacade->find($id);
        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse("El padre no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['centres' =>
                $this->utils->serializeArray(
                    $parent->getCentres(), new CentreNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{idParent}/centres/{idCentre}", name="asociarCentroPadre")
     * @Method("POST")
     */
    public function associatedCentreAction(Request $request, $idParent, $idCentre)
    {
        $parent = $this->progenitorFacade->find($idParent);
        $centre = $this->centreFacade->find($idCentre);

        $parent->addCentre($centre);
        $this->progenitorFacade->edit();
        $centre->addParent($parent);
        $this->centreFacade->edit();

        return $this->responseFactory->successfulJsonResponse('El centro ha sido asociado al padre correctamente');
    }

    /**
     * @Route("/{id}/centres", name="desasociarCentroPadre")
     * @Method("DELETE")
     */
    public function desassociatedCentreAction($id){
        $parent = $this->progenitorFacade->find($id);
        $this->progenitorFacade->clearCentresOf($parent);
        return $this->responseFactory->successfulJsonResponse([
            'Centros totales' => $parent->getCentres()->count()
        ]);
    }


    /**
     * @Route("/{id}", name="editarPadre")
     * @Method("PUT")
     */
    public function editAction(Request $request, $id)
    {
         $parent = $this->progenitorFacade->find($id);
         if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse('El padre no existe');

         $parent->setName($request->request->get('newName'));
         $this->progenitorFacade->edit();

         return $this->responseFactory->successfulJsonResponse('El padre ha sido editado correctamente');
    }

    /**
     * @Route("/{id}/students", name="listarHijosDelPadre")
     * @Method("GET")
     */
    public function getStudentsAction(Request $request, $id)
    {
        $parent = $this->progenitorFacade->find($id);
        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse("El padre no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['childrens' =>
                $this->utils->serializeArray(
                    $parent->getChildren(), new StudentNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{idParent}/students/{idStudent}", name="desasociarHijoDelPadre")
     * @Method("DELETE")
     */
    public function disassociateStudentsAction(Request $request, $idParent, $idStudent)
    {
        $parent = $this->progenitorFacade->find($idParent);
        $student = $this->studentFacade->find($idStudent);

        if ($parent != null && $student != null) {
            $parent->removeChild($student);
            $this->progenitorFacade->edit();
            $student->removeParent($parent);
            $this->studentFacade->edit();

            return $this->responseFactory->successfulJsonResponse('Hijo desasociado del padre correctamente');
        }else{
            return $this->responseFactory->unsuccessfulJsonResponse('El hijo o el padre no existe');
        }
    }

    /**
     * @Route("/{id}/messages", name="listarMensajesDelPadre")
     * @Method("GET")
     */
    public function getMessagesAction(Request $request, $id)
    {
        $parent = $this->progenitorFacade->find($id);
        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse("El padre no existe");

        if ($request->query->get('type') == 'Circular') {
            return $this->responseFactory->successfulJsonResponse(
                ['circulars' =>
                    $this->utils->serializeArray(
                        $parent->getMessagesOfType('Circular'), new CircularNormalizer()
                    )
                ]
            );
        }

        if ($request->query->get('type') == 'Poll') {
            return $this->responseFactory->successfulJsonResponse(
                ['polls' =>
                    $this->utils->serializeArray(
                        $parent->getMessagesOfType('Poll'), new PollNormalizer()
                    )
                ]
            );
        }

        if ($request->query->get('type') == 'Authorization') {
            return $this->responseFactory->successfulJsonResponse(
                ['authorizations' =>
                    $this->utils->serializeArray(
                        $parent->getMessagesOfType('Authorization'), new AuthorizationNormalizer()
                    )
                ]
            );
        }
    }

    /**
     * @Route("", name="verSiExisteElPadre")
     * @Method("GET")
     */
    public function getExistParentAction(Request $request)
    {
        $parent = $this->progenitorFacade->findByTelephone($request->query->get('telephone'));
        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse(['found' => false]);

        return $this->responseFactory->successfulJsonResponse([
            'id' => $parent->getId(),
            'telephone' => $parent->getTelephone(),
            'name' => $parent->getName(),
            'smsCode' => '123456',
            'found' => true,
        ]);
    }

    /**
     * @Route("/{idParent}/authorizations/{idAuthorization}", name="listarAutorizacionesDelPadre")
     * @Method("GET")
     */
    public function getAction(Request $request, $idParent, $idAuthorization)
    {
        $studentId = $request->query->get("student");
        $authorization = $this->authorizationFacade->find($idAuthorization);
        $student = $this->studentFacade->find($studentId);
        $parent = $this->progenitorFacade->find($idParent);
        return $this->responseFactory->successfulJsonResponse([
            'subject' => $authorization->getSubject(),
            'message' => $authorization->getMessage(),
            'sendingDate' => $authorization->getSendingDate()->format('Y-m-d H:i:s'),
            'limitDate' => $authorization->getLimitDate()->format('Y-m-d H:i:s'),
            'attachmentId' => $authorization->getAttachments()[0]->getId(),
            'attachmentName' => $authorization->getAttachments()[0]->getName(),
            'reply' => $this->getReply($parent, $student, $authorization),
            'replyId' => is_null($parent->getAuthorizationReply($student, $authorization)) ? null : ($parent->getAuthorizationReply($student, $authorization)->getId()),
            'studentName' => $student->getName() . ' ' . $student->getSurname(),
            'authorized' => $student->isAuthorizedTo($authorization)
        ]);
    }

    public function getReply($parent, $student, $authorization)
    {
        $reply = $parent->getAuthorizationReply($student, $authorization);
        return is_null($reply) ? null :
            ($reply->getAuthorized() ? true : false);
    }
}