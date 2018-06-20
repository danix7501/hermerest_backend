<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 20/06/2018
 * Time: 1:17
 */

namespace AppBundle\Controller;


use AppBundle\Entity\AccountToken;
use AppBundle\Services\Facades\AccountTokenFacade;
use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\Mailer;
use AppBundle\Services\ResponseFactory;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/verify_account")
 */
class VerifyAccountController extends Controller
{

    private $accountTokenFacade;
    private $userFacade;
    private $responseFactory;
    private $mailer;

    public function __construct(UserFacade $userFacade,
                                Mailer $mailer,
                                AccountTokenFacade $accountTokenFacade,
                                ResponseFactory $responseFactory)
    {
        $this->userFacade = $userFacade;
        $this->mailer = $mailer;
        $this->accountTokenFacade = $accountTokenFacade;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @Route("", name="enviarCorreoVerificarCuenta")
     * @Method("POST")
     */
    public function sendEmailAction(Request $request)
    {
        $user = $this->userFacade->findByUsername($request->get('username'));
        if ($user == null) return $this->responseFactory->unsuccessfulJsonResponse('El email no está registrado');
        $this->accountTokenFacade->removeAllFromUsuario($user);

        $accountToken = new AccountToken();
        $accountToken->setUser($user);
        $accountToken->setToken(md5(uniqid()));
        $accountToken->setExpiresAt((new DateTime())->modify('+1 day'));
        $this->accountTokenFacade->create($accountToken);

        $this->mailer->sendMail(
            'Verificar cuenta',
            Mailer::VERIFY_ACCOUNT,
            ['enlace' => sprintf('http://localhost:4200/activate-account/%s', $accountToken->getToken())],
            $user->gerUsername()
        );

        return $this->responseFactory->successfulJsonResponse([]);
    }

    /**
     * @Route("/{token}", name="verificarCuenta")
     * @Method("POST")
     */
    public function verifyAction(Request $request, $token)
    {
        $accountToken = $this->accountTokenFacade->findByToken($token);
        if ($accountToken == null) return $this->responseFactory->unsuccessfulJsonResponse('El token no existe');
        if ($accountToken->isExpired()) return $this->responseFactory->unsuccessfulJsonResponse('El token ha caducado');
        $accountToken->getUser()->setActivado(true);

        $this->userFacade->edit();
        $this->accountTokenFacade->removeAllFromUsuario($accountToken->getUser());

        return $this->responseFactory->successfulJsonResponse([]);
    }


}