<?php

namespace App\Handlers\Commands;

use App\Commands\ImportCorpus;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ConllParser;

class ImportCorpusHandler
{
    /**
     * Create the command handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the command.
     *
     * @param  ImportCorpus  $command
     * @return void
     */
    public function handle(ImportCorpus $command)
    {
		$parser = new ConllParser($command->corpus,$command->filename);
        $parser->parse();
    }

}
