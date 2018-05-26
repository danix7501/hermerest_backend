<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 13:21
 */

namespace AppBundle\Normalizers;

use AppBundle\Entity\Message;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class MessageNormalizer implements NormalizerInterface
{

    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'subject' => $object->getSubject(),
            'message' => $object->getMessage(),
            'sendingDate' => $object->getSendingDate(),
            'attachments' => (new AttachmentNormalizer())->normalize($object->getAttachments()[0])
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Message;
    }
}