<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use App\Repositories\DiscussionRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\RelationRepository;
use App\Models\Message;
use App\Models\DeletionReason;
use App\Models\Discussion;
use App\Models\AnnotationUser;
use Gwaps4nlp\FaqManager\Models\QuestionAnswer;
use App\Events\MessagePosted;
use Illuminate\Http\Request;
use Event, Auth, DB;

class DiscussionController extends Controller
{

    /**
     * Instantiate a new DiscussionController instance.
     *
     * @param  App\Repositories\MessageRepository $messages
     * @return void
     */
    public function __construct(MessageRepository $messages, DiscussionRepository $discussions, RelationRepository $relations)
    {
        $this->middleware('auth');
        $this->middleware('admin')->only('getDelete');
        $this->messages=$messages;
        $this->relations=$relations;
        $this->discussions=$discussions;
    }
    
    /**
     * History of the annotations played
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getHistory(Request $request)
    {
        $request['history'] = 1;
        return $this->getIndex($request);
    }

    /**
     * Display the list of discussions
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */    
    public function getIndex(Request $request, $id=null)
    {

        $perPage = array(10=>10,20=>20,50=>50);
        $params['per-page']= (in_array($request->input('per-page'),[10,20,50]))? $request->input('per-page') : 10;
        $params['relation_id']= $request->has('relation_id')?$request->input('relation_id'):0;
        $params['path'] = 'discussion';

        if($id){
            $sql = Discussion::select('entity_id as annotation_id')
                ->where('id',$id)
                ->where('entity_type','App\Models\Annotation');
        }
        elseif($request->has('history')) {
            $params['path'] = route('history');
            $sql = AnnotationUser::select('annotations.id as annotation_id')
                ->join('annotations','annotations.id','=','annotation_users.annotation_id') 
                ->where('user_id',Auth::user()->id)
                ->orderBy('annotation_users.created_at','desc');

        } else {
            $sql_message = Message::selectRaw('count(*) as count, discussion_id')->groupBy('discussion_id');
            $sql = Discussion::select('entity_id as annotation_id')
                ->join(DB::raw("({$sql_message->toSql()}) as messages"),'messages.discussion_id','=','discussions.id')
                ->join('annotations','annotations.id','=','discussions.entity_id')                
                ->join('relations','annotations.relation_id','=','relations.id')
                ->where('entity_type','App\Models\Annotation')
                ->where('relations.level_id','<=',Auth::user()->level_id)
                ->orderBy('discussions.created_at','desc');

                if($request->has('followed')){
                    $sql->join('discussion_user','discussion_user.discussion_id','=','discussions.id')
                        ->where('discussion_user.user_id','=',Auth::user()->level_id);
                }

        }

        if($params['relation_id']){
            $sql->where('annotations.relation_id',$params['relation_id']);
        }

        $annotation_ids= $sql->paginate($params['per-page']);
        $annotation_ids->setPath($params['path']);
        $annotation_ids->appends($params);

        $show_messages = $request->has('show')? true : false;

        $relations = $this->relations->getListPlayable(Auth::user());

        if($request->ajax())
            return view('partials.discussion.index',compact('annotation_ids','perPage','relations','params','show_messages'));
        else
            return view('front.discussion.index',compact('annotation_ids','perPage','relations','params','show_messages'));
    }

    /**
     * Display the thread of an annotation
     *
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getThread(AnnotationRepository $annotations, Request $request)
    {
        $user = Auth::user();
        if($request->input('entity_type')=="App\Models\Annotation")
            $entity = $annotations->get($request->input('entity_id'));
        elseif($request->input('entity_type')=="Gwaps4nlp\FaqManager\Models\QuestionAnswer")
            $entity = QuestionAnswer::findOrFail($request->input('entity_id'));
        else
            abort(404);

        $discussion = $this->discussions->getByEntiy($entity);

        if($discussion)
            $thread = $this->messages->getByDiscussion($discussion);
        else
            $thread = [];
        return view('partials.discussion.thread',compact('user','entity','thread'));
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
        if(Auth::user()->isAdmin()){
            $data['content'] = strip_tags($request->input('content'),'<a>');
        } else {
            $data['content'] = strip_tags($request->input('content'));
        }
        $data_discussion['entity_type'] = $request->input('entity_type');
        $data_discussion['entity_id'] = $request->input('entity_id');

        $discussion = Discussion::FirstOrCreate($data_discussion);

        if($request->has('follow-thread'))
            Auth::user()->discussions()->attach($discussion->id);

        $data['parent_message_id'] = $request->input('parent_message_id');
        $data['discussion_id'] = $discussion->id;

        $message = Message::create($data);
        $entity = $discussion->entity;
        $thread = $this->messages->getByDiscussion($discussion);
        Event::fire(new MessagePosted($message,$discussion));
        return view('partials.discussion.thread',compact('user','entity','thread'));     
    }

    /**
     * Follow a discussion
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFollowThread( Request $request)
    {
        $data['entity_type'] = 'App\Models\Annotation';
        if($request->has('discussion_id')){
            $data['id'] = $request->input('discussion_id');
        } else {
            $data['entity_id'] = $request->input('id');   
        }
        $discussion = Discussion::FirstOrCreate($data);
        Auth::user()->discussions()->attach($discussion->id);
        if($request->ajax()){

        } else {
            return $this->getIndex($request,$discussion->id);
        }   
    }
    /**
     * Stop following a discussion
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getUnFollowThread(Request $request)
    {
        $data['entity_type'] = 'App\Models\Annotation';
        if($request->has('discussion_id')){
            $data['id'] = $request->input('discussion_id');
        } else {
            $data['entity_id'] = $request->input('id');   
        }
        
        $discussion = Discussion::FirstOrCreate($data);
        Auth::user()->discussions()->detach($discussion->id);
        if($request->ajax()){

        } else {
            return $this->getIndex($request,$discussion->id);
        }
    }

    /**
     * Delete a message
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDelete(AnnotationRepository $annotations,Request $request)
    {
        $deletion_reason = DeletionReason::where('slug','admin-delete')->first();
        $message_id = $request->input('message_id');
        $message = Message::findOrFail($message_id);
        $message->deletion_reason_id = $deletion_reason->id;
        $message->save();
        $message->delete();
        return $this->getThread($annotations, $request);

    }

    private function getEntity(){

    }
    
}
