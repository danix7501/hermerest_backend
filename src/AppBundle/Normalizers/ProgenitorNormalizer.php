<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 19/05/2018
 * Time: 15:26
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\Progenitor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProgenitorNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'telephone' => $object->getTelephone(),
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Progenitor;
    }
}