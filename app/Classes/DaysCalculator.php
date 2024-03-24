<?php

namespace App\Classes;

class DaysCalculator extends Calculator{

    public function calculate(): array
    {
        $array = $this->getSQLResponse();
        $array = $this->toArrayFromSQL($array);
        $array = $this->handleArray($array);
        return $array;
    }

    protected function getSQLResponse(): array
    {
        return $this->connection->select(
            'SELECT a.date as date, DAY(a.date) AS day, b.hour AS hour, c.minute AS minute, c.fullness as diff 
            FROM (SELECT * FROM days WHERE total is NULL) AS a 
            JOIN (SELECT * FROM hours WHERE total is NULL) AS b 
            JOIN (SELECT * FROM minutes) AS c 
            ON a.day_id=b.day_id AND b.hour_id=c.hour_id 
            ORDER BY day, hour, minute');
    }

    protected function toArrayFromSQL(array $array): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item->date][$item->hour][$item->minute] = $item->diff;
        }
        return $result;
    }

    protected function handleArray(array $array): array
    {
        $result = array_fill(1, 31, []);
        foreach ($array as $date => $arrayValue) {
            $day = date('d', strtotime($date));
            $result[$day][] = (int) round(array_sum($this->calculationHoursTotal($arrayValue)) / 12);
        }
        return $result;
    }

    private function calculationHoursTotal(array $array): array
    {
        $dayCounterFullness = 0;
        $result = [];
        for ($hour = 10; $hour < 22; ++$hour) {
            $leftBorder = 0;
            $hourFullness = 0;
            if (array_key_exists($hour, $array)) {
                foreach ($array[$hour] as $minute => $diff){
                    $hourFullness += ($minute - $leftBorder) * $dayCounterFullness;
                    $dayCounterFullness += $diff;
                    $leftBorder = $minute;
                }
            }
            $hourFullness += ((60 - $leftBorder) * $dayCounterFullness);
            $hourFullness /= 60;
            $result[$hour] = (int) round($hourFullness);
        }
        return $result;
    }
    
}