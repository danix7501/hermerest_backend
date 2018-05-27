<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 19:56
 */

namespace AppBundle\Controller;

use AppBundle\Entity\PollReply;
use AppBundle\Services\Facades\PollFacade;
use AppBundle\Services\Facades\PollOptionFacade;
use AppBundle\Services\Facades\PollReplyFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/pollsreplies")
 */
class PollReplyController extends Controller
{

    private $pollReplyFacade;
    private $pollOptionFacade;
    private $progenitorFacade;
    private $responseFactory;

    public function __construct(PollReplyFacade $pollReplyFacade,
                                PollOptionFacade $pollOptionFacade,
                                ProgenitorFacade $progenitorFacade,
                                ResponseFactory $responseFactory)
    {
        $this->pollReplyFacade = $pollReplyFacade;
        $this->pollOptionFacade = $pollOptionFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @Route("", name="respuestaEncuesta")
     * @Method("POST")
     */
    public function createReplyPollAction(Request $request)
    {
        $pollOption = $this->pollOptionFacade->find($request->request->get('pollOptionId'));
        $parent = $this->progenitorFacade->find($request->request->get('parent'));

        $pollReply = new PollReply();
        $pollReply->setParent($parent);
        $pollReply->setPollOption($pollOption);
        $this->pollReplyFacade->create($pollReply);

        return $this->responseFactory->successfulJsonResponse([]);
    }

    /**
     * @Route("/{id}", name="editarRespuestaEncuesta")
     * @Method("PUT")
     */
    public function editReplyPollAction(Request $request, $id)
    {
        $pollReply = $this->pollReplyFacade->find($id);
        $poll = $this->pollOptionFacade->find($request->request->get('pollOptionId'));
        if ($pollReply == null) return $this->responseFactory->unsuccessfulJsonResponse('No existe la opción');

        $pollReply->setPollOption($poll);
        $this->pollReplyFacade->edit();

        return $this->responseFactory->successfulJsonResponse([]);
    }
}