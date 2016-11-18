<?php
if(!isset($challenges_repo))
	$challenges_repo = App::make('App\Repositories\ChallengeRepository');
$challenge=$challenges_repo->getOngoing();
if($challenge){
    $challenge_starts_at = new \Carbon\Carbon($challenge->start_date);
    $challenge_ends_at = new \Carbon\Carbon($challenge->end_date);
}
?>

<header class="row">
	<div class="col-md-8 col-md-offset-1" id="blocLogo" title="{{ trans('site.home') }}">
		<a href="{!! url('') !!}">
		{!! Html::image('img/zombieLogo.png','ZombiLingo',array('id'=>'zombieLogo')) !!}
		{!! Html::image('img/logo.png','logo',array('id'=>'logo')) !!}
		</a>
	</div>
	@if(session('statut') == 'guest')
		<div class="col-md-1 col-md-offset-1" id="blocInformation">
	@else
		<div class="col-md-1" id="blocInformation">
	@endif
		<a href="{!! route('informations') !!}" style="float:right">
			<div id="information" title="{{ trans('site.informations') }}"> </div>
		</a>
	</div>
@if(Auth::check())
	<div class="col-md-1" id="blocDeconnection">
		<a href="{!! url('auth/logout') !!}" id="link_logout">
			{!! Html::image('img/deco.png',trans('site.quit'),array('style'=>'height:87px;width:105px;')) !!}
		</a>
		<a href="{!! url('user/home') !!}">
			<span id='username-info'>
			{{ Auth::user()->username}}
			</span>
		</a>
	</div>
@endif
@if($challenge)
	@if($challenge->type_score=="duel")
		<a href="{{ url('duel') }}?corpus_id={{ $challenge->corpus_id }}">
	@else
		<a href="{{ url('game') }}?corpus_id={{ $challenge->corpus_id }}">
	@endif

<span style="line-height:0.9em;position:absolute;top:0%;right:24%;width:15%;text-align:center;color:#4A1710;cursor:pointer;" id="blocChallenge">
<div id="tipChallenge" style="line-height:1.42857">
    {!! $challenge->description !!}
</div>
<span style="margin-top:58%;font-size:0.7vw;" onblur="$('tipChallenge').show();">
	{!! Html::image($challenge->image,'Challenge Foot',array('id'=>'logo-challenge', 'style'=>'height:200px;')) !!}<br/>
	<span style="position:relative;left:25px;line-height:1em;">Challenge "{{ $challenge->name }}"<br/>du {{ $challenge_starts_at->format('d/m') }} au {{ $challenge_ends_at->format('d/m') }}.</span><br/>

</span>
</span>
</a>
@endif
<style>
#blocChallenge > #tipChallenge {
	position:absolute;
	display:none;
	width:400px;
	left:-375px;
	top:25px;
	padding: 10px;
	border: 2px solid #CCC;
	background-color: #9BC5AA;
	border-radius: 10px;
	color: #75211F;
	z-index:999;
}
#blocChallenge:hover > #tipChallenge {
	display:block;
}
</style>
</header>