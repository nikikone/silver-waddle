<?php

namespace App\Classes;
use Illuminate\Database\Connection;

class DatabaseHandler{

    protected readonly Connection $connection;
    private const TimeZoneDifferenceUnix = 36000;

    public function __construct(Connection $connection) 
    {
        $this->connection = $connection;
    }

    public function send(object $request): void
    {
        if ($this->checkConditions($request)) {
            $objectDay = $this->checkAvailabile($request, "days");
            if (is_null($objectDay)) {
                $id = $this->createDay($request);
                $id = $this->createHour($request, $id);
                $this->createMinute($request, $id);
            } else {
                $objectHour = $this->checkAvailabile($request, "hours", $objectDay->day_id);
                if (is_null($objectHour)) {
                    $id = $this->createHour($request, $objectDay->day_id);
                    $this->createMinute($request, $id);
                } else {
                    $objectMinute = $this->checkAvailabile($request, "minutes", $objectHour->hour_id);
                    if (is_null($objectMinute)) {
                        $this->createMinute($request, $objectHour->hour_id);
                    } else {
                        $this->updateMinute($request, $objectMinute->minute_id);
                    }
                }
            }
        }
    }

    private function createDay(object $request): int
    {
        $parameters = ["date" => gmdate('Y-m-d', $request->getTime() + self::TimeZoneDifferenceUnix)];
        $this->connection->insert('INSERT INTO days (`date`) 
                                   VALUES (:date)', 
                                   $parameters);
        return $this->getLastInsertID();
    }

    private function createHour(object $request, int $day_id): int
    {
        $parameters = ["day_id" => $day_id, 
                       "hour" =>  gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix)];
        $this->connection->insert(
            'INSERT INTO hours (`day_id`, `hour`) 
            VALUES (:day_id, :hour)', 
            $parameters);
        return $this->getLastInsertID();
    }

    private function createMinute(object $request, int $hour_id): void
    {
        $parameters = ["hour_id" => $hour_id, 
                       "minute" => gmdate('i', $request->getTime() + self::TimeZoneDifferenceUnix) + 1, 
                       "diff" => $request->getDiff()];
        $this->connection->insert(
            'INSERT INTO minutes (`hour_id`, `minute`, `fullness`) 
            VALUES (:hour_id, :minute, :diff)', 
            $parameters);
    }

    private function getLastInsertID(): int
    {
        return $this->connection->selectOne(
            "SELECT LAST_INSERT_ID() as id;")->id;
    }

    private function updateMinute($request, $minute_id): void
    {
        $parameters = ["diff" => $request->getDiff(), 
                       "minute_id" => $minute_id];
        $this->connection->update(
            "UPDATE minutes SET fullness = fullness + :diff 
            WHERE minute_id = :minute_id", 
            $parameters);
    }

    private function checkAvailabile(object $request, string $type, int $id = -1): object|null
    {
        $result = null;
        switch ($type) {
            case "days":
                $result = $this->connection->selectOne(
                    "SELECT * 
                    FROM days 
                    WHERE date = :date", 
                    ["date" => gmdate("Y-m-d", $request->getTime() + self::TimeZoneDifferenceUnix)]);
                break;
            case "hours":
                $result = $this->connection->selectOne(
                    "SELECT * 
                    FROM hours 
                    WHERE day_id = :day_id AND hour = :hour", 
                    ["hour" => gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix), 
                    "day_id" => $id]);
                break;
            case "minutes":
                $result = $this->connection->selectOne(
                    "SELECT * 
                    FROM minutes 
                    WHERE hour_id = :hour_id AND minute = :minute", 
                    ["minute" => gmdate('i', $request->getTime() + self::TimeZoneDifferenceUnix) + 1, 
                    "hour_id" => $id]);
                break;
        }
        return $result;
    }

    private function checkConditions(object $request): bool
    {
        if (!((int) gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix) < 22 && 
              (int) gmdate('H', $request->getTime() + self::TimeZoneDifferenceUnix) >= 10)) {
            return False;
        }
        return True;
    }

    public function updateHourDeleteMinuts(int $hour_id, int $total): void
    {
        $this->updateHour($total, $hour_id);
        $this->deleteMinuts($hour_id);
    }

    private function deleteMinuts(int $hour_id): void
    {
        $parameters = ["hour_id" => $hour_id];
        $this->connection->delete(
            'DELETE FROM minutes 
            WHERE hour_id = :hour_id', 
            $parameters);
    }

    public function updateDayTotal($day_total, $day_id): void
    {
        $this->updateDay($day_total, $day_id);
    }

    public function getDayHourNotTotal(): array
    {
        return $this->connection->select(
            'SELECT a.day_id, a.date, b.hour_id, b.hour 
            FROM (SELECT * FROM days WHERE total is NULL) as a 
            JOIN (SELECT * FROM hours WHERE total is NULL) as b 
            ON a.day_id = b.day_id;');
    }

    public function createHourWhitTotal(int $hour, int $total, int $day_id): void
    {
        $parameters = ["day_id" => $day_id, "hour" =>  $hour, "total" => $total];
        $this->connection->insert(
            'INSERT INTO hours (`day_id`, `hour`, `total`) 
            VALUES (:day_id, :hour, :total)', 
            $parameters);
    }

    private function updateHour($total, $hour_id): void
    {
        $parameters = ["total" => $total, "hour_id" => $hour_id];
        $this->connection->update(
            'UPDATE hours SET total = :total 
            WHERE hour_id = :hour_id', 
            $parameters);
    }

    private function updateDay($total, $day_id): void
    {
        $parameters = ["total" => $total, "day_id" => $day_id];
        $this->connection->update(
            'UPDATE days SET total = :total 
            WHERE day_id = :day_id', 
            $parameters);
    }

}