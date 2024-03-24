<?php

namespace App\Classes;

class HoursCalculator extends Calculator{

    public function calculate(): array{
        $array = $this->getSQLResponse();
        $array = $this->toArrayFromSQL($array);
        $array = $this->handleArray($array);
        krsort($array);
        return $array;
    }
    
    protected function getSQLResponse(): array{
        return $this->connection->select('SELECT a.date as date, DAY(a.date) AS day, b.hour AS hour, c.minute AS minute, c.fullness as diff 
                                          FROM (SELECT * FROM days WHERE total is NULL) AS a 
                                          JOIN (SELECT * FROM hours WHERE total is NULL) AS b 
                                          JOIN (SELECT * FROM minutes) AS c 
                                          ON a.day_id=b.day_id AND b.hour_id=c.hour_id 
                                          ORDER BY day, hour, minute');
    }

    protected function toArrayFromSQL(array $array): array{
        $result = [];
        foreach ($array as $item) {
            $result[$item->date][$item->hour][$item->minute] = $item->diff;
        }
        return $result;
    }

    protected function handleArray(array $array): array{
        $result = [];
        foreach($array as $date =>&$array_value){
            $result[$date] = $this->calculationHoursTotal($array_value);
        }
        return $result;
    }

    private function calculationHoursTotal(array &$array): array{
        $day_counter_fullness = 0;
        $result = [];
        for ($hour = 10; $hour < 22; ++$hour){
            $left_coretca = 0;
            $hour_fullness = 0;
            if (array_key_exists($hour, $array)){
                foreach($array[$hour] as $minute => &$diff){
                    $hour_fullness += ($minute - $left_coretca) * $day_counter_fullness;
                    $day_counter_fullness += $diff;
                    $left_coretca = $minute;
                }
            }
            $hour_fullness += ((60 - $left_coretca) * $day_counter_fullness);
            $hour_fullness /= 60;
            $result[$hour] = (int) round($hour_fullness);
        }
        return $result;
    }
}