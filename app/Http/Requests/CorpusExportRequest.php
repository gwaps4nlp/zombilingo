<?php namespace App\Http\Requests;

class CorpusExportRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'corpus_id' => 'required|exists:corpuses,id'
		];
	}

}