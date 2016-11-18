<?php

namespace App\Repositories;

use App\Models\Corpus;
use App\Models\ExportedCorpus;
use App\Models\User;
use App\Models\Source;

class ExportedCorpusRepository extends BaseRepository
{

	/**
	 * Create a new ExportedCorpusRepository instance.
	 *
	 * @param  App\Models\ExportedCorpus $exported_corpus
	 * @return void
	 */
	public function __construct(
		ExportedCorpus $exported_corpus)
	{
		$this->model = $exported_corpus;
	}

	/**
	 * Get the last export of mwe
	 *
	 * @return ExportedCorpus
	 */
	public function getLastMwe()
	{
		$admin = User::getAdmin();
		return $this->model->where('user_id',$admin->id)->where('type','mwe')->orderBy('created_at','desc')->first();
	}

	/**
	 * Get the last exports of corpus
	 *
	 * @return array App\Models\ExportedCorpus
	 */
	public function getLast()
	{
		$admin = User::getAdmin();
        $source_preannotated = Source::getPreAnnotated();
        $corpora = Corpus::where('source_id','=',$source_preannotated->id)->get();
        $result = [];
        foreach($corpora as $corpus){
        	$exported_corpus = $this->model->where('user_id',$admin->id)->where('corpus_id',$corpus->id)->orderBy('created_at','desc')->first();
        	if($exported_corpus)
        		$result[]= $exported_corpus;
        }
		return $result;
	}

}
