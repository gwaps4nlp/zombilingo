<?php
$annotation_ids = [];
foreach($duel->annotations as $annotation){
    $annotation_ids[]=$annotation->id;
}
?>
<?php
$data = [];
?>
<div class="row" id="discussion">
   <h3>{{ $duel->relation->description }}</h3>
    <div class="col-12">
    Légende : <span class="answer_legend answer user_majority"></span> Tu as répondu comme la majorité des joueurs, <span class="answer_legend answer user_minority"></span> sinon - <span class="answer_legend answer others"></span> Réponses des autres joueurs 
    <table class="table">
    @foreach($annotation_ids as $annot)
        <?php 
            $annotation_id = (is_numeric($annot))?$annot:$annot->annotation_id;
            $annotation = App\Models\Annotation::find($annotation_id);
            $data[$annotation_id] = [];
            $data[$annotation_id]['focus'] = ($annotation->relation->type=="trouverDependant")? $annotation->governor_position : $annotation->word_position;
            $data[$annotation_id]['type-focus'] = ($annotation->relation->type=="trouverDependant")? "governor" : "dependent";
            $data[$annotation_id]['answers'] = [];

            $count = array();
            $nb_answers = 0;
            $nb_answers_having_majority = 0;
            $max_answers = 0;
            $user_answer = -1;
            $nb_messages = ($annotation->discussion)? $annotation->discussion->messages()->count():0;

            foreach($duel->annotation_users as $answer){

                if($answer->annotation_id != $annotation->id) continue;

                $answered_word = ($annotation->relation->type=="trouverDependant")?$answer['word_position']:$answer['governor_position'];
                $focus_word = ($annotation->relation->type=="trouverDependant")?$answer['governor_position']:$answer['word_position'];
                
                if($annotation->relation_id!=$answer->relation_id) continue;

                if(!isset($count[$answered_word])){
                    $count[$answered_word]=0;
                    if($annotation->relation->type=="trouverDependant")
                        $data[$annotation_id]['answers'][]=['relation'=>$annotation->relation->slug,'count'=>0,'percent'=>0,'governor'=>$annotation->governor_position,'dependent'=>$answer->word_position,'answer'=>$answer->word_position];
                    else
                        $data[$annotation_id]['answers'][]=['relation'=>$annotation->relation->slug,'count'=>0,'percent'=>0,'governor'=>$answer->governor_position,'dependent'=>$annotation->word_position,'answer'=>$answer->governor_position];
                }
                $count[$answered_word]++;
                $nb_answers++;
                if($answer['user_id']==Auth::user()->id){
                    $data[$annotation_id]['user_answer'] = $answered_word;
                    $user_answer = $answered_word;
                }
            }
            if(!isset($count['99999'])) $count['99999']=0;
            
            foreach($data[$annotation_id]['answers'] as $key=>$answer){
                $nb_answers_word = ($annotation->relation->type=="trouverDependant")? $count[$answer['dependent']] : $count[$answer['governor']];
                $data[$annotation_id]['answers'][$key]['count'] =  $nb_answers_word;
                $data[$annotation_id]['answers'][$key]['percent'] =  round(100*$nb_answers_word/$nb_answers);               
            }

            $class_refused = 'others';
            foreach($data[$annotation_id]['answers'] as $key=>$answer){
                if($user_answer == $answer['answer']){
                    if($answer['count']==$max_answers && $nb_answers_having_majority==1){
                        $data[$annotation_id]['answers'][$key]['label'] = 'user_majority';
                    }
                    else{
                        $data[$annotation_id]['answers'][$key]['label'] = 'user_minority';
                    }
                } else {
                    $data[$annotation_id]['answers'][$key]['label'] = 'others';
                }

                if($answer['answer']=='99999'){
                    $class_refused = $data[$annotation_id]['answers'][$key]['label'];
                }

            }
            if($nb_answers<1) {
                unset($data[$annotation_id]);
                continue;
            }
            $percent_not_relation = round(100*$count['99999']/$nb_answers);
        ?>
        <tr>
            <td>
                <div><em>{{ trans_choice('site.zombies-answered', $nb_answers,['nb_answers' => $nb_answers]) }}</em></div>
                <span class="sentence" id="annotation_{{ $annotation_id }}" focus="{{ $annotation->focus_position }}">{{ $annotation->sentence->content }}</span>
                <div style="display:inline-block;" class="py-2">
                    <span style="margin-right:20px;position:relative;">
                    @if(Auth::user()->followsDiscussionAnnotation($annotation_id))
                        <span style="position:relative;" data-id="{{ $annotation_id }}" class="unfollow-thread-button btn btn-small btn-faded btn-outline btn-green"><i class="fa fa-check" style="color:green;" aria-hidden="true"></i> {{ trans('discussion.discussion-followed') }}</span>
                        <span style="position:relative;display:none;" data-id="{{ $annotation_id }}" class="follow-thread-button btn btn-small btn-faded btn-outline btn-green">{{ trans('discussion.follow-discussion') }}</span>
                    @else
                        <span style="position:relative;display:none;" data-id="{{ $annotation_id }}" class="unfollow-thread-button btn btn-small btn-faded btn-outline btn-green"><i class="fa fa-check" style="color:green;" aria-hidden="true"></i> {{ trans('discussion.discussion-followed') }}</span>
                        <span style="position:relative;" data-id="{{ $annotation_id }}" class="follow-thread-button btn btn-small btn-faded btn-outline btn-green">{{ trans('discussion.follow-discussion') }}</span>
                    @endif
                    </span>
                </div>
                <div style="display:inline-block;">
                    <span style="margin-right:20px;position:relative;">
                        <span style="position:relative;" data-id="{{ $annotation->id }}" data-type="{{ get_class($annotation) }}" class="message-button btn btn-small btn-faded btn-outline btn-green">{{ trans('discussion.discuss-the-answer') }}
                            <span class="badge" style="background-color:green;">{{ $nb_messages }}</span>
                        </span>
                    </span>
                </div>
                <span id="thread_{{ $annotation_id }}" class="thread" style="display:none;"></span>
            </td>
            <td style="vertical-align:top;text-align:center;padding-top:4rem;">
                {!! Html::image('img/osEnCroixSeuls.png','logo',array('class'=>'croix-os','style'=>'height:65px;width:65px;')) !!}<br/>
                <span class="answer_refused {{ $class_refused }}">{{ $percent_not_relation }} %</span>
            </td>
        </tr>

    @endforeach
    </table>

    </div>
    <a href="{{ url('duel/revenge').'/'.$duel->id }}" class="btn btn-success change">{{ trans('game.make-revenge') }}</a>
    <button type="button" class="btn btn-success" data-dismiss="modal">{{ trans('site.close') }}</button>    
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('brat/style-vis.css') }}"/>
<style>

    .table td {
        border-top: 0px solid #eceeef;
    }

    .answer_legend {
        position: relative;
        top: 4px;
        display:inline-block;
        min-width:36px;
        min-height:20px;
    }
    rect.user_majority {
        fill: #7FF081;
    }
    rect.user_minority {
        fill: #FFAC98;
    }
    rect.others {
        fill: #7fa2ff;
    }
    span.answer, span.answer_refused {
        padding:0 5px;
        font-size: 15px;
        border: 1px solid #002998;
        color:#4a1710;      
    }
    span.others {
        background-color:#7fa2ff;
    }
    span.user_majority, #majority2 {
        background-color:#7FF081;
    }
    span.user_minority, #minority2 {
        background-color:#FFAC98;
    }
    div.others {
        background-color:#7fa2ff;
    }
    div.user_majority {
        background-color:#7FF081;
    }
    div.user_minority {
        background-color:#FFAC98;
    }
</style>

<script>
    var data_brat = {!! json_encode($data) !!};
$(document).ready(function(){
    
    embedBratVisualizations();
});
</script>
