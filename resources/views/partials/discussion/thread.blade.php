<div class="container-thread">
  {!! Form::open(['url' => 'report/send', 'method' => 'post', 'role' => 'form', 'class'=>'form-message', 'data-id'=>$entity->id, 'data-type'=>'']) !!} 
    <div class="form-group">
        <textarea class="message" name="content" type="text" style="resize:auto;padding:7px;overflow: hidden; word-wrap: break-word; min-height: 60px; height: 60px;width: 90%;color:#3C3C3C" placeholder="Discute de la réponse avec d'autres joueurs."></textarea><br/>
        <input type="checkbox" name="follow-thread" value="1" /> Suivre la discussion <a target="_blank" href="{{ url('faq#follow-thread') }}" class="scroll badge-help badge badge-pill badge-success">?</a>
    </div>
  <input type="hidden" name="entity_id" value="{{ $entity->id }}" />
	<input type="hidden" name="entity_type" value="{{ get_class($entity) }}" />
    <button type="submit" disabled="disabled" class="btn btn-success submitMessage">Publier</button>
    <button type="button" class="btn btn-danger btn-default cancelReport">{{ trans('site.cancel') }}</button>
  {!! Form::close() !!}
@if(count($thread))
	{{ count($thread) }} {{ trans_choice('site.commentary',count($thread)) }}
	@include('partials.discussion.message',['parent_id'=>null])
@else
	Personne n'a encore commenté.
@endif
</div>