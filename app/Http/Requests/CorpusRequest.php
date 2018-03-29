<?php namespace App\Http\Requests;

class CorpusRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'id' => 'required|exists:corpuses'
		];
	}

}
