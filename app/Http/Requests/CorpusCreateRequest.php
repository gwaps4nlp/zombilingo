<?php namespace App\Http\Requests;

class CorpusCreateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:50',
			'title' => 'max:500',
			'url_source' => 'max:500',
			'url_info_license' => 'max:500',
			'description' => 'required',
			'source_id' => 'required|exists:sources,id',
			'language_id' => 'required|exists:languages,id',
			'license_id' => 'required|exists:licenses,id',
		];
	}

}
