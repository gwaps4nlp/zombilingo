<nav class="row">
	<a href="{!! route('game') !!}" class="connected">
		<div class="col-md-1 col-md-offset-1 colored text-center">
			<?php
			echo Html::image('img/onglet1.png',trans('site.play'),array('class'=>'onglet'));
			if(!isset($herbe) || $herbe){
				echo Html::image('img/herbe2.png','herbe',array('id'=>'herbe2','class'=>'herbe'));
			}
			?>

			<span>
				{{ trans('site.play') }}
			</span>
		</div>
	</a>
@if(!Auth::check())
	<a href="{!! route('demo') !!}">
		<div class="col-md-1 colored text-center">
			<?php echo Html::image('img/onglet2.png',trans('site.try'),array('class'=>'onglet')); ?>
			<span>
				{{ trans('site.try') }}
			</span>
		</div>
	</a>
	<div class="col-md-1 colored text-center">
		<?php echo Html::image('img/herbe1.png','herbe',array('id'=>'herbe1','class'=>'herbe'));?>
	</div>
@else
	<a href="{!! route('shop') !!}">
		<div class="col-md-1 colored text-center">
			<?php
			echo Html::image('img/onglet2.png',trans('site.shop'),array('class'=>'onglet'));
			?>	
			<span>
				{{ trans('site.shop') }}
			</span>
		</div>
	</a>
	<a href="{!! url('duel') !!}">
		<div class="col-md-1 colored text-center">
			<?php
			$duels = App::make('App\Repositories\DuelRepository');
			$count_duels_not_completed = $duels->countNotCompleted(Auth::user());
			?>
			@if($count_duels_not_completed)
				<div id="count_pending_duel" style="display:none;">{{ $count_duels_not_completed }}</div>		
				{!! Html::image('img/onglet-pancarte.png',trans('site.duels'),array('id'=>'onglet-duel','class'=>'onglet')) !!}
			@else
				{!! Html::image('img/onglet1.png',trans('site.duels'),array('class'=>'onglet')) !!}
			@endif
			<?php
				$duel =true;
			?>

			<?php
			if(!isset($herbe) || $herbe){
				echo Html::image('img/herbe1.png','herbe',array('id'=>'herbe1','class'=>'herbe'));
				echo Html::image('img/herbe3.png','herbe',array('id'=>'herbe3','class'=>'herbe'));
			}?>					
			<span>
				{{ trans('site.duels') }}
			</span>
		</div>
	</a>

	@if(App::environment('local'))
    <a href="{!! route('pos-game') !!}" class="connected">
		<div class="col-md-1 colored text-center">
			<?php
			echo Html::image('img/onglet1.png',trans('site.playpos'),array('class'=>'onglet'));
			?>

			<span>
				{{ trans('site.playpos') }}
			</span>
		</div>
	</a>
	@endif
	<a target="_blank" href="https://gforge.inria.fr/forum/forum.php?forum_id=11387&group_id=3411">
		<div class="col-md-1 colored text-center">
			<?php echo Html::image('img/onglet3.png',trans('site.forum'),array('class'=>'onglet')); ?>
			<?php
			if(!isset($herbe) || $herbe){
				echo Html::image('img/herbe1.png','herbe',array('id'=>'herbe1','class'=>'herbe'));
				echo Html::image('img/herbe3.png','herbe',array('id'=>'herbe3','class'=>'herbe'));
			}?>
			<span>
				{{ trans('site.forum') }}
			</span>
		</div>
	</a>
	@if(Auth::user()->isAdmin())
	<a href="{!! url('admin') !!}">
		<div class="col-md-1 colored text-center">
		{!! Html::image('img/onglet1.png',trans('site.admin'),array('class'=>'onglet')) !!}
			<span>
				{{ trans('site.admin') }}
			</span>
		</div>
	</a>		
	@endif
	<a href="{!! url('user/players') !!}">
	<?php 
	$offset = Auth::user()->isAdmin()? 3:4;
	$offset += App::environment('local')?0:1;
	?>
		<div class="col-md-1 col-md-offset-{{ $offset }} colored text-center">
			<?php echo Html::image('img/onglet4.png',trans('site.players'),array('class'=>'onglet'));
			if(!isset($herbe) || $herbe){
				echo Html::image('img/herbe1.png','herbe',array('id'=>'herbe4','class'=>'herbe'));
				echo Html::image('img/herbe3.png','herbe',array('id'=>'herbe5','class'=>'herbe'));
			}
			?>
			<span>
				{{ trans('site.players') }}
			</span>
		</div>
	</a>
@endif
</nav>