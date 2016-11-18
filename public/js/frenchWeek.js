$('document').ready(function(){
	$.ajaxSetup({
        data: {
            user_token: $.cookie('user_cookie')
        }
    });
	
	
	var graph1data = [];

	$.each(frenchWeekRanking, function () {
        	graph1data.push({
        	name: this.pseudo_user,
            y: parseInt(this.points)
        });
    });

	var options1 = {
        chart: {
            type: 'column',
            renderTo:'graph1'
        },
        title: {
            text: 'Score par utilisateur'
        },
        xAxis: {
            type:'category'
        },
        yAxis: {
            title: {
                text: 'Points'
            }
        },
        series: [{
                    name: 'Points',
                    colorByPoint: true,
                    data: graph1data
                }],
    }
    
    var chart1 = new Highcharts.Chart(options1);

    var graph2data = [];

    $.each(frenchWeekAnnot, function () {
            graph2data.push({
            name: this.pseudo_user,
            y: parseInt(this.count)
        });
    });

    var options2 = {
        chart: {
            type: 'column',
            renderTo:'graph2'
        },
        title: {
            text: 'Nombre d\'annotations par utilisateur'
        },
        xAxis: {
            type:'category'
        },
        yAxis: {
            title: {
                text: 'Annotations'
            }
        },
        series: [{
                    name: 'Annotations',
                    colorByPoint: true,
                    data: graph2data
                }],
    }
    
    var chart2 = new Highcharts.Chart(options2);
 	
 	
});
