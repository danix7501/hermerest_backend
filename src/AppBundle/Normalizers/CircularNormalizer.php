<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 13:50
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\Circular;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CircularNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'subject' => $object->getSubject(),
            'message' => $object->getMessage(),
            'sendingDate' => $object->getSendingDate(),
            'attachments' => count($object->getAttachments()) == 0 ? null : (new AttachmentNormalizer())->normalize($object->getAttachments()[0])
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Circular;
    }
}