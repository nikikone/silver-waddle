<?php

namespace App\Console\Commands;

use App\Classes\Updateor;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;

class HandleDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle database to update info from total attribute in "days" and "hours" tables';
    private Updateor $updateor;
    protected readonly Connection $connection;

    public function __construct(DatabaseManager $database){
        parent::__construct();
        $this->connection = $database->connection();
        $this->updateor = new Updateor($this->connection);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->updateor->start();
    }

}
