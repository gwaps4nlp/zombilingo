<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Cache, Artisan;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class PageController extends Controller
{
	/**
     * Create a new PageController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    /**
     * Show a listing of the pages.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        $stream = fopen(storage_path().'/routes.txt', 'w+');
        Artisan::call('route:list');
        $output = Artisan::output();
        $pages = Page::orderBy('slug')->get();
        return view('back.page.index',compact('pages'));
    }

    /**
     * Edit a page.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|integer',
		]);
		$page = Page::findOrFail($request->id);
        return view('back.page.edit',compact('page'));
    }

    /**
     * Update a the description of a page.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postEdit(Request $request)
    {
		$this->validate($request, [
			'id' => 'required|exists:pages',
			'slug' => 'required|max:50',
			'title' => 'required|max:150',
			'meta_description' => 'required|max:250',
		]);

		$page = Page::findOrFail($request->id);
		$page->slug=$request->slug;
		$page->title=$request->title;
		$page->meta_description=$request->meta_description;
		$page->save();
		Cache::forget($trophy->key);
        return $this->getIndex();
    }

}