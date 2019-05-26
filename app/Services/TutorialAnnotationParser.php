<?php  namespace App\Services;

use App\Models\TutorialAnnotation;
use App\Models\Relation;
use App\Models\Sentence;
use App\Models\Annotation;

class TutorialAnnotationParser extends CsvParser {

protected $_columns = array('relation_name','level','type','sentid','word_position','explanation');

protected $_ignoreFirstLine = false;

public $numberImported = 0;

public $errors=[];

public function parseLine($line){
	try {
		
		try {
			$relation_id = Relation::getIdBySlug($line['relation_name']);
		} catch (\Exception $Ex){
			throw new \Exception("Line {$this->lines_done} : Unknown relation \"{$line['relation_name']}\".");
		}

		$level = $line['level'];
		if(!preg_match('/[0-9]+/',$level))
			throw new \Exception("Line {$this->lines_done} : Invalid value of level \"{$line['level']}\".");
		
		$type = $line['type'];
		if($type!="1" && $type!="-1" )
			throw new \Exception("Line {$this->lines_done} : Invalid value for the type (must be 1 or -1).");	

		try {
			$sentence = Sentence::where('sentid','=',$line['sentid'])->firstOrFail();
		} catch (\Exception $Ex){
			throw new \Exception("Line {$this->lines_done} : Unknown sentence \"{$line['sentid']}\".");
		}		

		try {
			$annotation = Annotation::where('word_position','=',$line['word_position'])->where('sentence_id','=',$sentence->id)->firstOrFail();
		} catch (\Exception $Ex){
			throw new \Exception("Line {$this->lines_done} : Unknown word at position \"{$line['word_position']}\".");
		}			

		$tutorial_annotation = TutorialAnnotation::where(['relation_id'=>$relation_id,'annotation_id'=>$annotation->id])->first();
		if($tutorial_annotation)
			throw new \Exception("Line {$this->lines_done} : Annotation of tutorial already exists.");

		TutorialAnnotation::create(['relation_id'=>$relation_id,'level'=>$level,'annotation_id'=>$annotation->id,'explanation'=>$line['explanation'],'type'=>$line['type']]);
		$this->numberImported++;



	} catch (\Exception $Ex) {
		$this->errors[]= $Ex->getMessage();
	}
}

}
