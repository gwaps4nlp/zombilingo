<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Trophy;
use Illuminate\Http\Request;
use Cache;

class TrophyController extends Controller
{
	/**
     * Create a new TrophyController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
     /**
     * Show a listing of the trophies.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
		$trophies = Trophy::orderBy('key')->orderBy('required_value')->get();
        return view('back.trophy.index',compact('trophies'));
    }    
     /**
     * Show a form to edit a trophy.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|integer',
		]);
		$trophy = Trophy::findOrFail($request->id);
        return view('back.trophy.edit',compact('trophy'));
    }    
     /**
     * Update a trophy.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|integer',
			'name' => 'required|max:50',
			'key' => 'required|max:50',
			'required_value' => 'required|integer',
			'points' => 'required|integer',
			'description' => 'required|max:50',
			'is_secret' => 'required|boolean',
		]);
		$trophy = Trophy::findOrFail($request->id);
		$trophy->name=$request->name;
		$trophy->key=$request->key;
		$trophy->description=$request->description;
		$trophy->required_value=$request->required_value;
		$trophy->points=$request->points;
		$trophy->is_secret=$request->is_secret;
		$trophy->save();
		Cache::forget($trophy->key);
        return $this->getIndex();
    }

}