<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sentence;
use DB;

class UpdateSentence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:sentences {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sentences text to deal with au/à+le problem';

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
        $filename = $this->argument('filename');
        $handle = fopen($filename, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $split = explode("\t", trim($line));
                $sents = Sentence::where('sentid', $split[0])->get();
                foreach ($sents as $sent) {
                    $id = $sent->id;
                    $new_content=$split[1];
                    DB::update('update Sentences set content = ? where id = ?', [$new_content, $id]);
                    $this->info($split[0] . " --> " . $id);
                }
            }
            if (!feof($handle)) {
                $this->info("Error: fgets() failed\n");
            }
            fclose($handle);
        }
    }
}
