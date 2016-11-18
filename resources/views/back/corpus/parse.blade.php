@extends('front.template-iframe')

@section('css')
<style>
table.conll tr td:nth-child(2),table.conll tr td:nth-child(6){
    border-left: solid 1px grey;
}
table.conll tr.last {
	border-bottom: double 1px grey;
}
table.stats {
	font-size:90%;
}
table.stats th, table.stats td{
	text-align:center;
}
table.stats td.identic{
	color: #fff;
	background-color: #5cb85c;
	border-color: #4cae4c;
}
table.stats td.different{
	color: #fff;
	background-color: #d9534f;
	border-color: #d43f3a;
}
</style>
@stop
@section('main')
@if(isset($result))
	{!! Form::open(['url' => 'corpus/save-parse', 'method' => 'post', 'role' => 'form', 'target'=>'post-save-parse']) !!}
		
	    <div class="col-md-10 col-md-offset-1">
	    <h3>Output Talismane</h3>
			<textarea style="width:100%;height:500px;" name="text">{!! $result !!}</textarea>
			<a href="{{ url('corpus/file?file=').$talismane->output_file }}" class="btn btn-info">Get raw output file</a>
			<a data-toggle="collapse" data-target="#command_talismane" class="btn btn-info">Show command</a>
			<div id="command_talismane" class="collapse">{{ $talismane->command }}</div><br/>	
	    </div>
	    <input type="hidden" value="{{ $talismane->output_file }}" name="files[]" />
	    <div class="col-md-10 col-md-offset-1">
	    	<h3>Output Grew</h3>
			<textarea style="width:100%;height:500px;" name="text">{!! $result_grew !!}</textarea>
			@foreach($grew->files as $key=>$file_name)
				<a href="{{ url('corpus/file?file=').$file_name }}" class="btn btn-info">Get output file {{ $key }}</a>
			@endforeach
			
			<a data-toggle="collapse" data-target="#command_grew" class="btn btn-info">Show command</a>
			<div id="command_grew" class="collapse">
			@foreach($grew->commands as $key=>$command)
				{{ $key }} :<br/>
				{{ $command }}<br/>
			@endforeach			
			</div>
	    </div>
	    <input type="hidden" value="{{ $grew->output_file }}" name="files[]" />
	{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...') !!}	    
	{!! Form::submit('Sauvegarder en base', null,['class' => 'btn btn-success']) !!}
	{!! Form::close() !!}
<iframe name="post-save-parse" style="width:100%;" frameborder="0" scrolling="no" onload="resizeIframeAgain(this);resizeIframe(this);">
</iframe>
<?php
$stats = array();
$stats['pos'] = array();
$stats['relation'] = array();
$stats['category'] = array();
$map_cat_pos = Array(
	'A' => Array('ADJ','ADJWH'),
	'ADV' => Array('ADV','ADVWH'),
	'C' => Array('CC','CS'),
	'CL' => Array('CLS','CLR','CLO'),
	'D' => Array('DET','DETWH'),
	'ET' => Array('ET'),
	'I' => Array('I'),
	'N' => Array('NC','NPP'),
	'P' => Array('P'),
	'P+D' => Array('P+D'),
	'P+PRO' => Array('P+PRO'),
	'PONCT' => Array('PONCT'),
	'PREF' => Array('PREF'),
	'PRO' => Array('PRO','PROREL','PROWH'),
	'V' => Array('V','VPP','VINF','VS','VPR','VIMP'),
);
$map_pos_cat = array();
foreach($map_cat_pos as $cat=>$poss){
	foreach($poss as $pos){
		$map_pos_cat[$pos]=$cat;
	}
}

$poss = App\Models\CatPos::where('parent_id','!=',0)->orderBy('slug')->get();
$relations = App\Models\Annotation::select('slug')->join('relations','annotations.relation_id','=','relations.id')->orderBy('slug')->groupBy('relation_id')->whereNotIn('slug', ['not-exists', 'UNK'])->get();
foreach($map_cat_pos as $cat=>$pos){
	$stats['category'][$cat]=array();
	foreach($map_cat_pos as $cat2=>$pos){
		$stats['category'][$cat][$cat2]=0;
	}
}
foreach($poss as $pos){
	$stats['pos'][str_replace('_pos','',$pos['slug'])] = array();
	foreach($poss as $pos2){
		$stats['pos'][str_replace('_pos','',$pos['slug'])][str_replace('_pos','',$pos2['slug'])]=0;
	}
}
foreach($relations as $relation){
if(!$relation['slug']) continue;
	$stats['relation'][$relation['slug']] = array();
	foreach($relations as $relation2){
		if(!$relation2['slug']) continue;
		$stats['relation'][$relation['slug']][$relation2['slug']]=0;
	}
}
	$common_governors = 0;
	$diff_governors = 0;
	$nb_tokkens = 0;
?>
@foreach($array_conll_talismane as $index_sentence=>$sentence)

	<?php
	$diff=false;

	foreach($sentence as $index_word=>$word){
		if(isset($array_conll_talismane[$index_sentence][$index_word])&&isset($array_conll_grew[$index_sentence][$index_word])){

			$pos_talismane = $array_conll_talismane[$index_sentence][$index_word]['pos_id'];
			$pos_grew = $array_conll_grew[$index_sentence][$index_word]['pos_id'];
			$category_grew = $array_conll_grew[$index_sentence][$index_word]['category_id'];
			if(!isset($map_pos_cat[$pos_talismane])){
				echo "POS INCONNU :".$pos_talismane."<br/>";
				continue;
			}
			if(!isset($map_pos_cat[$pos_grew])){
				echo "POS INCONNU :".$pos_grew."<br/>";
				continue;
			}			
			$stats['category'][$map_pos_cat[$pos_talismane]][$map_pos_cat[$pos_grew]] = $stats['category'][$map_pos_cat[$pos_talismane]][$map_pos_cat[$pos_grew]] +1;
			
			if(!isset($stats['pos'][$pos_talismane]))
				$stats['pos'][$pos_talismane] = array();

			if(!isset($stats['pos'][$pos_talismane][$pos_grew]))
				$stats['pos'][$pos_talismane][$pos_grew] = 0;
			
			$stats['pos'][$pos_talismane][$pos_grew] = $stats['pos'][$pos_talismane][$pos_grew] + 1;



			$relation_talismane = $array_conll_talismane[$index_sentence][$index_word]['relation_id'];
			$relation_grew = $array_conll_grew[$index_sentence][$index_word]['relation_id'];

			if(!isset($stats['relation'][$relation_talismane]))
				$stats['relation'][$relation_talismane] = array();
			if(!isset($stats['relation'][$relation_talismane][$relation_grew]))
				$stats['relation'][$relation_talismane][$relation_grew] = 0;

			$stats['relation'][$relation_talismane][$relation_grew] = $stats['relation'][$relation_talismane][$relation_grew] + 1;

			if($array_conll_talismane[$index_sentence][$index_word]['pos_id']=="PONCT") continue;

			$nb_tokkens++;

			if($array_conll_talismane[$index_sentence][$index_word]['relation_id']=="root" && $array_conll_grew[$index_sentence][$index_word]['relation_id']=="_") continue;

			if($array_conll_talismane[$index_sentence][$index_word]['pos_id']!=$array_conll_grew[$index_sentence][$index_word]['pos_id']){
				$array_conll_talismane[$index_sentence][$index_word]['diff_pos'] = true;
				$diff=true;
			}
			if($array_conll_talismane[$index_sentence][$index_word]['relation_id']!=$array_conll_grew[$index_sentence][$index_word]['relation_id']){
				$array_conll_talismane[$index_sentence][$index_word]['diff_relation'] = true;
				$diff=true;
			}

			if($array_conll_talismane[$index_sentence][$index_word]['governor_position']!=$array_conll_grew[$index_sentence][$index_word]['governor_position']){
				$array_conll_talismane[$index_sentence][$index_word]['diff_governor'] = true;
				$diff=true;
				if($array_conll_talismane[$index_sentence][$index_word]['relation_id']==$array_conll_grew[$index_sentence][$index_word]['relation_id']){
					$diff_governors++;
				}				
			} else {				
				$common_governors++;
			}
		} else {
			$diff=false;
			break;
		}
	}
	?>
@endforeach
<div>
	Common governors = {{ $common_governors }} / {{ $nb_tokkens }}.
</div>
<div>
	Same rel, governors diff = {{ $diff_governors }} / {{ $nb_tokkens }}.
</div>
<h3>Differences by category</h3> 
<table class="table table-bordered stats">
<tr><th></th>
@foreach($stats['category'] as $col => $stat)
<th style="padding:0 3px;">{{ $col }}</th>
@endforeach
</tr>
@foreach($stats['category'] as $col => $stat)
<tr><th style="padding:0 3px;">{{ $col }}</th>
	@foreach($stat as $row => $value)
		<?php
		$class="";
		if($row==$col) $class="identic";
		elseif($row!=$col && $value!=0) $class="different";
		else $value="";
		?>
		<td class="{{ $class }}">{{ $value }}</td>
	@endforeach
</tr>
@endforeach
</table>
<h3>Differences by POS</h3> 
<table class="table table-bordered stats">
<tr><th></th>
@foreach($stats['pos'] as $col => $stat)
<th style="padding:0 3px;">{{ $col }}</th>
@endforeach
</tr>
@foreach($stats['pos'] as $col => $stat)
<tr><th style="padding:0 3px;">{{ $col }}</th>
	@foreach($stat as $row => $value)
		<?php
		$class="";
		if($row==$col) $class="identic";
		elseif($row!=$col && $value!=0) $class="different";
		else $value="";
		?>
		<td class="{{ $class }}">{{ $value }}</td>
	@endforeach
</tr>
@endforeach
</table>

<h3>Differences by relation</h3> 
<table class="table table-bordered stats">
<tr><th></th>
@foreach($stats['relation'] as $col => $stat)
<th style="padding:0 3px;">{{ $col }}</th>
@endforeach
</tr>
@foreach($stats['relation'] as $col => $stat)
<tr><th style="padding:0 3px;">{{ $col }}</th>
	@foreach($stat as $row => $value)
		<?php
		$class="";
		if($row==$col) $class="identic";
		elseif($row!=$col && $value!=0) $class="different";
		else $value="";
		?>
		<td class="{{ $class }}">{{ $value }}</td>
	@endforeach
</tr>
@endforeach
</table>

<h3>Details</h3>
<table class="table table-condensed conll" style="width:50%">
<tr>
	<th>index</th><th colspan="4">talismane</th><th colspan="4">grew</th>
</tr>
@foreach($array_conll_talismane as $index_sentence=>$sentence)
	<tr>
		<td colspan="9">phrase {{ $index_sentence }}</td>
	</tr>
	<?php
	$nb_words = count($sentence);
	?>
	@foreach($sentence as $index_word=>$word)
		<?php

		if(isset($array_conll_grew[$index_sentence][$index_word]) && isset($array_conll_talismane[$index_sentence][$index_word]) ){
			$word_grew = $array_conll_grew[$index_sentence][$index_word];
			$word_talismane = $array_conll_talismane[$index_sentence][$index_word];
		?>
		<tr class="{{ ($index_word == $nb_words)?'last':'' }}">
			<td>{{ $index_word }}</td><td>{{ $word_talismane['word'] }}</td><td class="{{ isset($word_talismane['diff_pos'])?'danger':'' }}">{{ $word_talismane['pos_id'] }}</td><td class="{{ isset($word_talismane['diff_governor'])?'danger':'' }}">{{ $word_talismane['governor_position'] }}</td><td class="{{ isset($word_talismane['diff_relation'])?'danger':'' }}">{{ $word_talismane['relation_id'] }}</td>
			<td>{{ $word_grew['word'] }}</td><td class="{{ isset($word_talismane['diff_pos'])?'danger':'' }}">{{ $word_grew['pos_id'] }}</td><td class="{{ isset($word_talismane['diff_governor'])?'danger':'' }}">{{ $word_grew['governor_position'] }}</td><td class="{{ isset($word_talismane['diff_relation'])?'danger':'' }}">{{ $word_grew['relation_id'] }}</td>
			<td>{{ $word_talismane['pos_id'] }} {{ $word_grew['pos_id'] }}</td>
		</tr>
		<?php
		} else {
			echo '<tr><td>Erreur mot '.$index_word.'</td></tr>';
		}
		?>			
	@endforeach
@endforeach
</table>
@endif
@stop