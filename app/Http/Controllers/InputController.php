<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\{Connection, DatabaseManager};

class InputController extends Controller
{
    private readonly Connection $connection;

    public function __construct(DatabaseManager $database)
    {
        $this->connection = $database->connection();
    }

    public function index(InputRequest $request): InputResponse
    {
        $object = $this->connection->selectOne('SELECT 2+:value AS a', ['value' => $request->getTime()]);
        return new InputResponse($object?->a === $request->getTime() + 2);
    }
}
