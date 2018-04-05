<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gwaps4nlp\Core\Repositories\UserRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\SentenceRepository;
use App\Repositories\RelationRepository;
use App\Repositories\LevelRepository;
use App\Repositories\CorpusRepository;
use App\Models\AnnotationUserAnnotator;
use App\Models\Relation;
use App\Models\Sentence;
use Gwaps4nlp\Core\Models\Source;
use App\Models\CatPos;
use App\Models\Annotation;
use App\Models\ConfigAnnotator;
use Illuminate\Http\RedirectResponse;
use DB;

class AnnotatorController extends Controller
{
	/**
     * Create a new AdminController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    /**
     * Show a listing of projects of annotations.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        $projects = $this->projects->getAll();
        return view('back.annotator.index',compact('projects'));
    } 

    /**
     * Show the graph of a given sentence
     *
     * @param  int $id the identifier of the sentence
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getGraph($id, RelationRepository $relation, SentenceRepository $sentences)
    {
        $relations = $relation->getList();
        $pos = CatPos::where('parent_id','!=',0)->orderBy('slug')->pluck('slug','id');
        $sentence = $sentences->getById($id);
        $attributes_select = ['sentence_id','corpus_id','word_position','governor_position','relation_id','source_id','category_id','pos_id','features','lemma','word'];
        $first = $sentence->annotations_user_annotator()
                        ->selectRaw(join(',',$attributes_select).', user_id, 0 as best, 0 as id, 1000 as score');
        $annotations = $sentence->annotations()
                        ->selectRaw(join(',',$attributes_select).', 0 as user_id, best, id, score')
                        ->union($first)
                        ->with('parsers')
                        ->orderBy('word_position')
                        ->orderBy('best','desc')      
                        ->get();
        $parsers = [];
        $config = ConfigAnnotator::where('user_id',Auth::user()->id)->first();

        foreach($annotations as $annotation){
            foreach($annotation->parsers as $parser){
                if(!isset($parsers[$parser->id]))
                    $parsers[$parser->id] = $parser;
            }
        }
        return view('back.annotator.correction',compact('relations','sentence','parsers','pos','annotations','config'));
    }
    /**
     * Show the graph of a given sentence
     *
     * @param  int $id the identifier of the sentence
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getCorrection($id, RelationRepository $relation, SentenceRepository $sentences)
    {
        $relations = $relation->getList();
        $pos = CatPos::where('parent_id','!=',0)->orderBy('slug')->pluck('slug','id');
        $sentence = $sentences->getById($id);
        $attributes_select = ['sentence_id','corpus_id','word_position','governor_position','relation_id','source_id','category_id','pos_id','features','lemma','word'];
        $first = $sentence->annotations_user_annotator()
            ->selectRaw(join(',',$attributes_select).', user_id, 0 as best, 0 as id, 1000 as score');
        $annotations = $sentence->annotations()
            ->selectRaw(join(',',$attributes_select).', 0 as user_id, best, id, score')
            ->union($first)
            ->with('parsers')
            ->orderBy('word_position')
            ->orderBy('user_id','desc')
            ->orderBy('best','desc')      
            ->get();
        $parsers = [];
        $config = ConfigAnnotator::where('user_id',Auth::user()->id)->first();
        foreach($annotations as $annotation){
            foreach($annotation->parsers as $parser){
                if(!isset($parsers[$parser->id]))
                    $parsers[$parser->id] = $parser;
            }
        }
        return view('back.annotator.correction',compact('relations','sentence','parsers','pos','annotations','config'));
    } 

    /**
     * Show the graph of a given sentence
     *
     * @param  int $id the identifier of the sentence
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getGraphCorpus($corpus_id, RelationRepository $relation, SentenceRepository $sentence_repo, CorpusRepository $corpuses)
    {
        $corpus = $corpuses->getById($corpus_id);
        
        $sentences_ids = DB::table('annotations')->selectRaw('distinct sentence_id ids')->where('corpus_id',$corpus->id)->pluck('ids');   

        $sentences = Sentence::whereIn('id',$sentences_ids)->paginate(1);

        $relations = $relation->getList();
        $pos = CatPos::where('parent_id','!=',0)->orderBy('slug')->pluck('slug','id');
        $sentence = $sentence_repo->getById($sentences->first()->id);

        $attributes_select = ['sentence_id','corpus_id','word_position','governor_position','relation_id','source_id','category_id','pos_id','features','lemma','word'];
        
        if($corpus->isPreAnnotatedForEvaluation()){

            $first = $sentence->annotations()->where('corpus_id',$corpus->id)
            ->selectRaw(join(',',$attributes_select).', "gold" user_id, 0 as best, 0 as id, 1000 as score');
            
            $annotations = $sentence->annotations()->where('corpus_id', '<>', $corpus->id)
            ->selectRaw(join(',',$attributes_select).', 0 as user_id, best, id, score')
            ->union($first)
            ->with('parsers')
            ->orderBy('word_position')
            ->orderBy('best','desc')
            ->get();
            $mode = 'diff';
        } else {
            $annotations = $sentence->annotations()->where('corpus_id', $corpus->id)
            ->selectRaw(join(',',$attributes_select).', 0 as user_id, best, id, score')
            ->with('parsers')
            ->orderBy('word_position')
            ->orderBy('best','desc')
            ->get();
            $mode = 'view';
        }
        $parsers = [];
        foreach($annotations as $annotation){
            foreach($annotation->parsers as $parser){
                if(!isset($parsers[$parser->id]))
                    $parsers[$parser->id] = $parser;
            }
        }
        return view('back.sentence.graph',compact('relations','sentences','sentence','parsers','pos','annotations','mode'));
    }     
    /**
     * Save annotations.
     *
     * @return Illuminate\Http\Response
     */
    public function postSave(Request $request)
    {
        $data_sentence = $request->input('sentence');
        $sentence_id = $data_sentence['id'];

        // reference ?
        $sentence = Sentence::findOrFail($sentence_id);



        foreach($request->input('annotations') as $key=>$annotation){

            if(!is_array($annotation)) continue;
            $relation_id = Relation::getIdBySlug($annotation['relation_name']);
            if(isset($annotation['pos']) && $annotation['pos']!=''){
                $pos = CatPos::where('slug',$annotation['pos']."_pos")->first();
                if($pos){
                    $pos_id = $pos->id;
                    $category_id = $pos->parent_id;                        
                }else {
                    $pos_id = 0;
                    $category_id = 0;
                }
            }
            else {
                $pos_id = 0;
                $category_id = 0;
            }

            if($sentence->isReference()){
                Annotation::where('sentence_id',$sentence->id)
                    ->where('word_position',$annotation['word_position'])
                    ->where('source_id',Source::getReference()->id)
                    ->update([
                        'relation_id' => $relation_id,
                        'governor_position'=> $annotation['governor_position'],
                        'word'=> $annotation['word'],
                        'category_id'=> $category_id,
                    ]);
            } else {
                $new_annotation = AnnotationUserAnnotator::updateOrCreate(
                    ['sentence_id' => $sentence_id, 
                     'user_id' => Auth::user()->id, 
                     'word_position'=> $annotation['word_position'], 
                     ],
                    ['relation_id' => $relation_id,
                     'governor_position'=> $annotation['governor_position'],
                     'pos_id'=> $pos_id,
                     'word'=> $annotation['word'],
                     'category_id'=> $category_id,
                     'source_id' => 5,            
                     'corpus_id' => 14,            
                     'lemma' => $annotation['lemma'],               
                     'features' => $annotation['features'],              
                    ]
                );
            }

        }

    }

    /**
     * Save config annotator's config.
     *
     * @return Illuminate\Http\Response
     */
    public function postSaveConfig(Request $request)
    {
        $config = $request->input('config');
        $config_json = json_encode($config);

        $new_config = ConfigAnnotator::updateOrCreate(
            ['user_id' => Auth::user()->id],
            ['config' => $config_json]
        );

    }



}
