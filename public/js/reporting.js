$('document').ready(function(){
	$.ajaxSetup({
        data: {
            user_token: $.cookie('user_cookie')
        }
    });
	
	$( "#select-user" ).on('change', function(){
        $( "#select-user" ).parent('form').submit();
        $.ajax({
            url : base_url + 'admin/reporting',
            method : 'POST',
            dataType: "json",
            data : {
                id_user : $( "#select-user" ).val()
            },
            success : function(response){
            	graph4data = [];
              	data = response;
              	$.each(data, function () {
        			graph4data.push({
        				name: this.affichage_phenomene,
            			y: parseInt(this.count)
        			});
    			});
    				chart4.series[0].setData(graph4data,true); 	
            },
            error : function(response){
            	graph4data = [];
            	chart4.series[0].setData(graph4data,true); 
            }
        });
    });

    $( "#select-relation" ).on('change', function(){
        $( "#select-relation" ).parent('form').submit();
        $.ajax({
            url : base_url + 'admin/reporting',
            method : 'POST',
            dataType: "json",
            data : {
                id_phenomene : $( "#select-relation" ).val()
            },
            success : function(response){
                graph1data = [];
                data = response;
                $.each(data, function () {
                    graph1data.push({
                        name: this.username,
                        y: parseInt(this.count)
                    });
                });
                    chart1.series[0].setData(graph1data,true);  
            },
            error : function(response){
                graph1data = [];
                chart1.series[0].setData(graph1data,true); 
            }
        });
    });


	var graph1data = [];

	$.each(annByUsers, function () {
        	graph1data.push({
        	name: this.username,
            y: parseInt(this.count)
        });
    });

	var options1 = {
        chart: {
            type: 'column',
            renderTo:'graph1'
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
                    data: graph1data
                }],
    }
    

    var graph2data = [];
	var graph2total = [];
	var total2 = 0;

	$.each(annByWeek , function () {
		if (this.week == null) {
			week = "Données non datées";
		}else{
            week = "Semaine " +  String.substr(this.week,4, 2)+" "+String.substr(this.week,0, 4);
		}
        graph2data.push({
        	name: week,
            y: parseInt(this.count)
        });
        	total2 +=  parseInt(this.count);
        	graph2total.push(total2);
    });	

	var options2 = {
        chart: {
            zoomType: 'xy',
            renderTo:'graph2'
        },
         tooltip: {
            shared: true
        },
        title: {
            text: 'Nombre d\'annotations par semaine'
        },
        xAxis: {
            type:'category'
        },
        yAxis: {
            title: {
                text: 'Annotations'
            }
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: [{
                    name: 'Annotations',
                    colorByPoint: true,
                    data: graph2data,
                    type:'column'
                },{
                    name: 'Total',
                    type: 'spline',
                    data: graph2total
                }],
    }

    var graph3data = [];
    var graph3total = [];
	var total3 = 0;

	$.each(subByWeek , function () {
		if (this.week == null) {
			week = "Données non datées";
		}else{
			week = "Semaine " +  String.substr(this.week,4, 2)+" "+String.substr(this.week,0, 4);
		}
        graph3data.push({
        	name: week,
           	y: parseInt(this.count)
        });
        total3 +=  parseInt(this.count);
        graph3total.push(total3);
    });

	var options3 = {
        chart: {
            zoomType: 'xy',
            renderTo:'graph3'
        },
        tooltip: {
            shared: true
        },
        title: {
            text: 'Nombre d\'inscriptions par semaine'
        },
        xAxis: {
            type:'category'
        },
        yAxis: [{
            title: {
                text: 'Inscriptions'
            }
        }],
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: [{
                    name: 'Inscriptions',
                    type: 'column',
                    colorByPoint: true,
                    data: graph3data
                },{
                    name: 'Total',
                    type: 'spline',
                    data: graph3total
                }],
    }

    var graph4data = [];

	$.each(countByPhenom, function () {
        	graph4data.push({
        	name: this.name,
            y: parseInt(this.count)
        });
    });

	var options4 = {
        chart: {
            type: 'column',
            renderTo:'graph4'
        },
        title: {
            text: 'Nombre d\'annotations par phénomènes'
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
                    data: graph4data
                }],
    }

    var graph5data = [];

    $.each(daysByUser, function () {
            graph5data.push({
            name: this.username,
            y: parseInt(this.count)
        });
    });

    var options5 = {
        chart: {
            type: 'column',
            renderTo:'graph5'
        },
        title: {
            text: 'Nombre de jour avec annotations par joueur'
        },
        xAxis: {
            type:'category'
        },
        yAxis: {
            title: {
                text: 'Jours'
            }
        },
        series: [{
                    name: 'Jours',
                    colorByPoint: true,
                    data: graph5data
                }],
    }

    var chart1 = new Highcharts.Chart(options1);
    var chart2 = new Highcharts.Chart(options2);
    var chart3 = new Highcharts.Chart(options3);
    var chart4 = new Highcharts.Chart(options4);
 	var chart5 = new Highcharts.Chart(options5);
 	
});
