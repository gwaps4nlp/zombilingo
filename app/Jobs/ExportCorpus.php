<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ConllExporter;
use App\Models\Corpus;

class ExportCorpus extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
	
	public $parser;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ConllExporter $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {	
        $this->parser->export();
    }
	
	public function queue($queue, $command)
	{
		$queue->pushOn('export-'.app()->environment(), $command);
	}	
}
