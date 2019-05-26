@extends('back.template')

@section('content')

<h1>Add a new news</h1>
{!! Form::open(['url' => 'news/create', 'method' => 'post', 'role' => 'form']) !!}
	
	{!! Form::control('text', 0, 'title', $errors, 'Title (will be the subject of the message if sent by email)') !!}
	{!! Form::control('textarea', 0, 'content', $errors, 'Content') !!}
	{!! Form::control('selection', 0, 'language_id', $errors, 'Language',$languages,null,'Select a language...') !!}
	<div class="form-group  {{ $errors->has('send_by_email') ? 'has-error' : '' }}">
		<label for="send_by_email" class="control-label">Send by email</label>
		{!! $errors->first('send_by_email', '<small class="help-block">:message</small>') !!}
		{!! Form::radio('send_by_email','0',old('send_by_email'),['onchange'=>"$('#scheduled_date').hide();"]) !!} No
		{!! Form::radio('send_by_email','1',old('send_by_email'),['id'=>'send_by_email','onchange'=>"$('#scheduled_date').slideDown();"]) !!} Yes
	</div>
	<div class="form-group  {{ $errors->has('date_scheduled	')||$errors->has('hour_scheduled')  ? 'has-error' : '' }}" id="scheduled_date" style="display:none;">
		<label for="date_scheduled" class="control-label">Date of sending (dd/mm/yy)</label>
		{!! $errors->first('date_scheduled', '<small class="help-block">:message</small>') !!}
			<input class="datepicker" name="date_scheduled" value="{{ old('date_scheduled') }}" />
		<label for="hour_scheduled" class="control-label">Hour (hh:mm)</label>
		{!! $errors->first('hour_scheduled', '<small class="help-block">:message</small>') !!}
			<input class="" name="hour_scheduled" value="{{ old('hour_scheduled') }}"/>
	</div>


	<input type="submit" value="Save" class="btn btn-success" />
	<a href="{{ url('news/index') }}" class="btn btn-warning" role="button">Cancel</a>	
{!! Form::close() !!}

@stop

@section('style')
	{!! Html::style('css/bootstrap-datepicker3.css') !!}
@stop

@section('scripts')
	{!! Html::script('js/bootstrap-datepicker.js') !!}
	<script>
	$('.datepicker').datepicker({
	    format: 'dd/mm/yyyy',
	    autoclose: true,
	    todayBtn: "linked",
	});
	function init(){
		if($('#send_by_email').is(':checked'))
			$('#scheduled_date').slideDown();

	}
    window.onload = init();
	</script>
@stop