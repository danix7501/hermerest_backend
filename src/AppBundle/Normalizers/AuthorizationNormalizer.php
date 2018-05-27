<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 13:16
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\Authorization;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthorizationNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'subject' => $object->getSubject(),
            'message' => $object->getMessage(),
            'sendingDate' => $object->getSendingDate(),
            'limitDate' => $object->getLimitDate(),
            'attachments' => count($object->getAttachments()) == 0 ? null : (new AttachmentNormalizer())->normalize($object->getAttachments()[0])
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Authorization;
    }
}