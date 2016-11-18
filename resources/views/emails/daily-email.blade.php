@extends('emails.template')

@section('content')

<?php
switch ($email->type) {
    case "daily":
        $now = "0 day";
        $previous_period = "1 day";
        $type = "quotidien";
		$duration="";
        break;
    case "weekly":
        $now = "0 day";
        $previous_period = "1 week";
        $type = "hebdomadaire";
		$duration=" en une semaine";
        break;
    case "monthly":
        $now = "0 day";
        $previous_period = "1 month";
        $type = "mensuel";
		$duration=" en un mois";
        break;
}
?>

    <h1 style="font-size:18px;font-weight:500;color:#505050;line-height:24px;margin:0 0 15px 0">
        Salut <strong>{{ $user->username }}</strong>,&nbsp;c'est l'heure de jouer avec <span>ZombiLingo</span> !
    </h1>
    @if(date('Y-m-d')=='2016-10-17')
        <h2 style="font-size:18px;font-weight:700;color:#505050;line-height:24px;margin:15px 0 2px 0">
            Résultat du challenge "Attrapez-les toutes" du 16/09 au 09/10 :
        </h2>
        
        <p style="color:#505050;font-size:16px;font-weight:normal;line-height:24px;margin:0">
            Félicitations à Chouchou (1er avec 1677662 points et 2505 annotations), Methos31 (2e avec 1505025 points et 2330 annotations) et Lycos (3e avec 353647 points et 996 annotations).
        </p>
        <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
            <?php
                $score = $scores->getByUserAndCorpus($user,17);
            ?>
            @if(!$score)
                Tu n'as pas participé.
            @else
                <strong>Bravo, tu as terminé {{ $score->rank }}e</strong> avec {{ Html::formatScore($score->score) }} points.
                <strong>Merci pour ta participation</strong>.
            @endif
        <?php
            $leaders_challenge = $scores->leadersChallenge(17,15);
        ?>
        <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
            <tr><th style="text-align:left;">Position</th><th style="text-align:left;">Pseudo</th><th style="text-align:right">Points</th></tr>
        <?php $rank=1; ?>     
        @foreach($leaders_challenge as $leader)
            <tr class="rank rank-points" {!! ($leader->user_id==$user->id)?'style="background-color:#CEF6CE;"':'' !!}><td>{{ $rank }}</td><td>{{ $leader->username }}</td><td style="text-align:right">{{ Html::formatScore($leader->score) }}</td></tr>            
        <?php $rank++; ?>
        @endforeach
        </table>    
        </p>
    @endif 
    @if(false)
    <h2 style="font-size:18px;font-weight:700;color:#505050;line-height:24px;margin:15px 0 2px 0">
        Challenge "Les Zombies jouent au foot..."
    </h2>
    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
        <?php
            $score = $scores->getByUserAndPeriode($user,null,$now,17);
            $previous_score = $scores->getByUserAndPeriode($user,null,$previous_period,17);
            if($score && $previous_score)
                $diff_rank =  $previous_score->rank - $score->rank;
            else 
                $diff_rank = 0;
        ?>
        @if(!$score)
            Tu n'es pas encore classé. Nous t'attendons...
        @elseif(!$previous_score)
            Belle entrée dans le classement !
        @else        
            Tu es {{ $score->rank }}e au classement du challenge. 
            @if($diff_rank==0)
                Ton classement n'a pas évolué.
            @elseif($diff_rank==1)
                Bien joué ! Tu as gagné {{ $diff_rank }} place !<br/>
            @elseif($diff_rank>1)
                Bien joué ! Tu as gagné {{ $diff_rank }} places !<br/>
            @elseif($diff_rank==-1)
                Tu as perdu {{ abs($diff_rank) }} place. N'abandonne pas, c'est facilement rattrapable !<br/>
            @else
                Tu as perdu {{ abs($diff_rank) }} places. N'abandonne pas, c'est facilement rattrapable !<br/>
            @endif
        @endif

    <?php
        $leaders_challenge = $scores->leadersChallenge(17,10);
    ?>
    <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
        <tr><th style="text-align:left;">Position</th><th style="text-align:left;">Pseudo</th><th style="text-align:right">Evolution</th><th style="text-align:right">Points</th></tr>
    <?php $rank=1; ?>     
    @foreach($leaders_challenge as $leader)
        <?php
            $score = $scores->getByUserAndPeriode($leader->user_id,null,$now,17);
            $previous_score = $scores->getByUserAndPeriode($leader->user_id,null,$previous_period,17);
            if($score && $previous_score)
                $diff_rank =  Html::formatRank($previous_score->rank - $score->rank);
            elseif($score && !$previous_score)
                $diff_rank = '<span style="color:green;">new</span>';
            else 
                $diff_rank = "-";
        ?>    
        <tr class="rank rank-points" {!! ($leader->user_id==$user->id)?'style="background-color:#CEF6CE;"':'' !!}><td>{{ $rank }}</td><td>{{ $leader->username }}</td><td style="text-align:center;">({!! $diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($leader->score) }}</td></tr>            
    <?php $rank++; ?>
    @endforeach
    </table>
    </p>
    <h2 style="font-size:16px;font-weight:700;color:#888;line-height:24px;margin:2px 0 2px 0">
    Classement de tes ennemis
    </h2>
    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
    @if(count($enemies))
        <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
            <tr><th style="text-align:left;">Position</th><th style="text-align:left;">Pseudo</th><th>Evolution</th><th style="text-align:right">Points</th></tr>       
        @foreach($enemies as $enemy)
            <?php
                $score = $scores->getByUserAndPeriode($enemy->friend,null,$now,17);
                $previous_score = $scores->getByUserAndPeriode($enemy->friend,null,$previous_period,17);
                if($score && $previous_score)
                    $diff_rank =  Html::formatRank($previous_score->rank - $score->rank);
                else 
                    $diff_rank = "-";
            ?>
            @if($score)
            <tr class="rank rank-points" {!! ($enemy->friend->id==$user->id)?'style="background-color:#CEF6CE;"':'' !!}><td>{{ $score->rank }}</td><td>{{ $enemy->friend->username }}</td><td style="text-align:center;">({!! $diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($score->score) }}</td></tr>
            @else
            <tr class="rank rank-points" {!! ($enemy->friend->id==$user->id)?'style="background-color:#CEF6CE;"':'' !!}><td>NC</td><td>{{ $enemy->friend->username }}</td><td style="text-align:center;">({!! $diff_rank !!})</td><td style="text-align:right">0</td></tr>
            @endif      
        @endforeach
        </table>        
    @else
        Tu n'as actuellement aucun ennemi.
    @endif
    </p>
    @endif
    <?php
        $new_duels = $duels->getPendingNotSeen($user,$previous_period);
        $count_duel_in_progress_not_seen = count($new_duels);
    ?>
    @if($count_duel_in_progress_not_seen)
    <h2 style="font-size:18px;font-weight:700;color:#505050;line-height:24px;margin:15px 0 2px 0">
        Nouveaux duels
    </h2>
        {{ trans_choice('game.new-duels-received',$count_duel_in_progress_not_seen, ['number_duels' => $count_duel_in_progress_not_seen]) }}. <a class="btn btn-success" href="{{ asset('') }}duel/index?tab=in_progress">{{ trans('game.see-now') }}</a><br/>
    @endif

    <h2 style="font-size:18px;font-weight:700;color:#505050;line-height:24px;margin:15px 0 2px 0">
        Classement général
    </h2>

    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
        <?php
            $score_user = $scores->getByUserAndPeriode($user,null,$now);
            $previous_score_user = $scores->getByUserAndPeriode($user,null,$previous_period);
            if($score_user && $previous_score_user)
                $diff_rank =  $previous_score_user->rank - $score_user->rank ;
            else 
                $diff_rank = 0;
        ?>
        @if(!$score_user)
            Tu n'es pas encore classé. Nous t'attendons...
        @else
            Tu es {{ $score_user->rank }}e au classement général. 
            @if($diff_rank==0)

            @elseif($diff_rank==1)
                Bien joué ! Tu as gagné {{ $diff_rank }} place{{ $duration }} !<br/>
            @elseif($diff_rank>1)
                Bien joué ! Tu as gagné {{ $diff_rank }} places{{ $duration }} !<br/>
            @elseif($diff_rank==-1)
                Tu as perdu {{ abs($diff_rank) }} place{{ $duration }}.<br/>
            @else
                Tu as perdu {{ abs($diff_rank) }} places{{ $duration }}.<br/>
            @endif
        @endif
        @if($scores_user['total']) 
        <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
            <tr><th style="text-align:left;">Position</th><th style="text-align:left">Pseudo</th><th>Evolution</th><th style="text-align:right">Points</th></tr>
 
        @foreach($neighbors['total']['sup'] as $neighbor)
            <?php
                $score = $scores->getByUserAndPeriode($neighbor->user_id,null,$now);
                $previous_score = $scores->getByUserAndPeriode($neighbor->user_id,null,$previous_period);
                if($score && $previous_score){
                    $diff_rank =  Html::formatRank($previous_score->rank - $score->rank);
                }
                else 
                    $diff_rank = '-';
            ?>        
            <tr class="rank rank-points"><td>{{ $neighbor->rank }}</td><td>{{ $neighbor->username }}</td><td style="text-align:center">({!! $diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($neighbor->score) }}</td></tr>
        @endforeach
            <?php
                if($score_user && $previous_score_user){
                    $diff_rank =  Html::formatRank($previous_score_user->rank - $score_user->rank);
                }
                else 
                    $diff_rank = '-';
            ?>

            <tr class="rank rank-points self" style="background-color:#CEF6CE;"><td>{{ $scores_user['total']->rank }}</td><td>{{ $scores_user['total']->username }}</td><td style="text-align:center">({!! $diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($scores_user['total']->score) }}</td></tr>
        
            @foreach($neighbors['total']['inf'] as $neighbor)
                <?php
                    $score = $scores->getByUserAndPeriode($neighbor->user_id,null,$now);
                    $previous_score = $scores->getByUserAndPeriode($neighbor->user_id,null,$previous_period);
                    if($score && $previous_score){
                        $diff_rank =  Html::formatRank($previous_score->rank - $score->rank);
                    }
                    else 
                        $diff_rank = '-';
                ?>            
                <tr class="rank rank-points"><td>{{ $neighbor->rank }}</td><td>{{ $neighbor->username }}</td><td style="text-align:center">({!! $diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($neighbor->score) }}</td></tr>
            @endforeach
        @endif
        </table>

    </p>

    <h2 style="font-size:16px;font-weight:700;color:#888;line-height:24px;margin:2px 0 2px 0">
    Classement de tes ennemis
    </h2>
    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
    @if(count($enemies))
		<?php
		$user->friend = $user;
		$enemies->push($user);
		foreach($enemies as &$enemy){
			$score = $scores->getByUserAndPeriode($enemy->friend,null,$now);
			$previous_score = $scores->getByUserAndPeriode($enemy->friend,null,$previous_period);
			if($score && $previous_score){
				$diff_rank =  Html::formatRank($previous_score->rank - $score->rank);
				$enemy->order = $score->rank;
				$enemy->rank = $score->rank;
				$enemy->diff_rank = $diff_rank;		
				$enemy->score = $score->score;				
			} elseif($score) {
				$diff_rank = "-";
				$enemy->rank = $score->rank;
				$enemy->order = $score->rank;
				$enemy->diff_rank = $diff_rank;
				$enemy->score = $score->score;
			} else {
				$diff_rank = "-";
				$enemy->rank = "-";
				$enemy->order = 10e10;
				$enemy->diff_rank = $diff_rank;
				$enemy->score = 0;
			}
		}
		
		$enemies = $enemies->sortBy('order');
		?>
        <table style="width:100%;padding-bottom:20px" border="0" cellpadding="0" cellspacing="0">
            <tr><th style="text-align:left;">Position</th><th style="text-align:left;">Pseudo</th><th>Evolution</th><th style="text-align:right">Points</th></tr>		
        @foreach($enemies as $enemy)
            <?php
                $score = $scores->getByUserAndPeriode($enemy->friend,null,$now);
                $previous_score = $scores->getByUserAndPeriode($enemy->friend,null,$previous_period);
                if($score && $previous_score)
                    $diff_rank =  Html::formatRank($score->rank - $previous_score->rank);
                else 
                    $diff_rank = "-";
            ?>
            <tr class="rank rank-points" {!! ($enemy->friend->id==$user->id)?'style="background-color:#CEF6CE;"':'' !!}><td>{{ $enemy->rank }}</td><td>{{ $enemy->friend->username }}</td><td style="text-align:center;">({!! $enemy->diff_rank !!})</td><td style="text-align:right">{{ Html::formatScore($enemy->score) }}</td></tr>            
		@endforeach
		</table>
    @else
        Tu n'as actuellement aucun ennemi.
    @endif
    </p>


    @if(count($pending_enemies)>0)
        <h2 style="font-size:18px;font-weight:700;color:#505050;line-height:24px;margin:15px 0 2px 0">
            Demande en ennemi
        </h2>  
    @endif
    @if(count($pending_enemies)>1)
        <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
            Tu as <a href="{{ asset('') }}user/home?enemies=1" style="color:#111;font-weight:bold;text-decoration:none;font-size:15px!important">{{ count($pending_enemies) }} demandes en ennemi</a> en attente.
        </p>
    @elseif(count($pending_enemies)==1)
        <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
            Tu as <a href="{{ asset('') }}user/home?enemies=1" style="color:#111;font-weight:bold;text-decoration:none;font-size:15px!important">{{ count($pending_enemies) }} demande en ennemi</a> en attente.
        </p>    
    @endif    
@stop
