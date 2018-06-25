<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 16/06/2018
 * Time: 13:06
 */

namespace AppBundle\Normalizers;


use AppBundle\Entity\Teacher;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TeacherNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'course' => $object->getCourse() == null ? null : (new CourseNormalizer())->normalize($object->getCourse()),
            'username' => $object->getUser()->getUsername()
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Teacher;
    }
}