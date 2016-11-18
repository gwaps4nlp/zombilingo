@extends('back.template')

@section('content')

@include('back.sentence.block-search')

<p>{{$sentence->content}}</p>
<p>Sentid : {{ $sentence->sentid }}</p>
<p>Corpus : {{ $sentence->corpus->name }}</p>

<table class="table">
<thead>
	<tr>
		<th>index</th>
		<th>word</th>
		<th>gov_word</th>
		<th>rel</th>	
		<th>score</th>
		<th>lemma</th>
		<th>cat</th>
		<th>pos</th>
		<th>gov</th>
		<th>feat</th>
		<th>parser</th>
	</tr>
</thead>
<?php 
$columns = array('word_position','word','governor_word','relation_name','score','lemma','category_id','pos_id','governor_position','features');
?>
@foreach($annotations as $annotation)

<tr class="<?php echo ($annotation->isUser())?'user':''?>">
	@foreach($columns as $column)
		
	<td>
		@if($column=='governor_word')
			@foreach($annotations->where('word_position', $annotation->governor_position)->slice(0,1) as $gov)
				{{ $gov->word }}
			@endforeach
		@elseif($column=='word')
			@if(!$annotation->isUser() && $annotation->relation->level_id<8 && ($annotation->relation->type=="trouverTete"||$annotation->relation->type=="trouverDependant"))
				{!! link_to('game/admin-game/begin/'.$annotation->relation_id.'?annotation_id='.$annotation->id,$annotation->$column,['target'=>'blank']) !!}
			@else
				{{ $annotation->$column }}
			@endif
		@else
			{{ $annotation->$column }}
		@endif
	</td>	
	@endforeach
	<td>
	<?php
		$parsers = $annotation->parsers;
		foreach($parsers as $i=>$parser){
			echo $parser->name;
			if($i<count($parsers)-1)
				echo ";<br/>";
		}
	?>
	</td>
</tr>
@endforeach

@stop

@section('style')
<style>
.table > tbody > tr > td {
	padding:2px;
	font-size:0.9em;
}
.table > tbody > tr.user {
	background-color: #fff2c3;
}
</style>
@stop

