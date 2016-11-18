<?php
$annotation = $annotations->first()->sentence->annotations->first();
$sentence = $annotations->first()->sentence;
$parsers = [];
foreach($sentence->annotations as $annotation){
	foreach($annotation->parsers as $parser){
		if(!isset($parsers[$parser->id]))
			$parsers[$parser->id]=$parser;
	}

}
?>

<table class="table table-striped table-bordered">
<tr><th colspan="2"></th><th colspan="3">Control</th>

@foreach($parsers as $parser)
	<th colspan="3">{{ $parser->name }}</th>
@endforeach

<th colspan="3">zombilingo</th>
</tr>
	@foreach($annotations as $annot)

		<?php
		$conll = [];
		$annots = $annot->sentence->annotations()->with('parsers')->get();

		?>
<tr><td colspan="14">{{ $annot->sentence->sentid }}<br/>{{ $annot->sentence->content }}</td></tr>

		@foreach($annots as $annotation)
			@if($annotation->source_id==1)
					<?php
					$conll[$annotation->word_position]['ref']=$annotation;
					?>	
			@elseif($annotation->source_id==3)
		
				@foreach($annotation->parsers as $parser)
					<?php
					$conll[$annotation->word_position][$parser->id]=$annotation;
					?>				
				@endforeach

			@endif
			<?php 
			if($annotation->source_id!=1 && $annotation->best){
				$conll[$annotation->word_position]['ZombiLingo']=$annotation;
			}
			?>
		@endforeach

		@foreach($conll as $word_position=>$annotation)
		<tr>
		@if(isset($annotation['ref']))
			<td>{{ $annotation['ref']->pos_id }}</td>
			<td>{{ $annotation['ref']->relation_name }}</td>
			<td>{{ $annotation['ref']->governor_position }}</td>
		@endif
		@foreach($parsers as $parser)
			@if(isset($annotation[$parser->id]))
				<td>{{ $annotation[$parser->id]->word_position }}</td>
				<td>{{ $annotation[$parser->id]->word }}</td>			
				<td>{{ $annotation[$parser->id]->pos_id }}</td>
				<td>{{ $annotation[$parser->id]->relation_name }}</td>
				<td>{{ $annotation[$parser->id]->governor_position }}</td>
			@else
			ERREUR !! {{ $annotation['ref']->id }} {{ $parser->id }}
			@endif
		@endforeach	
		@if(isset($annotation['ZombiLingo']))
			<?php 
				// if($annotation['ref']->pos_id == $annotation['ZombiLingo']->pos_id)
				// 	$class="identical";
				// else
				// 	$class="different";
			$class="identical";
			?>
			<td class="{{ $class }}">{{ $annotation['ZombiLingo']->pos_id }}</td>
			<?php 
				// if($annotation['ref']->relation_name == $annotation['ZombiLingo']->relation_name)
				// 	$class="identical";
				// else
				// 	$class="different";
			$class="identical";	
			?>
			<td class="{{ $class }}">{{ $annotation['ZombiLingo']->relation_name }}</td>
			<td>{{ $annotation['ZombiLingo']->governor_position }}</td>
		@else
			<td>-</td>
			<td>-</td>
			<td>-</td>
		@endif


		

		</tr>
		@endforeach
	@endforeach
</table>