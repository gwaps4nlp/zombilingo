@extends('back.template')

@section('content')

	{!! link_to('news/create','Add a new news',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	<h1>Index of news</h1>
	<table class="table table-striped">
	<thead>
	<tr>
		<th>Content</th>
		<th>Title</th>
		<th>Language</th>
		<th>Date</th>
		<th>Email</th>
		<th>Action</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($news as $new)
		<tr>    

		<td>{{ $new->content }}</td>
		<td>{{ $new->title }}</td>
		<td>{{ $new->language->label }}</td>
		<td>{{ substr($new->created_at,0,10) }}</td>
		@if($new->send_by_email==1)
			<td>
				{{ $new->scheduled_at }}<br/>
				@if($new->sent_at)
					sent : yes
				@else
					sent : no
				@endif
			</td>
		@else
			<td>-</td>
		@endif
		<td style="width:100px;">
		<a href="{{ url('news/edit',['id'=>$new->id]) }}" style="margin-left:20px;"><span class="glyphicon glyphicon-edit"></span></a>
		{!! Form::open(['url' => 'news/delete', 'method' => 'post', 'role' => 'form','style'=>'display:inline']) !!}
		<input type="hidden" name="id" value="{{ $new->id }}" />
		<button type="submit" class="btn btn-link" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette news ?')">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
		{!! Form::close() !!}

		</td>
 	
		</tr>
	@endforeach
	</tbody>
	</table>

@stop