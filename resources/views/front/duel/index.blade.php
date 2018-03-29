<?php
if($request->has('tab'))
    $active_tab = $request->input('tab');
elseif ($duels->countCompletedNotSeen($user))
    $active_tab = 'completed';
elseif ($duels->countNotCompleted($user))
    $active_tab = 'in_progress';
else
    $active_tab = 'available';
?>

@extends('front.template-duel')

@section('main')

<div id="index-duel">
    <div id="block-game" class="row" >
    <div class="col-12">
    <div id="duel-summary" class="float-right" style="text-align:right;">
                    <span class="win">{{ trans('game.won-duels') }} : {{ $duels->countWon($user) }}</span><br/>
                    <span class="lost">{{ trans('game.lost-duels') }} : {{ $duels->countLost($user) }}</span><br/>
                    <span class="draw">{{ trans('game.draws') }} : {{ $duels->countDraw($user) }}</span>
    </div>    
    <h1>
        {{ trans('site.duels') }}
        {!! link_to('duel/new',trans('game.new-duel'),['id'=>"openNewDuel",'class'=>"btn btn-success",'style'=>"display:inline;"]) !!}        
    </h1>

    <br/>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $active_tab=='available'? 'active':'' }}" data-toggle="tab" href="#available">
                {{ trans('game.available-duels') }} <span class="badge">{{ $duels->countPendingAvailable($user) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $active_tab=='in_progress'? 'active':'' }}" data-toggle="tab" href="#in_progress">
                {{ trans('game.my-duels-in-progress') }} <span class="badge">{{ $duels->countInProgress($user)-$duels->countNotCompleted($user) }}</span>
                @if($duels->countNotCompleted($user))
                    <span class="badge" style="background-color:#75211f;">+{{ $duels->countNotCompleted($user) }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $active_tab=='completed'? 'active':'' }}" data-toggle="tab" href="#completed" onclick="showResults();">
                {{ trans('game.my-duels-ended') }} <span class="badge" id="completed_seen">{{ $duels->countCompleted($user) - $duels->countCompletedNotSeen($user)}}</span>
                @if($duels->countCompletedNotSeen($user))
                    <span class="badge" id="completed_not_seen" style="background-color:#75211f;">+<span id="count_completed_not_seen">{{ $duels->countCompletedNotSeen($user) }}</span></span>
                @endif                
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="available" class="tab-pane fade in {{ $active_tab=='available'? 'show active':'' }}">
            @if(count($available_duels))
            <table class="table">
                @foreach($available_duels as $duel)
                    <tr id="duel_{{ $duel->id }}" class="duel pending-duel" title="Rejoindre le duel" data-href="{{ url('duel/join') }}?duel_id={{ $duel->id }}">
                    @foreach($duel->users as $key=>$user_duel)
                        @if($key==0)
                        <td style="text-align:right;width:40%;">
                        @else
                        <td style="text-align:left;width:40%;">
                        @endif
                            {{ $user_duel->username }}<br/>
                            <?php $progress = round(100*$user_duel->pivot->turn/$duel->nb_turns); ?>
                            <div class="progress">        
                                <div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
                                    {{ $progress }}%
                                </div>
                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
                                @if(!$progress)
                                    0%
                                @endif
                                </div>
                            </div>    
                        </td>
                        @if($key==0)
                            <td style="width:0%;">vs.</td>
                        @endif
                    @endforeach
                    
                    @if(count($duel->users)<2)
                        <td style="width:40%;">
                            {{ trans('game.waiting-for-an-opponent') }}<br/>
                            <div class="progress">
                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:100%">
                                    0%
                                </div>
                            </div>  
                        </td>
                    @endif
                    <td style="width:20%;">
                    @if($duel->relation)
                        {{ $duel->relation->name }}<br/>
                        {{ trans('game.duel-in-n-turns',['turns'=>$duel->nb_turns]) }}
                    @endif
                    </td>        
                    </tr>
                @endforeach
            </table>
            <div class="bloc_pagination">
            {!! $available_duels->render() !!}
            </div>
            @else
                {{ trans('game.no-duel-available') }}
            @endif            
        </div>
        <div id="in_progress" class="tab-pane fade in {{ $active_tab=='in_progress'? 'show active':'' }}">
            @if(count($pending_duels))
            <table class="table">
                @foreach($pending_duels as $duel)
                    <tr class="duel link-level" action="duel" id_phenomene="{{ $duel->id }}">
                    @foreach($duel->users as $key=>$user_duel)
                        <?php if($user_duel->id!=$user->id) continue; ?>
                        <td style="text-align:right;width:40%;">
                            @include('partials.duel.block-in-progress',['duel'=>$duel,'user'=>$user_duel])
                        </td>
                    @endforeach
                    <td style="width:0%;">vs.</td>
                    @foreach($duel->users as $key=>$user_duel)
                        <?php if($user_duel->id==$user->id) continue; ?>
                        <td style="text-align:left;width:40%;">
                            @include('partials.duel.block-in-progress',['duel'=>$duel,'user'=>$user_duel])
                        </td>
                    @endforeach
                    @if(count($duel->users)<2)
                        <td style="width:40%;">
                            en attente d'un adversaire<br/>
                            <div class="progress">
                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:100%">
                                    0%
                                </div>
                            </div>  
                        </td>
                    @endif
                        <td style="width:20%;">
                        @if($duel->relation)
                            {{ $duel->relation->name }}<br/>
                            {{ trans('game.duel-in-n-turns',['turns'=>$duel->nb_turns]) }}
                        @endif
                        </td>        
                    </tr>
                @endforeach
            </table>
            <div class="bloc_pagination">
            {!! $pending_duels->render() !!}
            </div>
            @else
                {{ trans('game.no-duel-in-progress') }}
            @endif
        </div>
        <div id="completed" class="tab-pane fade in {{ $active_tab=='completed'? 'show active':'' }}"> 
            @if(count($completed_duels))
            <table class="table">
                @foreach($completed_duels as $duel)
                    <?php
                    if($duel->user($user)->pivot->result==1)
                        $class= 'win';
                    elseif($duel->user($user)->pivot->result==-1)
                        $class='lost';
                    else
                        $class='draw';

                    ?>
                    <tr action="duel" id_phenomene="{{ $duel->id }}" class="duel duel-completed completed {{ (!$duel->user($user)->pivot->seen)?'not-seen':'' }} {{ $duel->user($user)->pivot->seen }} {{ $user->id }} {{ $class }}">
                    
                    <td style="padding:23px;">
                        @if($class=='win')
                            <i class="fa fa-thumbs-up" style="color:#EFD807"></i>
                        @elseif($class=='lost')
                            <i class="fa fa-thumbs-down" style="color:rgb(117, 33, 31)"></i>
                        @endif
                    </td>
                    @foreach($duel->users as $key=>$user_duel)
                        <?php if($user_duel->id!=$user->id) continue; ?>
                        <td style="text-align:right;width:20%;" class="{{ $user_duel->pivot->result }}">
                            {{ $user_duel->username }}<br/>
                            <div class="score">
                                <span>{{ $user_duel->pivot->score }}</span>
                            </div>
                        </td>
                    @endforeach

                    <td style="width:0%;">vs.</td>
                    @foreach($duel->users as $key=>$user_duel)
                        <?php
                        if($user_duel->id==$user->id) continue; 
                        ?>
                        <td style="text-align:left;width:20%;" class="{{ $user_duel->pivot->result }}">
                            <span class="rank" user_id="{{ $user_duel->id }}">{{ $user_duel->username }}</span><br/>
                            <div class="score">
                                <span data-goal="{{ $user_duel->pivot->score }}" data-value="0">{{ $user_duel->pivot->score }}</span>
                            </div>
                        </td>
                    @endforeach

                    <td style="width:30%;">
                        <div class="result">
                        @foreach($duel->users->sortByDesc('pivot.final_score') as $key=>$user_duel)
                            @if($user_duel->pivot->final_score>0)
                                {{ $user_duel->username }} gagne {{ $user_duel->pivot->final_score }} points<br/>
                            @endif
                        @endforeach
                        </div>
                    </td>

                    <td style="width:30%;">
                    @if($duel->relation)
                        {{ $duel->relation->name }}<br/>
                    @endif
                        {{ trans('game.duel-in-n-turns',['turns'=>$duel->nb_turns]) }}<br/>
                    </td>
                  
                    </tr>
                @endforeach
            </table>
            <div class="bloc_pagination">
            {!! $completed_duels->render() !!}
            </div>
            @else
                {{ trans('game.no-duel-ended') }}
            @endif
        </div>
    </div>
    </div>
    </div>
