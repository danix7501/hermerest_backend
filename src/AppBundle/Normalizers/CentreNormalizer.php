<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 21:19
 */

namespace AppBundle\Normalizers;

use AppBundle\Entity\Centre;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CentreNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
        ];
    }


    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Centre;
    }
}