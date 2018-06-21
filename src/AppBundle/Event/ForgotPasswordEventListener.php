<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 20/06/2018
 * Time: 1:05
 */

namespace AppBundle\Event;


use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\Mailer;
use CoopTilleuls\ForgotPasswordBundle\Event\ForgotPasswordEvent;

class ForgotPasswordEventListener
{

    private $mailer;
    private $userFacade;

    public function __construct(UserFacade $userFacade,
                                Mailer $mailer)
    {
        $this->userFacade = $userFacade;
        $this->mailer = $mailer;
    }

    /**
     * @param ForgotPasswordEvent $event
     */
    public function onCreateToken(ForgotPasswordEvent $event)
    {
        $this->mailer->sendMail(
            'Recuperar contraseña',
            Mailer::FORGOT_PASSWORD,
            ['link' => sprintf('http://localhost:4200/recovery-password/%s', $event->getPasswordToken()->getToken())],
            $event->getPasswordToken()->getUser()->getUsername()
        );
    }

    /**
     * @param ForgotPasswordEvent $event
     */
    public function onUpdatePassword(ForgotPasswordEvent $event)
    {
        $event->getPasswordToken()->getUser()->setPassword(hash('sha256', $event->getPassword()));
        $this->userFacade->edit();
    }
}