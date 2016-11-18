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
{!! link_to('tutorial-annotation/import','Import a file',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	<h1>{{ trans('back/index.tutorial-items') }}</h1>
	@if(count($tutorial_annotations))
		<table class="table">
		<thead>
			<tr>
				<th>Sentence</th>			
				<th>Sentid</th>
				<th>Indication</th>
				<!-- <th>visible</th> -->
			</tr>
		</thead>
		@foreach($tutorial_annotations as $tutorial_annotation)
		<tr>
			<td>
				<span class="sentence" focus="{{ $tutorial_annotation->focus }}">{{ $tutorial_annotation->sentence->content }}</span>
			</td>
			<td>
				{!! link_to('sentence/'.$tutorial_annotation->sentence->id, $tutorial_annotation->sentence->sentid) !!}
			</td>
			<td>
				{{ $tutorial_annotation->explanation }}
			</td>			
			<!-- <td>
				@if($tutorial_annotation->visible)
				<span class="glyphicon glyphicon-ok-sign visible" id="{{ $tutorial_annotation->id }}_1"></span>
				<span class="glyphicon glyphicon-ban-circle disabled" id="{{ $tutorial_annotation->id }}_0"></span>
				@else
				<span class="glyphicon glyphicon-ok-sign disabled" id="{{ $tutorial_annotation->id }}_1"></span>
				<span class="glyphicon glyphicon-ban-circle visible" id="{{ $tutorial_annotation->id }}_0"></span>
				@endif
			</td> -->
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
				<td><a href="{{ url('tutorial-annotation/index',['relation_id'=>$relation->relation_id]) }}">{{ $relation->relation_name }}</a></td>
				<td>{{ $relation->count }}</td>
				<td><a href="{{ url('tutorial-annotation/delete-by-relation',['relation_id'=>$relation->relation_id]) }}"  onclick="return confirm('Are you sure ?')">empty</a></td>
			</tr>
		@endforeach
	@else
		None items for the tutorials.	
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
        $.ajax({url: base_url + "tutorial-annotation/change-visibility", data: {
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