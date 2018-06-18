<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 18/06/2018
 * Time: 0:27
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Device;
use AppBundle\Services\Facades\DeviceFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/push-messages")
 */
class PushNotificationController extends Controller
{

    private $deviceFacade;
    private static $androidAuthKey = "AAAA81CuUIE:APA91bE_nV5iM__sXBcWSAVhRz3rZ3nVTMHYRTK4fZgSFlykLcaBZsWndob0WKeljZ2vQDg6pw7sfZ_kdlRtvIzrE7b_wIM3S2bul3MecSBP_LzY1GORR6hfVF_ZBHrsi21vqoP6fbB1";


    public function __construct(DeviceFacade $deviceFacade)
    {
        $this->deviceFacade = $deviceFacade;
    }
    /**
     * @Route("/send", name="enviarNotificacionPush")
     * @Method("POST")
     */
    public function sendPushMessageAction(Request $request) {

        if ($request->getMethod() != 'POST') {
            return new Response("Request is not POST");
        }

        $title = $request->request->get("title");
        $message = $request->request->get("message");

        if (!$title || !$message) {
            return new Response('Empty paramters');
        }

        $devices = $this->deviceFacade->findAll(); //we will send the notification to all devices

        if (!$devices) {
            return new Response("There aren't any devices registered");
        }

        $tokens = array(); //we must store the tokens which we want to send a message
        foreach ($devices as $d) {
            $tokens[] = $d->getToken();
        }

        $params = array ("title"    =>  $title,    "message"   =>  $message, "id"  => 55); // the id put whatever you want

        return new Response($this->sendMessage($tokens, $params) );
    }

    public static function sendMessage($tokenArray,$params) {
        $data = array(
            'registration_ids' => $tokenArray,
            'data' => $params
        );
        $headers = array(
            "Content-Type:application/json",
            "Authorization:key=" . self::$androidAuthKey
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result; //we return the response of the server, this will be the data thats the controller will return in a Response
    }
}