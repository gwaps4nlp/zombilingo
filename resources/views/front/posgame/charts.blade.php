
@foreach($annotations as $annotation)
<?php
$ids[] = $annotation->id;
?>
<div>
    <!--/* real chart */-->
    <!--<div id="chart_{{ $annotation->id }}" style="min-width:300px"></div>-->

    <!--test chart-->
    <div id="chart_post_{{ $annotation->id }}" style="padding-left: 15px; padding-top:15px; right:15px; margin-right: 50px; display:block">
    </div>

</div>
@endforeach


<script type="text/javascript" src="https://raw.github.com/highslide-software/highcharts.com/v2.1.9/js/highcharts.src.js">
var cur = 0;
$.each(ids, function (key, valeur) {

    var data = {!! $stats_array !!};
    var figure_index=  {!! $figures_array !!};
    console.log("figure_index");
    console.log(figure_index    );
    console.log("json data");
    data_cur=JSON.stringify(data[cur]);
    console.log(data[cur]);
    nb_data=data[cur].length;
    /* create an array of nb_data size, in answer_index[cur] put {y: 13, marker: {symbol: 'url(img/level/thumbs/z4.png)'}} */
    create_charts_2(valeur, data[cur],figure_index[cur]); //, categories_array[cur]);
    console.log("les données");
    console.log(cur);
    console.log(data[cur]);
    cur = cur + 1;
});
function create_charts_2(valeur, data_columns, figure_index) { //, var_categories) {
    /* doit prendre en paramètre les valeurs des series pour chaque $id d'annotation */
    /* for each chart id element, create appropriate chart */
    console.log("dans create charts");
    console.log(figure_index);
    console.log("dans create charts");
    console.log(data_columns)
    console.log(JSON.stringify(data_columns));
    $("#chart_" + valeur).highcharts({
        chart: {
            type: 'column',
            backgroundColor: 'transparent',
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
                tickWidth: 0,
                lineWidth: 0,
                categories: [],
                y:0,
                labels: {
                    x: 5,
                    style: {
                        color: '#00000',
                        fontWeight: 'bold',
                        fontSize: '15px'
                    }
                },
                title: {
                    text: ''
                }
            },
        yAxis: {
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        exporting: {
            buttons: {
                contextButton: {
                    enabled: false
                }
            }},
        plotOptions: {
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
        },
        series: [
            {
                name: "Catégorie",
                colorByPoint: true,
                data: data_columns,
                pointWidth: 110,
                stacking: 'normal',
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.1f}%'
                }
            }, {
                name: 'New York',
                type: 'scatter',
//                [0,{y: 13, marker: {symbol: 'url(img/level/thumbs/z4.png)'}}]
//                 ["y: 13, marker: {symbol: .../level/thumbs/z4.png)'}"]
//                 ["{y: 13, marker: {symbol:...level/thumbs/z4.png)'}}", "0"]
//                [["y: 13, marker: {symbol: 'url(img\/level\/thumbs\/z4.png)'}","0","0"],["y: 13, marker: {symbol: 'url(img\/level\/thumbs\/z4.png)'}","0"],["y: 13, marker: {symbol: 'url(img\/level\/thumbs\/z4.png)'}"],["y: 13, marker: {symbol: 'url(img\/level\/thumbs\/z4.png)'}","0"],["y: 13, marker: {symbol: 'url(img\/level\/thumbs\/z4.png)'}"]]
                data: figure_index}
        ]

    });
}
;

</script>