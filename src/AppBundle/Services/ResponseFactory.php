<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 17/04/2017
 * Time: 20:33
 */

namespace AppBundle\Services;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    public function successfulJsonResponse($content)
    {
        return $this->getJsonResponse(
            Response::HTTP_OK,
            [
                'success' => true,
                'content' => $content,
            ]
        );
    }

    public function unsuccessfulJsonResponse($error)
    {
        return $this->getJsonResponse(
            Response::HTTP_OK,
            [
                'success' => false,
                'error' => $error,
            ]
        );
    }

    public function unauthorizedJsonResponse()
    {
        return $this->getJsonResponse(
            Response::HTTP_UNAUTHORIZED,
            [
                'success' => false,
                'error' => 'El token no existe o es incorrecto',
            ]
        );
    }

    private function getJsonResponse($statusCode, $data)
    {
        $response = new JsonResponse();
        $response->setStatusCode($statusCode);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        $response->setData($data);

        return $response;
    }
}