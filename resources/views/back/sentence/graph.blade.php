@extends('back.template')

@section('content')

<div id="sentence"></div>
<select style="position:absolute;width:70px;" id="select_relations">
@foreach($relations as $relation_id => $slug)
<option value="{{ $relation_id }}">{{ $slug }}</option>
@endforeach
</select>
<!--
@foreach($annotations as $annotation)
{{ $annotation->id }} :<br/>
    @foreach($annotation->parsers as $parser)
    {{ $parser->id }} : <p>{{ $parser->name }}</p>
    @endforeach     
@endforeach 
-->

<div id="graph" style="position:absolute;overflow-x: scroll;width:1500px;">


    
            
<svg id="graph_svg" width="500" height="200">

</svg>
</div>
<button onclick="deleteRelations();" style="position:relative;">Clear</button>
<button onclick="modifyRelations();" style="position:relative;">Modify</button>
<button onclick="validateRelations();" style="position:relative;">Validate</button>


<style>
#myMarkerrelation_2_1{
    cursor: move;
}
#graph select{
    font-size: 12px;
}
#sentence {
    
}
#sentence .highlight {
color : red;
}
</style>

@stop
@section('scripts')
{!! Html::script('js/svg/jquery.svg.js') !!}
<script>

var sentence = displaySentence("{{ $sentence->content }}",0,0);
$('#sentence').html(sentence);
var all_annotations = {!! $annotations->makeVisible(['word','word_position','governor_position','relation_name','source_id','parsers','score'])->toJson() !!};
var annotations = [];
var words = [];
var relations = [];
var colors = ['green','blue'];
$('#graph_svg').svg();
var graph_svg = $('#graph_svg').svg('get');
drawInitial(graph_svg);
var origin="",destination="";
var annotationsSortByDistanceToGovernor;
var width_min_relation=0;
var margin_label;
var svg;
function drawInitial(_svg) {
    svg = (_svg)?_svg:svg;
    svg.clear();
    var x=0;
    var y=100;
    var font_size_sentence = 30;
    var font_size_label = 10;    
    var current_word = 0;
    var index = 0;
    var margin_inter_relation = 30;
    width_min_relation = $('#select_relations').width() + 2*margin_inter_relation ;
    var width_words =[];
    margin_label=5;
    $(all_annotations).each(function(i){
        if(this.word_position!=99999){
            

            all_annotations[i].distance_to_governor = Math.abs(this.word_position - this.governor_position);
            all_annotations[i].deplacement_to_governor = this.word_position-this.governor_position;
            if(this.word_position!=current_word){
                current_word=this.word_position;
                words.push(this.word_position);
                svg.text(x,y,this.word,{fontFamily: 'Verdana', fontSize: font_size_sentence, fill: 'black', id: "word_"+this.word_position});
                var elm = svg.getElementById("word_"+this.word_position);
                x+=15+elm.getComputedTextLength();
                width_words[current_word] = elm.getComputedTextLength();
                $(elm).hover(function(){
                    $(this).attr('fill','red');
                },
                function(){
                    $(this).attr('fill','blue');
                });
                $(elm).click(function(){
                    if(origin=="")
                        origin = this.id;
                    else if(origin==this.id)
                        origin = "";
                    else {
                        destination=this.id;

                        var id_destination = destination.match(/\d+/)[0];
                        var id_origin = origin.match(/\d+/)[0];

                        var annotation = {word_position:id_destination,governor_position:id_origin};
                        annotation.distance_to_governor = Math.abs(id_destination - id_origin);
                        annotation.deplacement_to_governor = id_destination-id_origin;
                        annotation.parsers = [];  
                        annotations.push(annotation);
                        deleteRelationsSVG();

                        traceRelations(svg);
                        destination="";
                        origin="";

                    }

                });
            }
            annotations[index++]=all_annotations[i];
        } 

    });

    // current_x=0;
    // $(all_annotations).each(function(i){
    //     if(this.word_position!=current_word){

    //         current_word=this.word_position;
    //         var elm = svg.getElementById("word_"+this.word_position);
    //         $(elm).attr({x:current_x});
    //         if(current_word < annotations.length){
    //             var min_dist = width_min_relation -0.5*width_words[current_word]-0.5*width_words[current_word+1];
    //             min_dist = Math.max(min_dist,20);
    //             current_x+=width_words[current_word]+min_dist;
    //         } else {
    //             current_x+=width_words[current_word];
    //         }
    //     }
    // });  
    svg.configure({width: x, height: 500}, true);
    traceRelations(svg);
}
function deleteRelations(){
    $('.relation').remove();
    relations=[];
    annotations=[];
    drawInitial();
    // var graph_svg = $('#graph_svg').svg();
    // drawInitial(graph_svg);
    // $('defs').remove();
    // $('user').remove();
}
function validateRelations(){
    $('.select_relation').hide();
    $('.label_relation').show();
    // relations=[];
    // annotations=[];    
    // $('defs').remove();
    // $('user').remove();
}

