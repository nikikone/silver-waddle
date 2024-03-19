<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseManager $database)
    {
        $value = (int)$this->argument('value');

        $connection = $database->connection();
        $result = $connection->select('SELECT 1+:someVal AS val', ['someVal' => $value]);

        dd($result);
    }
}
