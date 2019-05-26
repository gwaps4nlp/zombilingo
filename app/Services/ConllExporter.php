<?php  namespace App\Services;

use App\Models\Relation;
use App\Models\Sentence;
use App\Models\Annotation;
use App\Models\Corpus;
use App\Models\User;
use Gwaps4nlp\Core\Models\Source;
use App\Models\ExportedCorpus;
use App\Repositories\SentenceRepository;
use App\Events\BroadCastExport;
use Event;
use DB;

class ConllExporter {

protected $_columns = array('word_position','word','lemma','pos','category','features','governor_position','relation_name','projective_governor','projective_relation_name');

protected $_ignoreFirstLine = false;
protected $_separator = "\t";
public $annotations_done = null;
public $sentences_done = null;
public $error = '';
protected $content_sentence = null;
protected $sentid = null;
public $corpus = null;
protected $user;
public $url_file = null;
// public $errors = array();
protected $annotations = array();
protected $lines = array();
protected $number_columns = 0;
public $nb_sentences;
protected $file;

public function __construct(Corpus $corpus, User $user, $type_export="simple"){
    $this->annotations_done = 0;
    $this->sentence_id = 0;
    $this->sentences_done = 0;
    $this->nb_sentences = 0;
    $this->url_file = "";
	$this->corpus = $corpus;
	$this->user = $user;
	$this->type_export = $type_export;
	$this->number_columns = count($this->_columns);
}

public function export($file=null){

	//Event::fire(new BroadCastExport($this));

	$this->nb_sentences = $this->corpus->sentences()->count();
	if(!$this->nb_sentences) {
		$this->error = "Aucune phrase dans le corpus";
		//Event::fire(new BroadCastExport($this));
		return;
	}

	if(!$file)
		$file = str_replace(' ','-',$this->corpus->name).'-'.date('Ymd_His').'.conll';

	$this->file = fopen(storage_path('export/'.$file),"w");
	$this->addHeader();

	$sentences = $this->corpus->sentences()->get();

    foreach ($sentences as $sentence) {
    	fputs($this->file, "# sentid: ".$sentence->sentid."\n");
		$annotations = $sentence->annotations()->with('parsers')->with('relation')
		->whereNotNull('custom_score')
		->orderBy('sentence_id')
		->orderBy('word_position')
		->orderBy('custom_score','desc')
		->orderBy(DB::raw('if(word_position>governor_position, word_position-governor_position,governor_position-word_position)'))
		->orderBy('governor_position')
		->get();

		$previous_annotation = $annotations->first();

		foreach ($annotations as $annotation) {

			if($this->type_export!="simple"){
				$annotation->relation_id.=":".$annotation->custom_score;

				foreach($annotation->parsers as $parser){
					if(preg_match('/parser: grew/',$parser->name)) 
						$annotation->relation_id.="_G";
					if(preg_match('/parser: talismane/',$parser->name)) 
						$annotation->relation_id.="_T";
				}

				if($annotation->source_id==Source::getUser()->id)
					$annotation->relation_id.="_Z";
			}
			//we pass to the next word
			if($annotation->word_position!=$previous_annotation->word_position){

				$this->addToFile($previous_annotation);
				$previous_annotation = $annotation;

			// }elseif($annotation->score == $previous_annotation->score && $annotation->id != $previous_annotation->id){
			}elseif($this->type_export!="simple" && $annotation->id != $previous_annotation->id){

				$previous_annotation->relation_id .= '|'.$annotation->relation_id;
				$previous_annotation->governor_position .= '|'.$annotation->governor_position;

			} elseif($annotation->score < $previous_annotation->score){
				//TODO : add an option to choose if this annotation have to be exported 
				//$this->addToFile($previous_annotation,'#');
				//$previous_annotation = $annotation;
			}
			
		}
		//adding the last annotations of the sentence		
		if($previous_annotation)
			$this->addToFile($previous_annotation);	
		fputs($this->file, "\n");


		if(!(++$this->sentences_done % 10))
			Event::fire(new BroadCastExport($this));
	}

	
	if($this->sentences_done>0){
		$exported_corpus = ExportedCorpus::create(['file'=>$file,'user_id'=>$this->user->id,'corpus_id'=>$this->corpus->id]);
		$exported_corpus->type = $this->type_export;
		$exported_corpus->nb_sentences = $this->sentences_done;
		$exported_corpus->save();
		$this->url_file = 'asset/conll?exported_corpus_id='.$exported_corpus->id;		
	} else {
		$this->error = "Aucune phrase exportée";
	}
	
	fclose($this->file);
	Event::fire(new BroadCastExport($this));

	}
	
	protected function addHeader(){
		$header='';
		$header.='# License : '.$this->corpus->license->label."\n";
		$header.='# Description : '.str_replace("\n","\n# ",$this->corpus->description)."\n";
		$header.='# Contact : contact@zombilingo.org'."\n";

		fputs($this->file, $header."\n");
	}

	protected function addToFile($annotation,$commentary=''){
		if($annotation->word_position==99999)
			$commentary='#';
		$annotation->setVisible($this->_columns);
		foreach($this->_columns as $columns){
			$ligne_conll[]=$annotation->$columns;
		}
		fputs($this->file, $commentary.implode($ligne_conll, $this->_separator)."\n");
	}

}
