<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\CorpusExportRequest;
use App\Http\Controllers\Controller;
use Gwaps4nlp\Core\Repositories\UserRepository;
use Gwaps4nlp\Core\Repositories\LicenseRepository;
use Gwaps4nlp\Core\Repositories\LanguageRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\CorpusRepository;
use App\Repositories\RelationRepository;
use App\Repositories\SentenceRepository;
use App\Services\ConllParser;
use App\Services\ConllExporter;
use App\Services\Talismane;
use App\Services\Melt;
use App\Services\Grew;
use App\Services\MweExporter;
use App\Http\Requests\CorpusRequest;
use App\Http\Requests\ExportCorpusRequest;
use App\Http\Requests\CorpusFileRequest;
use App\Http\Requests\CorpusCreateRequest;

use App\Models\ExportedCorpus;
use App\Models\Annotation;
use App\Models\AnnotationUser;
use App\Models\Parser;
use App\Models\StatsParser;
use App\Models\Relation;
use App\Models\Corpus;
use App\Models\CatPos;
use Storage, Response, DB;

use Illuminate\Http\RedirectResponse;
use App\Jobs\ParseCorpus;
use App\Jobs\ExportCorpus;
use Event, Queue, Config, App;

use App\Services\SeleniumServer;
use App\Jobs\Selenium;

class CorpusController extends Controller
{
    /**
     * Create a new CorpusController instance.
     *
     * @param  App\Repositories\CorpusRepository $corpus
     * @param  App\Repositories\LanguageRepository $language
     * @param  App\Repositories\LicenseRepository $license
     * @return void
     */
    public function __construct(CorpusRepository $corpus, LanguageRepository $language, LicenseRepository $license)
    {
        $this->middleware('admin');
        $this->corpus = $corpus;
        $this->language = $language;
        $this->license = $license;
    }

    /**
     * Show the detail of a given corpus.
     *
     * @param  int $id the id of the corpus
     * @return Illuminate\Http\Response
     */
    public function getShow(Corpus $corpus)
    {
        return view('back.corpus.show',compact('corpus'));
    }

    /**
     * Show a listing of the corpora.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        $corpora = $this->corpus->getAll();
        $languages = $this->language->getList();
        $licenses = $this->license->getList();
        return view('back.corpus.index',compact('corpora','languages','licenses'));
    }

    /**
     * Show a listing of the corpora with stat about players.
     *
     * @return Illuminate\Http\Response
     */
    public function getStatPlayer(Request $request)
    {
        if($request->has('date')){
          $date = $request->input('date');
        } else {
          $date = null;
        }
        $corpora = $this->corpus->getAll();
        $languages = $this->language->getList();
        $licenses = $this->license->getList();
        return view('back.corpus.stat-player',compact('corpora','languages','licenses','date'));
    }


    /**
     * Show a form to edit a corpus.
     *
     * @param  Corpus $corpus the corpus to edit
     * @return Illuminate\Http\Response
     */
    public function getEdit(Corpus $corpus)
    {
        $languages = $this->language->getList();
        $licenses = $this->license->getList();
        $reference_corpora = $this->corpus->getListReference();
        $evaluation_corpora = $this->corpus->getListEvaluation();
        $preannotated_corpora = $this->corpus->getListPreAnnotated();
        return view('back.corpus.edit',compact('corpus','languages','licenses','reference_corpora','evaluation_corpora','preannotated_corpora'));
    }

    /**
     * Save the update of a corpus.
     *
     * @param  Corpus $corpus the corpus to save
     * @param  App\Http\Requests\CorpusCreateRequest $request
     * @return Illuminate\Http\Response
     */
    public function postEdit(Corpus $corpus, CorpusCreateRequest $request)
    {
        $corpus->update($request->all());
        $this->attachCorpora($corpus,$request);
        return $this->getIndex();
    }

