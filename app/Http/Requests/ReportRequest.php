<?php namespace App\Http\Requests;

class ReportRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'message[]' => 'max:1000'
		];
	}

}
