@extends('front.master')

@section('container')
<div id="body-upl">
	<div class="container container-upl">
	    @yield('main')
	</div>
<div>
<style type="text/css">
#body-upl {
    /*background-color: rgb(235, 188, 120);*/
    background-size: 100% auto;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: left 70px;
    min-height: calc(100vh - 70px);
}
</style>
@stop

