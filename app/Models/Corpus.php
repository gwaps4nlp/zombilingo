<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Sentence;

class Corpus extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'corpuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','description','source_id','language_id','license_id','playable','title','url_source','url_info_license'];  
     
    protected $visible = ['id','name'];
	
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
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function all_sentences()
	{
	  return Sentence::whereIn('corpus_id', array_merge([$this->id],$this->subcorpora->pluck('id')->toArray()));;
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
	public function subcorpora()
	{
	  return $this->belongsToMany('App\Models\Corpus','corpus_subcorpus','corpus_id','subcorpus_id');
	}

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function language()
	{
	  return $this->belongsTo('App\Models\Language');
	}

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function license()
	{
	  return $this->belongsTo('App\Models\License');
	}

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function source()
	{
	  return $this->hasOne('App\Models\Source');
	}
	
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function annotations()
	{
	  return $this->hasManyThrough('App\Models\Annotation','App\Models\Sentence');
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
