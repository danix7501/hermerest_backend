<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 20/06/2018
 * Time: 1:07
 */

namespace AppBundle\Services;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class Mailer
{
    const VERIFY_ACCOUNT = "verify_account";
    const FORGOT_PASSWORD = "forgot_password";
    const NOTIFY_ACCOUNT_CREATED = "notificacion_cuenta_creada";

    private $from;
    private $mailer;
    private $templating;


    public function __construct($from, Swift_Mailer $mailer, Twig_Environment $templating)
    {
        $this->from = $from;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendMail($subject, $template, $parameters, $to)
    {
        $to = "romerocalerod@gmail.com";
        $template = $this->templating->render('emails/' . $template . '.html.twig', $parameters);

        $this->mailer->send(
            (new Swift_Message($subject))
                ->setFrom($this->from)
                ->setTo($to)
                ->setBody($template, 'text/html')
        );
    }

}