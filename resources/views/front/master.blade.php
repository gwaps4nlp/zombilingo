<?php
use Gwaps4nlp\Models\ConstantGame;
use App\Services\Html\ModalBuilder as Modal;
$challenges_repo = App::make('App\Repositories\ChallengeRepository');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
        <meta name="description" content="@yield('description')" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - Zombilingo</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        {!! Html::style(mix("build/css/app.css")) !!}

        @yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}">

        @include('front.navbar')

        @yield('container')

        <div id="containerModal"></div>
        <script>
            @include('js.data-js')
        </script>
        {{-- <script src="{{ asset('js/socket.io.js') }}"></script> --}}
        <script src="{{ asset(mix("build/js/all.js")) }}"></script>

        @yield('scripts')

        <input type="hidden" id="connected" value="{{ (Auth::check())?Auth::user()->id:'0' }}" />
        <?php
        $new_log = Session::has('inputs.new_log');
        if (Auth::check()) {
            $questuser= App::make('App\Repositories\QuestUserRepository');
            $trophyuser=App::make('Gwaps4nlp\Core\Repositories\TrophyUserRepository');
            $test=$trophyuser->trophyCreated(Auth::user());
            $questuser_not_created=$questuser->notCreated(Auth::user());
            if ($questuser_not_created){
                $questuser->giveQuest(Auth::user());
            }
        }
        if($new_log){
            $modal = App::make('App\Services\Html\ModalBuilder');
            $duels = App::make('App\Repositories\DuelRepository');
            $news = App::make('Gwaps4nlp\NewsManager\Repositories\NewsRepository');

            $count_duel_completed = $duels->countCompletedNotSeen(Auth::user());
            $count_duel_in_progress_not_seen = $duels->countInProgressNotSeen(Auth::user());
            $count_duel_in_progress = $duels->countInProgress(Auth::user());
            $count_news_not_seen = $news->countNotSeen(Auth::user());

            $count_duel_available = $duels->countPendingAvailable(Auth::user());
            $news_not_seen = $news->getNotSeen(Auth::user());

            $friend_requests = Auth::user()->getAskFriendRequests();
            $open_modal = $count_duel_in_progress_not_seen||$count_duel_completed||$count_news_not_seen||count($friend_requests)||$count_duel_available||$questuser_not_created;

            if($open_modal){
                $html = '';
                $html .= '<h2>Salut '.Auth::user()->username.' !</h2>';
                if ($questuser_not_created){
                    $progress = (100*$questuser->getQuestScore($user))/$questuser->getRequiredValue($user);
                    $progress2 = 100-$progress;
                    $description = $questuser->getQuestDescription($user);
                    $key = $questuser->returnKey($user);
                    $html .= '<div class="description"><br/>'.$description.' '.$key.'
    <br/>   
</div>
<div class="progress">   
    <div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
        '.$progress.'%
    </div>
    <div class="progress-bar progress-bar-danger" id="progress-bars" role="progressbar" style="color:#000;background-color:#fff;width:'.$progress2.'%">
  </div>
</div>';

                if($new_log) {
                    }
                    if($count_news_not_seen){
                        $html .= '<h3>Il y a du nouveau sur ZombiLingo</h3>';
                        $html .= '<div style="text-align:left;">';
                        foreach($news_not_seen as $new_not_seen){
                            $html .= $new_not_seen->content.'<hr/>';
                            $new_not_seen->users()->updateExistingPivot(Auth::user()->id,['seen'=>1]);
                        }
                        $html .= '</div>';
                    }
                    $html.='<div style="text-align:center;">';
                    if(count($friend_requests)){
                        if(count($friend_requests)==1)
                            $html .= '<h3>Tu as reçu une nouvelle demande en ennemi :</h3>';
                        else
                            $html .= '<h3>Tu as reçu des nouvelles demandes en ennemi :</h3>';
                        $html .= '<div id="resultAmi"></div>';
                        $html .= '<div style="text-align:center;">';
                        foreach($friend_requests as $friend_request){
                         $html .= '<div class="demande" user_id="'.$friend_request->user->id.'">
                                de '.
                                link_to('user/'.$friend_request->user->id, $friend_request->user->username).'&nbsp;
                                <a href="#autres" class="accepter btn btn-success" url="'.url('user/accept-friend/'.$friend_request->user->id).'">Accepter</a>&nbsp;
                                <a href="#autres" class="annuler btn btn-success" url="'.url('user/cancel-friend/'.$friend_request->user->id).'">Refuser</a>
                                </div>';

                        }
                        $html .= '</div>';
                    }
                    if($count_duel_completed)
                        $html .= trans_choice('game.new-duels-completed',$count_duel_completed, ['number_duels' => $count_duel_completed]).'<br/><a class="btn btn-success" href="'.route('duel').'?tab=completed" id="see_completed_duels">'.trans('game.see-now').'</a><br/>';
                    if($count_duel_in_progress_not_seen)
                        $html .= trans_choice('game.new-duels-received',$count_duel_in_progress_not_seen, ['number_duels' => $count_duel_in_progress_not_seen]).'<br/><a class="btn btn-success" href="'.route('duel').'?tab=in_progress" id="see_duels_in_progress">'.trans('game.see-now').'</a><br/>';
                    if($count_duel_available)
                        $html .= trans_choice('game.new-duels-pending',$count_duel_available, ['number_duels' => $count_duel_available]).'<br/><a class="btn btn-success" href="'.route('duel').'?tab=available" id="see_available_duels">'.trans('game.see-now').'</a><br/>';
                }
                echo $modal->modal($html,'modalLogin');


                echo "<script>
                        $('#modalLogin').modal('show');
                    </script>";
            }
        }
        ?>


    </body>
</html>
