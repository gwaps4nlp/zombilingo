<?php
use App\Models\ConstantGame;
use App\Services\Html\ModalBuilder as Modal;
$challenges_repo = App::make('App\Repositories\ChallengeRepository');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
        <meta name="description" content="@yield('description')" />
        <title>@yield('title') - Zombilingo</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        <!-- CSS principal -->
        {!! Html::style(elixir("css/app.css")) !!}
        
        @yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}">
        @include('front.header')
        @include('front.navbar')

        @yield('main')
        
        <div id="containerModal"></div>
        <script>
            @include('js.data-js')
        </script>
        <script src="{{ asset(elixir("js/app.js")) }}"></script>


        @yield('scripts')

        <?php

            if(Auth::check()){
                //Coccinelle
                if(rand(0,100) < ConstantGame::get('proba-bat') && time() > Auth::user()->last_mwe+ConstantGame::get('time-mwe')){
                    echo '<a href="'.url('game/mwe/begin/1').'">'. HTML::image('img/coccinelle.png', 'rigor mortis', ['id'=>"coccinelle"]) . '</a>';
                    session()->put('mwe.enabled',1);
                    echo HTML::script('js/coccinelle.js');
                }else{
                    if(rand(0,100) < ConstantGame::get('proba-meat')){
                        echo HTML::image('img/viande.png', 'Vous avez gagné un objet !', ['id'=>"viande"]);
                        session()->put('object_won',1);
                        echo Html::script(route('asset',['asset'=>'js/bonus-object.js']));
                    }
                }
            }
        ?>
        <input type="hidden" id="connected" value="{{ (Auth::check())?Auth::user()->id:'0' }}" />

        <?php

        if(Session::has('inputs.new_log')){
            $modal = App::make('App\Services\Html\ModalBuilder');
            $duels = App::make('App\Repositories\DuelRepository');
            $news = App::make('App\Repositories\NewsRepository');
            $count_duel_completed = $duels->countCompletedNotSeen(Auth::user());
            $count_duel_in_progress_not_seen = $duels->countInProgressNotSeen(Auth::user());
            $count_duel_in_progress = $duels->countInProgress(Auth::user());
            $count_news_not_seen = $news->countNotSeen(Auth::user());
            $count_duel_available = $duels->countPendingAvailable(Auth::user());
            $news_not_seen = $news->getNotSeen(Auth::user());
            $friend_requests = Auth::user()->getAskFriendRequests();
            $open_modal = $count_duel_in_progress_not_seen||$count_duel_completed||$count_news_not_seen||count($friend_requests);

            if($open_modal){

                $html = '<h2>Salut '.Auth::user()->username.' !</h2>';
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
                    $html .= trans_choice('game.new-duels-completed',$count_duel_completed, ['number_duels' => $count_duel_completed]).'<br/><a class="btn btn-success" href="'.url('duel/index').'?tab=completed">'.trans('game.see-now').'</a><br/>';
                if($count_duel_in_progress_not_seen)
                    $html .= trans_choice('game.new-duels-received',$count_duel_in_progress_not_seen, ['number_duels' => $count_duel_in_progress_not_seen]).'<br/><a class="btn btn-success" href="'.url('duel/index').'?tab=in_progress">'.trans('game.see-now').'</a><br/>';
                if($count_duel_available)
                    $html .= trans_choice('game.new-duels-pending',$count_duel_available, ['number_duels' => $count_duel_available]).'<br/><a class="btn btn-success" href="'.url('duel/index').'?tab=available">'.trans('game.see-now').'</a><br/>';
                $html.='</div>';
                echo $modal->modal($html,'modalLogin');

                echo "<script>
                        $('#modalLogin').modal('show');
                    </script>";
            }
        }
        ?>


    </body>
</html>
