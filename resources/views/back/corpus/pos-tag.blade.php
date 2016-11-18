@extends('front.template-iframe')

@section('main')
@if(isset($result))
	{!! Form::open(['url' => 'corpus/parse', 'method' => 'post', 'role' => 'form', 'target'=>'parse']) !!}
	    <div class="col-md-10 col-md-offset-1">
			<textarea style="width:100%;height:500px;" name="text">{!! $result !!}</textarea>
	    </div>
	    <div class="col-md-10 col-md-offset-1">
			<textarea style="width:100%;height:500px;" name="text-melt">{!! $result_melt !!}</textarea>
	    </div>
	{!! Form::submit('Parse', null,['class' => 'btn btn-success']) !!}
	{!! Form::close() !!}
<iframe name="parse" style="width:100%;" frameborder="0" scrolling="no" onload="resizeIframeAgain(this);resizeIframe(this);resizeIframeAgain(this);resizeIframe2(this);" />		
@endif
@stop