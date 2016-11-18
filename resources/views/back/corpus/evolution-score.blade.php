@extends('back.template')

@section('main')
<?php
$data = [];

// $data= [];
?>
<h1>F-score = f(number of answers by annotation)</h1>
@foreach($scores_relation as $relation_id => $scores)
<div id="container-{{ $relation_id }}"></div>
<?php 
    $data[$relation_id] = [];
    $current = -1;
?>
    @foreach($scores as $score)
    	<?php
        if($current!=$score->answers_by_annot){
            $data[$relation_id][] = [floatval($score->answers_by_annot) , floatval($score->fscore)];
    		$names[$relation_id] = $score->relation_name;
            $current=$score->answers_by_annot;
        }
    	?>
    @endforeach
@endforeach

@stop

@section('scripts')
    {!! Html::script('js/highcharts.js') !!}

<script>

	$('.datepicker').datepicker({
	    format: 'yyyy-mm-dd',
	    autoclose: true,
	    todayBtn: "linked",
	});
	
$(function () {
    @foreach($scores_relation as $relation_id => $scores)
        $('#container-{{ $relation_id }}').highcharts({
            chart: {
                margin: [70, 50, 60, 80],
            },
            title: {
                text: "{{ $names[$relation_id] }}"
            },
            tooltip: {
                headerFormat: '<table>',
                pointFormat: '<tr><td style="padding:0">Fscore : <b>{point.y:.3f}</b></td></tr>' +
                    '<tr><td style="color:{series.color};padding:0">answers (average) : <b>{point.x:.2f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },                
            xAxis: {
                title: {
                    text: 'Number of answers by annotation'
                },                
                gridLineWidth: 1,
                minPadding: 0.02,
                maxPadding: 0.02,
                min: 0.0,
                max: 5.0,
                maxZoom: 60
            },
            yAxis: {
                title: {
                    text: 'Fscore'
                },
                minPadding: 0.02,
                maxPadding: 0.02,
                maxZoom: 60,
                min: 0.5,
                max: 1.0,                
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },

            series: [{
                data: {!! json_encode($data[$relation_id]) !!} 
            }]
        }); 

    @endforeach
});
</script>    
@stop