    /**
     * Show the form to create a new corpus.
     *
     * @return Illuminate\Http\Response
     */
    public function getCreate()
    {
        $corpora = $this->corpus->getAll();
        $languages = $this->language->getList();
        $licenses = $this->license->getList();
        $reference_corpora = $this->corpus->getListReference();
        $evaluation_corpora = $this->corpus->getListEvaluation();
        $preannotated_corpora = $this->corpus->getListPreAnnotated();
        return view('back.corpus.create',compact('corpora','languages','licenses','reference_corpora','evaluation_corpora','preannotated_corpora'));
    }

    /**
     * Create a new corpus
     *
     * @param  App\Http\Requests\CorpusCreateRequest $request
     * @return Illuminate\Http\Response
     */
    public function postCreate(CorpusCreateRequest $request)
    {
        $corpus = $this->corpus->create($request->all());
        $this->attachCorpora($corpus,$request);
        return $this->getIndex();
    }

    /**
     * Attach corpora to an another
     *
     * @param  App\Models\Corpus $corpus
     * @param  App\Http\Requests\CorpusCreateRequest $request
     * @return Illuminate\Http\Response
     */
    private function attachCorpora(Corpus $corpus, CorpusCreateRequest $request){
        $corpus->bound_corpora()->detach();
        $corpus->evaluation_corpora()->detach();
        $corpus->subcorpora()->detach();
        if($corpus->isPreAnnotated()){
            if(is_array($request->input('reference_corpus')))
            foreach($request->input('reference_corpus') as $corpus_id){
                $reference_corpus = $this->corpus->getById($corpus_id);
                $corpus->bound_corpora()->save($reference_corpus);
            }
            if(is_array($request->input('evaluation_corpus')))
            foreach($request->input('evaluation_corpus') as $corpus_id){
                $evaluation_corpus = $this->corpus->getById($corpus_id);
                $corpus->evaluation_corpora()->save($evaluation_corpus);
            }
            if(is_array($request->input('subcorpus')))
            foreach($request->input('subcorpus') as $corpus_id){
                $subcorpus = $this->corpus->getById($corpus_id);
                $corpus->subcorpora()->save($subcorpus);
            }
        }
    }

    /**
     * Delete a corpus.
     *
     * @param  App\Http\Requests\CorpusRequest $request
     * @return Illuminate\Http\Response
     */
    public function getDelete(CorpusRequest $request)
    {
        $this->corpus->destroy($request->input('id'));
        return new RedirectResponse(url('/corpus/post-export'));
    }

    /**
     * Display the form to import a corpus file.
     *
     * @return Illuminate\Http\Response
     */
    public function getImport()
    {
        $corpora = $this->corpus->getList();
        return view('back.corpus.import',compact('corpora'));
    }

    /**
     * Display a form to import a corpus from a wikiepdia's page or from a raw text.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getImportFromUrl(Request $request)
    {
        $melt = new Talismane();
        $melt->getVersion();
        $sentences='';
        $corpora = $this->corpus->getList();
        $sentence_splitters = Config::get('parser.sentence-splitter');
        return view('back.corpus.import-url',compact('corpora','sentences','sentence_splitters'));
    }

    /**
     * Import a text from wikipedia and clean it.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postImportFromUrl(Request $request)
    {

        if($request->hasFile('conll')){
            return $this->postParse($request);
        }

        $this->validate($request, [
            'url' => 'required|url|max:255'
        ]);
        $url = $request->input('url');

        $url = str_replace("'","%27",$url);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $text = curl_exec ($ch);

        $dom = new \DomDocument();

        foreach(Range(0,3) as $attempt){
            try {
                $dom -> loadHTML($text);
                continue;
            } catch (\Exception $Ex) {
                $message = $Ex->getMessage();
                if(preg_match_all('/Tag ([a-z]+) invalid/',$message,$match,PREG_SET_ORDER)){
                    $tag = $match[0][1];
                    $text = preg_replace('#</?'.$tag.'[^>]*>#is', '', $text);
                }

            }
        }

        $content = $dom->getElementById('bodyContent');
        $sentences='';
        foreach($content->getElementsByTagName('p') as $paragraph){
            $sentences.=strip_tags($paragraph->textContent)."\n";
        }

        $sentences = $this->cleanText($sentences);

        Storage::disk('local')->put('original-text.txt', $sentences);
        $sentence_splitters = Config::get('parser.sentence-splitter');
        return view('back.corpus.import-url',compact('sentences','url','sentence_splitters'));
    }

    /**
     * Clean a text.
     *
     * @param  string $text the text to clean
     * @return string the clean text
     */
    private function cleanText($text){
        $text=preg_replace('/\[([0-9]+|[a-zA-Z]\s[0-9]+|Note\s[0-9]+)\]/','',$text);
        $text=preg_replace('/(,)+\./','.',$text);
        $text=preg_replace('/(,)+/',',',$text);

        $tab = ['/’/','/—/','/«\xC2\xA0/','/\xC2\xA0»/','/« /','/ »/','/«/','/»/'];
        $tabr = ['\'','-','"','"','"','"','"','"'];
        $text=preg_replace($tab,$tabr,$text);

        $text=preg_replace('/\{\{(.+)\}\}/','',$text);
        return $text;
    }

