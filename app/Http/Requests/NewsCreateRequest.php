<?php namespace App\Http\Requests;

class NewsCreateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'content' => 'required|max:1000',
			'send_by_email' => 'required|boolean',
			'language_id' => 'required|exists:languages,id',
			'hour_scheduled' => 'required_if:send_by_email,1|regex:/[0-9]{2}:[0-9]{2}/',
			'date_scheduled' => 'required_if:send_by_email,1|date_format:d/m/Y',
			'title' => 'required_if:send_by_email,1|max:75',
		];
	}

}