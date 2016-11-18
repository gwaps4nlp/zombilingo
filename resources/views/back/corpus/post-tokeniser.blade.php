@extends('front.template-iframe')

@section('main')
@if(isset($result))
	{!! Form::open(['url' => 'corpus/pos-tagger', 'method' => 'post', 'role' => 'form', 'target'=>'pos-tagger']) !!}
	    <div class="col-md-10 col-md-offset-1">
			<textarea style="width:100%;height:500px;" name="text">{!! $result !!}</textarea>
	    </div>
	{!! Form::submit('POS tagger', null,['class' => 'btn btn-success']) !!}
	{!! Form::close() !!}
<iframe name="pos-tagger" style="width:100%;" frameborder="0" scrolling="no" onload="resizeIframeAgain(this);resizeIframe(this);" />
@endif
@stop