    /**
     * Split a raw text in sentences and show the result
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postSentencesSplitter(Request $request){

        $text = $request->input('raw-text');
        $url = $request->input('url');
        $sentence_splitter = $request->input('sentence_splitter');
        App::bind('App\Services\ParserInterface', Config::get('parser.'.$sentence_splitter.'.service'));
        $parser = App::make('App\Services\ParserInterface');
        $sentences_splitted = $parser->splitSentences($text);
        $parsers = Config::get('parser.parser');
        return view('back.corpus.sentences-splitted',compact('sentences_splitted','url','parser','parsers'));
    }

    /**
     * Tokenize a text and show the result
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postTokeniser(Request $request){

        $text = $request->input('text');
        $talismane = new Talismane();
        $result = $talismane->tokenize($text);
        return view('back.corpus.post-tokeniser',compact('result'));
    }

    /**
     * POS tag a text and show the result
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postPosTagger(Request $request){

        $text = $request->input('text');

        $talismane = new Talismane();
        $melt = new Melt();

        $result = $talismane->posTag($text);
        $result_melt = $melt->posTag($text);

        return view('back.corpus.pos-tag',compact('result','result_melt'));
    }

    /**
     * Parse a text and show the result
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postParse(Request $request){
        $talismane = new Talismane($request->input('sentence_filter'));
        $grew = new Grew($request->input('sentence_filter'));
        $grew->files = [];
        $grew->commands = [];
        if($request->hasFile('conll')){
            $result = $talismane->parseFromConll($request->file('conll')->getRealPath());
            $result_grew = $grew->parseFromConll($request->file('conll')->getRealPath());
            $array_conll_talismane = $this->ConllToArray($result);
            $array_conll_grew = $this->ConllToArray($result_grew);
        } else {
            $text = $request->input('text');
            $text_id = $request->input('url')."_".date("Ymd");
            $result = $talismane->parse($text,$text_id);
            $result_grew = $grew->parse($text,$text_id);
            // $result_grew = "";
            $array_conll_talismane = $this->ConllToArray($result);
            $array_conll_grew = $this->ConllToArray($result_grew);
            // $array_conll_grew = [];
        }

        $corpora = $this->corpus->getList();

        return view('back.corpus.parse',compact('corpora','result','result_grew','array_conll_talismane','array_conll_grew','talismane','grew'));
    }

    /**
     * Compute the stats from start_date to end_date of a given corpus
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getComputeStatsByDate(Request $request){

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $start = new \Carbon\Carbon($start_date);
        $end = new \Carbon\Carbon($end_date);
        $days = $start->diff($end)->days;

        for($i = 0; $i <= $days; $i++)
        {
            $date = '';
            echo $start->format('Y-m-d');
            $corpus = $this->corpus->getById($request->input('id'));
            StatsParser::updateStats($corpus, $start->format('Y-m-d'));
            $start->addDays(1);
            echo "<br/>";
        }

    }

    /**
     * Show the comparison between 2 different parsings of the same corpus
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getCompare(Request $request){

        if($request->has('date'))
            $date = $request->input('date');
        else
            $date = \Carbon\Carbon::now()->subDay()->format('Y-m-d');


        $corpora_list = $this->corpus->getListEvaluation();
        $corpora = $this->corpus->getEvaluation();

        if($request->has('corpus_id'))
            $corpus_evaluation = $this->corpus->getById($request->input('corpus_id'));
        else
            $corpus_evaluation = $this->corpus->getById($corpora->last()->id);

        $corpus_evaluated = $corpus_evaluation->evaluated_corpus()->first()->id;

        $stats_parser = StatsParser::getStats($corpus_evaluation, $date);
        if($stats_parser->isEmpty()){
            $date = StatsParser::select('date')->where('corpus_control',$corpus_evaluation->id)->orderBy('date','desc')->first()->date;
            $stats_parser = StatsParser::getStats($corpus_evaluation, $date);
        }
        $stats_parser_tot = StatsParser::getStatsTotal($corpus_evaluation, $date, false);
        $stats_parser_total = StatsParser::getStatsTotal($corpus_evaluation, $date, false, ['ponct']);
        $stats_parser_except_root = StatsParser::getStatsTotal($corpus_evaluation, $date, false, ['ponct','root']);
        $stats_parser_playable = StatsParser::getStatsTotal($corpus_evaluation, $date, true);
        $rate_correct_answers["trouverTete"] = StatsParser::getRateCorrectAnswers("trouverTete");
        $rate_correct_answers["trouverDependant"] = StatsParser::getRateCorrectAnswers("trouverDependant");
        $rate_correct_answers_incorrect_relation["trouverTete"] = StatsParser::getRateCorrectAnswersIncorrectRelation("trouverTete");
        $rate_correct_answers_incorrect_relation["trouverDependant"] = StatsParser::getRateCorrectAnswersIncorrectRelation("trouverDependant");

        $parser_id = ($request->has('parser_id'))?$request->input('parser_id'):'game';
        $stats_relation = StatsParser::getStatsRelationByRelation($corpus_evaluation->id, $parser_id);
        $scores_by_parser = StatsParser::getScores($corpus_evaluation);
        $stats_by_relation = [];
        $scores = [];
        $data_gnuplot = "Parser\t";

        foreach($scores_by_parser as $score){
            $scores[$score->parser_id] = $score;
        }

        foreach($stats_relation as $stat){
            if(!isset($relations_confusion_matrix[$stat->control_relation_name]))
                $relations_confusion_matrix[$stat->control_relation_name] = $stat->control_relation_id;
            if(!isset($relations_confusion_matrix[$stat->parser_relation_name]))
                $relations_confusion_matrix[$stat->parser_relation_name] = $stat->parser_relation_id;
            $stats_by_relation[$stat->control_relation_id][$stat->parser_relation_id] = $stat->count;
        }

        ksort($relations_confusion_matrix,SORT_NATURAL);
        $stats = array();
        $parsers = array();
        $relations = array();
        $a_relations = array();

        $a_relations[] = "total<br/>".$stats_parser_playable->first()->done."/".$stats_parser_playable->first()->total."<br/>avg:".round($stats_parser_playable->first()->answers/$stats_parser_playable->first()->total,2)."";

        foreach($stats_parser as $stat){
            if(!isset($parsers[$stat->parser_id])){
                $parsers[$stat->parser_id] = $stat->parser_name;
                $parsers_name[] = '"'.$stat->parser_name.'"';
            }
            if(!isset($relations[$stat->relation_name])){
                $relations[$stat->relation_name] = $stat->relation_id;
                $a_relations[] = $stat->relation_name."<br/>".$stat->done."/".$stat->total."<br/>avg:".round($stat->answers_by_annot,2)."";
            }
            $stats[$stat->relation_name][$stat->parser_id] = $stat;
        }

        $data_gnuplot .= join($parsers_name,"\t")."\n";

        foreach($stats as $relation_name=>$stat){
            $data_gnuplot .= '"'.str_replace("_","\\\\_",$relation_name)."\"\t";
            $data_relation = [];
            foreach($parsers as $_parser_id=>$parser){
                $data_relation[]=$stat[$_parser_id]->fscore;
            }
            $data_gnuplot .= join($data_relation,"\t")."\n";
        }
        Storage::put('fscore.dat', $data_gnuplot);
        $command = config('gnuplot.binary')." -e \"output='".public_path('img')."/test.svg';datafile='".storage_path('app')."/fscore.dat'\" ".storage_path('gnuplot')."/fscore.gnu";
        exec($command,$output,$retour);
        return view('back.corpus.compare',compact('parser_id','corpora','corpora_list','corpus_evaluation','stats','parsers','relations','a_relations','stats_by_relation','scores','request','relations_confusion_matrix','rate_correct_answers','rate_correct_answers_incorrect_relation','stats_parser_tot','stats_parser_total','stats_parser_except_root','stats_parser_playable','date'));

    }

    /**
     * Show the comparison between 2 different parsings of the same corpus
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getCompareConll(Request $request, AnnotationRepository $annotation){

        $corpus = $this->corpus->getById($request->input('corpus_id'));
        $annotations = $annotation->getByRelation($corpus, $request->input('parser1_id'),$request->input('parser1_relation_id'),$request->input('parser2_id'),$request->input('parser2_relation_id'));
        return view('back.corpus.compare-conll',compact('corpus','annotations'));

    }

    /**
     * Show a graph of the confidence of users versus the number of annotations produced
     *
     * @return Illuminate\Http\Response
     */
    public function getConfidenceByUser(){

        $confidence_by_user = Annotation::getConfidenceByUser();
        return view('back.corpus.confidence-by-user',compact('confidence_by_user'));

    }

