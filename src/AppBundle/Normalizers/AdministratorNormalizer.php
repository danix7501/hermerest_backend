<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 18/5/18
 * Time: 22:53
 */

namespace AppBundle\Normalizers;

use AppBundle\Entity\Centre;
use AppBundle\Entity\Administrator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AdministratorNormalizer implements NormalizerInterface
{

    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'centre' => (new CentreNormalizer())->normalize($object->getCentre()),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Administrator;
    }
}