<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 20:53
 */
namespace AppBundle\Normalizers;

use AppBundle\Entity\Centre;
use AppBundle\Entity\Course;
use AppBundle\Services\Utils;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CourseNormalizer implements NormalizerInterface
{

    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'centre' => (new CentreNormalizer())->normalize($object->getCentre()),
            'numberOfStudents' => count($object->getStudents()),
        ];
    }


    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Course;
    }

}