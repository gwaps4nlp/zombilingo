@extends('back.template')

@section('main')

<?php 
$precisions = [];
$recalls = [];
$fscores = [];
?>
{!! Form::open(['url' => 'corpus/compare', 'method' => 'get', 'role' => 'form']) !!}
	<div class="form-group  {{ $errors->has('date') ? 'has-error' : '' }}" id="date">
		<label for="start_date" class="control-label">Statistics until the date (including) :</label>
		{!! $errors->first('date', '<small class="help-block">:message</small>') !!}
			<input class="datepicker" name="date" value="{{ $date }}" />
	</div>
{!! Form::submit('Submit', null,['class' => 'btn btn-success']) !!}	
{!! Form::close() !!}
  
<h3>Rate of correct answers of the players (evaluation corpus)</h3>
<table class="table table-bordered table-striped" style="width:60%">
<tr><td></td><th>find dependent</th><th>find head</th><th>total</th></tr>
<tr>
	<th>relation<sub>preannot</sub>=relation<sub>evaluation</sub></th>
	<td title="{{ $rate_correct_answers['trouverDependant']->correct }}/{{ $rate_correct_answers['trouverDependant']->total }}">{{ round($rate_correct_answers['trouverDependant']->correct/$rate_correct_answers['trouverDependant']->total*100,1) }}%</td>
	<td title="{{ $rate_correct_answers['trouverTete']->correct }}/{{ $rate_correct_answers['trouverTete']->total }}">{{ round($rate_correct_answers['trouverTete']->correct/$rate_correct_answers['trouverTete']->total*100,1) }}%</td>
	<td title="{{ $rate_correct_answers['trouverTete']->correct+$rate_correct_answers['trouverDependant']->correct }}/{{ $rate_correct_answers['trouverTete']->total+$rate_correct_answers['trouverDependant']->total }}">{{ round(($rate_correct_answers['trouverTete']->correct+$rate_correct_answers['trouverDependant']->correct)/($rate_correct_answers['trouverTete']->total+$rate_correct_answers['trouverDependant']->total)*100,1) }}%</td>
</tr>
<tr>
	<th>relation<sub>preannot</sub>&ne;relation<sub>evaluation</sub></th>
	<td title="{{ $rate_correct_answers_incorrect_relation['trouverDependant']->correct }}/{{ $rate_correct_answers_incorrect_relation['trouverDependant']->total }}">{{ round($rate_correct_answers_incorrect_relation['trouverDependant']->correct/$rate_correct_answers_incorrect_relation['trouverDependant']->total *100,1) }}%</td>
	<td title="{{ $rate_correct_answers_incorrect_relation['trouverTete']->correct }}/{{ $rate_correct_answers_incorrect_relation['trouverTete']->total }}">{{ round($rate_correct_answers_incorrect_relation['trouverTete']->correct/$rate_correct_answers_incorrect_relation['trouverTete']->total *100,1) }}%</td>
	<td title="{{ $rate_correct_answers_incorrect_relation['trouverTete']->correct+$rate_correct_answers_incorrect_relation['trouverDependant']->correct }}/{{ $rate_correct_answers_incorrect_relation['trouverTete']->total+$rate_correct_answers_incorrect_relation['trouverDependant']->total }}">{{ round(($rate_correct_answers_incorrect_relation['trouverTete']->correct+$rate_correct_answers_incorrect_relation['trouverDependant']->correct)/($rate_correct_answers_incorrect_relation['trouverTete']->total+$rate_correct_answers_incorrect_relation['trouverDependant']->total )*100,1) }}%</td>
