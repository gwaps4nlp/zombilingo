@extends('back.template')

@section('style')
  <style>
  .modal-header, h4, .close {
      background-color: #5cb85c;
      color:white !important;
      text-align: center;
      font-size: 30px;
  }
  .modal-footer {
      background-color: #f9f9f9;
  }
  </style>
@stop

@section('content')
  <h2>Modal Login Example</h2>
  <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-default btn-lg" id="myBtn">Login</button>
@include('partials.auth.modal-login')
 


@stop

@section('scripts')
<script>
$(document).ready(function(){
    $("#myBtn").click(function(){
        $("#modalLogin").modal();
    });
});
</script>
@stop



