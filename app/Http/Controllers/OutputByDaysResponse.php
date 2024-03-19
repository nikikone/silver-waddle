<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class OutputByDaysResponse extends JsonResponse
{
    /**
     * @param array<int, string> $byHours
     */
    public function __construct(array $byHours)
    {
        parent::__construct(['byDays' => $byHours]);
    }
}
