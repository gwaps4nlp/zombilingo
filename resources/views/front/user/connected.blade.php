@extends('front.template')

@section('main')
<div class="row">
    <div class="col-md-10 col-md-offset-1 center">
    <h1 class="text-center">{{ trans('site.list-connected-users') }}</h1>
        @forelse ($users as $user)
            @if (Auth::check())
                {!! link_to('user/'.$user->id,$user->username) !!}<br/>
            @else
                {{ $user->username }}<br/>
            @endif
        @empty
            <p>{{ trans('site.nobody-connected') }}Personne n'est connecté</p>
        @endforelse
    </div>
</div>

@stop