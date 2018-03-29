<div id="container-game" class="container-fluid">
<div class="row">
    <div id="block-profil" class="col-2 pl-5 pr-2 pt-3">
    @if($game->mode_stage=='expert')
    	Attention, tu es en mode expert.<br/>
    	Tes réponses sont enregistrées comme référence.
    	<a class="btn btn-success" href="{{ url('upl/admin-index') }}">Quitter</a>
    @elseif($game->mode_stage=='admin')
    	Tu es en mode test.<br/>
    	Tes réponses ne sont pas enregistrées.
    	<a class="btn btn-success" href="{{ url('upl/admin-index') }}">Quitter</a>
    @elseif($game->stage->mode=='game')
	    Score : <span class="score">{{ $game->score }}</span><br/>
	    <div class="d-none">
		    XP : <span class="experience">{{ $game->stage_user->experience }}</span><br/>
		    Money : <span class="money">{{ $game->stage_user->money }}</span><br/>
		    Samples : <span class="samples">{{ $game->stage_user->samples }}</span>
	    </div>
    @endif
    </div>
    <div class="col-8 text-center">
        <h5 id="label-phenomenon" class="py-3">Trouve les expressions multi-mots présentes dans la phrase</h5>
        <div class="row game-element" id="phase" style="">
            <div id="progress">0%</div>
            <div id="progress-container" class="progress">
                <div class="progress-bar-game" id="phaseBar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">  
                </div>
                @if(isset($game->theme)&&$game->theme=="pyramids-dark")
                    {!! Html::image('img/sceptre_darkgrey.png','progression',array('id'=>'progressBar','style'=>'margin:0 20%;position:absolute;left:0;width:60%;')) !!}
                @else
                    {!! Html::image('img/bone.png','progression',array('id'=>'progressBar','style'=>'margin:0 20%;position:absolute;left:0;width:60%;')) !!}
                @endif

            </div>
        </div>        
    </div>
    <div class="col-2 aideTool">   
        <h5 class="mt-3">Besoin<br/>d'aide ?</h5>
        <div id="" class="savant help">
            <div class="aideTip" id="helpRelation">
                <div class="help-upl">
                <img class="scientist" src="{{ asset('img/savant.jpg') }}" style="float:right;border-radius: 50px;width:80px;border: solid 2px black;" />
                @if($game->stage->mode=='demo')
                	{!! $game->stage->help !!}
    	            @include('partials.upl.help-demo')
    			@elseif($game->stage->mode=='training')
    				{!! $game->stage->help !!}
    			@elseif($game->stage->mode=='game')
    				@include('partials.upl.help-demo')
    			@endif
                </div>
            </div>
        </div>
    </div> 
</div>
<div class="row">
    <div class="col-6" id="infos">
    </div>
</div>
<div class="row" id="content">
    <div class="col-lg-1"></div>
    <div class="col-lg-10"><h3 id="indication" class="text-center">&nbsp;</h3></div>
    <div class="col-lg-1"></div>
    <div class="col-lg-1" style="top:0px;"></div>
    <div class="col-lg-10 mx-auto" id="sentence-container" style="top:0px;">
        <div id="sentence" class="sentence sentence-upl">

        </div>
    </div>
    <div class="col-lg-1 d-none d-lg-block d-flex justify-content-end flex-column">

    </div>
</div>
<div class="row">
    <div class="col-lg-1"></div>
	<div class="col-lg-10">
		<div id="container-upl" class="row">

			<div class="col-9 pl-0">
				<div id="new-upl" class="">
				&nbsp;
				</div>
				<button class="btn btn-sm ml-4 btn-success" id="add-upl" style="display:none;">
					<i class="fa fa-plus" aria-hidden="true"></i>
				</button>
			</div>
			<div class="col-3">
				<div>
				<button class="tool btn ml-4 btn-success float-right mt-2" id="btn-validate-upl" data-toggle="popover" data-placement="left" data-content="Attention : valide uniquement si tu penses avoir trouvé tous les phénomènes présents dans la phrase.">
					Valider <span class="badge badge-game ml-1" id="upl_found">0</span>
				</button>
				<button class="btn btn-small btn-green mt-2 ml-4 float-right" id="next-sentence" style="display:none;" title="{{ trans('game.next-sentence') }}">
				{{ trans('game.next-sentence') }}
				</button>
				</div>
			</div>
		
		</div>
		<div class="row">
			<div id="validated-upl" class="col-12">

			</div>
		</div>	
	</div>
</div>

<div class="row text-center" style="position:relative;top:50px;">
    <h3 id="message-objet" class="m-auto"></h3>
</div>

<div class="row game-element pl-3" id="bottom" style="position:relative;top:50px;">

    <div class="col-1">

    </div>
	    <div class="col-9 text-center">
        <div id="result"></div>
    </div>

</div>

</div>