    /**
     * Show a graph of the evolution of f-score versus relation
     *
     * @param  App\Repositories\RelationRepository $relations
     * @return Illuminate\Http\Response
     */
    public function getEvolutionScores(RelationRepository $relations){

        foreach($relations->getListPlayable() as $relation_id=>$name){
            $scores_relation[$relation_id] = StatsParser::getStatsRelation('best',$relation_id,5,1,0);
        }
        return view('back.corpus.evolution-score',compact('scores_relation'));

    }

    /**
     * Show the difference of pos between two different parsings
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDiffByPos(Request $request){

        if(!$request->has("parser1")){
            $parser1 = "ref";
            $parser2 = 1;
        } else {
            $parser1 = $request->input("parser1");
            $parser2 = $request->input("parser2");
        }

        $corpora = $this->corpus->getListEvaluation();
        $default_corpus = $this->corpus->getById(16);
        $differences_by_pos = StatsParser::getDiffByPos(16, $parser1, $parser2);
        $differences_by_cat = StatsParser::getDiffByCat(16, $parser1, $parser2);
        $categories = CatPos::where('parent_id','=',0)->orderBy('slug')->get();
        $pos = CatPos::where('parent_id','!=',0)->orderBy('slug')->get();
        $parsers = Parser::all();

        $diff_by_pos = array();
        $diff_by_cat = array();
        foreach($pos as $pos1){
            $diff_by_pos[$pos1->slug] = array();
            foreach($pos as $pos2){
                $diff_by_pos[$pos1->slug][$pos2->slug] = 0;
            }
        }
        foreach($differences_by_pos as $diff_pos){
            $diff_by_pos[$diff_pos->pos1_slug][$diff_pos->pos2_slug] = $diff_pos->count;
        }

        foreach($categories as $cat1){
            $diff_by_cat[$cat1->slug] = array();
            foreach($categories as $cat2){
                $diff_by_cat[$cat1->slug][$cat2->slug] = 0;
            }
        }
        foreach($differences_by_cat as $diff_cat){
            $diff_by_cat[$diff_cat->pos1_slug][$diff_cat->pos2_slug] = $diff_cat->count;
        }
        return view('back.corpus.diff-by-pos',compact('corpora','default_corpus','diff_by_pos','diff_by_cat','categories','pos','parsers','parser1','parser2'));

    }

    /**
     * Show the difference of relations between two different parsings
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDiffByRelation(Request $request){

        if(!$request->has("parser1")){
            $parser1 = "ref";
            $parser2 = 1;
        } else {
            $parser1 = $request->input("parser1");
            $parser2 = $request->input("parser2");
        }

        $corpora_parsers = $this->corpus->getListParsers();

        $corpora = $this->corpus->getListEvaluation()->union($this->corpus->getListPreAnnotated());

        if($request->has('corpus_id'))
            $corpus = $this->corpus->getById($request->input('corpus_id'));
        else
            $corpus = $this->corpus->getById(16);

        $differences_by_relation = StatsParser::getDiffByRelation($corpus, $parser1, $parser2);
        $relations = Relation::where('type','!=','special')->orderBy('slug')->get();

        $parsers = Parser::all();

        $diff_by_relation = array();

        foreach($relations as $relation1){
            $diff_by_relation[$relation1->slug] = array();
            foreach($relations as $relation2){
                $diff_by_relation[$relation1->slug][$relation2->slug] = 0;
            }
        }
        foreach($differences_by_relation as $diff_relation){
            $diff_by_relation[$diff_relation->relation1_slug][$diff_relation->relation2_slug] = $diff_relation->count;
        }

        return view('back.corpus.diff-by-relation',compact('corpora','corpora_parsers','corpus','default_corpus','diff_by_relation','relations','parsers','parser1','parser2'));

    }

    /**
     * Convert a text in conll format to array
     *
     * @param  string $conll
     * @return array
     */
    private function ConllToArray($conll){
        $_columns_10 = array('word_position','word','lemma','category_id','pos_id','features','governor_position','relation_id','projective_governor_position','projective_relation_id');
        $lines = explode("\n",$conll);
        $index_sentence = 0;
        $result = array();
        $result[$index_sentence] = array();
        $tab = ['/aux_pass/','/aux_tps/','/aux_caus/','/mod_rel/','/dep_coord/','/prep/','/sub/','/p_obj$/'];
        $tabr = ['aux.pass','aux.tps','aux.caus','mod.rel','dep.coord','obj.p','obj.cpl','p_obj.o'];
        foreach($lines as $line){
            if($line && substr($line,0,1)!="#"){
                $line = explode("\t",$line);
                $line = array_combine($_columns_10,$line);
                $word_position = $line['word_position'];
                $line['relation_id'] = preg_replace($tab,$tabr,$line['relation_id']);
                $result[$index_sentence][$word_position] = $line;
            } else {
                if(isset($result[$index_sentence]) && empty($result[$index_sentence])){

                } else {
                    $index_sentence++;
                    $result[$index_sentence]=array();
                }
            }
        }

        if(empty($result[$index_sentence])) unset($result[$index_sentence]);

        return $result;
    }

