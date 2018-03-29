<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Relation;
use App\Repositories\RelationRepository;
use App\Models\User;
use Auth;

class DuelCreateRequest extends Request {


	public function __construct(RelationRepository $relations){
		$this->relations=$relations;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'relation_id' => 'exists:relations,id',
			'nb_turns' => 'required|in:10,20,50,100',
			'challenger_id' => 'exists:friends,friend_id,user_id,'.Auth::user()->id
		];
	}

    /**
     * Validate request
     * @return
     */
    public function moreValidation($validator)
    {
        $validator->after(function($validator)
        {
            if ($this->has('relation_id') && $this->input('relation_id')) {
            	$relation = Relation::find($this->input('relation_id'));
                if($relation->level_id > Auth::user()->level_id){
                	$validator->errors()->add('relation_id',trans('validation.custom.relation_id.level_user'));
                }
                if ($this->has('challenger_id') && $this->input('challenger_id')) {
                	$challenger = User::find($this->input('challenger_id'));
	                if($relation->level_id > $challenger->level_id){
	                	$validator->errors()->add('relation_id', trans('validation.custom.relation_id.level_challenger'));
	                }
                }
                $relation = $this->relations->getByUser(Auth::user(),$this->input('relation_id'));
                if(!$relation->tutorial)
                	$validator->errors()->add('relation_id', 'Pour choisir ce phénomène, tu dois d\'abord en faire la formation. <a style="text-decoration:underline;" href="'.url('game/training/begin',[$relation->id]).'"">La faire maintenant.</a>');
            }
        });
    }

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
	    return [
	        'relation_id.exists' => 'Phénomène inconnu',
	        'nb_turns'  => 'Nombre de tours incorrect',
	        'challenger_id'  => 'Ennemi inconnu',
	        'level_id'  => "Tu n'as pas le niveau requis pour jouer ce phénomène",
	    ];
	}

}
