@extends('front.template')

@section('main')
{!! Form::open(['url' => 'annotation-user/index', 'method' => 'get', 'role' => 'form']) !!}
<div class="row" id="index-game">
<div class="col-md-10 col-md-offset-1 center" id="blocJeu">
<div class="col-md-10 col-md-offset-1">

	{!! Form::control('selection', 3, 'corpus_id', $errors, '',$corpora,null,trans('game.all-corpora'),$params['corpus_id']) !!}
	{!! Form::control('selection', 3, 'relation_id', $errors, '',$relations,null,trans('game.all-phenomena'),$params['relation_id']) !!}
</div>
<div class="col-md-10 col-md-offset-1">
	<div class="form-group col-lg-3">
		<input type="checkbox" value="1" name="undecided" {{ ($params['undecided'])?'checked="checked"':'' }} /> score<sub>preannot</sub> &le; score<sub>users</sub>
	</div>
	<div class="form-group col-lg-3 col-md-offset-3">
		<input type="submit" value="Filtrer" class="btn btn-success" />
	</div>
</div>
	{!! Form::close() !!}	
<div class="col-md-10 col-md-offset-1">
Nombre de résultats : {!! $annotations_user->total() !!}
{!! $annotations_user->render() !!}
</div>
<table class="table">
@foreach($annotations_user as $annotation)
	<tr>
		<td>{{ $annotation->relation->name }}</td>
		<td><span class="sentence" focus="{{ $annotation->focus }}" user_answer="{{ $annotation->user_answer }}">{{ $annotation->sentence->content }}</span></td>
		<td style="width:20%">
	@foreach($annotation->statistics as $key=>$stats)
		<span class="source_{{ $stats->source_id }} rank_{{ $key }} {{ ($stats->user_answer==$annotation->user_answer)?'user_answer':'' }}">{{ $stats->word }}({{ $stats->number_answers }} - {{round($stats->number_answers/$stats->number_answers_total,2)}})</span>  {{ $stats->score }} - {{ round($stats->score/$stats->score_total,2) }}<br/>
	@endforeach
		</td>
	</tr>
@endforeach
</table>
</div>
</div>
@stop

@section('scripts')
<script>
$(document).ready(function(){
    $(".sentence").each(function(){
    	var sentence = displaySentenceStats($(this).html(), $(this).attr('focus'), $(this).attr('user_answer'));
        $(this).html(sentence);
    });  
});
</script>
@stop
@section('style')
<style>
.source_1 {
	background-color:#81F7BE;
}
.source_2.rank_0 {
	background-color:#FF4000;
}
.source_3.rank_0 {
	background-color:#9FF781;
}
.source_3 {
	font-style: italic;
}
.highlight {
    font-weight: 900;
    font-style: italic;
}
.user_answer {
    font-weight: 900;
    color:green;
	border:solid 1px grey;
}
</style>
@stop