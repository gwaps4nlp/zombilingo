<?php

namespace App\Repositories;

use App\Models\Corpus;
use App\Models\ExportedCorpus;
use App\Models\User;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Repositories\BaseRepository;

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
	 * Get the last exports of corpus
	 *
	 * @return array App\Models\ExportedCorpus
	 */
	public function getLast()
	{
		$admin = User::getAdmin();
        $source_preannotated = Source::getPreAnnotated();
        $corpora = Corpus::where('exportable','=',1)->get();
        $result = [];
        foreach($corpora as $corpus){
        	$exported_corpus = $this->model->where('user_id',$admin->id)->where('corpus_id',$corpus->id)->orderBy('created_at','desc')->first();
        	if($exported_corpus)
        		$result[]= $exported_corpus;
        }
		return $result;
	}

}
