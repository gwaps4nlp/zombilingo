<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

abstract class Request extends FormRequest {

	public function authorize()
	{
		// Honeypot 
		return  $this->input('address') == '';
	}

    public function validator(){

        $v = Validator::make($this->input(), $this->rules(), $this->messages(), $this->attributes());

        if(method_exists($this, 'moreValidation')){
            $this->moreValidation($v);
        }

        return $v;
    }

}
