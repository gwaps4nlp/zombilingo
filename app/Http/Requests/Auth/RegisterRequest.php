<?php namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class RegisterRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'username' => 'required|max:30|unique:users',
			'email' => 'email|max:255|unique:users',
			'password' => 'required|min:4|confirmed',
		];
	}

}
