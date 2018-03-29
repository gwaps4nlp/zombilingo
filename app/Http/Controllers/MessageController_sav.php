<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use App\Repositories\AnnotationRepository;
use App\Models\Message;
use App\Events\MessagePosted;
use Illuminate\Http\Request;
use Event, Auth;

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
     * Save a new message
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postNew(AnnotationRepository $annotations, Request $request)
    {
        $data['user_id'] = Auth::user()->id;
        $data['content'] = strip_tags($request->input('content'));
        $data['annotation_id'] = $request->input('annotation_id');
        $data['message_id'] = $request->input('message_id');
        $message = Message::create($data);
        $parent_message = ($message->message_id)? Message::find($data['message_id']) : $message;
        $annotation = $annotations->get($request->input('annotation_id'));
        $thread = $this->messages->getByAnnotation($annotation);
        // Event::fire(new MessagePosted($parent_message));
        return view('partials.message.thread',compact('user','annotation','thread'));        
    }

    /**
     * Save a new message
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postFollowThread(AnnotationRepository $annotations, Request $request)
    {
        $data['user_id'] = Auth::user()->id;
        $data['message_id'] = $request->input('message_id');
        Auth::user()->messages()->attach($request->input('message_id'));
   
    }
    /**
     * Save a new message
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postUnFollowThread(AnnotationRepository $annotations, Request $request)
    {
        $data['user_id'] = Auth::user()->id;
        $data['message_id'] = $request->input('message_id');
        Auth::user()->messages()->detach($request->input('message_id'));
   
    }
    
}
