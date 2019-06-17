@extends('front.template')

@section('main')

<div id="home">

		{!! Html::image('img/labo-opacity-reduce.jpg','Points',['style'=>'z-index:-1;position:absolute;top:0;left:0;width:100%;border-radius:1% 1%;']) !!}
		{!! Html::image('img/tuyaux-small.png','Tuyau',['style'=>'z-index:1;position:absolute;margin-top:41.4%;left:0%;width:12.9%;']) !!}
		{!! Html::image('img/porte-document.png','Points',['style'=>'z-index:1;position:absolute;margin-top:44%;left:18%;width:81%;']) !!}
		{!! Html::image('img/objet-friends.png','Amis',['style'=>'z-index:1;position:absolute;margin-top:10%;left:43%;width:16%;']) !!}
		{!! Html::image('img/leader-board-home.png','Leader board',['style'=>'z-index:0;position:absolute;margin-top:-2.5%;left:71%;width:27%;']) !!}
		<div class="label-friends" style="position: absolute;margin-top: 8.8%;left: 50%;">
			{{ trans('site.your-enemies') }}
		</div>
		<div class="label" style="position: absolute;margin-top: 42.2%;left: 22%;">
			News
		</div>
		<div class="label" style="position: absolute;margin-top: 45.2%;left: 42%;">
			{{ trans('site.account') }}
		</div>
		<div class="label" style="position: absolute;margin-top: 41.7%;left: 58%;">
			{{ trans('site.statistics') }}
		</div>
		<div id="news" onclick="$('#modalNews').modal();">
			@if(count($news))
	            {!! substr(strip_tags($news->first()->content),0,70).'...' !!}<br/>
	            <span class="link curvilinear">lire la suite...</span>
            @endif
		</div>
		<div id="scores">
			<div id="block-trophies">
				{!! Html::image('img/objet-medaille.png','Trophies',['id'=>'trophies']) !!}
				<span style="position: relative;left: 17%;">{{ count($user->trophies) }}</span>
				<div id="panel-trophies">
					<table>
					<tr><th style="text-align:center;">{{ trans('site.your-trophies') }}</th></tr>
					<tr>
						<td class="text" style="padding:10px;">
							@foreach ($trophy->getAll() as $key => $_trophy)
							    <span class="trophee">
							    @if($user->hasTrophy($_trophy))
							        {!! Html::image('img/trophee/'.$_trophy->image,$_trophy->name.' : '.$_trophy->description) !!}
							    @elseif($_trophy->is_secret)
						            {!! Html::image('img/trophee/secret.png','Trophée secret') !!}
							    @endif
							    </span>
							@endforeach
						</td>
					</tr>
					<!--<tr>
						<td class="img">
							{!! Html::image('img/objet-rigormortis.png','Rigor Mortis',['style'=>'width:100%;']) !!}
						</td>
						<td class="text">
							{{ trans('site.hidden-games') }}<br/>
				            <?php
				            if($user->number_mwes > 0){
				                echo '<span class="label">Rigor mortis : </span>' . $user->number_mwes . '<br />';
				            }
				            ?>
						</td>
					</tr>-->
					</table>
				</div>
			</div>
			<div>
				{!! Html::image('img/argent.png','Money',['id'=>'money']) !!}
				<span style="position: relative;left: -23%;">{{ Html::formatScore($user->money) }}</span>
			</div>
			<div>
				{!! Html::image('img/cerveau.png','Points',['id'=>'points']) !!}
				<span>{{ Html::formatScore($user->score) }}</span>
			</div>
			<div>
			{!! Html::image('img/objet-evolution.png','Niveau',['id'=>'level']) !!}
			</div>
		</div>
		{!! Html::image('img/level/zombie'.$user->level->id.'.png','ZombiLingo',array('style'=>'display:none;z-index:0;position:absolute;margin-top:18%;left:9%;width:11.5%;')) !!}
		<div id="zombie-level" style="position:absolute;margin-top:18%;left:7%;width:11.5%;">
			{!! Html::image('img/level/zombie'.$user->level->id.'.png','ZombiLingo',array('style'=>'z-index:0;margin:0;width:100%;')) !!}
		</div>
		<div id="friends" onclick="$('#modalFriends').modal();">
			<div id="img-level"></div>
			<span id="label-friend"></span>
		</div>
		<div id="number-friends">
			{{ count($user->getAcceptedFriends()) }}
	    	@if(count($user->getAskFriendRequests()))
				<span id="pending-enemies" style="color:red;">({{ count($user->getAskFriendRequests()) }})</span>
	    	@endif
		</div>
		<div id="leader-board">
            <div id="periode-board">
                @if($challenge)
                    <div id="challenge" class="periode-choice focus">{{ trans('home.challenge') }}</div>
                    <input type="hidden" id="periode" value="challenge" />
                @else
                    <div id="week" class="periode-choice focus">{{ trans('home.week') }}</div>
                    <input type="hidden" id="periode" value="week" />
                @endif
                <div id="month" class="periode-choice">{{ trans('home.month') }}</div>
                <div id="total" class="periode-choice">{{ trans('home.total') }}</div>
                @if($challenge && $challenge->type_score=="annotations")
                    <div id="toggleScore" class="score-choice annotations">annotations</div>
                    <input type="hidden" id="type_score" value="annotations" />
                @else
                    <div id="toggleScore" class="score-choice points">points</div>
                    <input type="hidden" id="type_score" value="points" />
                @endif

            </div>
            <div id="leaders-1-2">
            <?php
					foreach(array_keys($leaders) as $ranking_periode){
					?>
					@if(!$leaders[$ranking_periode]->contains('user_id',$user->id)&&$scores_user[$ranking_periode])
						<?php $in_leader[$ranking_periode] = false; ?>
					@else
						<?php $in_leader[$ranking_periode] = true; ?>
					@endif

					@if(!$leaders_annotations[$ranking_periode]->contains('user_id',$user->id)&&$scores_annotation_user[$ranking_periode])
						<?php $in_leader_annotations[$ranking_periode] = false; ?>
					@else
						<?php $in_leader_annotations[$ranking_periode] = true; ?>
					@endif

					<?php
					$rank = 1;
                    foreach ($leaders[$ranking_periode]->splice(0,6) as $ranking) {
	                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-points '.$ranking_periode.' '.(($ranking->user_id==$user->id)?'self':'').'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
						$rank++;
					}
					$rank_annotations = 1;
                    foreach ($leaders_annotations[$ranking_periode]->splice(0,6) as $ranking) {
	                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-annotations '.$ranking_periode.' '.(($ranking->user_id==$user->id)?'self':'').'">'.$rank_annotations . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
						$rank_annotations++;
					}
				}
            ?>
            </div>
            <div id="leaders-3-4-5">
            <?php

					foreach(array_keys($leaders) as $ranking_periode){
						$rank = 7;
						$rank_annotations = 7;
					?>
					@if(!$in_leader[$ranking_periode]&&$scores_user[$ranking_periode])
						@foreach($neighbors[$ranking_periode]['sup'] as $neighbor)
							<div user_id="{{ $neighbor->user_id }}" class="rank rank-points {{ $ranking_periode }}">{{ $neighbor->rank }} {{ $neighbor->username }}&nbsp;: {{ Html::formatScore($neighbor->score) }}</div>
						@endforeach
							<div user_id="{{ Auth::user()->id }}" class="rank rank-points {{ $ranking_periode }} self">{{ $scores_user[$ranking_periode]->rank }} {{ $scores_user[$ranking_periode]->username }}&nbsp;: {{ Html::formatScore($scores_user[$ranking_periode]->score) }}</div>
						@if($scores_user[$ranking_periode])
							@foreach($neighbors[$ranking_periode]['inf'] as $neighbor)
								<div user_id="{{ $neighbor->user_id }}" class="rank rank-points {{ $ranking_periode }}">{{ $neighbor->rank }} {{ $neighbor->username }}&nbsp;: {{ Html::formatScore($neighbor->score) }}</div>
							@endforeach
						@endif
					@else
                        <?php
                        foreach ($leaders[$ranking_periode]->splice(0,5) as $ranking) {
		                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-points '.$ranking_periode.' '.(($ranking->user_id==$user->id)?'self':'').'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
							$rank++;
						}
						?>
					@endif
					@if(!$in_leader_annotations[$ranking_periode]&&$scores_annotation_user[$ranking_periode])
						@foreach($neighbors_annotations[$ranking_periode]['sup'] as $neighbor)
							<div user_id="{{ $neighbor->user_id }}" class="rank rank-annotations {{ $ranking_periode }}">{{ $neighbor->rank }} {{ $neighbor->username }}&nbsp;: {{ Html::formatScore($neighbor->score) }}</div>
						@endforeach
							<div user_id="{{ Auth::user()->id }}" class="rank rank-annotations {{ $ranking_periode }} self">{{ $scores_annotation_user[$ranking_periode]->rank }} {{ $scores_annotation_user[$ranking_periode]->username }}&nbsp;: {{ Html::formatScore($scores_annotation_user[$ranking_periode]->score) }}</div>
						@if($scores_annotation_user[$ranking_periode])
							@foreach($neighbors_annotations[$ranking_periode]['inf'] as $neighbor)
								<div user_id="{{ $neighbor->user_id }}" class="rank rank-annotations {{ $ranking_periode }}">{{ $neighbor->rank }} {{ $neighbor->username }}&nbsp;: {{ Html::formatScore($neighbor->score) }}</div>
							@endforeach
						@endif
					@else
                        <?php
                        foreach ($leaders_annotations[$ranking_periode]->splice(0,5) as $ranking) {
		                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-annotations '.$ranking_periode.' '.(($ranking->user_id==$user->id)?'self':'').'">'.$rank_annotations . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
							$rank_annotations++;
						}
						?>
					@endif

					<?php
				}
            ?>
            </div>
        </div>
        <div id="stats">
            {{ trans('game.parties-won') }} : <span>{{ $user->won }}</span><br />
            {{ trans('game.perfect-parties') }} : <span>{{ $user->perfect }}</span><br />
            {{ trans('game.number-objects-found') }} : <span>{{ $user->number_objects }}</span><br />
		</div>
		<div class="modal fade" id="modalNews" role="dialog">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>News</h2>
						<div  style="text-align:left;">
						@foreach($news as $new)
							{{ substr($new->created_at,0,10) }} : {!! $new->content !!}<br/>
						@endforeach
						</div>
			            <div class="modal-footer">
	  						<button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('site.close') }}</button>
						</div>
			        </div>
			    </div>
		    </div>
		</div>
		<div id="account">
            <span class="link" id="changePassword">Modifier mon mot de passe.</span><br />
            <span class="link" id="changeEmail">Envoi des emails.</span><br />
            <span class="link" onclick="$('#modalDeleteAccount').modal();">Supprimer mon compte.</span><br />
		</div>
		<div id="panel-duel">
			<table>
			<tr>
				<td class="img" style="text-align:center;">
					<span style="font-size:2vw;">VS.</span>
				</td>
				<td class="text">
					Mes duels<br/>
					<div style="font-family:'Times';text-transform:initial;">
					{{ trans('game.won-duels') }} : {{ $duels->countWon($user) }}<br/>
					{{ trans('game.lost-duels') }} : {{ $duels->countLost($user) }}<br/>
					{{ trans('game.draws') }} : {{ $duels->countDraw($user) }}<br/>
					</div>
				</td>
			</tr>
			</table>
		</div>
		<div class="modal fade" id="modalChangePassword" role="dialog">
		    <div class="modal-dialog">
		    {!! Form::open(['url' => 'password/change', 'method' => 'post', 'role' => 'form', 'id'=>'form-change-password']) !!}
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Modification du mot de passe</h2>
						   <span id="error-change-password" class="error"></span>

				            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
				              <label for="password">{{ trans('site.new-password') }}</label>
				              <input type="password" class="form-control" name="password" id="password" placeholder="{{ trans('site.placeholder-enter-password') }}">
				              {{ $errors->first('password', '<small class="help-block">:message</small>') }}
				            </div>
				            <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
				              <label for="password_confirmation">{{ trans('site.confirm-password') }}</label>
				              <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="{{ trans('site.placeholder-confirm-password') }}">
				              {{ $errors->first('password_confirmation', '<small class="help-block">:message</small>') }}
				            </div>

			            <div class="modal-footer">
				            <button type="submit" class="btn btn-success">
				            	{{ trans('site.button-validate') }}
				            </button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">
				            	{{ trans('site.cancel') }}
				            </button>
						</div>
			        </div>
			    </div>
			{!! Form::close() !!}
		    </div>
		</div>

		<div class="modal fade" id="modalChangeEmail" role="dialog">
		    <div class="modal-dialog">
		    {!! Form::open(['url' => 'user/change-email', 'method' => 'post', 'role' => 'form', 'id'=>'form-change-email']) !!}
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Envoi des emails</h2>
						   <span id="error-change-email" class="error"></span>

				            <div class="form-group {{ $errors->has('email_frequency_id') ? 'has-error' : '' }}">
				              <label for="frequency">{{ trans('site.email-frequency') }}</label>
				              <div style="text-align:left;">
					              @foreach($email_frequency as $frequency)
					              	@if($frequency->id == $user->email_frequency_id)
					              		<input type="radio" name="email_frequency_id" value="{{ $frequency->id }}" checked="checked"/> {{ trans('site.'.$frequency->slug) }}<br/>
					              	@else
										<input type="radio" name="email_frequency_id" value="{{ $frequency->id }}" /> {{ trans('site.'.$frequency->slug) }}<br/>
					              	@endif
					              @endforeach
				              </div>
				              {!! $errors->first('email_frequency_id', '<small class="help-block">:message</small>') !!}
				            </div>
				            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
				              <label for="email">{{ trans('site.email') }}</label>
				              <input type="text" class="form-control" name="email" id="email" value="{{ $user->email }}">
				              {{ $errors->first('email', '<small class="help-block">:message</small>') }}
				            </div>

			            <div class="modal-footer">
				            <button type="submit" class="btn btn-success">{{ trans('site.button-validate') }}</button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
						</div>
			        </div>
			    </div>
			{!! Form::close() !!}
		    </div>
		</div>
		<div class="modal fade" id="modalDeleteAccount" role="dialog">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>Suppression du compte</h2>
				          {!! Form::open(['url' => 'user/delete', 'method' => 'get', 'role' => 'form', 'id'=>'form-delete']) !!}
				            <div class="form-group">
				            	{{ trans('site.confirm-delete-account') }}
				            </div>
				            <button type="submit" class="btn btn-success">{{ trans('site.button-validate') }}</button>
				            <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
				          {!! Form::close() !!}
			            <div class="modal-footer">

						</div>
			        </div>
			    </div>
		    </div>
		</div>

		@if(session()->has('message'))
			<div class="alert" role="alert" style="position: absolute;">
				{{ session('message') }}
			</div>
		@endif
		<div class="row">
			<div class="col-sm-2 col-md-2">

			</div>
			<div class="col-sm-2 col-md-2">

			</div>
			<div class="col-sm-2 col-md-2">

			</div>
			<div class="col-sm-2 col-md-2">

				<div class="modal fade" id="modalFriends" role="dialog">
				    <div class="modal-dialog">
				    <div class="modal-content">
				        <div class="modal-body">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
						<h2>{{trans('site.your-enemies')}}</h2>
							@if(count($user->getAcceptedFriends()))
								<div id="amis" present="1">

								@foreach ($user->getAcceptedFriends() as $friend)
									{!! link_to('user/'.$friend->friend->id, $friend->friend->username) !!}&nbsp;
									{{ Html::formatScore($friend->friend->score) }}&nbsp;
									{!! Html::image('img/cerveau_plein.png','Nombre de points') !!}<br />
								@endforeach
								</div>
							@else
								<p>{{trans('site.no-friend')}}</p>
							@endif

								<div id="amis" present="0">
									<p>{!! link_to('user/players', trans('site.find-friend'),['class'=>'btn btn-success','style'=>'font-size:20px;']) !!}</p>
								</div>

							@foreach ($user->getPendingFriendRequests() as $key=>$request)
								@if($key==0)
									<h2 id="soi">{{ trans('site.your-enemies-request') }}</h2>
								@endif
							<div class="demande" user_id="{{ $request->friend_id }}">
								{!! link_to('user/'.$request->friend->id, $request->friend->username) !!}&nbsp;
								<a href="#annuler" class="annuler" url="{{ url('user/cancel-friend/'.$request->friend->id) }}">{{ trans('site.cancel') }}</a>&nbsp;
							</div>
							@endforeach
							<div id="resultAmi"></div>
			                @if(count($user->getAskFriendRequests()))
			                    @foreach ($user->getAskFriendRequests() as $key => $ask_friend)
			                        <div class="demande" user_id="{{ $ask_friend->user->id }}">
			                        Demande en ennemi de
			                        {!! link_to('user/'.$ask_friend->user->id, $ask_friend->user->username) !!}&nbsp;
			                        <a href="#autres" class="accepter btn btn-success" url="{{ url('user/accept-friend/'.$ask_friend->user->id) }}">Accepter</a>
			                        <a href="#autres" class="annuler btn btn-success" url="{{ url('user/cancel-friend/'.$ask_friend->user->id) }}">Refuser</a>
			                        </div>
			                    @endforeach
			                @endif
				            <div class="modal-footer">
          						<button type="button" class="btn btn-danger" data-dismiss="modal">
          							{{ trans('site.close') }}
          						</button>
        					</div>
				        </div>
				    </div>

				    </div>
				</div>
			</div>
            <div class="col-sm-3 col-md-3">


            </div>

		</div>

