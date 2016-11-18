@extends('front.template')

@section('main')
<div class="row">

	<div class="col-md-10 col-md-offset-1 center" id="game">
		<h3>Fais glisser chaque mot sur sa définition</h3>

		<div class="row">
			<div class="col-md-2">
			<div id="results">
				Score : <span id="score"></span>/<span id="score_max"></span><br/>
				<button id="solution">Solution</button>
				<button id="reset">Recommencer</button>
			</div>		
			<ul id="words_list">
			</ul>
			</div>
			<div id="definitions_list" class="col-md-8">
			<ul>
				@foreach($words as $key=>$word)
				<li class="definition" id="def_{{ $word['id'] }}">
					<span class="empty" id="empty_{{ $word['id'] }}">_________</span> : 
					{{$word['definition']}}
				</li>
				@endforeach
			</ul>
			</div>
		</div>
	</div>
</div>
@stop
@section('css')
<style>
.word {
  border-radius: 10px;
  border: solid 1px white;
  cursor : grab;
  padding:2px 5px;
}
.definition .word {
  border: solid 0px white;
  cursor : grab;
  padding:0px;
  color:#F0C300;
}
#results{
	display: none;
	text-align:center;
}
.hidden {
  display:none;
}
#words_list li {
	list-style-type: none;
	height:35px;
	text-align:center;
}
#words_list {
	padding-left:10px;
}
#definitions_list .true {
  color:#00FF00;
}
#definitions_list .false {
  color:#C60800;
}
.thumb {
	width:60%;
	z-index:2;
}
#game {
	height:650px;
}
</style>
@stop
@section('scripts')
<script>
$(document).ready(function () {
    var words={!! json_encode($words) !!};
    words=words.sort(function(a,b){
    	return Math.random()>Math.random();
    });
    $.each(words,function(index,element){
    	var text = '<li><span class="word" id="'+element.id+'">'+element.word+'</span></li>';
    	$('#words_list').append(text);
    })
	var score = 0;
	var end_game=false;
	var previous=false;
	function endGame(){
		end_game=true;
		$('.word').removeClass("true false");
		$.each( words,function(index,element){
			var word_id = element.id;
			var answer_user = $("#def_"+word_id+" span.word")[0];
			if(answer_user.id ==  word_id){
				$(answer_user).addClass('true');
				score++;
			} else {
				$(answer_user).addClass('false');
			}
		});
		$('#score').text(score);
		$('#score_max').text(words.length);
		$('#results').show();
		score=0;
	}

    $('.word').draggable({
    	start : function(event,ui){
    		previous = $(this).parent();
    	},
    	stop : function(event,ui){

    	}
    	});

    $( ".definition" ).droppable({
    	accept: '.word',
      drop: function( event, ui ) {
        var word = ui.draggable;
        word.css('position','static');
        word.css('left','');
        word.css('top','');
        var word_prev = $(this).children('.word');
        if(word_prev.length==1){
        	word_prev.insertBefore(previous.children().first());
        }
        else
        	previous.children('.empty').show();

        $(this).children('.empty').hide();

        word.insertBefore($('#'+this.id+'> .empty'));
        
        word.draggable();
       
        word.css('position','relative');

        if($('#definitions_list span.word').length==words.length){
        	endGame();
        	return;
        }

      }
    });

    $( "#solution" ).click(function() {
    	$(".word").removeClass("true false");
		$.each( words ,function(index,element){
			var word_id = element.id;
			var word = $("#"+element.id);
			word.insertBefore($("#def_"+word_id+" > .empty"));
		});
	});
    $( "#reset" ).click(function() {
    	index=0;
    	end_game=false;
    	$('#results').css('display','none');
    	$('#words_list').html('');
		$(".word").detach();
	    $.each(words,function(index,element){
	    	var text = '<li><span class="word" id="'+element.id+'">'+element.word+'</span></li>';
	    	$('#words_list').append(text);
	    });
	    $('.empty').show();
	    $('.word').draggable({
	    	start : function(event,ui){
	    		previous = $(this).parent();
	    	},
	    	stop : function(event,ui){

	    	}
	    });
	});
});
</script>
@stop