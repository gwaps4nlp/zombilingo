<?php
$app_name = Config::get('app.name');
if(!isset($challenges_repo))
  $challenges_repo = App::make('App\Repositories\ChallengeRepository');
$challenge=$challenges_repo->getOngoing();
if($challenge){
  $challenge_starts_at = new \Carbon\Carbon($challenge->start_date);
  $challenge_ends_at = new \Carbon\Carbon($challenge->end_date);
}
?>
<div id="navbar-top" class="bg-light fixed-top pt-0 pb-0">
  <nav class="container navbar navbar-expand-sm navbar-light">
    <a class="navbar-brand"  href="{!! url('') !!}">
    @if($app_name == 'zombiludik')
      {!! Html::image('img/logo_zlud.png','logo',array('id'=>'zombi-logo','style'=>'height:40px;')) !!}
    @else
      {!! Html::image('img/logo.png','logo',array('id'=>'zombi-logo','style'=>'height:36px;')) !!}
    @endif
    </a>
    <div id="">
      <ul class="navbar-nav">
        <li class="d-none d-md-block nav-item">
          <a class="nav-link rounded-btn {{ Request::is('/')?'active':'' }}" href="{!! asset('') !!}">Accueil</a>
        </li>
        <li class="d-none d-md-block nav-item dropdown {{ Request::is('game')?'active':'' }}">
          <a class="nav-link rounded-btn" href="#" style="margin-bottom:5px;">
            {{ trans('site.play') }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li>
              <a href="{!! url('game') !!}">Mode classique</a>
            </li>
            <li>
              <a href="{!! url('duel') !!}">Mode duel</a>
            </li>
          </ul>
        </li>
        @if(!Auth::check())
          <li class="nav-item {{ Request::is('game/demo')?'active':'' }}">
            <a class="nav-link rounded-btn" href="{!! route('demo') !!}">{{ trans('site.try') }}</a>
          </li>
        @else
          <li class="d-none d-md-block nav-item dropdown {{ Request::is('discussion')?'active':'' }}">
            <a class="nav-link rounded-btn" href="#">{{ trans('site.forum') }}</a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
              <li>
                <a href="{!! url('discussion') !!}">{{ trans('site.discussions') }}</a>
              </li>
              <li>
                <a href="{!! route('history') !!}">{{ trans('site.my-sentences') }}</a>
              </li>
            </ul>
          </li>
          @if(Auth::user()->isAdmin())
            <li class="d-none d-xl-block nav-item {{ Request::is('admin')?'active':'' }}">
              <a class="nav-link rounded-btn" href="{!! url('admin') !!}">{{ trans('site.admin') }}</a>
            </li>
          @endif
        <li class="d-none d-md-block nav-item {{ Request::is('faq')?'active':'' }}">
          <a class="nav-link rounded-btn" href="{!! url('faq') !!}">{{ trans('site.faq') }}</a>
        </li>
        @endif
      </ul>
    </div>
    @if($challenge)
      <div class="ml-auto">
        <div>
          <a class="nav-link" href="https://interstices.info/jcms/p_95618"><img style="width:30px;" src="{{ asset('img/logo_interstices.png') }}" /></a>
        </div>
      </div>
      <div class="d-md-block align-self-center mr-auto" id="block-counter">

          <input type="hidden" value="{{ $challenge->corpus->number_answers }}" id="number_annotations" />
        @if($challenge->type_score=="duel")
          <a href="{{ url('duel') }}?corpus_id={{ $challenge->corpus_id }}">
        @else
          <a href="{{ url('game') }}?corpus_id={{ $challenge->corpus_id }}" data-offset="0 0" data-toggle="tooltip" data-placement="bottom" title="{{ 'Challenge "'.$challenge->name .'" du '. $challenge_starts_at->format('d/m').' au '.$challenge_ends_at->format('d/m') }}">

        @endif
          <ul id="countdown-pad"></ul>
        </a>
        </div>

    @endif
  @if(Auth::check())
    <?php
    $user = Auth::user();
    $score_user = $user->score;
    if($user->next_level){
      $score_next_level = $user->next_level->required_score;
      $score_level = $user->level->required_score;
      $progress_score = 100*($score_user-$score_level)/($score_next_level-$score_level);
      $score = $user->score . " / " . $user->next_level->required_score;
      $score_todo = 100-$progress_score;
      $score_html = '<div class="progress" style="width:150px;margin-bottom:10px;"><div style="padding-left:5px;height:20px;line-height:20px;color:#888;position:absolute;font-size:0.9em;">'.$score.'</div><div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211F;width:'.$progress_score.'%"></div><div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:'.$score_todo.'%"></div></div><br/>';
    } else {
      $score_html = '';
    }

    $score_html = '<img src="'.asset('img/cerveau_plein.png').'" style="height:30px;" /><span class="score" style="color:#4a1710;">'.Html::formatScore($user->score).'</span>';
    $money_html = '<img src="'.asset('img/money.png').'" style="height:30px;" /><span class="money" style="color:#4a1710;">'.Html::formatScore($user->money).'</span>';
    ?>
  @endif
  <div class="topbar-right align-self-end {{ ($challenge)? '':'ml-auto' }}" style="font-size: 15px;">
    @if(Auth::check())
    <div class="topbar-username dropdown ">
      <div class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" data-dropdown-hover-all="true" aria-haspopup="true" aria-expanded="false">
        <a class="rounded-avatar" href="{!! url('home') !!}">
          <img src="{{ asset('img/level/thumbs/'.Auth::user()->level->image) }}" alt="{{ Auth::user()->username }}" />
        </a>
        <span class="username">{{ Auth::user()->username }}</span>
        <i class="fa fa-chevron-down pl-1 pr-2"></i>
      </div>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <li><a href="{!! url('user/home') !!}">Mon laboratoire</a></li>
        @if(Auth::user()->isAdmin())
          <li class="d-xl-none"><a href="{!! url('admin') !!}">Administration</a></li>
        @endif
        <li><a href="{!! url('user/home?enemies') !!}">Mes ennemis</a></li>
        <li><a href="{!! url('user/home?email') !!}">Réception des emails</a></li>
        <li><a href="{!! url('user/home?password') !!}">Mot de passe</a></li>
        <li class="d-lg-none"><a href="{!! url('user/players') !!}">Classement</a></li>
        <li class="d-lg-none"><a href="{!! url('shop') !!}">Boutique virtuelle</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="{!! route('logout') !!}">Fermer la session</a></li>
      </ul>
    </div>

    <div class="dropdown-money d-none d-lg-block float-left" style="cursor:pointer;padding-top:10px;padding-left:15px;">
      <a href="{!! url('user/players') !!}" data-offset="-10 0" data-toggle="tooltip" data-placement="bottom" title="Classement">
      {!! $score_html !!}
      </a>
    </div>
    <div class="dropdown-score d-none d-lg-block pr-2 float-left" style="cursor:pointer;padding-top:10px;padding-left:15px;">
      <a href="{!! url('shop') !!}" data-offset="-10 0" data-toggle="tooltip" data-placement="bottom" title="Boutique virtuelle">
      {!! $money_html !!}
      </a>
    </div>
    @endif
    <div id="information" class="d-none d-md-block" style="float:left;width:31px;height:41px;padding-top:3px;margin-right:20px;margin-left:10px;" data-offset="0 0" data-toggle="tooltip" data-placement="bottom" title="{{ trans('site.informations') }}">
      <a href="{!! route('informations') !!}" >
        <img src="{{ asset('img/infos.png') }}"  style="float:left;width:31px;height:41px;" />
      </a>
    </div>

    <div style="float:left;width:31px;height:41px;padding-top:3px;margin-right:20px;margin-left:10px;" class="d-md-none">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".collapse" aria-controls=".collapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

  </div>


  </nav>

  <div class="collapse">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link rounded-btn {{ Request::is('/')?'active':'' }}" href="{!! asset('') !!}">Accueil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link rounded-btn" href="{!! url('game') !!}" style="margin-bottom:5px;">
          {{ trans('site.play') }}
        </a>
      </li>
      <li class="nav-item {{ Request::is('duel')?'active':'' }}">
        <a class="nav-link rounded-btn" href="{!! url('duel') !!}" style="margin-bottom:5px;">
          {{ trans('site.play') }} - Mode duel
        </a>
      </li>
      @if(!Auth::check())
        <li class="nav-item {{ Request::is('game/demo')?'active':'' }}">
          <a class="nav-link rounded-btn" href="{!! route('demo') !!}">{{ trans('site.try') }}</a>
        </li>
      @else
      <li class="nav-item {{ Request::is('discussion')?'active':'' }}">
        <a class="nav-link rounded-btn" href="{!! url('discussion') !!}">{{ trans('site.forum') }}</a>
      </li>
        @if(Auth::user()->isAdmin())
        <li class="nav-item {{ Request::is('admin')?'active':'' }}">
          <a class="nav-link rounded-btn" href="{!! url('admin') !!}">{{ trans('site.admin') }}</a>
        </li>
        @endif
      @endif
      <li class="nav-item {{ Request::is('faq')?'active':'' }}">
        <a class="nav-link rounded-btn" href="{!! url('faq') !!}">{{ trans('site.faq') }}</a>
      </li>
      <li class="nav-item {{ Request::is('infos')?'active':'' }}">
        <a class="nav-link rounded-btn" href="{!! route('informations') !!}">{{ trans('site.informations') }}</a>
      </li>
    </ul>
  </div>

</div>
