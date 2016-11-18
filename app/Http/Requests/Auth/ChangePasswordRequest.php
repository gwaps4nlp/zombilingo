<?php namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class ChangePasswordRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'password' => 'required|min:4|confirmed',
		];
	}

}