function modifyRelations(){

    var inter_relation_y = 20;
    $('.label_relation').each(function(){
        var relation_name = $(this).html();
        var top = parseInt($(this).attr('y'),10)-margin_label;
        var left = parseInt($(this).attr('x'),10) - ($('#select_relations').width()+25)/2;
        var relation_id = $(this).attr('id');
        var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
        var id = 'select_relation_'+relation_id;
        var new_menu = $('#select_relations').clone().attr('id',id).css({'top':top+"px",'left':left+"px"});
        new_menu.prependTo("#graph");
        new_menu.addClass('select_relation relation relation_'+relation_id);
        new_menu.children().filter(function() {
            return this.text == relation_name; 
        }).attr('selected', true);
    });
    $('.label_relation').hide();
    relations=[];
    annotations=[];    
}

function modifyRelation(relation_id){

    var inter_relation_y = 20;
    $('#label_'+relation_id).each(function(){
        var relation_name = $(this).html();
        var top = parseInt($(this).attr('y'),10)-margin_label;
        var left = parseInt($(this).attr('x'),10) - ($('#select_relations').width()+25)/2;
        var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
        var id = 'select_relation_'+relation_id;
        var new_menu = $('#select_relations').clone().attr('id',id).css({'top':top+"px",'left':left+"px"});
        new_menu.prependTo("#graph");
        new_menu.addClass('select_relation relation relation_'+relation_id);
        new_menu.children().filter(function() {
            return this.text == relation_name; 
        }).attr('selected', true);

        new_menu.change(function(){
            var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
            $('#label_'+relation_id).html($(this).find(":selected").text());
        });
    });
    $('#label_'+relation_id).hide();
    //relations=[];
    //annotations=[];    
}

function deleteRelationsSVG(){
    $('.relation').remove();
    // $('defs').remove();
    // $('user').remove();
}
function deleteRelation(governor,dependent){
    $('.relation_'+governor+'_'+dependent).remove();
}
function traceRelations(svg){
    // var annot = annotations;
    annotationsSortByDistanceToGovernor = annotations.sort(function(a,b){return a.distance_to_governor-b.distance_to_governor;});
    var height = 100;
    relations=[];
    $(annotationsSortByDistanceToGovernor).each(function(index){
        if(this.parsers.length==1)
            parser_id = this.parsers[0].id;
        else if (this.parsers.length==0 && this.source_id==3)
            parser_id = 0;
        else
            parser_id = null;
        if(this.governor_position>0){
            var level = getLevelRelation(this.governor_position,this.word_position);
            traceArrowRelation(svg,level,this.governor_position,this.word_position,this.relation_name, parser_id);
            var relation = {governor:this.governor_position,dependent:this.word_position,level:level};
            relations.push(relation);
        }
    });
}
function traceArrow(svg,relation){
   
}
function removeAnnotation(governor,dependent){
    $(annotations).each(function(index){
        if(this.governor_position==governor && this.word_position==dependent){
            this.governor_position=0;
        }
    });
}

