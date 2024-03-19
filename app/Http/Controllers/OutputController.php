<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\{Connection, DatabaseManager};
use Random\RandomException;

class OutputController extends Controller
{
    private readonly Connection $connection;

    public function __construct(DatabaseManager $database)
    {
        $this->connection = $database->connection();
    }

    public function byDays(): OutputByDaysResponse
    {
        $object = $this->connection->selectOne('SELECT 2+5 AS a');
        return new OutputByDaysResponse([
            1  => 'low',
            2  => 'high',
            3  => $object->a > 0 ? 'high' : 'low',
            //.....
            28 => 'medium',
            29 => 'medium',
            30 => 'high',
            31 => 'medium',
        ]);
    }

    public function byHours(): OutputByHoursResponse
    {
        $result = [];
        for ($i = 10; $i <= 21; ++$i) {
            try {
                $result[$i] = ['low', 'medium', 'high'][random_int(0, 2)];
            } catch (RandomException) {
            }
        }

        return new OutputByHoursResponse($result);
    }
}
