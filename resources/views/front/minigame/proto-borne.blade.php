@extends('front.template-tactile-terminal')

@section('main')
<div class="row">

	<div class="col-md-12" id="game">
		<h3>Trouve le pays d'origine du mot :</h3>

		<div id="words_list">
		</div>
		<div id="countries_list">
			@foreach($countries as $key=>$country)
			<div class="country" id="{{ $key }}">
			{{$country['name']}}<br/>
			{!! Html::image('img/flags/flag_'.$key.'.svg',$country['name'],['class'=>'thumb']) !!}<br/>
			</div>
			@endforeach
		</div>
		<div style="position:absolute;top:0;right:0;margin-top: 20px;
margin-bottom: 10px;font-size:1.5vw;margin-right:2%;">
			<div id="results">
				Score : <span id="score">0</span><br/>
				Mot : <span id="index_word">1</span>/<span id="nb_words">10</span><br/>
				<a class="btn btn-success" style="font-size:24px;" onclick="window.location.href=''" id="reset">Nouvelle partie</a>
			</div>
		</div>
	</div>

</div>
<div id="footer">
<span>En partenariat avec</span><br/>
<img src="../img/dis-moi-10-mots.jpg" style="height:100px"/>
<img src="../img/logo_inria.png" style="height:100px"/>
<img src="../img/logo_sorbonne.png" style="height:100px"/>
</div>
<div id="modalResult" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content" id="end-party"></div></div></div>

@stop
@section('css')
<style>

@font-face {
  font-family: 'font-definition';
  src: url("../fonts/infini-romain.otf");
}
@font-face {
  font-family: 'font-definition-italic';
  src: url("../fonts/infini-italique.otf");
}