</div>

@stop
@section('scripts')
<script>
    var index=0;
    var friends;

    function init() {

    	friends = {!! $user->getAcceptedFriends()->toJson() !!};
    	if(friends.length>0)
        	animate();
        @if(count($user->getAskFriendRequests()))
        setInterval('animatePendingEnemies()',500);
        @endif
        @if(isset($_GET['enemies']))
        	$('#modalFriends').modal('show');
        @elseif(isset($_GET['email']))
        	$('#modalChangeEmail').modal('show');
        @elseif(isset($_GET['password']))
        	$('#modalChangePassword').modal('show');
        @endif
    }

    function animatePendingEnemies(){
    	$("#number-friends").fadeOut(900).delay(300).fadeIn(800);
    }
    function animate(){
    	var friend = friends[index];
    	$("#img-level").css( { "opacity": "0" });
    	$("#img-level").html('<img src="'+base_url+'img/level/'+friend.friend.level.image+'" class="level-friend" style="opacity:0.9"/>');
    	$("#img-level").animate( { "opacity": "0.8" }, 2000 );
    	$("#img-level").css( { "display": "block" });
    	$("#label-friend").text(friend.friend.username);
    	$("#label-friend").css({ "display":"inline", "left": "-95%", "color":"#2b3233" });

        $("#label-friend").animate({ "left": "95%" }, 4000, "linear",function(){
	        $("#label-friend").css({ "transform": "scaleX(-1)", "color" : "#8ea9ac" });
	        $("#label-friend").animate({ "left": "-95%" }, 4000,"linear",function(){
	        	$("#img-level").animate( { "opacity": "0" }, 1000 );
	        	$("#label-friend").css({ "display":"none", "transform": "scaleX(1)", "color" : "#2b3233" });
	        	setTimeout(function(){ index++;next(); }, 1000);
	        });
        });

    }
    function next(){
        if(index>=friends.length) index=0;
        animate();
    }

    window.onload = init();
