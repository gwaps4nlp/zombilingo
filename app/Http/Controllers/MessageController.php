<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use App\Repositories\DuelRepository;
use App\Repositories\UserRepository;
use App\Repositories\AnnotationRepository;
use App\Models\ConstantGame;
use App\Models\Corpus;
use App\Models\Source;
use App\Models\Message;
use App\Http\Requests\DuelCreateRequest;
use Illuminate\Http\Request;

use Response, View, App, Auth, DB;

class MessageController extends Controller
{

    /**
     * Instantiate a new MessageController instance.
     *
     * @param  App\Repositories\MessageRepository $messages
     * @return void
     */
    public function __construct(MessageRepository $messages)
    {
        $this->middleware('auth');
        $this->messages=$messages;
    }
    
    /**
     * Display the index of the duel games
     *
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getThread(AnnotationRepository $annotations, Request $request)
    {
        $user = Auth::user();
		$annotation = $annotations->get($request->input('annotation_id'));
		$thread = $this->messages->getByAnnotation($annotation);
        return view('partials.message.thread',compact('user','annotation','thread'));
    }


    /**
     * Send a new report.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postNew(Request $request)
    {
		$data['user_id'] = Auth::user()->id;
        $data['content'] = strip_tags($request->input('content'));
        $data['annotation_id'] = $request->input('annotation_id');
        $data['message_id'] = $request->input('message_id');
        $report = Message::create($data);
		$response = 'Merci pour ta participation';
        return Response::json(['html'=>$response]);
    }
    
}
