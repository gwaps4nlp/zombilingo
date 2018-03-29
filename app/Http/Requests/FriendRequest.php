<?php namespace App\Http\Requests;

class FriendRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'user_id' => 'required|max:100',
		];
	}

}
