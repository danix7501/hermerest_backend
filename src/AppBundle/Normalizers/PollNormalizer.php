<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 27/5/18
 * Time: 15:19
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\Poll;
use AppBundle\Services\Utils;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PollNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'subject' => $object->getSubject(),
            'message' => $object->getMessage(),
            'sendingDate' => $object->getSendingDate(),
            'limitDate' => $object->getLimitDate(),
            'options' => (new Utils())->serializeArray($object->getPollOptions(), new PollOptionNormalizer()),
            'attachments' => count($object->getAttachments()) == 0 ? null : (new AttachmentNormalizer())->normalize($object->getAttachments()[0])
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Poll;
    }
}