    /**
     * Launch the process to import a corpus file.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postImport(Request $request)
    {

        $corpus = $this->corpus->getById($request->input('corpus_id'));

        $destinationPath= storage_path()."/import/";
        $fileName=$corpus->name.'.conll';

        $request->file('corpus_file')->move($destinationPath,$fileName);
        $filePath = $destinationPath.$fileName;

        $sentence_filter = $request->input('sentence_filter');
        if(!$sentence_filter) $sentence_filter = 'all';

        $mode = $request->input('mode');
        if(!$mode) $mode = 'insert';

        $parser = new ConllParser($corpus,$filePath,$sentence_filter,$mode);
        $parser->parse();
        // $job = (new ParseCorpus($parser));

        // $this->dispatch($job);

        return view('back.corpus.post-import',compact('corpus'));
        // return new RedirectResponse(url('/corpus/post-import'));
    }

    /**
     * Save a parsing.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postSaveParse(Request $request)
    {

        $corpus = $this->corpus->getById($request->input('corpus_id'));

        foreach($request->input("files") as $file){
            $filePath = storage_path()."/app/".$file;
            $parser = new ConllParser($corpus,$filePath);
            $parser->parse();
            // $job = (new ParseCorpus($parser));
            // $this->dispatch($job);
        }
        return view('back.corpus.post-save-parse',compact('corpus'));
    }

    /**
     * Show progress of current imports.
     *
     * @return Illuminate\Http\Response
     */
    public function getPostImport()
    {

        $redis = Queue::getRedis();
        $list_imports_pending = $redis->command('LRANGE',['queues:import', '0', '-1']);
        $imports_pending = [];
        foreach($list_imports_pending as $item){
            $object = json_decode($item)->data;
            $job = unserialize($object->command);
            $imports_pending[]= $job;
        }

        return view('back.corpus.post-import',compact('imports_pending'));
    }

