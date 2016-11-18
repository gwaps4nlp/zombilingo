<?php
$players_annotations=[];
?>
<div id="modalCompareDuel">
    <h1>{{ $duel->relation->description }}</h1>

    <div id="content">
        légende : <span class="challenger_answer user_answer">réponses communes</span>, <span class="user_answer">mes réponses</span>, <span class="challenger_answer">réponses de l'adversaire</span>.<br/>
    	
        <table class="table">
    	@foreach($duel->annotations as $annotation)
    		<tr><td><span class="sentence" id="annotation_{{ $annotation->id }}" focus="{{ $annotation->focus_position }}" user_answer="{{ $annotation->user_answer }}">{{ $annotation->sentence->content }}</span></td></tr>
    	@endforeach
    	</table>
    	
        @foreach($duel->annotation_users as $annotation)
    		<?php 
    		$players_annotations[] = ['annotation_id'=> $annotation->pivot->annotation_id , 'answer' => $annotation->pivot->answer, 'user_id'=> $annotation->user_id];
    		?>
    	@endforeach
    </div>
    <a href="{{ url('duel/revenge').'/'.$duel->id }}" class="btn btn-lg btn-success change">Faire la revanche</a>
    <button type="button" class="btn btn-lg btn-success" data-dismiss="modal">{{ trans('site.close') }}</button>
</div>

<script>
var players_annotations = {!! json_encode($players_annotations) !!};
$(document).ready(function(){
    $(".sentence").each(function(){
    	var sentence = displaySentenceStats($(this).html(), $(this).attr('focus'), $(this).attr('user_answer'));
        $(this).html(sentence);
    });
    $(players_annotations).each(function(){
    	if(this.user_id=={{ Auth::id() }})
    		$('#annotation_'+this.annotation_id).children('span[word_position='+this.answer+']').addClass('user_answer');
    	else
    		$('#annotation_'+this.annotation_id).children('span[word_position='+this.answer+']').addClass('challenger_answer');
    });
});
</script>