function getLevelRelation(governor,dependent){

    // var dependents = getDependents(governor,dependent);
    var level_max = 0;
    var borne_inf = governor>dependent?dependent:governor;
    var borne_sup = governor<dependent?dependent:governor;
    $(relations).each(function(){

        if((this.governor>borne_inf&&this.governor<borne_sup)||(this.dependent>borne_inf&&this.dependent<borne_sup))
            if(this.level>level_max)
            level_max = this.level;
    });   
    return level_max+1;
}
function getDependents(governor,dependent=null){
    var dependents = [];
    $(annotations).each(function(index){
        if(this.governor_position==governor && (dependent==null || this.word_position<dependent))
            dependents.push(this);
    });
    return dependents;
}
function getNumberOfDependents(governor,dependent=null){
    var number = 0;
    $(annotations).each(function(index){
        // if(this.governor_position==0) continue;
        var same_side_of_governor = Math.sign((dependent-governor)/(this.word_position-governor))>0?true:false;
        if( (this.governor_position==governor && (dependent==null || (same_side_of_governor && this.word_position>dependent) || (!same_side_of_governor && this.word_position<dependent))))
            number++;
        var same_side_of_governor = Math.sign((dependent-governor)/(governor-this.governor_position))>0?true:false;
        if( (this.word_position==governor && this.governor_position!=0 && (dependent==null || (same_side_of_governor && this.governor_position<dependent) || (!same_side_of_governor && this.governor_position>dependent))))
            number++;
       
       
    });
    return number;
}
function getDegree(governor,dependent=null){
    var number = 0;
    $(annotations).each(function(index){
        if(this.governor_position==governor || this.word_position==governor)
            number++;
    });
    return number;
}
// function getDependents(governor){
    // var dependents = {};
   
// }
function getWordByPosition(id_word){
    var id_word = id_word.match(/\d+/)[0];
    var annotation;
    $(annotations).each(function(i){
        if(this.word_position==id_word){
            annotation = this;
        }
    });
    return annotation;
}

function decalWords(svg,_word,decalage){
    current_word = 0;
    
    $(all_annotations).each(function(i){

        if(this.word_position!=current_word && this.word_position>=_word){

            current_word=this.word_position;

            var elm = svg.getElementById("word_"+this.word_position);

            var x=parseInt($(elm).attr('x'),10)+decalage;
            $(elm).attr('x',x);

            if(current_word==words.length){
                var elm = svg.getElementById("word_"+current_word);
                x+=15+elm.getComputedTextLength();
                svg.configure({width: x, height: 500}, true);
            }
        }
    });
}

