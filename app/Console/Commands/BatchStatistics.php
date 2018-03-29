<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Annotation;
use App\Models\StatsParser;
use App\Repositories\CorpusRepository;


class BatchStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch to compute scores of parser with different parameters';

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
        $scores_init = [0,5,10];
        $weights_level = [1,2];
        $weights_confidence_user = [0,7,14];
        $yesterday_date = \Carbon\Carbon::now()->subDay()->format('Y-m-d');
        foreach($evaluation_corpora as $corpus){
            foreach($scores_init as $score_init){
                foreach($weights_level as $weight_level){
                    foreach($weights_confidence_user as $weight_confidence_user){
                        StatsParser::updateStats('game', $corpus, $yesterday_date,$score_init, $weight_level, $weight_confidence_user);
                    }
                }
            }
        }
    }
}
