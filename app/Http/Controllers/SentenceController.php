<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\SentenceRepository;
use App\Repositories\RelationRepository;
use App\Models\Sentence;
use App\Http\Controllers\Controller;
use Response, DB;

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
        $this->middleware('ajax')->only('getSearch');
    }

    /**
     * Display a form to search sentences.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {   
        return view('back.sentence.index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  App\Http\Requests $request
     * @return Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {
        $search = $request->input('search');
        if(preg_match('/^[0-9]+$/',$search)){
            $sentence = Sentence::find($search);
            return $this->show($sentence);
        } else {
            $sentences = Sentence::where('sentid','like','%'.$search.'%')->orWhere('content','like','%'.$search.'%')->get();
            if(count($sentences)==1)
                return $this->show($sentences->first());
        }
        return view('back.sentence.post-index',compact('sentences'));
    }

    /**
     * Display a listing of sentences.
     *
     * @return Illuminate\Http\Response
     */
    public function getSearch(Request $request)
    {   
        $search = $request->input('term');
        // $sentences = DB::select('select id, content, MATCH (content) AGAINST (? IN NATURAL LANGUAGE MODE ) as relevance from sentences where MATCH (content) AGAINST (? IN NATURAL LANGUAGE MODE )', [$search, $search]);
        $sentences = Sentence::select('id','content')->where('content','like','%'.$search.'%')->limit(10)->get();
        $data =[];
        foreach($sentences as $sentence){
            $data[] = array(
                'id' => $sentence->id,
                'label' => $sentence->content,
                'value' => $sentence->content,
            );
        }
        return Response::json($sentences);
    }

    /**
     * Show the specified sentence.
     *
     * @param  int  $id the identifier of the sentence
     * @return Illuminate\Http\Response
     */
    public function show(Sentence $sentence)
    {
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
    public function getGraph(Sentence $sentence, RelationRepository $relation)
    {
        $relations = $relation->getList();
        $annotations = $sentence->annotations()->with('parsers')
        ->orderBy('word_position')
        ->orderBy('score','desc')
        ->get();
        return view('back.sentence.graph',compact('relations','sentence','annotations'));
    }

}
