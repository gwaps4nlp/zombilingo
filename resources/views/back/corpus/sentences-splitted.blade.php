@extends('front.template-iframe')

@section('main')

@if(isset($sentences_splitted))
	{!! Form::open(['url' => 'corpus/parse', 'method' => 'post', 'role' => 'form', 'target'=>'parse', 'id'=>'form-parse']) !!}
	    <div class="col-md-10 offset-md-1">
			<textarea style="width:100%;height:500px;" name="text">{!! $sentences_splitted !!}</textarea>
	    </div>
	<div class="clearfix"></div>
	<a href="{{ url('corpus/file?file=').$parser->output_file }}" class="btn btn-info">Get raw output file</a>
	<a data-toggle="collapse" data-target="#command" class="btn btn-info">Show command</a>
	<div id="command" class="collapse">{{ $parser->command }}</div><br/>


	<div class="form-group">
	  <select name="parser">
	    @foreach($parsers as $parser)
	      <option value="{{ $parser }}">{{ $parser }}</option>
	    @endforeach
	  </select>
		Sentid prefix : <input type="text" value="{{ $url }}" name="url" id="sentid_prefix"/>
		{!! Form::submit('Parse', null,['class' => 'btn btn-success']) !!}	  

	</div>
	{!! Form::close() !!}
<iframe name="parse" style="width:100%;" frameborder="0" scrolling="no" onload="resizeIframe(this)" />	
@endif



@endsection

@section('scripts')
<script type="text/javascript">
$('#form-parse').submit(function(event){
  event.preventDefault();
  if($('#sentid_prefix').val()==''){
  	alert('Veuillez renseigner le sentid prefix');
  } else {
  	$( "#form-parse" ).submit();
  }
});
</script>
@endsection

