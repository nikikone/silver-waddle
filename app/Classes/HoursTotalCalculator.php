<?php

namespace App\Classes;

class HoursTotalCalculator extends Calculator{

    public function calculate(): array{
        $array = $this->getSQLResponse();
        $array = $this->toArrayFromSQL($array);
        krsort($array);
        return $array;
    }

    protected function getSQLResponse(): array{
        return $this->connection->select('SELECT b.date AS date, a.hour AS hour, a.total AS total 
                                          FROM hours AS a JOIN days AS b 
                                          ON a.day_id = b.day_id 
                                          WHERE a.total is not NULL;');
    }

    protected function toArrayFromSQL(array $array): array{
        foreach($array as $item){
            $result[$item->date][$item->hour] = $item->total;
        }
        return array_values($result);
    }

    protected function handleArray(array $array): array{
        return [];
    }
}