</tr>
</table>
<div id="container-precision"></div>
<div id="container-recall"></div>
<div id="container-fscore"></div>
{!! Html::image('img/test.svg', 'alt', array( 'width' => "50%" )) !!}
<table class="table table-bordered table-striped stats">
	<thead>
	<tr>
		<td rowspan="2">relation</td>
		@foreach($parsers as $parser_id=>$parser_name)
			<td colspan="3">
			<?php
			$precisions[$parser_name] = ['name'=>$parser_name,'data'=>[]];
			$recalls[$parser_name] = ['name'=>$parser_name,'data'=>[]];
			$fscores[$parser_name] = ['name'=>$parser_name,'data'=>[]];
			?>
			{{ $parser_name }}<br/>
			UAS = {{ $scores[$parser_id]->uas }}, LAS = {{ $scores[$parser_id]->las }}
			</td>
		@endforeach
		<tr>
		@foreach($parsers as $parser)
			<td>precision</td>
			<td>recall</td>
			<td>F1</td>
		@endforeach
		</tr>
	</tr>
	</thead>
	<tbody>
	<tr class="total_parser">
		<td>Total<br/>(except ponct)</td>
		@foreach($stats_parser_total as $stat)
			<td><?php
				$precision = ($stat->total_parser>0)?round($stat->correct/$stat->total_parser,3):0;
				echo '<span title="'.$stat->correct.'/'.$stat->total_parser.'">'.$precision.'</span>';

			?></td>
			<td><?php
				$recall = round($stat->correct/$stat->total_control,3);
				echo '<span title="'.$stat->correct.'/'.$stat->total_control.'">'.$recall.'</span>';
			?></td>
			<td>
			<?php
				$fscore = ($precision>0)? 2 * $precision * $recall / ($precision + $recall):0;
				echo '<span>'.round($fscore,3).'</span>';
			?>			
			</td>		
		@endforeach
	</tr>
	<tr class="total_parser">
		<td>Total<br/>(except ponct &amp; root)</td>
		@foreach($stats_parser_except_root as $stat)
			<td><?php
				$precision = ($stat->total_parser>0)?round($stat->correct/$stat->total_parser,3):0;
				echo '<span title="'.$stat->correct.'/'.$stat->total_parser.'">'.$precision.'</span>';

			?></td>
			<td><?php
				$recall = round($stat->correct/$stat->total_control,3);
				echo '<span title="'.$stat->correct.'/'.$stat->total_control.'">'.$recall.'</span>';
			?></td>
			<td>
			<?php
				$fscore = ($precision>0)? 2 * $precision * $recall / ($precision + $recall):0;
				echo '<span>'.round($fscore,3).'</span>';
			?>			
			</td>		
		@endforeach
	</tr>
	<tr class="total_parser">
		<td>Total<br/>(playable relations)</td>
		@foreach($stats_parser_playable as $stat)
			<td><?php
				$precision = ($stat->total_parser>0)?round($stat->correct/$stat->total_parser,3):0;
				echo '<span title="'.$stat->correct.'/'.$stat->total_parser.'">'.$precision.'</span>';
				$precisions[$stat->parser_name]['data'][]=$precision;
			?></td>
			<td><?php
				$recall = round($stat->correct/$stat->total_control,3);
				$recalls[$stat->parser_name]['data'][]=$recall;
				echo '<span title="'.$stat->correct.'/'.$stat->total_control.'">'.$recall.'</span>';
			?></td>
			<td>
			<?php
				$fscore = ($precision>0)? round(2 * $precision * $recall / ($precision + $recall),3):0;
				$fscores[$stat->parser_name]['data'][]=$fscore;
				echo '<span>'.$fscore.'</span>';
			?>			
			</td>		
		@endforeach
	</tr>		
	@foreach($stats as $relation_name=>$stat)
	<tr>
		<td>{{ $relation_name }}</td>
		@foreach($parsers as $parser_id=>$parser_name)
			<td><?php
			if($stat[$parser_id]['total_parser']>0)
				echo '<span title="'.$stat[$parser_id]['correct'].'/'.$stat[$parser_id]['total_parser'].'">'.$stat[$parser_id]['precision'].'</span>';
			else
				echo '-';
			$precisions[$parser_name]['data'][] = round($stat[$parser_id]['precision'],3);
			?></td>
			<td><?php
				echo '<span title="'.$stat[$parser_id]['correct'].'/'.$stat[$parser_id]['total_control'].'">'.$stat[$parser_id]['recall'].'</span>';
				$recalls[$parser_name]['data'][] = round($stat[$parser_id]['recall'],3);
			?></td>
			<td>
			<?php
				echo '<span>'.$stat[$parser_id]['fscore'].'</span>';
				$fscores[$parser_name]['data'][] = round($stat[$parser_id]['fscore'],3);
			?>			
			</td>
		@endforeach
	</tr>
	@endforeach
	</tbody>
</table>

