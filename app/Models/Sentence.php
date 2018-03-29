<?php

namespace App\Models;

use Gwaps4nlp\Models\Sentence as Gwaps4nlpSentence;
use Gwaps4nlp\Models\Source;
use App\Models\Annotation;

class Sentence extends Gwaps4nlpSentence
{
    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function annotations()
    {
        return $this->hasMany('App\Models\Annotation')->orderBy('word_position');
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function annotations_user_annotator()
    {
        return $this->hasMany('App\Models\AnnotationUserAnnotator');
    }

    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function annotations_parser($parser_id=null)
    {
        if($parser_id)
            return $this->hasMany('App\Models\Annotation')->join('annotation_parser','annotations.id','=','annotation_parser.annotation_id')->where('annotation_parser.parser_id','=',$parser_id)->orderBy('word_position')->get();
        else
            return $this->hasMany('App\Models\Annotation')->whereIn('source_id',array(1,3))->orderBy('word_position')->get();
    }

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function tokens()
	{
	  return $this->addSelect(DB::raw("distinct word_position"))->hasMany('App\Models\Annotation')->orderBy('word_position');
	}


    public function getComplexity($parser_id=null){
        //On initialise l'index
        $i = 1.5;

        //On initalise la difficulté
        $difficulte = 0;

        //On compte le nombre d'annotations
        if($parser_id)
            $annotations = $this->annotations_parser($parser_id);
        else
            $annotations = $this->annotations_parser();

        $nbAnnotations = count($annotations);
        //On va parcourir tous les espaces entre les mots
        while($i < $nbAnnotations ){

            //On initialise le nombre d'arcs traversant
            $nbTravers = 0;

            //On parcourt toutes les annotations
            foreach ($annotations as $annotation){

                //Si ce n'est pas le root
                if($annotation['word_position'] != 0){
                    //Si on a un arc qui traverse
                    if( ($annotation['word_position'] < $i && $annotation['governor_position'] > $i )
                        || ($annotation['word_position'] > $i && $annotation['governor_position'] < $i) ){
                        $relation = Relation::getById($annotation['relation_id']);
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


    /**
     *
     * @return boolean
     */
    public function isReference()
    {
      return $this->source_id == Source::getReference()->id;
    }

	// Called by sentence->content
	public function getContentAttribute($value)
	{
		if ($this->corrected_content)
			return $this->corrected_content;
		else
			return $value;
	}


}
