<ul class="messages">
@foreach($thread as $key=>$message)
	<?php
	if($parent_id!=$message->parent_message_id) continue;
	?>
	<li>
		{{ link_to('user/'.$message->user->id,$message->user->username,['target'=>'_blank']) }}<br/>
		@if($message->trashed())
			@if($message->deletion_reason->slug!='user-update')
				<em>{{ trans('discussion.'.$message->deletion_reason->slug) }}</em>
			@endif
		@else
			<p>{!! nl2br(htmlentities($message->content)) !!}</p>
		<div>
			@if(Auth::user()->isAdmin())
				<span class="delete-message link" data-message-id="{{ $message->id }}" data-entity-id="{{ $entity->id }}" data-type="{{ get_class($entity) }}">Supprimer</span> - 
			@endif
			<span class="open-asnwer" onclick="$(this).next('.form-message').slideDown();">Répondre</span>
			  {!! Form::open(['url' => 'report/send', 'style'=>'display:none;', 'method' => 'post', 'role' => 'form', 'class'=>'form-message', 'data-id'=>$entity->id]) !!}
				<div class="form-group">
					<textarea class="message" name="content" type="text" placeholder="Répondre."></textarea>
				</div>
				<input type="checkbox" name="" value="1" /> Suivre la discussion <br/>
				<input type="hidden" name="entity_id" value="{{ $entity->id }}" />
				<input type="hidden" name="entity_type" value="{{ get_class($entity) }}" />
				<input type="hidden" name="parent_message_id" value="{{ $message->id }}" />
				<button type="submit" disabled="disabled" class="btn btn-success submitMessage">Publier</button>
				<button type="button" class="btn btn-danger btn-default cancelAnswer"  onclick="$(this).next('.fade').removeClass('in').addClass('hide');" >{{ trans('site.cancel') }}</button>
			  {!! Form::close() !!}							
		</div>
		@endif
		@include('partials.message.message',['parent_id'=>$message->id])
	</li>
@endforeach
</ul>