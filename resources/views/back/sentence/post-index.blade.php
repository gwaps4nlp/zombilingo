@extends('back.template')

@section('content')
@include('back.sentence.block-search')

@if(count($sentences))
	<table class="table">
	<thead>
		<tr>
			<th>id</th>
			<th>content</th>
			<th>corpus</th>
		</tr>
	</thead>
	@foreach($sentences as $sentence)
	<tr>
		<td>
			{!! link_to('sentence/'.$sentence->id, $sentence->sentid) !!}
		</td>
		<td>
			{{ $sentence->content }}
		</td>
		<td>
			{{ $sentence->corpus->name }}
		</td>
	</tr>
	@endforeach
@else
	<p><strong>No results</strong></p>
@endif

@stop