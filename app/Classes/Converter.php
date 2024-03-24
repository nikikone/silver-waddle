<?php

namespace App\Classes;

class Converter{

    public static function mergeDaysArray(array $array_first, array $array_second): array{
        $result = [];
        for($key = array_key_first($array_first); $key <= array_key_last($array_first); ++$key){
            $new_array = array_merge($array_first[$key], $array_second[$key]);
            if (sizeof($new_array) != 0){
                $result[$key] = (int) round(array_sum($new_array) / sizeof($new_array));
            } else {
                $result[$key] = 0;
            }
        }
        return $result;
    }

    public static function mergeHoursArray(array $array_first, array $array_second): array{
        $result = [];
        $array = array_merge($array_first, $array_second);
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
        return $result;
    }

    public static function decorateArrayToOutput(array $array){
        $constant_value = 80;
        foreach($array as $key => &$value){
            $procent = (int) round(($value / $constant_value) * 100);
            if ($procent <= 50){
                $array[$key] = "low";
            } elseif ($procent > 50 && $procent <= 80){
                $array[$key] = "medium";
            } else {
                $array[$key] = "high";
            }
        }
        return $array;
    }
}