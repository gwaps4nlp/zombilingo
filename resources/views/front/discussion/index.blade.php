@extends('front.template')

@section('main')

	@include('partials.discussion.index',['annotation_ids'=>$annotation_ids])

@stop

@section('scripts')

<script>

embedBratVisualizations();

$('.follow-thread-button').click(followThread);
$('.unfollow-thread-button').click(unFollowThread);
@if($show_messages)
	$('.message-button').trigger('click');	
@endif
</script>
@stop