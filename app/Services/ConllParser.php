<?php  namespace App\Services;

use App\Models\Relation;
use App\Models\Sentence;
use App\Models\Annotation;
use Gwaps4nlp\Models\Source;
use App\Models\Parser;
use App\Models\CatPos;
use App\Repositories\SentenceRepository;
use App\Events\BroadCastImport;
use Event;

class ConllParser extends CsvParser {

protected $_columns_10 = array('word_position','word','lemma','category_id','pos_id','features','governor_position','relation_id','projective_governor_position','projective_relation_id');

protected $_columns_5 = array('word_position','word','pos_id','governor_position','relation_id');

protected $_ignoreFirstLine = false;
protected $sentence = null;
protected $content_sentence = null;
protected $sentid = null;
protected $parser_name = null;
protected $parser = null;
public $corpus = null;
public $errors = array();
public $error = '';
protected $annotations = array();
protected $sentence_filter;
protected $mode;
protected $parse_sentence;
public $sentences_done;

    public function __construct($corpus,$filename,$sentence_filter='all',$mode='insert'){
        $this->sentence_filter = $sentence_filter;
        $this->mode = $mode;
        $this->sentences_done = 0;
        $this->lines_done = 0;
        $this->corpus = $corpus;
        parent::__construct($filename);
    }

    public function parseLine($line){
        try {
            if($line['word_position']==1){
                
                //extract of the sentid from attribute features
                if(!$this->sentid){
                    preg_match('/\|?sentid=(?<sentid>[^|]+)\|?/', $line['features'], $matches);
                    if(isset($matches['sentid'])) {
                        $this->sentid = trim($matches['sentid']);
                        if($this->sentence_filter=='1mod4'){
                            if(substr($this->sentid,-5)%4==1){
                                $this->parse_sentence = true;
                            }
                            else {
                                $this->parse_sentence = false;
                            }
                        }
                        elseif($this->sentence_filter=='3mod4'){
                            if(substr($this->sentid,-5)%4==3)
                                $this->parse_sentence = true;
                            else
                                $this->parse_sentence = false;
                        } else
                            $this->parse_sentence = true;
                    }
                }

                if($this->sentid && $this->parse_sentence) {

                    $this->sentence = Sentence::where('sentid',$this->sentid)->first();
                    // if($sentence)
                    //     throw new \Exception("sentence {$this->sentid} already exists, import aborted");
                    if(!$this->sentence){
                        $this->sentence = Sentence::create([
                            'corpus_id' => $this->corpus->id,
                            'source_id' => $this->corpus->source_id,
                            'sentid' => $this->sentid,
                        ]);
                    }

                    $this->sentence->content=$line['word'].' ';
                } elseif(!$this->parse_sentence) {
                    return;
                } else {
                    throw new \Exception("sentid not found");
                }

            } elseif($this->sentence  && $this->sentence->id && $this->parse_sentence) {
                $this->sentence->content.=$line['word'].' ';
            } else {
                $this->sentid = null;
                return;
            }
            
            if(!$this->parse_sentence) return;

            if(!$line['relation_id']) $line['relation_id'] = "_";
            if(!$line['governor_position']) $line['governor_position'] = 0;
            $line['relation_id'] = Relation::getIdBySlug($line['relation_id']);
            $line['category_id'] = CatPos::getIdBySlug($line['category_id']);
            $line['pos_id'] = CatPos::getIdBySlug($line['pos_id']."_pos");
            $line['projective_relation_id'] = 0;
            $line['projective_governor_position'] = 0;
            $line['sentence_id'] = $this->sentence->id;
            $line['source_id'] = $this->corpus->source_id;
            $line['corpus_id'] = $this->corpus->id;
            
            $this->annotations[]= $line;
        } catch (\Exception $ex) {
            $this->error="Line {$this->lines_done} : ".$ex->getMessage();
            Event::fire(new BroadCastImport($this));
            $this->error="";
        }

    }

    public function checkNumberColumns($line){

        if(count($line)==5)
            $this->_columns = $this->_columns_5;
        else
            $this->_columns = $this->_columns_10;

    }

