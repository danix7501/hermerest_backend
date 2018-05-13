<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\JwtAuth;
use AppBundle\Services\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
    private $jwtAuth;
    private $userFacade;
    private $responseFactory;

    public function __construct(JwtAuth $jwtAuth,
                                ResponseFactory $responseFactory,
                                UserFacade $userFacade)
    {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
        $this->userFacade = $userFacade;
    }

    /**
     * @Route("/login", name="login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {
        $user = $this->userFacade->findByEmailAndPlainPassword(
            $request->get('username'),
            $request->get('password')
        );

        if ($user == null)
            return $this->responseFactory->unsuccessfulJsonResponse('No se ha encontrado un usuario con las credenciales introducidas');


        return $this->responseFactory->successfulJsonResponse(
            $this->jwtAuth->encodeUser($user)
        );
        echo 'hola';
        die();

    }
}
