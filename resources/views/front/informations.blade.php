@extends('front.template')

@section('main')
	<div id="informations" class="p-md-5">
	@include('lang/'.App::getLocale().'/informations')

	<table class="table table-striped">
	<thead>
		<tr><th width="15%">{{ trans('site.name-corpus') }}</th><th width="45%">{{ trans('site.description') }}</th><th>{{ trans('site.license') }}</th><th>{{ trans('site.file-conll') }}</th></tr>
	</thead>
	<tbody>
	@foreach($last_exported_corpora as $last_exported_corpus)
	<tr>
		<td>{{ $last_exported_corpus->corpus->name }}</td>
		<td>{!! nl2br($last_exported_corpus->corpus->description) !!}</td>
		<td><span data-toggle="tooltip" data-placement="auto left" title="{{ $last_exported_corpus->corpus->license->label }}" class="license">{!! Html::image('img/'.$last_exported_corpus->corpus->license->image) !!}</span></td>
		<td>
			<a target="_blank" href="{{ url('asset/conll').'?exported_corpus_id='.$last_exported_corpus->id }}">{{ trans('site.download-file') }}</a>
			<br/>
			<span class="date-export">{{ trans('site.date-export') }} : {{ $last_exported_corpus->created_at }}</span><br/>
		</td>
	</tr>
	@endforeach
	</tbody>
	</table>
	</div>
@stop

@section('scripts')
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop

@section('css')
<style>
.license img{
	width:60px;
}
</style>
@stop