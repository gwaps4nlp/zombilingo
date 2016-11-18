@extends('back.template')

@section('main')
<?php
$data = [];
$categories = [];
// $data= [];
?>
<div id="container-graph"></div>
@foreach($confidence_by_user as $confidence)
	<?php
		$categories[] = $confidence->username;
		$data[] = [$confidence->total_annotations, $confidence->success_rate*100];
	?>
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
    $('#container-graph').highcharts({
        chart: {
            type: 'scatter',
            margin: [70, 50, 60, 80],
        },
        title: {
            text: 'Rate of correct answers versus the number of annotations produced.'
        },
        tooltip: {
            headerFormat: '<table>',
            pointFormat: '<tr><td style="padding:0">Rate of correct answers : <b>{point.y:.2f}%</b></td></tr>' +
                '<tr><td style="color:{series.color};padding:0">Number of annotations : <b>{point.x:.0f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },                
        xAxis: {
            gridLineWidth: 1,
            minPadding: 0.2,
            maxPadding: 0.2,
            maxZoom: 60
        },
        yAxis: {
            title: {
                text: 'Value'
            },
            minPadding: 0.2,
            maxPadding: 0.2,
            maxZoom: 60,
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
        	categories: {!! json_encode($categories) !!} ,
            data: {!! json_encode($data) !!} 
        }]
    });	
});
</script>    
@stop