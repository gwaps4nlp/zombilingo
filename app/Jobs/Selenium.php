<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\SeleniumServer;

class Selenium extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
	
	public $selenium;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SeleniumServer $selenium)
    {
        $this->selenium = $selenium;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {	
        $this->selenium->start();
    }
	
	// public function queue($queue, $command)
	// {
	// 	$queue->pushOn('import-'.app()->environment(), $command);
	// }
}
