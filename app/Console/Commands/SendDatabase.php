<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the database backup to a remote server';

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
        $dump_file = storage_path('app/dump.sql');

        $command_send_file = "scp ".$dump_file." ".config('database.backup-server.user')."@".config('database.backup-server.host').":".config('database.backup-server.path');

        system($command_send_file, $return_var);

    }
}
