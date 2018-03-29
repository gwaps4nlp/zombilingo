@extends('back.template')

@section('content')

<h1>List of pages</h1>

<table class="table">
	<thead>
		<tr>
		<th>url</th>
		<th>title</th>
		<th>meta description</th>
		<th>action</th>
		</tr>
	</thead>
	<tbody>
	@foreach($pages as $page)
	<tr>
	<td>{{ $page->slug }}</td>
	<td>{{ $page->title }}</td>
	<td>{{ $page->meta-description }}</td>
	<td>{!! link_to('page/edit/?id='.$page->id,'edit') !!}</td>
	</tr> 
	@endforeach
	</tbody>
</table>
@stop