<?php

namespace App\Classes;
use Illuminate\Database\Connection;

class Updateor{

    protected readonly Connection $connection;
    private DatabaseHandler $dbHandler;
    private HoursCalculator $hoursCalc;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->dbHandler = new DatabaseHandler($this->connection);
        $this->hoursCalc = new HoursCalculator($this->connection);
    }

    public function start(): void
    {
        $hoursOfDates = $this->hoursCalc->calculate();
        $objects = $this->dbHandler->getDayHourNotTotal();
        $dateToID = $this->arrayObjectsDayToIdDay($objects);
        $DayIdToHourIdToHour = $this->arrayObjectsToArray($objects);
        $this->iterator($hoursOfDates, $DayIdToHourIdToHour, $dateToID);
    }

    private function iterator(array $hoursOfDates, array $DayIdToHourIdToHour, array $dateToID): void
    {
        foreach($hoursOfDates as $date => $hours){
            $day_id = $dateToID[$date];
            $day_total = (int) round(array_sum($hours) / 12);
            foreach( $hours as $hour => $fullness) {
                if (array_key_exists($hour, $DayIdToHourIdToHour[$day_id]))  {
                    $hour_id = $DayIdToHourIdToHour[$day_id][$hour];
                    $this->dbHandler->updateHourDeleteMinuts($hour_id, $fullness);
                } else {
                    $this->dbHandler->createHourWhitTotal($hour, $fullness, $day_id);
                }
            }
            $this->dbHandler->updateDayTotal($day_total, $day_id);
        }
    }

    private function arrayObjectsToArray(array $array): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item->day_id][$item->hour] = $item->hour_id;
        }
        return $result;
    }

    private function arrayObjectsDayToIdDay(array $array): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item->date] = $item->day_id;
        }
        return $result;
    }
}