<?php namespace App\Http\Requests;

class TrophyCreateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:50',
			'key' => 'required|max:50',
			'required_value' => 'required|integer',
			'points' => 'required|integer',
			'description' => 'required|max:50',
			'is_secret' => 'required|boolean',
		];

	}

}