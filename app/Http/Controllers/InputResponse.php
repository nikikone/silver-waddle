<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class InputResponse extends JsonResponse
{
    public function __construct(bool $success)
    {
        parent::__construct(['success' => $success]);
    }
}
