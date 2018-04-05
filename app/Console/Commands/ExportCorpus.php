<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Corpus;
use App\Models\Annotation;
use Gwaps4nlp\Core\Models\Source;
use App\Models\ExportedCorpus;
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
        // Annotation::computeScore();
        $user = User::where('username','admin')->first();
        $corpora = Corpus::where('exportable',1)->get();

        foreach($corpora as $corpus){
            if(count($corpus->subcorpora)){
                $nb_sentences = 0;
                $files = [];
                $readme = $corpus->description."\xA";
                $readme .= "This archive contains the files :\xA";
                foreach($corpus->subcorpora as $subcorpus){
                    print("sub : ".$subcorpus->id. "\xA");
                    $parser = new ConllExporter($subcorpus,$user);
                    $file = str_replace(' ','-',$subcorpus->name).'-'.date('Ymd').'.conll';
                    $parser -> export($file);
                    if($parser->sentences_done>0){
                        $files[] = storage_path('export/'.$file);
                        $nb_sentences += $parser->sentences_done;
                        $readme.=$file."\xA";
                        if($subcorpus->url_source)
                            $readme.="Source : ".$subcorpus->url_source."\xA";
                        $readme.="License : ".$subcorpus->license->label."\xA\xA";
                    }
                }

                $file = str_replace(' ','-',$corpus->name).'-'.date('Ymd').'.zip';

                $zipper = new \Chumper\Zipper\Zipper;

                $zipper->make(storage_path('export/'.$file))
                    ->folder($file)
                    ->add($files)
                    ->addString("README.txt", $readme);

                $exported_corpus = ExportedCorpus::create(['file'=>$file,'user_id'=>$user->id,'corpus_id'=>$corpus->id]);
                $exported_corpus->type = 'simple';
                $exported_corpus->nb_sentences = $nb_sentences;
                $exported_corpus->save();           
            }
            else {
                $parser = new ConllExporter($corpus,$user);
                $file = str_replace(' ','-',$corpus->name).'-'.date('Ymd').'.conll';
                $parser -> export($file);                
            }

        }
        $parser = new MweExporter($user);
        $file = 'mwe-'.date('Ymd').'.csv';
        $parser->export($file);        

    }
}
