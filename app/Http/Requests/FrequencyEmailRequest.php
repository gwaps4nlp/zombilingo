<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class FrequencyEmailRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'email' => 'email|max:255|required',
			'email_frequency_id' => 'required|exists:email_frequencies,id',
		];
	}

}
