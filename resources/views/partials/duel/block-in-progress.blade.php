 <span class="rank" user_id="{{ $user->id }}">{{ $user->username }}</span><br/>
<?php $progress = round(100*$user->pivot->turn/$duel->nb_turns); ?>
<div class="progress">        
    <div class="progress-bar progress-bar-success progress-bar-success-duel" role="progressbar" style="width:{{ $progress }}%">
        {{ $progress }}%
    </div>
    <div class="progress-bar progress-bar-danger progress-bar-danger-duel" role="progressbar" style="width:{{ 100-$progress }}%">
    @if(!$progress)
        0%
    @endif
    </div>
</div>