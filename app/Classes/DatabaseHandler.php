<?php

namespace App\Classes;
use Illuminate\Database\Connection;

class DatabaseHandler{

    protected readonly Connection $connection;
    const TimeZoneDifferenceUnix = 36000;

    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    public function send(object $request){
        if ($this->checkConditions($request)){
            $object_day = $this->checkAvailabile($request, "days");
            if(is_null($object_day)){
                $id = $this->createDay($request);
                $id = $this->createHour($request, $id);
                $this->createMinute($request, $id);
            } else {
                $object_hour = $this->checkAvailabile($request, "hours", $object_day->day_id);
                if (is_null($object_hour)){
                    $id = $this->createHour($request, $object_day->day_id);
                    $this->createMinute($request, $id);
                } else {
                    $object_minute = $this->checkAvailabile($request, "minutes", $object_hour->hour_id);
                    if (is_null($object_minute)){
                        $this->createMinute($request, $object_hour->hour_id);
                    } else {
                        $this->updateMinute($request, $object_minute->minute_id);
                    }
                }
            }
        }
    }

    private function createDay(object $request): int{
        $parameters = ["date" => gmdate('Y-m-d', $request->getTime() + self::TimeZoneDifferenceUnix)];
        $this->connection->insert('INSERT INTO days (`date`) VALUES (:date)', $parameters);
        return $this->getLastInsertID();
    }

    private function createHour(object $request, int $day_id): int{
        $parameters = ["day_id" => $day_id, "hour" =>  gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix)];
        $this->connection->insert('INSERT INTO hours (`day_id`, `hour`) VALUES (:day_id, :hour)', $parameters);
        return $this->getLastInsertID();
    }

    private function createMinute(object $request, int $hour_id): void{
        $parameters = ["hour_id" => $hour_id, "minute" => gmdate('i', $request->getTime() + self::TimeZoneDifferenceUnix) + 1, "diff" => $request->getDiff()];
        $this->connection->insert('INSERT INTO minutes (`hour_id`, `minute`, `fullness`) VALUES (:hour_id, :minute, :diff)', $parameters);
    }

    private function getLastInsertID(){
        return $this->connection->selectOne("SELECT LAST_INSERT_ID() as id;")->id;
    }

    private function updateMinute($request, $minute_id){
        $parameters = ["diff" => $request->getDiff(), "minute_id" => $minute_id];
        $this->connection->update("UPDATE minutes SET fullness = fullness + :diff WHERE minute_id = :minute_id", $parameters);
    }

    private function checkAvailabile(object $request, string $type, int $id = -1){
        $result = null;
        switch ($type){
            case "days":
                $result = $this->connection->selectOne("SELECT * 
                                                        FROM days 
                                                        WHERE date = :date", 
                                                        ["date" => gmdate("Y-m-d", $request->getTime() + self::TimeZoneDifferenceUnix)]);
                break;
            case "hours":
                $result = $this->connection->selectOne("SELECT * 
                                                        FROM hours 
                                                        WHERE day_id = :day_id AND hour = :hour", 
                                                        ["hour" => gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix), "day_id" => $id]);
                break;
            case "minutes":
                $result = $this->connection->selectOne("SELECT * 
                                                        FROM minutes 
                                                        WHERE hour_id = :hour_id AND minute = :minute", 
                                                        ["minute" => gmdate('i', $request->getTime() + self::TimeZoneDifferenceUnix) + 1, "hour_id" => $id]);
                break;
        }
        return $result;
    }

    private function checkConditions(object $request){
        if (!((int) gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix) < 22 && (int) gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix) >= 10)){
            return False;
        }
        return True;
    }

}