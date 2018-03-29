<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ConllParser;
use App\Models\Corpus;

class ParseCorpus extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
	
	public $parser;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ConllParser $parser)
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
        $this->parser->parse();
    }
	
	public function queue($queue, $command)
	{
		$queue->pushOn('import-'.app()->environment(), $command);
	}	
}
