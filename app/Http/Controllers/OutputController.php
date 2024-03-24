<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\{Connection, DatabaseManager};
use Random\RandomException;
use App\Classes\{DaysCalculator, DaysTotalCalculator, HoursCalculator, HoursTotalCalculator, Converter};

class OutputController extends Controller
{
    private readonly Connection $connection;
    private DaysCalculator $daysCalc;
    private DaysTotalCalculator $daysTotalCalc;
    private HoursCalculator $hoursCalc;
    private HoursTotalCalculator $hoursTotalCalc;

    public function __construct(DatabaseManager $database)
    {
        $this->connection = $database->connection();
        $this->daysCalc = new DaysCalculator($this->connection);
        $this->daysTotalCalc = new DaysTotalCalculator($this->connection);
        $this->hoursCalc = new HoursCalculator($this->connection);
        $this->hoursTotalCalc = new HoursTotalCalculator($this->connection);
    }

    public function byDays(): OutputByDaysResponse
    {
        $objects = $this->daysCalc->calculate();
        $objects_total = $this->daysTotalCalc->calculate();
        $objects = Converter::mergeDaysArray($objects, $objects_total);
        $result = Converter::decorateArrayToOutput($objects);
        return new OutputByDaysResponse($result);
    }

    public function byHours(): OutputByHoursResponse
    {
        $objects = $this->hoursCalc->calculate();
        $objects_total = $this->hoursTotalCalc->calculate();
        $objects = Converter::mergeHoursArray($objects, $objects_total);
        $result = Converter::decorateArrayToOutput($objects);
        return new OutputByHoursResponse($result);
    }

}
