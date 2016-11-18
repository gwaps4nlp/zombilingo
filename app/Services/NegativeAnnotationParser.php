<?php  namespace App\Services;

use App\Models\NegativeAnnotation;
use App\Models\Relation;
use App\Models\Sentence;
use App\Models\Annotation;

class NegativeAnnotationParser extends CsvParser {

protected $_columns = array('relation_name','sentid','word_position','explanation');

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

		$negative_annotation = NegativeAnnotation::where(['relation_id'=>$relation_id,'annotation_id'=>$annotation->id])->first();
		if($negative_annotation)
			throw new \Exception("Line {$this->lines_done} : True negative already exists.");
	
		NegativeAnnotation::create(['relation_id'=>$relation_id,'annotation_id'=>$annotation->id,'explanation'=>$line['explanation']]);
		$this->numberImported++;
	} catch (\Exception $Ex) {
		$this->errors[]= $Ex->getMessage();
	}
}

}
