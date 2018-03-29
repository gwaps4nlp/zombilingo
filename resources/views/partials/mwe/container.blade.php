<div id="container-mwe" class="container-fluid">
    <div class="row">
        <div id="bloc-mwe" class="col-8 offset-2 text-center">
            <h1 id="mwe_content"></h1>
            <p>
            <span id="mwe_id"></span>
            <a href="#" id="frozen" value="1" class="btn btn-success mwe">{{ trans('game.frozen') }}</a>
            <a href="#" id="unfrozen" value="0" class="btn btn-success mwe">{{ trans('game.not-frozen') }}</a>
            <a href="#" id=""  value="skip" class="btn btn-success mwe">{{ trans('game.skip') }}</a>
            </p>
       </div>

        <div class="col-md-1 col-md-offset-1">
        	<h4 style="position:relative;margin-left:10px;">{{ trans('game.need-help') }}</h4>
            <div class="savant help aideTool">
                <div class="aideTip">
                    {{ trans('game.tip-mwe') }}
                </div>
            </div>
        </div>
    </div>
</div>
