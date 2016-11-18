<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Corpus;
use App\Models\Annotation;
use App\Models\Source;
use App\Services\ConllExporter;
use App\Services\MweExporter;

class ExportCorpus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corpus:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export pre-annotated corpora and mwes list';

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
        Annotation::computeScore();
        $user = User::where('username','admin')->first();
        $source_preannotated = Source::getPreAnnotated();
        $corpora = Corpus::where('source_id','=',$source_preannotated->id)->get();

        foreach($corpora as $corpus){
            $parser = new ConllExporter($corpus,$user);
            $file = str_replace(' ','-',$corpus->name).'-'.date('Ymd').'.conll';
            $parser -> export($file);
        }
        $parser = new MweExporter($user);
        $file = 'mwe-'.date('Ymd').'.csv';
        $parser->export($file);        

    }
}
