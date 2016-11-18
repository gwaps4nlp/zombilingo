<ul class="messages">
@foreach($thread as $key=>$message)
	<?php
	if($parent_id!=$message->message_id) continue;
	?>
	<li>
		{{ $message->user->username }}<br/>
		<p>{!! nl2br(htmlentities($message->content)) !!}</p>
		<div>
			<span class="open-asnwer" onclick="$(this).next('.fade').removeClass('hide').addClass('in');">Répondre</span>
			  {!! Form::open(['url' => 'report/send', 'method' => 'post', 'role' => 'form', 'class'=>'hide form-message fade']) !!} 
				<div class="form-group" onfocus="$(this).parent().children('.submitMessage').removeAttr('disabled');">
					<textarea id="message" class="message" name="content" type="text" style="resize:none;padding:7px;overflow: hidden; word-wrap: break-word; min-height: 60px; height: 60px;width: 90%;color:#3C3C3C" placeholder="Répondre."></textarea>
				</div>
				<input type="hidden" name="annotation_id" value="{{ $annotation->id }}" />
				<input type="hidden" name="message_id" value="{{ $message->id }}" />
				<button type="submit" class="btn btn-success submitMessage">Publier</button>
				<button type="submit" class="btn btn-danger btn-default"  onclick="$(this).next('.fade').removeClass('in').addClass('hide');" >{{ trans('site.cancel') }}</button>
			  {!! Form::close() !!}							
		</div>
		<?php
		$filtered = $thread->filter(function ($item) use ($message) {
			return $item->message_id == $message->id;
		});
		?>
		@include('partials.message.message',['parent_id'=>$message->id])
	</li>
@endforeach
</ul>