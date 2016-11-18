@extends('front.template')

@section('main')
<div class="row">

	<div class="col-md-10 col-md-offset-1 center" id="game">
		<h3>Fais glisser le mot sur son pays d'origine</h3>
		<div id="results">
			Score : <span id="score"></span>/<span id="score_max"></span><br/>
			<button id="solution">Solution</button>
			<button id="reset">Recommencer</button>
		</div>
		<div id="words_list">
			@foreach($words as $key=>$word)
			<span class="word hidden" id="{{ $key }}">
			{{$word['word']}}
			</span>
			@endforeach
		</div>
		
		@foreach($countries as $key=>$country)
		<div class="country" id="{{ $key }}">
		{{$country['name']}}<br/>
		{!! Html::image('img/flags/flag_'.$key.'.svg',$country['name'],['class'=>'thumb']) !!}<br/>
		</div>
		@endforeach
	</div>

</div>
@stop
@section('css')
<style>
.word {
  border-radius: 10px;
  border: solid 1px white;
  cursor : grab;
  padding:10px 5px;
}
.country .word {
  border: solid 0px white;
  cursor : grab;
  font-size: 0.8em;
  padding:0px 5px;
}
#results{
	display: none;
	position:absolute;
	left:275px;
	width:300px;
	text-align:center;
}
.hidden {
  display:none;
}
#words_list {
  position:absolute;
  text-align:center;
	left:330px;
	top:100px;
	width:150px;
}
.true {
  border: solid 1px #00FF00;
  color:#00FF00;
}
.false {
  border: solid 1px red;
  color:#ED0000;
  color:#C60800;
}
.country {
	//background-color:white;
	//color:black;
	width:100px;
	padding:0px;
	padding-bottom:30px;
	text-align:center;
	position:absolute;
	left:350px;
	top:100px;
	//border-radius: 30px;
	//border: solid 1px white;
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
    var countries={!! json_encode($countries) !!};
    var words={!! json_encode($words) !!};
	var angle=0;
	var index=0;
	var score = 0;
	var end_game=false;
	function endGame(){
		end_game=true;
		score=0;
		$.each( $(".country"),function(index,element){
			var country_id = element.id;
			$.each( $("#"+country_id+" > .word"),function(index,element){
				var word_id = element.id;
				if(words[word_id].country_id==country_id){
					$(element).addClass('true');
					score++;
				} else {
					$(element).addClass('false');

				}
			});			
		});
		$('#score').text(score);
		$('#score_max').text(words.length);
		$('#results').show();
	}

	function computeScore(){
		score = 0;
		$.each( $(".country"),function(index,element){
			var country_id = element.id;
			$.each( $("#"+country_id+" > .word"),function(index,element){
				var word_id = element.id;
				if(words[word_id].country_id==country_id){
					score++;
				}
			});			
		});
	}

	$.each( $(".country"),function(index,element){

		var offset = $('#'+element.id).offset();
		x= 50 + 2*index*60;
		y= 100 + Math.sin(angle)*100;
		$('#'+element.id).animate({left: x+'px', top:y+'px'});
		angle+=3.14/($(".country").length-1);
	});

    $('.word').draggable();


    $('#0').removeClass('hidden');
    $( ".country" ).droppable({
    	accept: '.word',
      drop: function( event, ui ) {
        var word = ui.draggable;
        word.css('position','static');
        word.css('left','');
        word.css('top','');

        word.draggable();

       
        word.css('position','relative');
        word.appendTo($(this));
 		if(end_game){
 			word.removeClass("true false");
 			if(words[word[0].id].country_id==word.parent(".country")[0].id){
 				word.addClass('true');
 				score++;
 			}
 			else{
 				word.addClass('false');
 			}
 			computeScore();
 			$('#score').text(score);
 		}       
        if(word[0].id==index){
        	index++;
        	if(index==words.length)
        		endGame();
        	else
        		$('#'+index).removeClass('hidden');
        }

      }
    });

    $( "#solution" ).click(function() {
		$.each( $(".country"),function(index,element){
			var country_id = element.id;
			$.each( $("#"+country_id+" > .word"),function(index,element){
				var word_id = element.id;
				$(element).removeClass('true false');
				if(words[word_id].country_id==country_id){
					
				} else {
					$('#'+word_id).appendTo($('#'+words[word_id].country_id));
				}
			});			
		});
	});

    $( "#reset" ).click(function() {
    	score=0;
    	index=0;
    	end_game=false;
    	$('#results').css('display','none');
		$.each( $(".word"),function(index,element){
			$(element).removeClass("true false");
			$(element).addClass("hidden");
			$(element).appendTo($('#words_list'));
			$(element).draggable({ cursor: "crosshair"});
			$('#0').removeClass('hidden');

		});
	});
});
</script>
@stop