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
    const passwordLength = 8;
    const characters = 'abcdefghijklmnopqrstuvwxyz012345678901234567890123456789';


    public function generateRandomPassword()
    {
        $password = '';
        $max = strlen(Utils::characters) - 1;

        for ($i = 0; $i < Utils::passwordLength; $i++)
            $password .= Utils::characters[mt_rand(0, $max)];

        return $password;
    }

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