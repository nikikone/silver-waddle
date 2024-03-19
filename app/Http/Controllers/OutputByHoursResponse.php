<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class OutputByHoursResponse extends JsonResponse
{
    /**
     * @param array<int, string> $byHours
     */
    public function __construct(array $byHours)
    {
        parent::__construct(['byHours' => $byHours]);
    }
}
