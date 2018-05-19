<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 19/05/2018
 * Time: 9:34
 */

namespace AppBundle\Normalizers;

use AppBundle\Entity\Student;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class StudentNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'surname' => $object->getSurname(),
            'course' => (new CourseNormalizer())->normalize($object->getCourse())
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Student;
    }
}