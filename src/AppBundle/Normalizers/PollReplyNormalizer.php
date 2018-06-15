<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 15/06/2018
 * Time: 23:00
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\PollReply;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PollReplyNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'pollOption' => $object->getPollOption(),
            'parent' => (new ProgenitorNormalizer())->normalize($object->getParent())
        ];
    }


    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PollReply;
    }
}