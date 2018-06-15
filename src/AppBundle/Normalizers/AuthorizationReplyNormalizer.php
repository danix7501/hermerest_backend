<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 15/06/2018
 * Time: 1:29
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\AuthorizationReply;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthorizationReplyNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'authorization' => (new AuthorizationNormalizer())->normalize($object->getAuthorization()),
            'parent' => (new ProgenitorNormalizer())->normalize($object->getParent()),
            'student' => (new StudentNormalizer())->normalize($object->getStudent()),
            'authorized' => $object->getAuthorized()
        ];
    }


    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AuthorizationReply;
    }
}