<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 09/05/2018
 * Time: 23:12
 */

namespace AppBundle\Services;

use AppBundle\Services\Facades\UserFacade;
use Doctrine\ORM\EntityManager;
use Exception;
use Firebase\JWT\JWT;

class JwtAuth
{
    private $entityManager;
    private $key;
    private $userFacade;

    public function __construct(EntityManager $entityManager,
                                UserFacade $userFacade)
    {
        $this->entityManager = $entityManager;
        $this->userFacade = $userFacade;
        $this->key = 'clavesecreta';
    }

    /**
     * @param $user
     * @return string
     *
     * TODO: CADUCIDAD TOKEN (QUITAR EL 10)
     */
    public function encodeUser($user)
    {
        $associatedUser = $this->userFacade->getAssociatedUserFor($user);
        return JWT::encode([
            "sub" => $user->getId(),
            "id" => $associatedUser->getId(),
            "name" => $associatedUser->getName(),
            "username" => $user->getUsername(),
            "rol" => $user->getRol(),
            "iat" => time(),
            "exp" => time() + (7 * 24 * 60 * 60 * 10)
        ], $this->key, 'HS256');
    }

    public function encodeUserParent($user)
    {
        $associatedUser = $this->userFacade->getAssociatedUserFor($user);
        return JWT::encode([
            "sub" => $user->getId(),
            "id" => $associatedUser->getId(),
            "name" => $associatedUser->getName(),
            "telephone" => $associatedUser->getTelephone(),
            "username" => $user->getUsername(),
            "found" => true,
            "smsCode" => '123456',
            "rol" => $user->getRol(),
            "iat" => time(),
            "exp" => time() + (7 * 24 * 60 * 60 * 10)
        ], $this->key, 'HS256');
    }

    /**
     * @param $token
     * @return object
     */
    public function decodeToken($token)
    {
        $count = 1;
        return JWT::decode(str_replace('Bearer ', '', $token, $count), $this->key, ['HS256']);
    }

    public function checkTokenById($token, $id)
    {

        return $this->decodeToken($token)->sub == $id;
    }

    public function checkTokenAndRole($encodedToken, $roles)
    {
        $count = 1;
        try {
            $decodedToken = JWT::decode(
                str_replace('Bearer ', '', $encodedToken, $count),
                $this->key,
                ['HS256']
            );
        } catch (Exception $exception) {
            return false;
        }

        return isset($decodedToken->rol) && in_array($decodedToken->rol, $roles);
    }

}