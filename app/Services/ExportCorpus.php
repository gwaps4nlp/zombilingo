<?php  namespace App\Services;

use App\Models\Relation;
use App\Models\Sentence;
use App\Models\Annotation;
use App\Repositories\SentenceRepository;


class ExportCorpus extends CsvParser {

protected $_columns = array('word_position','word','lemma','category_id','pos_id','features','governor_position','relation_id','projective_governor_position','projective_relation_id');

protected $_ignoreFirstLine = false;
protected $sentence = null;
protected $content_sentence = null;
protected $sentid = null;
protected $corpus = null;
public $errors = array();
protected $annotations = array();
protected $lines = array();
public $nb_sentences = 0;

    public function __construct($corpus,$filename){
    	$this->corpus = $corpus;
    	parent::__construct($filename);
    }

    public function parseLine($line){

        if($line['word_position']==1){
    		
    		//extract of the sentid from attribute features
            preg_match('/\|?sentid=(?P<sentid>[^|]+)\|?/', $line['features'], $matches);
    		
            if(isset($matches['sentid'])) {
            	$this->sentid = $matches['sentid'];
    			$this->sentence = Sentence::create([
    				'corpus_id' => $this->corpus->id,
    				'source_id' => $this->corpus->source_id,
    				'sentid' => $this->sentid,
    			]);
    			$this->sentence->content=$line['word'].' ';
            } else {
    			$this->errors[]="sentid not found line no.".$this->_line_number;
    		}
        } else {
    		$this->sentence->content.=$line['word'].' ';
    	}
    	$line['relation_id'] = Relation::getIdBySlug($line['relation_id']);
    	$line['projective_relation_id'] = Relation::getIdBySlug($line['projective_relation_id']);
    	$line['sentence_id'] = $this->sentence->id;
    	$line['source_id'] = $this->corpus->source_id;
    	$this->annotations[]= $line;
    }

    public function parseBlankLine($line){
    	$this->postParse();
    }

    public function postParse() {
    	
    	if(!is_null($this->sentence->id)){
    		Annotation::insert($this->annotations);
    		$this->sentence->difficulty = $this->getDifficulty();
    		$this->sentence->save();
    		$this->sentence->id=null;
    		$this->nb_sentences++;
    	}
    	$this->annotations=[];
    }
    private function getDifficulty(){
        //On initialise l'index
        $i = 1.5;

        //On initalise la difficulté
        $difficulte = 1;

        //On compte le nombre d'annotation
        $nbAnnotations = count($this->annotations);

        //On va parcourir tous les espaces entre les mots
        while($i < $nbAnnotations ){

            //On initialise le nombre d'arcs traversant
            $nbTravers = 0;

            //On toutes les annotations
            foreach ($this->annotations as $annotation){

                //Si ce n'est pas le root
                if($annotation['word_position'] != 0){
                    //Si on a un arc qui traverse
                    if( ($annotation['word_position'] < $i && $annotation['word_position'] > $i ) 
						|| ($annotation['word_position'] > $i && $annotation['word_position'] < $i) ){
                        $nbTravers++;
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
