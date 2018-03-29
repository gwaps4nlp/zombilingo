<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Annotation;
use App\Models\StatsParser;
use App\Repositories\CorpusRepository;

class ComputeStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:compute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute the statistics';

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
    public function handle(CorpusRepository $corpora)
    {
        $evaluation_corpora = $corpora->getEvaluation();

        foreach($evaluation_corpora as $corpus){
            $yesterday_date = \Carbon\Carbon::now()->subDay()->format('Y-m-d');
            StatsParser::computeStats($corpus, $yesterday_date);
        }
    }
}
