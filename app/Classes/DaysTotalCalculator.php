<?php

namespace App\Classes;

class DaysTotalCalculator extends Calculator {
    # Переписать на день без total
    
    public function calculate(): array{
        $array = $this->getSQLResponse();
        $array = $this->toArrayFromSQL($array);
        #$array = $this->handleArray($array);
        return $array;
    }

    protected function getSQLResponse(): array{
        return $this->connection->select('SELECT DAY(date) as day, total 
                                         FROM days WHERE total is not NULL;');
    }

    protected function toArrayFromSQL(array $array): array{
        $result = array_fill(1, 31, []);
        foreach($array as $item){
            $result[$item->day][] = $item->total;
        }
        return $result;
    }

    protected function handleArray(array $array): array{
        return [];
    }

}