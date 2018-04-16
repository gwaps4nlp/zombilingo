<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Models\Corpus as Gwaps4nlpCorpus;

class Corpus extends Gwaps4nlpCorpus
{

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function subcorpora()
	{
	  return $this->belongsToMany('App\Models\Corpus','corpus_subcorpus','corpus_id','subcorpus_id');
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function bound_corpora()
	{
	  return $this->belongsToMany('App\Models\Corpus','preannotated_reference_corpus','corpus_id','reference_corpus_id');
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function evaluation_corpora()
	{
	  return $this->belongsToMany('App\Models\Corpus','preannotated_evaluation_corpus','corpus_id','evaluation_corpus_id');
	}
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function evaluated_corpus()
	{
	  return $this->belongsToMany('App\Models\Corpus','preannotated_evaluation_corpus','evaluation_corpus_id','corpus_id');
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function annotations()
	{
	  return Annotation::whereIn('corpus_id', array_merge([$this->id],$this->subcorpora->pluck('id')->toArray()));
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function annotations_users()
	{
	  return $this->annotations()->join('annotation_users','annotations.id','=','annotation_users.annotation_id');
	}
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function annotations_users_at_date($date)
	{
	  return $this->annotations_users()->where('annotation_users.created_at','<=',$date);
	}

	public function count_players()
	{
	  return $this->annotations_users()->select(DB::raw('count(DISTINCT user_id) as count'))->first()->count;
	}

	public function count_players_at_date($date)
	{
	  return $this
			->annotations_users()
			->where('annotation_users.created_at','<=',$date)
			->select(DB::raw('count(DISTINCT user_id) as count'))
			->first()
			->count;
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sentences()
	{
	  return $this->hasMany('App\Models\Sentence')->whereIn('corpus_id', array_merge([$this->id],$this->subcorpora->pluck('id')->toArray()));
	}

	/**
	 *
	 *
	 * @return boolean
	 */
	public function isReference()
	{
	  return $this->source_id == Source::getReference()->id;
	}

	/**
	 *
	 *
	 * @return boolean
	 */
	public function isPreAnnotated()
	{
	  return $this->source_id == Source::getPreAnnotated()->id;
	}

	/**
	 *
	 *
	 * @return boolean
	 */
	public function isPreAnnotatedForEvaluation()
	{
	  return $this->source_id == Source::getPreAnnotatedForEvaluation()->id;
	}

	/**
	 *
	 *
	 * @return boolean
	 */
	public function getControlCorpus()
	{
		$corpus_id = $this->id;
	  	return Corpus::where('source_id',1)->whereExists(function ($query) use ($corpus_id) {
                $query->select(DB::raw(1))
                      ->from('annotations as a_control')->join('annotations as a_parser',function ($join) {
				            $join->on('a_parser.sentence_id', '=', 'a_control.sentence_id');
				            $join->on('a_parser.word_position', '=', 'a_control.word_position');
				        })
                      ->whereRaw('a_control.corpus_id = corpuses.id')->whereRaw('a_control.source_id = 1')
                      ->whereRaw('a_parser.corpus_id = '.$corpus_id)->whereRaw('a_parser.source_id = 5');

            })->first();
	}
}