</script>
@stop

@section('css')
<style>
@if($challenge)
.week, .month, .total {
    display: none;
}
    @if($challenge->type_score=="annotations")
        .rank-points {
            display: none;
        }
    @else
        .rank-annotations {
            display: none;
        }
    @endif
@else
.challenge, .month, .total, .rank-annotations {
    display: none;
}
@endif
#header_new {
	z-index:1;
	position:relative;
}
@media (min-width: 1200px) {
	#home.col-md-10{
		width:85.333333%;
		left:-1%;
	}
}
#home, #home .link{
	border-radius : 1% 1%;
    position:relative;
	color:#3c1715;
}
#scores{
	position: absolute;
	margin-top: 18%;
	margin-left: 15%;
	font-family: "Charlemagne Std Bold";
}
.col-lg-10{
	padding-left:0;
	padding-right:0;
}
.herbe {
	display:none;
}
img#trophies{
	width: 2.5vw;
	position: relative;
	left: 0vw;
}
img#money{
	width: 6vw;
	position: relative;
	left: -1.7vw;
}
img#points{
	width: 4vw;
}
img#level{
	width: 4vw;
}
img.level {
    width: 70%;
    position: absolute;
    margin-top: 107%;
    left: -39%;
}
#leader-board {
	width: 101%;
	padding-top: 10%;
	font-family: "Charlemagne Std Bold";
	position: absolute;
	margin-top: -17%;
	margin-left: 18%;
	color:white;
}
#number-friends {
    width: 70%;
    font-family: "Charlemagne Std Bold";
    position: absolute;
    margin-top: 64%;
    margin-left: 0%;
    color: #FFF;
    z-index: 2;
    text-align: center;
    vertical-align: middle;
    padding: 6% 1%;
    cursor:pointer;
}
#friends {
	font-family: "Charlemagne Std Bold";
	position: absolute;
	margin-top: 87%;
	margin-left: -36%;
	color: #FFF;
	z-index: 2;
	text-align: center;
	vertical-align: middle;
	cursor: pointer;
	width: 41%;
	line-height:0.9vw;
	overflow:hidden;
}
img.level-friend {
    width: 66%;
    padding-bottom:10%;
}
#label-friend {
	width: 80%;
	color: #000;
	font-size: 0.7vw;
	left: 0px;
	position: absolute;
	top: 85%;
}
#leader-board img{
    width:100%;
    position:absolute;
    left:0;
}
#periode-board{
	position: absolute;
	margin-top: 39%;
	padding-left:59%;
	color: #FFF;
	font-size: 0.9vw;
	font-family: "anothershabby";
	right:30%;
	text-align: right;
	width:100%;
	z-index:2;
}
#periode-board .focus{
	font-size: 1.4vw;
	text-align: left;
	position: absolute;
	left: 48%;
	top: 34%;
}
#toggleScore{
	text-align: left;
	position: absolute;
	left:48%;
	cursor:pointer;
}
.periode-choice{
	text-transform: uppercase;
	cursor:pointer;
}
#my-position{
	text-transform: uppercase;
	cursor:pointer;
	font-size: 0.5vw;
}
.periode-choice:hover{
	text-shadow:0px 0px 5px #a8a8a8;
}
.rank_neighbor{
	display:none;
}
.rank.self, .rank_neighbor.self{
	color:#fffd6c;
}
#leaders-1-2{
    font-size:0.8vw;
    position:absolute;
    margin-top: 73%;
    text-align: left;
    padding-left:26%;
}
#leaders-3-4-5{
    font-size:0.8vw;
    margin-top: 127%;
    position:absolute;
    text-align: left;
    padding-left:14%;
}
#panel-trophies{
	background-image: url("../img/white-background.png");
	position: absolute;
	width: 320px;
	border-radius: 12px;
	left: 79.5%;
	margin-top: -76%;
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
	z-index:1;
}
#panel-trophies td.img{
	width:29%;
}
#panel-trophies td.text{
	vertical-align:middle;
	padding-left:4%;
}
#panel-duel{
	background-image: url("../img/white-background.png");
	position: absolute;
	width: 18%;
	border-radius: 12px;
	left: 20.5%;
	margin-top: 21%;
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
}
#panel-duel td.img{
	width:29%;
}
#panel-duel td.text{
	vertical-align:middle;
	padding-left:4%;
}
#block-trophies {
	cursor:pointer;
}
#block-trophies > #panel-trophies {
	display:none;
}
#block-trophies:hover > #panel-trophies {
	display:block;
}
div.label {
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.7vw;
	text-transform: uppercase;
	color:#261012;
}
div.label-friends {
	font-family: "Charlemagne Std Bold";
	font-weight: bold;
	font-size: 1.1vw;
	text-transform: uppercase;
	color:#261012;
}
#stats {
	position: absolute;
	margin-top: 51.3%;
	left: 61.5%;
	z-index: 2;
	font-family: "anothershabby";
	font-weight: normal;
	width: 15%;
	line-height: 1.5em;
	font-size: 1.2vw;
}
#news {
    position: absolute;
	margin-top: 51.2%;
	left: 21%;
    z-index: 2;
    font-family: "";
    font-weight: normal;
    font-size: 1.3vw;
    width: 18%;
    font-style: italic;
    line-height:1.2em;
}
#account {
	position: absolute;
	margin-top: 54%;
	left: 45.5%;
	z-index: 2;
	font-weight: normal;
	width: 12%;
	font-family: "anothershabby";
	font-size: 1.2vw;
	line-height: 1.2em;
}
#panel-trophies span.label{
	font-family: "anothershabby";
	text-transform: none;
	color:#261012;
	font-weight:normal;
	font-size:1vw;
}
#panel-duel span.label{
	font-family: "anothershabby";
	text-transform: none;
	color:#261012;
	font-weight:normal;
	font-size:1vw;
}
#stats span{
	font-family: "Charlemagne Std Bold";
	font-weight: normal;
	font-size:1.3vw;
}
.curvilinear {
	font-family: "anothershabby";
}
label {
	color : white;
}
</style>
@stop

