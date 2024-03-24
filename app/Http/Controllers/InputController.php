<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\{Connection, DatabaseManager};
use Illuminate\Support\Facades\DB;
use App\Classes\DatabaseHandler;

class InputController extends Controller
{
    private readonly Connection $connection;
    private DatabaseHandler $dbHendler;

    public function __construct(DatabaseManager $database)
    {
        $this->connection = $database->connection();
        $this->dbHendler = new DatabaseHandler($this->connection);
    }

    public function index(InputRequest $request): InputResponse
    {
        $this->dbHendler->send($request);
        return new InputResponse(True);
    }
}
