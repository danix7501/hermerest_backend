<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 19:53
 */

namespace AppBundle\Normalizers;


use AppBundle\Services\Facades\ScheduleFacade;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ScheduleNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'schedule' => $object->getSchedule(),
            'timeFrom' => $object->getTimeFrom(),
            'timeTo' => $object->getTimeTo(),
            'status' => $object->getStatus(),
            'student' => $object->getStudent() == null ? null : (new StudentNormalizer())->normalize($object->getStudent()),
            'teacher' => $object->getTeacher() == null ? null : (new TeacherNormalizer())->normalize($object->getTeacher())
        ];
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ScheduleFacade;
    }
}