    /**
     * Show progress of current exports.
     *
     * @return Illuminate\Http\Response
     */
    public function getPostExport()
    {

        $redis = Queue::getRedis();
        $list_exports_pending = $redis->command('LRANGE',['queues:import', '0', '-1']);
        $exports_pending = [];
        $exported_corpuses = ExportedCorpus::whereNotNull('corpus_id')->orderBy('created_at','desc')->get();

        return view('back.corpus.post-export',compact('exports_pending','exported_corpuses'));
    }

    /**
     * Show the form to export a corpus file .
     *
     * @return Illuminate\Http\Response
     */
    public function getFile(Request $request)
    {
        $headers=['Content-Type'=> "text/css"];
        $response = Response::download(storage_path('app/'.$request->file), $request->file, $headers);
        return $response;
    }

    /**
     * Show the form to export a corpus file .
     *
     * @return Illuminate\Http\Response
     */
    public function getExport()
    {
        $corpora = $this->corpus->getList();
        $exported_corpuses = ExportedCorpus::whereNotNull('corpus_id')->orderBy('created_at','desc')->paginate();
        return view('back.corpus.export',compact('corpora','corpus_id','exported_corpuses'));
    }

    /**
     * Show the form to export a file with the mwes.
     *
     * @return Illuminate\Http\Response
     */
    public function getExportMwe()
    {

        $exported_corpuses = ExportedCorpus::where('type','=','mwe')->orderBy('created_at','desc')->get();
        return view('back.corpus.export-mwe',compact('corpora','corpus_id','exported_corpuses'));
    }