body {
	color:black;
	font-family: "Arial,sans-serif;";
	overflow: hidden;
}
body.test {
	background: #aaa url("../img/page-seyes.png") no-repeat fixed;
	background-size: 100%;
}
h3 {
	font-size:1.5vw;
	margin-left:2%;
	font-family: "Arial,sans-serif;";

}
#footer {
	position:absolute;
	bottom:0;
	text-align:left;
	width:100%;
	margin-bottom:20px;
	margin-left:20px;
}
.badge {
    display: inline-block;
    min-width: 33px;
    padding: 3px 7px;
    font-size: 30px;
    font-weight: bold;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    background-color: #999;
    border-radius: 10px;
}
#end-party {
	font-size: 5vw;
	line-height: 1;
}
.word {
  padding:10px 5px;
  font-size:9vw;
  font-family:"font-definition";
}
.definition {
font-family: "font-definition-italic";
font-size: 3vw;
line-height: 4vw;
margin-top: 1vw;
margin-left: 20%;
margin-right: 20%;
}
#countries_list {
	position:absolute;
	top:27%;
}
.country .word {
  border: solid 0px white;
  cursor : grab;
  font-size: 1.5vw;
  padding:0px 5px;
  line-height:0.1em;
}
.country {
  border: solid 0px white;
  cursor : pointer;
  font-size: 1.5vw;
  padding:0px 5px;
}
.country .definition {
	display:none;
}
.modal-dialog {
    width: 50%;
    margin: 30px auto;
    text-align: center;
}
.modal-content {
	background-color: white;
	background-image: url("../img/background-page-seyes.png");
	background-clip: content-box;
	background-size: 100% 100%;
}
.hidden {
  display:none;
}
#words_list {
  text-align:center;
  margin:auto;
  margin-top: 4%;
  line-height: 4vw;
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
	font-family:"Arial,sans-serif;";
	font-size:2vw;
	line-height: 2vw;
	width:200px;
	padding:0px;
	padding-bottom:30px;
	text-align:center;
	position:absolute;
	left:350px;
	top:100px;
	color:#81818a;
}
.country .badge {
	display:none;
}
.thumb {
	width:100%;
	z-index:2;
 margin-bottom:20px;	
}
#game {
	height:650px;
}
</style>
@stop
@section('scripts')
{!! Html::script('js/data-minigame.js') !!}
<script>
$(document).ready(function () {
    // var countries={!! json_encode($countries) !!};
	var angle=0;
	var index=0;
	var score = 0;
	var end_game=false;
	var busy=false;
    // var words={!! json_encode($words) !!};
    words=words.sort(function(a,b){
    	return Math.random()>Math.random();
    });

    $.each(words,function(index,element){
    	var text = '<span class="word hidden" id="'+index+'"><span id="word_content_'+index+'">'+element.word+'<span><span class="badge" id="badge_'+index+'">5</span><br/><div class="definition" id="definition_'+index+'">'+element.definition+'</div></span>';
    	$('#words_list').append(text);
    })	
	var width = $( window ).width();
	var height = $( window ).height();

	$.each( $(".country"),function(index,element){
		var width = $( window ).width();
		x= 200 + index*(width-100)/6;
		y= 200 + Math.sin(angle)*height/9;
		$('#'+element.id).animate({left: x+'px', top:y+'px'});
		angle+=3.14/($(".country").length-1);
	});

    $('#0').removeClass('hidden');
    $('#game').css('height',$( window ).height());
    $(document).on('touchend click', '.country', function(e){
    	if(busy) return;
    	else
    	busy = true;
		if(words[index].country_id==this.id){
			var points = parseInt($("#badge_"+index).text(),10);
			score+=points;
			$('#score').text(score);
            $("#"+index).after('<span class="elm" style="color:green;position:absolute;z-index:1;">+'+points+'</span>');
            $('#'+index).appendTo($('#'+this.id));
			$('#'+index).css('font-size','1.5vw');

            $(".elm").animate({
            		fontSize : '400px',
                    opacity : 0,
                    top : '200px',
                    left : '1000px'
                }, 1000, function(){
                	$(this).remove();
                    nextWord(this.id);
                });			

		} else {
			var score_word = parseInt($("#badge_"+index).text(),10);
			score_word -=1;
			if(score_word<1){
				$('#word_content_'+index).css('position','absolute');
				$('#definition_'+index).css('display','none');
				var position_orig = $('#word_content_'+index).offset();
				var position = $('#countries_list').position();
				var position_country = $('#'+words[index].country_id).position();
				var top = $('#'+words[index].country_id).css('top')+position.top;
				var left = $('#'+words[index].country_id).css('left');
				var dleft = position_country.left-position_orig.left;
            $('#word_content_'+index).animate({
                    fontSize : '1.5vw',
                    top : (position.top+0.28*height)+'px',
                    left : '+='+dleft
                }, 1000, function(){
                	$('#word_content_'+index).css('position','initial');
		            $('#'+index).css('position','initial');
		            $('#'+index).appendTo($('#'+words[index].country_id));
					$('#'+index).css('font-size','1.5vw');
					$('#'+words[index].country_id).effect( 'pulsate', {opacity: 0.8,'background-color':'red'}, 500);
	                nextWord(this.id);					
                });	

			}
            else {
				var position = $("#badge_"+index).position();
	            $("#"+index).after('<span id="elm" style="color:red;position:absolute;z-index:1;">-1</span>');
	            $("#elm").css({'top':position.top+'px','left':position.left+'px'});
	            $("#elm").animate({
	            		fontSize : '500px',
	                    opacity : 0.4,
	                    top : '600px',
	                    left : '1000px'
	                }, 1000, function(){
	                    $(this).remove();
	                });
				if(score_word<0) score_word=0;
				$("#badge_"+index).text(score_word);
				busy = false;
			}
		}
        
    });

	function nextWord(country_id){

		index++;
		if($('#'+index).length<1)
			endGame();
		else {
			$('#index_word').text(index+1);
			$('#'+index).css('font-size','1px');
			$('#'+index).removeClass('hidden');
	        $('#'+index).animate({
	        		fontSize : '9vw'
	            }, 1000,function(){
	            	busy = false;
	            });
    	}	
	}

	function endGame(){

		$('#results').hide();
		$('#end-party').text('');
		var text = '<span style="font-size:3vw;">Partie terminée</span><br/>';
		text += '<span style="font-size: 5vw;">Score : '+score+'/50</span><br/><a href="" class="btn btn-success" style="font-size:48px;">Nouvelle partie</a>';
		$('#words_list').text('');
		$('#words_list').append(text);
		text += '<div style="font-size:2vw;margin-top:50px;margin-bottom:40px;">Plus de jeux sur ZombiLingo.org :<br/><img src="../img/qr_code_zombilingo.jpg" /></div>';
		$('#end-party').append(text);
		$('#modalResult').modal('show');
	}

});
</script>
@stop