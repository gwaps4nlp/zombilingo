<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Duel;
use App\Repositories\RelationRepository;
use App\Repositories\DuelRepository;
use Auth;

class DuelJoinRequest extends Request {

	public function __construct(RelationRepository $relations, DuelRepository $duels){
		$this->relations=$relations;
		$this->duels=$duels;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'duel_id' => 'required|exists:duels,id,state,pending'
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
        	if($this->has('duel_id')){
	            $duel = $this->duels->getAvailableDuel(Auth::user(), $this->input('duel_id'), null);
	            if($duel){
		            $relation = $this->relations->getByUser(Auth::user(),$duel->relation_id);
		            if(!$relation->tutorial)
		            	$validator->errors()->add('relation_id', 'Tu dois d\'abord faire la formation : '.$relation->name.'.<br/><a style="text-decoration:underline;" href="'.url('game/training/begin',[$relation->id]).'"">La faire maintenant.</a>');
	        	} else {
	        		$validator->errors()->add('duel_id', 'Duel inconnu.');
	        	}
        	}
        });
    }

}
