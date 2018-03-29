<?php namespace App\Http\Requests;

class ExportCorpusRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'corpus_id' => 'required|exists:corpuses,id',
			'score_init' => 'required',
			'weight_level' => 'required',
			'weight_confidence' => 'required'
		];
	}

}