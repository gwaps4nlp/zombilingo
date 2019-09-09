<?php
$data = [];
$hidden_titles = (isset($hidden_titles))?$hidden_titles:false;
$nb_sentences_played = 0;
$nb_answers_majority = 0;
$nb_answers_minority = 0;
$annotations_with_discussion = 0;
$nb_sentences_first_answer = 0;
?>
@if(isset($params))
	@if($params['path']=='discussion')
	<h1>Discussions</h1>
	@else
	<h1>Phrases jouées</h1>
	@endif
@endif
<div class="row" id="discussion">
	@if(isset($params))
	    <div class="col-9 d-flex my-4">
		    <div class="clearfix"></div>
		    {!! Form::open(['url' => $params['path'], 'method' => 'get', 'role' => 'form', 'class' => 'form-inline','id'=>"discussions-filter"]) !!}
		    <label>Phénomène : </label>
		    {!! Form::control('selection', 0, 'relation_id', $errors, '',$relations,null,trans('game.all-phenomena'),$params['relation_id']) !!}
		    <button type="submit" class="btn btn-success">Filtrer</button>
		    {!! Form::close() !!}
	    </div>
	    @if($annotation_ids->total()>10)
		    <div class="col-3  my-4">
		  		{!! Form::open(['url' => $params['path'], 'method' => 'get', 'role' => 'form', 'class' => 'form-inline','id'=>"discussions-selection"]) !!}
			    <label>Phrases par page : </label>
			    {!! Form::control('selection', 0, 'per-page', $errors, '',$perPage,null,null,$params['per-page']) !!}
			    <input type="hidden" name="relation_id" value="{{ $params['relation_id'] }}" />
			    {!! Form::close() !!}
		    </div>
	    @endif
    @endif

    <div class="col-12">
    Légende : <span class="answer_legend answer user_majority"></span> Tu as répondu comme la majorité des joueurs, <span class="answer_legend answer user_minority"></span> sinon - <span class="answer_legend answer others"></span> Réponses des autres joueurs
    <table class="table">
    @foreach($annotation_ids as $annot)
		<?php
			$nb_sentences_played++;
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
			if($nb_messages>0)
				$annotations_with_discussion++;
			foreach($annotation->answers as $answer){

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

			if($nb_answers==1){
				$nb_sentences_first_answer++;
			}

			foreach($data[$annotation_id]['answers'] as $key=>$answer){
				$nb_answers_word = ($annotation->relation->type=="trouverDependant")? $count[$answer['dependent']] : $count[$answer['governor']];
				if($nb_answers_word > $max_answers){
					$max_answers = $nb_answers_word;
					$nb_answers_having_majority = 1;

				} elseif ($nb_answers_word == $max_answers){
					$nb_answers_having_majority++;
				}

				$data[$annotation_id]['answers'][$key]['count'] =  $nb_answers_word;
				$data[$annotation_id]['answers'][$key]['percent'] =  round(100*$nb_answers_word/$nb_answers);
			}
			$class_refused = 'others';
			foreach($data[$annotation_id]['answers'] as $key=>$answer){
				if($user_answer == $answer['answer']){
					if($answer['count']==$max_answers && $nb_answers_having_majority==1){
						$data[$annotation_id]['answers'][$key]['label'] = 'user_majority';
						if($nb_answers>1)
							$nb_answers_majority++;
					}
					else{
						$data[$annotation_id]['answers'][$key]['label'] = 'user_minority';
						$nb_answers_minority++;
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
		    	@if(!$hidden_titles)
		    		<h4>{{ $annotation->relation->name }}</h4>
		    	@endif
		    	<div><em>{{ trans_choice('site.zombies-answered', $nb_answers,['nb_answers' => $nb_answers]) }}</em></div>
				@if(Auth::user()->isAdmin())
					<div><strong>{{ $annotation->sentence_id}}</strong>
						@if($annotation->sentence->isReference())
							(Phrase de référence)
						@endif
					</div>
				@endif
		    	<span class="sentence-brat" id="annotation_{{ $annotation_id }}" focus="{{ $annotation->focus_position }}">{{ $annotation->sentence->content}}.</span>
				<div style="display:inline-block;" class="py-2">
					<span style="margin-right:20px;position:relative;">
					@if(Auth::user()->followsDiscussionAnnotation($annotation_id))
						<span style="position:relative;" data-id="{{ $annotation_id }}" class="unfollow-thread-button btn btn-small btn-faded btn-outline btn-green"><i class="fa fa-check" style="color:green;" aria-hidden="true"></i> Discussion suivie</span>
						<span style="position:relative;display:none;" data-id="{{ $annotation_id }}" class="follow-thread-button btn btn-small btn-faded btn-outline btn-green">Suivre la discussion</span>
					@else
						<span style="position:relative;display:none;" data-id="{{ $annotation_id }}" class="unfollow-thread-button btn btn-small btn-faded btn-outline btn-green"><i class="fa fa-check" style="color:green;" aria-hidden="true"></i> Discussion suivie</span>
						<span style="position:relative;" data-id="{{ $annotation_id }}" class="follow-thread-button btn btn-small btn-faded btn-outline btn-green">Suivre la discussion</span>
					@endif
					</span>
				</div>
				<div style="display:inline-block;">
					<span style="margin-right:20px;position:relative;">
						<span style="position:relative;" data-id="{{ $annotation->id }}" data-type="{{ get_class($annotation) }}" class="message-button btn btn-small btn-faded btn-outline btn-green">Discuter de la réponse
							<span class="badge" style="background-color:green;">{{ $nb_messages }}</span>
						</span>
					</span>
				</div>
				@if(Auth::user()->isAdmin())
				<div style="display:inline-block;" class="float-right py-2">
					{!! link_to('game/admin-game/begin/'.$annotation->relation_id.'?save-mode=expert&annotation_id='.$annotation->id,"Annotation expert",['target'=>'blank', 'class'=>'btn btn-small btn-faded btn-outline btn-green']) !!}
				</div>
				@endif
		    	<span id="thread_{{ $annotation_id }}" class="thread" style="display:none;"></span>
		    </td>
		    <td style="vertical-align:top;text-align:center;padding-top:4rem;">
		    	{!! Html::image('img/osEnCroixSeuls.png','logo',array('class'=>'croix-os','style'=>'height:65px;width:65px;')) !!}<br/>
		    	<span class="answer_refused {{ $class_refused }}">{{ $percent_not_relation }} %</span>
		    </td>
	    </tr>

    @endforeach
    </table>
    @if(!is_array($annotation_ids))
    <div class="text-center">
		{{ $annotation_ids->links() }}
	</div>
	@endif
    </div>
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('brat/style-vis.css') }}"/>
	<style>
@if($hidden_titles)
	.table td {
	    border-top: 0px solid #eceeef;
	}
@endif
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
@if(!isset($params) && $nb_sentences_played>0)
function updateResultat(){
	$('#majority').html("{{ round(100*$nb_answers_majority/$nb_sentences_played,0) }}%");
	@if($nb_sentences_first_answer==1)
		$('#first-answer').show();
		$('.nb-first-answer').html('{{$nb_sentences_first_answer}}');
	@elseif($nb_sentences_first_answer>1)
		$('#first-answers').show();
		$('.nb-first-answer').html('{{$nb_sentences_first_answer}}');
	@endif

	@if($annotations_with_discussion==0)
		$('#no-discussion').show();
	@elseif($annotations_with_discussion==1)
		$('#one-discussion').show();
	@else
		$('#annotations_with_discussion').html("{{ $annotations_with_discussion }}");
		$('#several-discussions').show();
	@endif
}
updateResultat();
@endif


</script>
