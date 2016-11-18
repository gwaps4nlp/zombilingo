@extends('back.template')

@section('content')

<h1>{{ trans('game.go-up-level') }}</h1>
{{ trans('game.next-level') }} {!! Form::selectRange('level', 2, 7,'',['id'=>'level']) !!}
<button type="button" class="btn btn-info" id="btnLevel">Test</button><br/>
{{ trans('game.object-won') }} {!! Form::selectRange('object', 1, 5,'',['id'=>'object']) !!}
<button type="button" class="btn btn-info" id="btnObject">Test</button>
	<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 class="modal-title">{{ trans('game.go-up-level') }}</h4>
		</div>
		<div class="modal-body">
		  <p style="text-align:center;"><img src="" id="img_level"/></p>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.close') }}</button>
		</div>
	  </div>
	  
	</div>
	</div>
	
	<div class="modal fade" id="modalObjectWon" role="dialog">
	<div class="modal-dialog modal-sm">

	  <!-- Modal content-->
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 class="modal-title">{{ trans('game.object-won') }}</h4>
		</div>
		<div class="modal-body">
		  <p style="text-align:center;"><img src="" id="img_object"/></p>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.close') }}</button>
		</div>
	  </div>
  
	</div>
	</div>

@stop

@section('scripts')
	<script>
$(document).ready(function(){
    $("#btnLevel").click(function(){
		var level = $('#level').val();
		$("#img_level").attr('src','{!! asset('/img/level') !!}/level-'+level+'.gif');
		$("#myModal").modal("show");
    });
    $("#btnObject").click(function(){
		var object = $('#object').val();
		$("#img_object").attr('src','{!! asset('/img/objet') !!}/object-'+object+'.png');
		$("#modalObjectWon").modal("show");
    });
});	

	</script>
@stop