function traceArrowRelation(svg,level,_origin,_destination,_relation_name, _parser_id){
    if(_parser_id==0){
        var color = "red";
    } else if(_parser_id==1) {
        var color = "blue";
    } else if(_parser_id==2) {
        var color = "green";
    } else 
        var color = "black";
    var id_origin = "word_"+_origin;
    var id_destination = "word_"+_destination;
    var svg_word_origin = svg.getElementById(id_origin);
    var svg_word_destination = svg.getElementById(id_destination);

    var w_o = svg_word_origin.getComputedTextLength(); // width of the governor word
    var w_d = svg_word_destination.getComputedTextLength(); // width of the dependent word
    var y = parseInt($(svg_word_origin).attr("y"),10);
    var x_o = parseInt($(svg_word_origin).attr("x"),10);
    var x_d = parseInt($(svg_word_destination).attr("x"),10);
    
    var path = svg.createPath();
    //svg.path(defs,path.line([(x_o+w_o/2).toString(), y], [x_o+w_o/2, y-100], [x_d+w_d/2, y-100], [x_d+w_d/2, y]),{id: 'MyPath2'});
    var id_path = 'relation_'+_origin+'_'+_destination;
    var defs = svg.defs({'class':'relation '+id_path});
    // svg.path(defs,path.move(x_o+w_o/2,y).line(x_o+w_o/2, y+50).arc(3,3,45,1,0,x_o+w_o/2+3,y+50+3).line(x_d+w_d/2, y+50).line(x_d+w_d/2, y),{id: id_path});
    var height_init = 50;
    var inter_relation_y = 20;
    var inter_relation_x = 12;
    var courbure = 10;
    var decalage_y = 15;

    var height_arrow = 6;
    var width_arrow = 3;

    var position_origin;
    var sens = Math.sign(_destination-_origin);

    var degree_origin = getDegree(_origin);
    var degree_destination = getDegree(_destination);
    var number_decalage_origin = getNumberOfDependents(_origin,_destination);
    var number_decalage_destination = getNumberOfDependents(_destination,_origin);
    var decalage_x_origin = -(degree_origin-1)*inter_relation_x/2+number_decalage_origin*inter_relation_x;
    var decalage_x_destination = -(degree_destination-1)*inter_relation_x/2+number_decalage_destination*inter_relation_x;

    svg.marker(defs, 'myMarker'+id_path, 4, 5, 200, 200);
    // trace Arrows
    svg.path($('#myMarker'+id_path), path.move(0,0).line(0,5+width_arrow).line(height_arrow,5).line(0,5-width_arrow),{fill:color});
    // svg.use('#myMarker', {fill: 'none', stroke: 'black','class':'relation '+id_path});


    var defs = svg.defs({'class':'relation '+id_path});
    var path = svg.createPath();
    var x_origin = decalage_x_origin+x_o+w_o/2;
    var x_destination = decalage_x_destination+x_d+w_d/2;
    if(Math.abs(x_destination-x_origin)<width_min_relation){
        decalWords(svg,Math.max(_origin,_destination),width_min_relation-Math.abs(x_destination-x_origin));
        x_o = parseInt($(svg_word_origin).attr("x"),10);
        x_d = parseInt($(svg_word_destination).attr("x"),10);
        decalage_x_origin = -(degree_origin-1)*inter_relation_x/2+number_decalage_origin*inter_relation_x;
        decalage_x_destination = -(degree_destination-1)*inter_relation_x/2+number_decalage_destination*inter_relation_x;
        x_origin = decalage_x_origin+x_o+w_o/2;
        x_destination = decalage_x_destination+x_d+w_d/2;
    }



    var x_label = (x_origin+x_destination)/2;
    svg.path(defs,
        path.move(x_origin,y+decalage_y)
            .line(x_origin, y+height_init+inter_relation_y*level-courbure)
            .arc(sens*courbure,courbure, -1*sens*45,0,(sens>0)?0:1,x_origin+sens*courbure,y+height_init+inter_relation_y*level)
            .line(x_destination-sens*courbure, y+height_init+inter_relation_y*level)
            .arc(courbure,-courbure,-45,0,(sens>0)?0:1,x_destination,y+height_init+inter_relation_y*level-courbure)
            .line(x_destination, y+decalage_y),{id: id_path, style: 'marker-end:url(#myMarker'+id_path+')','class':'relation '+id_path});


    if(_relation_name)
        elm = svg.text(x_label,y+height_init+inter_relation_y*level-margin_label,_relation_name,{'class':'relation label_relation '+id_path+' label_'+_origin+'_'+_destination, 'contentEditable':'true', 'style' :'text-anchor:middle;text-align:center', fontFamily: 'Verdana', fontSize: '10', fill: color, id: "label_"+_origin+"_"+_destination});
    else {
        elm = svg.text(x_label,y+height_init+inter_relation_y*level-margin_label,'unk',{'class':'relation label_relation '+id_path+' label_'+_origin+'_'+_destination, 'contentEditable':'true', 'style' :'text-anchor:middle;text-align:center', fontFamily: 'Verdana', fontSize: '10', fill: color, id: "label_"+_origin+"_"+_destination});
    }

    $(elm).click(function(){
        var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
        modifyRelation(relation_id);
    });

    var path_relation = svg.use('#'+id_path, {margin: 10, strokeWidth: 2, fill: 'none', stroke: color,'class':'relation '+id_path, id: _origin+'_'+_destination});
    if(!_relation_name)
        modifyRelation(_origin+'_'+_destination);
    $('.relation_'+_origin+'_'+_destination).click(function(){
        $('.relation_'+this.id).remove();
        var rel = this.id.split('_');
        removeAnnotation(rel[0],rel[1]);
    });

    $(path_relation).hover(function(){

        var governor = $(this).attr('id').split('_')[0];
        var dependent = $(this).attr('id').split('_')[1];
        $('#word_index_'+governor).addClass('highlight');
        $('#word_index_'+dependent).addClass('highlight');
        var classname = $(this).attr('id');
        $('.relation_'+classname).attr('cursor','move');
        // $('.relation_'+classname).attr('stroke','red');
        // $('.label_'+classname).attr('stroke','none');
        // $('.label_'+classname).attr('fill','red');
    },
    function(){
        $('.mot').removeClass('highlight');
        // var classname = $(this).attr('id');
        // $('.relation_'+classname).attr('stroke','black');    
        // $('.label_'+classname).attr('stroke','none');
        // $('.label_'+classname).attr('fill','black');
    });
}
</script>
@stop
@section('style')
{!! Html::style('css/jquery.svg.css') !!}
@stop