    public function parseBlankLine($line=array("")){

        preg_match('/#\s?sentid:\s?(?<sentid>\S+)/', $line[0], $matches);

        if (isset($matches['sentid'])){
            $this->sentid = trim($matches['sentid']);
            
            if($this->sentence_filter=='1mod4'){

                if(substr($this->sentid,-5)%4==1)
                    $this->parse_sentence = true;
                else
                    $this->parse_sentence = false;
            }
            elseif($this->sentence_filter=='3mod4'){
                if(substr($this->sentid,-5)%4==3)
                    $this->parse_sentence = true;
                else
                    $this->parse_sentence = false;
            } else
                $this->parse_sentence = true;
        }

        if(!isset($this->parser)&&preg_match('/parser:/', $line[0])){
            $parser_name = trim(substr($line[0],1));
            $this->parser = Parser::where('name',$parser_name)->first();
            if(!$this->parser){
                $this->parser = Parser::create([
                    'name' => $parser_name
                ]);
            }
        }

        if(count($line)<2 && $line[0]==''){
            if($this->sentence && !is_null($this->sentence->id)){
                foreach($this->annotations as $annot){
                    $annotation_min = $annot;
                    unset($annotation_min['features']);
                    unset($annotation_min['lemma']);
                    unset($annotation_min['category_id']);
                    unset($annotation_min['pos_id']);                    

                    if($this->mode=="update"){
                        unset($annotation_min['relation_id']);
                        $annotation = Annotation::where($annotation_min)->first();
                        if($annotation){
                            $annotation->relation_id    = $annot['relation_id'];
                            $annotation->features       = $annot['features'];
                            $annotation->lemma          = $annot['lemma'];
                            $annotation->category_id    = $annot['category_id'];
                            $annotation->pos_id         = $annot['pos_id'];
                            $annotation->governor_position = $annot['governor_position'];
                            $annotation->save();
                        }
                    } else {
                        $annotation = Annotation::where($annotation_min)->first();
                        if(!$annotation) {
                            $annotation = Annotation::create($annot);
                            $annotation->playable = 1;
                            $annotation->save();
                            $annotations_in_competition = $annotation->getAnnotationsInCompetition();
                            if($annotations_in_competition->count()>0){
                                foreach($annotations_in_competition as $annotation_in_competition){
                                    $annotation_in_competition->playable = 0;
                                    $annotation_in_competition->save();
                                }
                                
                            }
                        }
                        // else {
                        //     // The annotation already exists => non-playable (the 2 parsers are agree)
                        //     $annotation->playable = 0;
                        //     $annotation->save();
                        // }

                        // if the annotation exists, we add the features and lemma if they are different from the existent
                        if(!$annotation->lemma){
                            $annotation->lemma = $annot['lemma'];
                        } elseif($annotation->lemma != $annot['lemma']){
                            $lemmas = explode("/",$annotation->lemma);
                            if(!in_array($annot['lemma'],$lemmas))
                                $annotation->lemma .= "/".$annot['lemma'];
                        }

                        if(!$annotation->features){
                            $annotation->features = $annot['features'];
                        } elseif($annotation->features != $annot['features']){
                            $feat = array();
                            $new_feat = array();
                            $features = explode("|",$annotation->features);
                            foreach($features as $f){
                                if($f && preg_match('/=/',$f)){
                                    list($attr,$value) = explode("=",$f,2);
                                    $feat[$attr]=$value;
                                }
                            }
                            $new_features = explode("|",$annot['features']);
                            foreach($new_features as $f){
                                if($f && preg_match('/=/',$f)){
                                    list($attr,$value) = explode("=",$f,2);
                                    $new_feat[$attr]=$value;
                                }
                            }
                            foreach($new_feat as $key=>$value){
                                if(isset($feat[$key]) && $feat[$key]!=$value){
                                    $feat[$key] .="/".$value; 
                                } elseif(!isset($feat[$key])){
                                    $feat[$key] = $value;
                                }
                            }
                            $annotation->features = "";
                            foreach($feat as $attr=>$value){
                                $annotation->features.=$attr."=".$value."|";
                            }
                        }


                        if($this->parser){
                            $annotation->parsers()->save($this->parser);
                        }

                        if(!$annotation->score){
                            if($this->corpus->source_id == Source::getReference()->id)
                               $annotation->score = 10;
                            else
                               $annotation->score = 5;
                        } else {
                            $annotation->score += 5;                        
                        }

                        $annotation->save();

                        if($this->number_of_lines>50)
                            if(!($this->lines_done % round($this->number_of_lines/100)))
                                Event::fire(new BroadCastImport($this));
                        }
                }
                if($this->mode=="insert"){
                    $this->sentence->difficulty = $this->getDifficulty();
                    $this->sentence->save();
                }
                $this->sentences_done++;
            }
            $this->sentence=null;
            $this->sentid=null;
            $this->annotations=[];
        }
    }

    public function postParse() {
        $this->parseBlankLine();
        $this->lines_done = $this->number_of_lines;
        Event::fire(new BroadCastImport($this));
    }
    
    private function getDifficulty(){
        //On initialise l'index
        $i = 1.5;

        //On initalise la difficulté
        $difficulte = 0;

        //On compte le nombre d'annotation
        $nbAnnotations = count($this->annotations);

        //On va parcourir tous les espaces entre les mots
        while($i < $nbAnnotations ){

            //On initialise le nombre d'arcs traversant
            $nbTravers = 0;

            //On parcourt toutes les annotations
            foreach ($this->annotations as $annotation){

                //Si ce n'est pas le root
                if($annotation['word_position'] != 0){
                    //Si on a un arc qui traverse
                    if( ($annotation['word_position'] < $i && $annotation['governor_position'] > $i ) 
                        || ($annotation['word_position'] > $i && $annotation['governor_position'] < $i) ){
                        $relation = Relation::find($annotation['relation_id']);
                        $nbTravers+=$relation->coefficient;
                    }
                }
            }

            //Si le nombre dépasse la difficulté
            if($nbTravers > $difficulte){
                //On change la difficulté
                $difficulte = $nbTravers;
            }
            $i++;
        }
        return $difficulte;
    }
}