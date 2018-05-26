<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 13:25
 */

namespace AppBundle\Normalizers;

use AppBundle\Entity\Attachment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttachmentNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName()
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Attachment;
    }
}