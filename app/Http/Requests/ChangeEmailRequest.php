<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class ChangeEmailRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'email' => 'email|max:255|unique:users,email,'.Auth::user()->id,
			'email_frequency_id' => 'exists:email_frequencies,id',
		];
	}

}
