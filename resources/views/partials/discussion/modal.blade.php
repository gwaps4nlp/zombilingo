<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-body">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <h1>Discussion</h1>
                <div class="sentence">{{ $annotation->sentence->content }}</div>
				<hr/>
                  {!! Form::open(['url' => 'report/send', 'method' => 'post', 'role' => 'form', 'class'=>'form-message']) !!} 
                    <div class="form-group">
                        <textarea id="message" class="message" name="content" type="text" style="resize:none;padding:7px;overflow: hidden; word-wrap: break-word; min-height: 60px; height: 60px;width: 90%;color:#3C3C3C" placeholder="Discute de la réponse avec d'autres joueurs."></textarea>
                    </div>
					<input type="hidden" name="annotation_id" value="{{ $annotation->id }}" />
                    <button type="submit" disabled="disabled" class="btn btn-success submitMessage" id="submitMessage">Publier</button>
                    <button type="submit" class="btn btn-danger btn-default" data-dismiss="modal" id="cancelReport">{{ trans('site.cancel') }}</button>
                  {!! Form::close() !!}
			@if(count($thread))
				{{ count($thread) }} {{ trans_choice('site.commentary',count($thread)) }}
				@include('partials.message.message',['parent_id'=>null])
			@else
				Personne n'a encore commenté.
			@endif
			<div class="modal-footer">

			</div>
		</div>        
	</div>
</div>