    /**
     * Export a file with the mwes.
     *
     * @return Illuminate\Http\Response
     */
    public function postExportMwe()
    {
        $parser = new MweExporter(Auth::user());
        $parser->export();
        $exported_corpuses = ExportedCorpus::where('type','mwe')->orderBy('created_at','desc')->get();
        return view('back.corpus.post-export-mwe',compact('exported_corpuses','parser'));
    }

    /**
     * Export a corpus file.
     *
     * @param App\Http\Requests\ExportCorpusRequest $request
     * @return Illuminate\Http\Response
     */
    public function postExport(ExportCorpusRequest $request)
    {
        $corpus = $this->corpus->getById($request->input('corpus_id'));

        Annotation::computeScore($request->input('score_init'),$request->input('weight_level'),$request->input('weight_confidence'));

        $parser = new ConllExporter($corpus,Auth::user(),$request->input('type_export'));
        $job = (new ExportCorpus($parser));

        $this->dispatch($job);
        return new RedirectResponse(url('/corpus/post-export'));
    }

    /**
     * Compute the complexity of all the sentences of the database
     *
     * @return Illuminate\Http\Response
     */
    public function getComputeComplexity(SentenceRepository $sentences_repo, CorpusRepository $corpus_repo)
    {

        $corpora = $corpus_repo->getAll();


        foreach($corpora as $corpus){
            if($corpus->id!=43)
                continue;
            $i = 0;
            $sentences = $corpus->sentences;

            $parsers = DB::table('parsers')->whereExists(function ($query) use ($corpus){
                $query->select(DB::raw(1))
                      ->from('annotations')
                      ->join('annotation_parser','annotation_id','=','annotations.id')
                      ->whereRaw('annotation_parser.parser_id = parsers.id')
                      ->whereRaw('annotations.corpus_id ='.$corpus->id);
            })->get();

            foreach($sentences as $sentence){

                $complexity = 0;
                if(count($parsers)>0){
                    echo "parsers<br/>";
                    foreach($parsers as $parser){
                        echo $parser->id."<br/>";
                        $complexity_parser = $sentence->getComplexity($parser->id);
                        if($complexity_parser > $complexity)
                            $complexity = $complexity_parser;

                    }
                } else {
                    $complexity = $sentence->getComplexity();
                }
                echo $sentence->id." : ".$complexity."<br/>";
                $sentence->complexity = $complexity;
                $sentence->save();

            }
        }

        return view('back.test.test');
    }

