<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 15:25
 */

namespace AppBundle\Controller;

use AppBundle\Entity\AuthorizationReply;
use AppBundle\Services\Facades\AuthorizationFacade;
use AppBundle\Services\Facades\AuthorizationReplyFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/authorizationsreplies")
 */
class AuthorizationsRepliesController extends Controller
{
    private $authorizationReplyFacade;
    private $authorizationFacade;
    private $responseFactory;
    private $progenitorFacade;
    private $studentFacade;

    public function __construct(AuthorizationReplyFacade $authorizationReplyFacade,
                                AuthorizationFacade $authorizationFacade,
                                StudentFacade $studentFacade,
                                ProgenitorFacade $progenitorFacade,
                                ResponseFactory $responseFactory)
    {
        $this->authorizationReplyFacade = $authorizationReplyFacade;
        $this->authorizationFacade = $authorizationFacade;
        $this->studentFacade = $studentFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @Route("", name="respuestaAutorizacion")
     * @Method("POST")
     */
    public function replyAuthorizationAction(Request $request)
    {
        $authorization = $this->authorizationFacade->find($request->request->get('authorization'));
        $parent = $this->progenitorFacade->find($request->request->get('parent'));
        $student = $this->studentFacade->find($request->request->get('student'));

        $authorizationReply = new AuthorizationReply();
        $authorizationReply->setAuthorization($authorization);
        $authorizationReply->setParent($parent);
        $authorizationReply->setStudent($student);
        $authorizationReply->setAuthorized($request->request->get('authorized'));
        $this->authorizationReplyFacade->create($authorizationReply);

        return $this->responseFactory->successfulJsonResponse([]);
    }
    /**
     * @Route("/{id}", name="editarRespuestaAutorizacion")
     * @Method("PUT")
     */
    public function editReplyAuthorizationAction(Request $request, $id)
    {
        $authorizationReply = $this->authorizationReplyFacade->find($id);
        if ($authorizationReply == null) return $this->responseFactory->unsuccessfulJsonResponse('No existe la autorización');

        $authorizationReply->setAuthorized($request->request->get('authorized'));
        $this->authorizationReplyFacade->edit();

        return $this->responseFactory->successfulJsonResponse([]);
    }


}