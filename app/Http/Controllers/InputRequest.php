<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class InputRequest extends FormRequest
{
    public function getDiff(): int
    {
        return (int)$this->get('diff', 0);
    }

    public function getTime(): int
    {
        return (int)$this->get('time', 0);
    }
}
