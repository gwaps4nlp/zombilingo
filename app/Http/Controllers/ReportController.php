<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\Report;
use App\Models\User;
use App\Models\Role;
use App\Repositories\RelationRepository;
use Response, Mail;

class ReportController extends Controller
{

    /**
     * Send a new report.
     *
     * @param  App\Http\Requests\ReportRequest $ReportRequest
     * @param  App\Repositories\RelationRepository $relations
     * @return Illuminate\Http\Response
     */
    public function postSend(ReportRequest $request, RelationRepository $relations)
    {
        if(Auth::check()){
            $data['user_id'] = Auth::user()->id;
            $data['message'] = "Utilisateur : ".Auth::user()->username.'\r\n';              
        } else {
            $data['message'] = "Utilisateur : Non connecté". '\r\n'; 
        }

        $data['message'] .= "Ma réponse : ".strip_tags($request->input('word')).'\r\n\r\n';
        $data['message'] .= strip_tags(join('\r\n',$request->input('message')));
        $data['annotation_id'] = $request->input('annotation_id');

        if($request->input('mode')=='demo')
            $data['relation_id'] = $relations->getBySlug(ConstantGame::get('relation-demo'))->id;
        else
            $data['relation_id'] = $request->input('relation_id');

        if($request->has('answer_email')){
            $data['message'] .= '\r\n'.strip_tags($request->input('answer_email').'\r\n'.$request->input('email'));
        }

        $data['mode'] = $request->input('mode');
        $data['user_answer'] = $request->input('user_answer');

        $report = Report::create($data);

        $role_admin = Role::where('slug','=','admin')->first();
        
        $administrators = User::where('role_id','=',$role_admin->id)
            ->where('email','!=','')->get();

        foreach($administrators as $recipient){
            Mail::send('emails.report', ['report' => $report], function ($m) use ($recipient) {
                $m->from('contact@zombilingo.org', 'Admin ZombiLingo');

                $m->to($recipient->email, $recipient->username)->subject('Rapport d\'anomalie');
            });
        }

        $response = 'Merci pour ta participation';
        return Response::json(['html'=>$response]);
    }  

}
