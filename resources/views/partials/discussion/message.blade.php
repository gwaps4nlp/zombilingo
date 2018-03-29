<ul class="messages">
@foreach($thread as $key=>$message)
	<?php
	if($parent_id!=$message->parent_message_id) continue;
	?>
	<li>
		{{ link_to('user/'.$message->user->id,$message->user->username,['target'=>'_blank']) }} 
		<span class="small text-light">a écrit 
		<?php
			\Carbon\Carbon::setLocale(App::getLocale());
			$date_message = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $message->created_at);
			echo $date_message->diffForHumans(); 
		?>
		 : </span>
		<br/>
		@if($message->trashed())
			@if($message->deletion_reason->slug!='user-update')
				<em>{{ trans('discussion.'.$message->deletion_reason->slug) }}</em>
			@endif
		@else
			@if(Auth::user()->isAdmin())
				<p>{!! nl2br($message->content) !!}</p>
			@else
				<p>{!! nl2br(htmlentities($message->content)) !!}</p>
			@endif
		<div>
			@if(Auth::user()->isAdmin())
				@if(get_class($entity)=='App\Models\Annotation')
					{!! link_to('game/admin-game/begin/'.$entity->relation_id.'?save-mode=user&user_id='.$message->user->id.'&annotation_id='.$entity->id,"Modifier l'annotation",['target'=>'blank', 'class'=>'link', 'style'=>'color:white;']) !!} - 
				@endif				
				<span class="delete-message link" data-message-id="{{ $message->id }}" data-entity-id="{{ $entity->id }}" data-type="{{ get_class($entity) }}">Supprimer</span> - 
			@endif
			<span class="open-asnwer" onclick="$(this).next('.form-message').slideDown();">Répondre</span>
			  {!! Form::open(['url' => 'report/send', 'style'=>'display:none;', 'method' => 'post', 'role' => 'form', 'class'=>'form-message', 'data-id'=>$entity->id]) !!}
				<div class="form-group">
					<textarea class="message" name="content" type="text" placeholder="Répondre."></textarea><br/>
					<input type="checkbox" name="follow-thread" value="1" /> Suivre la discussion <a target="_blank" href="{{ url('faq#follow-thread') }}" class="scroll badge-help badge badge-pill badge-success">?</a>
				</div>
				<input type="hidden" name="entity_id" value="{{ $entity->id }}" />
				<input type="hidden" name="entity_type" value="{{ get_class($entity) }}" />
				<input type="hidden" name="parent_message_id" value="{{ $message->id }}" />
				<button type="submit" disabled="disabled" class="btn btn-success submitMessage">Publier</button>
				<button type="button" class="btn btn-danger btn-default cancelAnswer"  onclick="$(this).next('.fade').removeClass('in').addClass('hide');" >{{ trans('site.cancel') }}</button>
			  {!! Form::close() !!}							
		</div>
		@endif
		@include('partials.discussion.message',['parent_id'=>$message->id])
	</li>
@endforeach
</ul>