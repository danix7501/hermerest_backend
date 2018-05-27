<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 17:06
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\PollOption;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PollOptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'option' => $object->getText(),
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PollOption;
    }
}