@extends('front.template')

@section('css')
    {!! Html::style('css/bootstrap-tour.css') !!}
    {!! Html::style('css/non-sass.css') !!}
@stop

@section('main')

<div class="row" id="index-game">

    <div class="col-md-10 col-md-offset-1 center" id="blocJeu">
		@if (session()->has('error'))
			@include('partials/error', ['type' => 'danger', 'message' => session('error')])
		@endif	
		@if(session()->has('message'))
			@include('partials/error', ['type' => 'success', 'message' => session('message')])
		@endif	
		@if(isset($info))
			@include('partials/error', ['type' => 'info', 'message' => $info])
		@endif

	    <div class="aideTool">
	        <div class="savant">
	            <div class="aideTip" id="helpRelation">
			        {{ trans('game.explication-1')}}<br/>
			        {{ trans('game.explication-2')}}<br/>
			        {{ trans('game.explication-3')}}<br/>
	            </div>
	        </div>
	    </div>

        <div id="relation-done-small">
		<ul>
		@foreach($relations as $relation)
			<?php
			$special = $relation->type=='special'&& $relation->tutorial>0 ;
			if($relation->todo>0 || $special)
				continue;
			?>
			<li class="col-lg-1 col-md-2 col-sm-2">
				{!! Html::image('img/tombstone-game.png','',['style'=> 'width:105%;']) !!}
                <div id="{{ $relation->id }}" class="relation-done" data-params="corpus_id={{ $game->corpus_id }}&relation_id={{ $relation->id }}">	
                <div class="relation_name-done-small {!! (strlen(str_replace(['≪','≫'],['',''],html_entity_decode($relation->name)))<25)?'short':'' !!}">
                	<span class="relation_name">{{ $relation->name }}</span>
                </div>
				<div class="scores-done-small">
					<div id="points{{ $relation->id }}" class="points">
						<span class="points">{{ trans('game.points') }} : {{ Html::formatScore($relation->score) }}</span>
					</div>
				@if($relation->type!='special')
					<div class="points">
						<span class="points">{{ trans('game.annotations') }} :<br/>{{ $relation->done.'/'.$relation->total }}</span>
					</div>
				@endif
				</div>
				</div>
			</li>
		@endforeach
		</ul>
		
		</div>
		<div style="clear: both;"></div>
		<br/>  
        <nav>
         <ul>
            <li class="col-lg-3 col-md-3 col-sm-3 phenomeneSelection">
			{!! Html::image('img/tombstone-user.png','',['style'=> 'width:100%;']) !!}
				<div class="relation-done relations-choice">
					{!! Form::open(['url' => '/game', 'method' => 'get', 'role' => 'form','id'=>'corpusChoice','style'=>'width:100%;']) !!}
					@if((Auth::user()->isAdmin() || Auth::user()->level_id>=2))	
						{!! Form::control('selection', 0, 'corpus_id', $errors, trans('site.choice-of-the-corpus'),$corpora,null,trans('site.choose-a-corpus'),$game->corpus_id) !!}
					@else
						<div class="form-group  ">
						<label for="corpus_id" class="control-label">{{ trans('game.choice-corpus') }}</label>
						<label class="control-label level-not-available">{{ trans('site.available-at-level-2') }}</label>
						</div>
					@endif
					{!! Form::close() !!}<br/>
				</div>
			</li>

			@foreach($relations as $relation)
				<?php
				$special = $relation->type=='special' && $relation->tutorial>0 ;
				if(!$relation->tutorial && $relation->type=='special') continue;
				if($relation->todo<=0 && !$special) continue;
				?>
				
	            <li class="col-lg-3 col-md-3 col-sm-3 phenomeneSelection level-{{ $relation->level_id }}">
				{!! Html::image('img/tombstone-game.png','',['style'=> 'width:100%;']) !!}
					<?php
					
					$class_relation = "detail-relation";
					if($relation->level_id>Auth::user()->level_id)
						$class_relation .= " disabled-relation";
					elseif($relation->todo<=0 && !$special)
						$class_relation .= " relation-done";
					?>
					<div class="level_relation">
						<span data-toggle="tooltip" data-placement="auto top" title="Points x{{ $relation->level_id }}" >{{ trans('game.level') }} {{ $relation->level_id }}</span>
					</div>
	                <div id="{{ $relation->id }}" class="{{ $class_relation }}">
		                <div class="relation_name {!! (strlen(str_replace(['≪','≫'],['',''],html_entity_decode($relation->name)))<25)?'short':'' !!}"><span class="relation_name">{{ $relation->name }}</span></div>
						<div class="links-level">
						@if($relation->tutorial && $relation->todo>0)
							<span>{!! link_to('',trans('game.game'),array('class'=>'btn btn-success link-level',
										'title' => trans('game.do-a-party-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'game',
										'id_phenomene' => $relation->id)) !!}</span>
						@else
							<span>{!! link_to('',trans('game.game'),array('class'=>'btn btn-success link-level link-level-disabled',
										'title' => trans('game.do-a-party-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'game',
										'id_phenomene' => $relation->id)) !!}</span>					
						@endif
						<br/>
						@if($relation->type!='special')
							<span>{!! link_to('',trans('game.training'),array('class'=>'btn btn-success link-level',
										'title' => trans('game.do-the-training-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'training',
										'id_phenomene' => $relation->id)) !!}</span>
						@endif

						@if($relation->type=='special'&& $relation->tutorial)
							<span>{!! link_to('',trans('game.game'),array('class'=>'btn btn-success link-level',
										'title' => trans('game.do-a-party-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'special',
										'id_phenomene' => $relation->id)) !!}</span>				
						@endif
						</div>
						<div class="scores">
							<div id="points{{ $relation->id }}" class="points">
								<span class="points">{{ trans('game.points') }} : {{ Html::formatScore($relation->score) }}</span>
							</div>
						@if($relation->type!='special')
							<div class="points">
								<span class="points">{{ trans('game.annotations') }} : {{ $relation->done.'/'.$relation->total }}</span>
							</div>
						@endif
						</div>
	                </div>
	            </li>
			@endforeach
			</ul>
        </nav>
    </div>
</div>

@stop

@section('scripts')
    {!! Html::script('js/bootstrap-tour.js') !!}
    @if($user->level_id < 2)
		{!! Html::script('js/tour-basic.js') !!}
		{!! Html::script('js/tour-game.js') !!}
    @endif
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('.relation-done').click(function(){
    	location.href=base_url+'annotation-user/index/?'+$(this).attr("data-params");
    });
});
</script>	
@stop