    /**
     * Delete a corpus
     *
     * @param App\Http\Requests\CorpusRequest $request
     * @return Illuminate\Http\Response
     */
    public function postDelete(CorpusRequest $request)
    {
        $this->corpus->destroy($request->input('id'));
        return $this->getIndex();
    }

    /**
     * Split Annotations
     *
     * @param App\Http\Requests\CorpusRequest $request
     * @return Illuminate\Http\Response
     */
    public function getSplitAnnotations()
    {
        $annotations = Annotation::where('word','LIKE','%\_%')->take(2)->get();
        $relation_dep_cpd = Relation::where('slug','dep_cpd')->first();
        foreach($annotations as $original_annotation){
            $word_splitted = explode('_' , $original_annotation->word);
            $shift = count($word_splitted)-1;
            $this->shiftRight($original_annotation->sentence, $original_annotation->word_position, $shift);
            $parsers = $original_annotation->parsers;

            foreach($word_splitted as $index => $word){
                echo $index."=>".$word;
                if($index==0){
                    $original_annotation->word = $word;
                    $original_annotation->save();
                } else {
                    $new_annotation = Annotation::firstOrCreate([
                        'corpus_id' => $original_annotation->corpus_id,
                        'sentence_id' => $original_annotation->sentence->id,
                        'relation_id' => $relation_dep_cpd->id,
                        'word' => $word,
                        'word_position' => $original_annotation->word_position + $index,
                        'governor_position' => $original_annotation->word_position,
                        'playable' => 0,
                    ]);
                    foreach($original_annotation->parsers as $parser){
                        $new_annotation->parsers()->save($parser);
                        echo $parser->name."<br/>";
                    }
                }
            }
            echo $original_annotation->word."<br/>";
            echo $original_annotation->sentence->content."<br/>";
        }

        return view('back.test.test');
    }
    /**
     * Split Annotations
     *
     * @param App\Http\Requests\CorpusRequest $request
     * @return Illuminate\Http\Response
     */
    private function shiftRight($sentence, $index, $shift)
    {
        Annotation::where('sentence_id', '=', $sentence->id)->where('word_position','>',$index)->update(['word_position' => DB::raw("word_position + $shift") ]);
        Annotation::where('sentence_id', '=', $sentence->id)->where('governor_position','>',$index)->update(['governor_position' => DB::raw("governor_position + $shift") ]);
        AnnotationUser::where('sentence_id', '=', $sentence->id)->where('word_position','>',$index)->update(['word_position' => DB::raw("word_position + $shift") ]);
        AnnotationUser::where('sentence_id', '=', $sentence->id)->where('governor_position','>',$index)->update(['governor_position' => DB::raw("governor_position + $shift") ]);
    }

}
