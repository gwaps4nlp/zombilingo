<?php namespace App\Http\Requests;

class ChallengeUpdateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:75',	
			'description' => 'required|max:1000',
			'language_id' => 'required|exists:languages,id',
			'corpus_id' => 'required|exists:corpuses,id',
			'type_score' => 'required',
			'image' => 'image',
			'start_date' => 'required|date_format:d/m/Y',
			'end_date' => 'required|date_format:d/m/Y|after:start_date'
		];
	}

}