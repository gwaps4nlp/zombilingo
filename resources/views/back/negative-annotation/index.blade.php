@extends('back.template')

@section('style')
<style type="text/css">
	.glyphicon-ban-circle.visible {
		color:red;
	}
	.glyphicon-minus-sign.visible {
		color:red;
	}
	.glyphicon-ok-sign.visible {
		color:green;
	}
	.disabled {
		color:grey;
	}
	.highlight {
		font-weight: 900;
		font-style: italic;
	}
	.glyphicon:empty{
		cursor:pointer;
		font-size:17px;
	}
</style>
@stop
@section('content')

{!! link_to('negative-annotation/import','Import a file',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	<h1>Negative items</h1>
	@if(count($negative_annotations))
		<table class="table">
		<thead>
			<tr>
				<th>sentence</th>			
				<th>sentid</th>
				<th>visible</th>
			</tr>
		</thead>
		@foreach($negative_annotations as $negative_annotation)
		<tr>
			<td>
				<span class="sentence" focus="{{ $negative_annotation->focus }}">{{ $negative_annotation->sentence->content }}</span>
			</td>
			<td>
				{!! link_to('sentence/'.$negative_annotation->sentence->id, $negative_annotation->sentence->sentid) !!}
			</td>			
			<td>
				@if($negative_annotation->visible)
				<span class="glyphicon glyphicon-ok-sign visible" id="{{ $negative_annotation->id }}_1"></span>
				<span class="glyphicon glyphicon-ban-circle disabled" id="{{ $negative_annotation->id }}_0"></span>
				@else
				<span class="glyphicon glyphicon-ok-sign disabled" id="{{ $negative_annotation->id }}_1"></span>
				<span class="glyphicon glyphicon-ban-circle visible" id="{{ $negative_annotation->id }}_0"></span>
				@endif
			</td>
		</tr>
		@endforeach
	@elseif(count($relations))
		<table class="table">
		<thead>
			<tr>
				<th>Relation</th>			
				<th>Number</th>
				<th>Action</th>
			</tr>
		</thead>
		@foreach($relations as $relation)	
			<tr>
				<td><a href="{{ url('negative-annotation/index',['relation_id'=>$relation->relation_id]) }}">{{ $relation->relation_name }}</a></td>
				<td>{{ $relation->count }}</td>
				<td><a href="{{ url('negative-annotation/delete-by-relation',['relation_id'=>$relation->relation_id]) }}"  onclick="return confirm('Are you sure ?')">empty</a></td>
			</tr>
		@endforeach
	@else
		None negative items.
	@endif
@stop

@section('scripts')
<script>
$(document).ready(function(){
    $(".sentence").each(function(){
    	var sentence = displaySentence($(this).html(), $(this).attr('focus'));
        $(this).html(sentence);
    });
    $('.glyphicon').on('click', function(){
    	
    	var params = $(this).attr('id').split("_");
        $.ajax({url: base_url + "negative-annotation/change-visibility", data: {
        	id : params[0],
        	visible : params[1]
        },
        success: function(result){

        if(parseInt(result.visible)){
        	$("#"+result.id+"_1").removeClass('disabled').addClass('visible');
        	$("#"+result.id+"_0").removeClass('visible').addClass('disabled');
        } else {
        	$("#"+result.id+"_0").removeClass('disabled').addClass('visible');
        	$("#"+result.id+"_1").removeClass('visible').addClass('disabled');        	
        }
    	}
	});
    });    
});
</script>
@stop