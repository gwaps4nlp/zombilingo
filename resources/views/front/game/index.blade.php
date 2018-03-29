@extends('front.template-game')

@section('main')

<div id="index-game" class="px-0">

    <div id="block-game">
		@if (session()->has('error'))
			@include('partials/error', ['type' => 'danger', 'message' => session('error')])
		@endif	
		@if(session()->has('message'))
			@include('partials/error', ['type' => 'success', 'message' => session('message')])
		@endif	
		@if(isset($info))
			@include('partials/error', ['type' => 'info', 'message' => $info])
		@endif

		<div class="row pt-3 pl-4">

	        <div id="relation-done-small" class="col-10">

				<ul class="row nav">
				@foreach($relations as $relation)
					<?php
					$special = $relation->type=='special'&& $relation->tutorial>0 ;
					if($relation->todo>0 || $special)
						continue;
					?>

					<li class="col-2 col-md-1 nav-relation-done">

						{!! Html::image('img/tombstone-grey.png','',['style'=> 'width:105%;']) !!}
			            <div id="{{ $relation->id }}" class="relation-done" data-params="corpus_id={{ $game->corpus_id }}&relation_id={{ $relation->id }}" style="display:flex;flex-direction:column;">	
			                <div style="flex:1;" class="relation-name-done-small {!! (strlen(str_replace(['≪','≫'],['',''],html_entity_decode($relation->name)))<25)?'short':'' !!}">
			                	<span class="relation-name">{{ $relation->name }}</span>
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

		    <div class="aideTool col-2">
		        <div id="savant" class="savant help">
		            <div class="aideTip" id="helpRelation">
				        {{ trans('game.explication-1')}}<br/>
				        {{ trans('game.explication-2')}}<br/>
				        {{ trans('game.explication-3')}}<br/>
		            </div>
		        </div>
		    </div>			
		</div>
		<div style="clear: both;"></div>
		<br/>  


         <ul class="row nav">
            <li class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-3  nav-corpus nav-item align-items-start">
            {!! Html::image('img/tombstone-user-grey.png','',['style'=> 'width:100%;z-index:-1;padding: 0%;']) !!}
				<div class="container-nav-corpus">
					<div class="level-relation">&nbsp;</div>
					<div class="label-choice-corpus" style="margin-top:15px;">
						<label for="corpus_id" class="control-label">{{ trans('game.choice-corpus') }}</label>
					</div>
					<div class="choice-corpus mx-auto">	
						{!! Form::open(['url' => '/game', 'method' => 'get', 'role' => 'form','id'=>'corpusChoice','style'=>'width:100%;']) !!}
						@if((Auth::user()->isAdmin() || Auth::user()->level_id>=2))	
							{!! Form::control('selection', 0, 'corpus_id', $errors, null,$corpora,null,trans('site.choose-a-corpus'),$game->corpus_id) !!}
						@else
							<label class="control-label level-not-available">{{ trans('site.available-at-level-2') }}</label>

						@endif
						{!! Form::close() !!}
					</div>
				<div class="scores">&nbsp;</div>	
				</div>
			</li>

			@foreach($relations as $relation)
				<?php
				$special = $relation->type=='special' && $relation->tutorial>0 ;
				if(!$relation->tutorial && $relation->type=='special') continue;
				if($relation->todo<=0 && !$special) continue;
				?>
				
	            <li class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-3 nav-relation nav-item level-{{ $relation->level_id }}">
	            {!! Html::image('img/tombstone-grey.png','',['style'=> 'width:100%;z-index:-1;padding: 0;']) !!}
	            	<div class="container-nav-relation">

	            	<?php

					
					$class_relation = "detail-relation";
					if($relation->level_id>Auth::user()->level_id)
						$class_relation .= " disabled-relation";
					elseif($relation->todo<=0 && !$special)
						$class_relation .= " relation-done";
					?>
					<div class="level-relation">
						<span data-toggle="tooltip" data-placement="top" title="Points x{{ $relation->level_id }} !" >{{ trans('game.level') }} {{ $relation->level_id }}</span>
					</div>
		             <div class="relation-name {!! (strlen(str_replace(['≪','≫'],['',''],html_entity_decode($relation->name)))<25)?'short':'' !!}">
		             	<span class="relation-name">{{ $relation->name }}</span>
		             </div>					
	                <div id="{{ $relation->id }}" class="{{ $class_relation }}">
						<div class="links-level">
						@if($relation->tutorial && $relation->todo>0)
							<span>{!! link_to('',trans('game.game'),array('class'=>'btn btn-success link-level',
										'title' => trans('game.do-a-party-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'game',
										'id'=>'game_'.$relation->id,
										'id_phenomene' => $relation->id)) !!}</span>
						@else
							<span>{!! link_to('',trans('game.game'),array('class'=>'btn btn-success link-level link-level-disabled',
										'title' => trans('game.do-a-party-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'game',
										'id'=>'game_'.$relation->id,
										'id_phenomene' => $relation->id)) !!}</span>					
						@endif
						<br/>
						@if($relation->type!='special')
							<span>{!! link_to('',trans('game.training'),array('class'=>'btn btn-success link-level',
										'title' => trans('game.do-the-training-with')." : " . $relation->name,
										'style' => "margin-right: 5px; margin-left: 5px;",
										'action'=>'training',
										'id'=>'training_'.$relation->id,
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
    </div>
</div>
<style type="text/css">

.aideTool  > .savant {
  position: relative;
  outline: none;
}

.aideTool > .help > .aideTip {
  /*visibility: hidden;*/
  display:none;
  position: absolute;
  bottom: 0px;
  left: -800px;
  z-index: 999;
  width: 800px;
  padding: 10px;
  border: 2px solid #ccc;
  background-color: #9bc5aa;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
  border-radius: 10px;
  color: #75211f;
}

.aideTool:hover > .help > .aideTip {
  display:block;
  visibility: visible;
}

.nav-relation-done {
	text-align: center;
}
.links-level a.link-level-disabled, #index-game .links-level a.link-level-disabled:hover {
    color: #3b3d3e;
    text-shadow: 0px -1px #b8b8b8;
    background-color: #4e5152;
    border: solid 0px #181818;
    box-shadow: -1px -1px 0px #181818;
}
</style>
@stop

@section('scripts')
    {!! Html::script('js/bootstrap-tour.js') !!}
    @if($user->level_id < 2)
		{!! Html::script('js/tour-basic.js') !!}
		{!! Html::script('js/tour-game.js') !!}
    @endif
@stop

