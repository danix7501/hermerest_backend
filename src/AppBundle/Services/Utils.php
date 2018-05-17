<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 20:41
 */

namespace AppBundle\Services;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



class Utils
{
    public function serializeArray($dataArray, NormalizerInterface $normalizer)
    {
        $result = [];

        foreach ($dataArray as $dataItem)
            array_push($result, $normalizer->normalize($dataItem));

        return $result;
    }

    public function serializeArrayWithDetail($dataArray, $normalizer)
    {
        $result = [];

        foreach ($dataArray as $dataItem)
            array_push($result, $normalizer->normalizeWithDetail($dataItem));

        return $result;
    }
}