<?php

namespace App\Classes;

class Converter{

    private const CONSTANT_VALUE = 80;

    public static function mergeDaysArray(array $arrayFirst, array $arraySecond): array
    {
        $result = [];
        for ($key = array_key_first($arrayFirst); $key <= array_key_last($arrayFirst); ++$key) {
            $new_array = array_merge($arrayFirst[$key], $arraySecond[$key]);
            if (sizeof($new_array) != 0) {
                $result[$key] = (int) round(array_sum($new_array) / sizeof($new_array));
            } else {
                $result[$key] = 0;
            }
        }
        return $result;
    }

    public static function mergeHoursArray(array $arrayFirst, array $arraySecond): array
    {
        $result = [];
        $array = array_merge($arrayFirst, $arraySecond);
        foreach ($array as $subArray) {
            foreach ($subArray as $key => $value) {
                if (!isset($result[$key])) {
                    $result[$key] = $value;
                } else {
                    $result[$key] += $value;
                }
            }
        }
        foreach ($result as $key => $value) {
            $result[$key] = (int) round($value / sizeof($array));
        }
        ksort($result);
        return $result;
    }

    public static function decorateArrayToOutput(array $array)
    {
        
        foreach ($array as $key => $value) {
            $procent = (int) round(($value / self::CONSTANT_VALUE) * 100);
            if ($procent <= 50) {
                $array[$key] = "low";
            } elseif ($procent > 50 && $procent <= 80) {
                $array[$key] = "medium";
            } else {
                $array[$key] = "high";
            }
        }
        return $array;
    }
}