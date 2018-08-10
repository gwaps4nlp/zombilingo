@extends('front.template')

@section('main')

<div class="row text-center">
    <div class="col-md-10 col-md-offset-1 center">
        <h1><a href="{{ url('admin') }}">Revenir à l'interface d'administration</a></h1>
        <h1>Import d'une page web</h1>

	{!! $sentences !!}


@stop