</div>

<div class="modal fade text-center" id="modalConfirmJoin" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="form-group">

                    </h3>
                    {!! Form::open(['url' => url('duel/join'), 'id' => 'form-join-duel', 'method' => 'post', 'role' => 'form']) !!}
                    <div class="row">
                        <div class="col-md-12 col-md-offset-0">
                            <div class="form-group">
                                <label class="control-label">
                                Souhaitez-vous rejoindre le duel ?
                                </label>                            
                                <span id="relation_id"></span>
                                <span id="duel_id"></span>
                            </div>
                            <div class="form-group" style="margin-top:25px;">
                                <input type="submit" id="submitJoinDuel" value="Rejoindre" class="btn btn-success btn-lg" />                            
                                <input type="button" value="{{ trans('site.cancel') }}" data-dismiss="modal" class="btn btn-danger btn-lg" />
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}              
                <div class="modal-footer">

                </div>                  
            </div>        
        </div>
    </div>
</div>

@include('partials.duel.modal-new')

@stop

@section('scripts')
<script>
var nb_completed_seen = {{ $duels->countCompleted($user) - $duels->countCompletedNotSeen($user) }};
var nb_not_seen = {{ $duels->countCompletedNotSeen($user) }};
    function initIndexDuel() {
        if($("tr.not-seen div.score span[data-goal]").length)
            animateScore($("tr.not-seen div.score span[data-goal]").first());
    }

    function showResults(){
        if($("tr.not-seen div.score span[data-goal]").length)
                animateScore($("tr.not-seen div.score span[data-goal]").first());
    }
    function animateScore(element){
        var $totalCerveaux = $(element);
        var goal = parseInt($totalCerveaux.attr('data-goal'),10);
        var value  = parseInt($totalCerveaux.attr('data-value'),10);
        var diff = goal-value ;
        if( diff > 0){
            if(diff < 10){
                var new_value = value+1;
            }else if(diff < 100){
                var new_value = value+10;
            }else{
                var new_value = value+100;
            }
            var parent = $(element).parent().get(0);
            $totalCerveaux[0].innerHTML=new_value.formatScore();
            $totalCerveaux.attr("data-value",new_value);             
            setTimeout(function(){animateScore(element)}, 500);
        } else {
            var elm = $(element).parents('tr.not-seen').get(0);
            $(element).parents('tr.not-seen').removeClass('not-seen');
            var duel_id = $(elm).attr('id_phenomene');
            $.ajax({
                url : base_url + 'duel/check-as-seen/' + duel_id
            });            
            nb_not_seen--;
            nb_completed_seen++;
            
            if($('tr.not-seen').length){    
                animateScore($("tr.not-seen div.score span[data-goal]").first());
            }

            $('#completed_seen').text(nb_completed_seen);
            if(nb_not_seen>0)
                $('#count_completed_not_seen').text(nb_not_seen);
            else
                $('#completed_not_seen').text("0");
        }
    }
    window.onload = initIndexDuel();

</script>   
@stop