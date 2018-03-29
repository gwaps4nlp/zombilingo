<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the database backup.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('The current database will be overwritten. Are you sure you want to continue ?')) {

            $dump_file = storage_path('app/dump.sql');

            $command = "mysql -h ".env('DB_HOST')." -u ".env('DB_USERNAME')." -p".env('DB_PASSWORD')." ".env('DB_DATABASE')." < ".$dump_file;

            system($command, $return_var);
            
        }

    }
}