<h3>Confusion Matrix</h3>
{!! Form::open(['url' => 'corpus/compare', 'method' => 'get', 'role' => 'form']) !!}
{!! Form::select('parser_id', $parsers, $parser_id, ['placeholder' => 'Select a parser...']); !!}
{!! Form::submit('OK', null,['class' => 'btn btn-success']) !!}
{!! Form::close() !!}
<table class="table table-bordered table-striped stats_by_relation">
	<thead>
		<tr class="header_relation">
			<td></td>
			@foreach($relations_confusion_matrix as $relation_name=>$relation_id)
				<td><div>{{ $relation_name }}</div></td>
			@endforeach
		</tr>
	</thead>
	<tfoot>
		<tr class="header_relation">
			<td></td>
			@foreach($relations_confusion_matrix as $relation_name=>$relation_id)
				<td><div>{{ $relation_name }}</div></td>
			@endforeach
		</tr>
	</tfoot>
	@foreach($relations_confusion_matrix as $relation_name=>$control_relation_id)
		<tr><td>{{ $relation_name }}</td>
		@foreach($relations_confusion_matrix as $relation_name=>$parser_relation_id)
			<?php
				$class="";
				if(isset($stats_by_relation[$control_relation_id][$parser_relation_id])){
					if($control_relation_id==$parser_relation_id)
						$class= "identical";
					else
						$class= "different";
				}
			?>
			<td class="{{ $class }}">{!!
			isset($stats_by_relation[$control_relation_id][$parser_relation_id])?
			'<span class="coef_matrix" data-params="corpus_id=16&control_relation_id='.$control_relation_id.'&parser_relation_id='.$parser_relation_id.'&parser_id='.$parser_id.'">'.$stats_by_relation[$control_relation_id][$parser_relation_id].'</span>':"" 
			!!}</td>
		@endforeach
		</tr>
	@endforeach
</table>
<?php
$datas = [];
$datas['precision'] = [];
$datas['recall'] = [];
$datas['fscore'] = [];
foreach($precisions as $p) $datas['precision'][]=$p;
foreach($recalls as $r) $datas['recall'][]=$r;
foreach($fscores as $score) $datas['fscore'][] =$score;

?>
@stop

@section('scripts')
    {!! Html::script('js/highcharts.js') !!}
    {!! Html::script('js/bootstrap-datepicker.js') !!}
<script>

	$('.datepicker').datepicker({
	    format: 'yyyy-mm-dd',
	    autoclose: true,
	    todayBtn: "linked",
	});

$(function () {
	$(document).on('click', '.coef_matrix', function(event){
		var target = $( event.target );
		if ($('#contentModal').length>0)
			$('#contentModal').remove();	
		target.parents('tr').after( '<tr><td id="contentModal"></td></tr>' );
		$('#contentModal').load(base_url+'corpus/compare-conll/?'+target.attr("data-params"));
    });
    $('#container-fscore').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'F-score'
        },
        xAxis: {
            categories: {!! json_encode($a_relations) !!},
            crosshair: true
        },
        yAxis: {
            min: 0,
            max: 1.0,
            title: {
                text: 'F-Score'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.3f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0
            }
        },
        series: {!! json_encode($datas['fscore']) !!}
    });
    $('#container-precision').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Precision'
        },
        xAxis: {
            categories: {!! json_encode($a_relations) !!},
            crosshair: true
        },
        yAxis: {
            min: 0,
            max: 1.0,
            title: {
                text: 'Precision'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.3f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0
            }
        },
        series: {!! json_encode($datas['precision']) !!}
    });
    $('#container-recall').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Recall'
        },
        xAxis: {
            categories: {!! json_encode($a_relations) !!},
            crosshair: true
        },
        yAxis: {
            min: 0,
            max: 1.0,
            title: {
                text: 'Recall'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.3f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0
            }
        },
        series: {!! json_encode($datas['recall']) !!}
    });
});
</script>
@stop


@section('css')
{!! Html::style('css/bootstrap-datepicker3.css') !!}
<style>

body {
	color:black;
}
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
table.stats_by_relation thead tr td div, table.stats_by_relation tfoot tr td div {
	transform: rotate(-90deg);
	position: relative;
	top: 32px;	
}
table.stats_by_relation tr td:nth-child(n+2) {
	width:50px;
}
table.stats_by_relation thead tr, table.stats_by_relation tfoot tr {
	height:70px;
}
table.stats_by_relation {
	font-size:70%;
	table-layout: fixed;
}
table.stats_by_relation th, table.stats_by_relation td{
	text-align:center;
}
table.stats_by_relation tr td.identical,table.stats_by_relation tr td.different{
	cursor: pointer
}
table.stats_by_relation tr td.identical{
	color: #fff;
	background-color: #5cb85c;
	border-color: #4cae4c;
}
table.stats_by_relation tr td.different{
	color: #fff;
	background-color: #d9534f;
}
table.table-striped tr.total_parser td {
	background-color:#d8fbd8;
}
table.stats.table-striped tbody tr td:nth-child(3n+1) {
	border-right-style: double;
	border-right-width: 3px;
	border-right-color: #bbb;
}
</style>
@stop