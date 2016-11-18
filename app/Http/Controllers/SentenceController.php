<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\SentenceRepository;
use App\Repositories\RelationRepository;
use App\Models\Sentence;
use App\Http\Controllers\Controller;

class SentenceController extends Controller
{
    
    /**
     * Create a new SentenceController instance.
     *
     * @param  App\Repositories\SentenceRepository $sentences
     * @return void
     */
    public function __construct(SentenceRepository $sentences)
    {
        $this->sentences=$sentences;
        $this->middleware('admin');
    }

    /**
     * Display a listing of sentences.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {   
        $search = $request->input('search');
        if(str_is('[0-9]+',$search))
            $sentences = $this->sentences->model->where('id','=',$search)->all();
        return view('back.sentence.index',compact('sentences'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  App\Http\Requests $request
     * @return Illuminate\Http\Response
     */
    public function postIndex(SentenceRepository $sentences, Request $request)
    {
        $search = $request->input('search');
        if(preg_match('/^[0-9]+$/',$search)){
            return $this->show($search);
        } else {
            $sentences = Sentence::where('sentid','like','%'.$search.'%')->orWhere('content','like','%'.$search.'%')->get();
            if(count($sentences)==1)
                return $this->show($sentences[0]->id);
        }
        return view('back.sentence.post-index',compact('sentences'));
    }

    /**
     * Show the specified sentence.
     *
     * @param  int  $id the identifier of the sentence
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        $sentence = $this->sentences->getById($id);
        $annotations = $sentence->annotations()->with('parsers')
        ->orderBy('word_position')
        ->orderBy('score','desc')
        ->get();
        return view('back.sentence.show',compact('sentence','annotations'));
    }

    /**
     * Show the graph of a given sentence
     *
     * @param  int $id the identifier of the sentence
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getGraph($id, RelationRepository $relation)
    {
        $relations = $relation->getList();
        $sentence = $this->sentences->getById($id);
        $annotations = $sentence->annotations()->with('parsers')
        ->orderBy('word_position')
        ->orderBy('score','desc')
        ->get();
        return view('back.sentence.graph',compact('relations','sentence','annotations'));
    }

}
