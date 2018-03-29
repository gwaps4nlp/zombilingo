<h1>Exports history</h1>
@if(count($exported_corpuses)>0)
	<table class="table table-striped"><thead><tr><th>Corpus</th><th>Utilisateur</th><th>Date</th><th>Action</th></tr></thead>
	<tbody>
	@foreach($exported_corpuses as $exported_corpus)
		<?php 
		if(!$exported_corpus->corpus) continue;
		?>
		<tr>
		<td>
			@if($exported_corpus->corpus_id && $exported_corpus->corpus)
				{{ $exported_corpus->corpus->name }}
			@else
				{{ $exported_corpus->type }}
			@endif
		</td>
		<td>{{ $exported_corpus->user->username }}</td>
		<td>{{ $exported_corpus->created_at }}</td>
		<td><a href="{{ url('asset/conll').'?exported_corpus_id='.$exported_corpus->id }}"><i class="fa fa-download"></i></a></td>
		</tr>
	@endforeach
	</tbody>
	</table>
@else
	Aucun export dans l'historique.
@endif