<?php namespace App\Http\Requests;

class CorpusFileRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'corpus_id' => 'required',
			'corpus_file' => 'required'
		];
	}

}
