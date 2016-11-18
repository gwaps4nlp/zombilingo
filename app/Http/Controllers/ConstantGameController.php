<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ConstantGame;
use Illuminate\Http\Request;
use Cache;

class ConstantGameController extends Controller
{
	/**
     * Create a new ConstantGameController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
     /**
     * Show a listing of the constants of the game.
     *     
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
		$constants = ConstantGame::all();
        return view('back.constant-game.index',compact('constants'));
    }    
     /**
     * Show a form to edit a constant.
     *
     * @param  Illuminate\Http\Request $request     
     * @return Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|integer',
		]);
		$constant = ConstantGame::findOrFail($request->id);
        return view('back.constant-game.edit',compact('constant'));
    }    
     /**
     * Update a constant.
     *
     * @param  Illuminate\Http\Request $request 
     * @return Illuminate\Http\Response
     */
    public function postEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|integer',
			'value' => 'required|max:50',
			'description' => 'required|max:200',
		]);
		$constant = ConstantGame::findOrFail($request->id);
		$constant->value=$request->value;
		$constant->description=$request->description;
		$constant->save();
		Cache::forget($constant->key);
        return $this->getIndex();
    }

}