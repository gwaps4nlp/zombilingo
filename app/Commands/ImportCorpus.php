<?php

namespace App\Commands;

use App\Commands\Command;
use App\Models\Corpus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class ImportCorpus extends Command implements ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

	public $corpus;
	public $filename;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Corpus $corpus, $filename)
    {
        $this->corpus = $corpus;
        $this->filename = $filename;
    }	
}
