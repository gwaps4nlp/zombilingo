@extends('back.template')

@section('content')
<iframe src="{{ url('translations') }}" style="width:100%;border:0;overflow-y: hidden" onload="resizeIframe(this);">
</iframe>
@stop