@extends('front.template')

@section('main')
<div id="sentence"></div>
<div id="elements_html">
<select style="position:absolute;width:70px;" id="select_relations" class="select_relations">
@foreach($relations as $relation_id => $slug)
<option value="{{ $relation_id }}">{{ $slug }}</option>
@endforeach
</select>
</div>
<!-- <div id='input'></div> -->
<!--
@foreach($annotations as $annotation)
{{ $annotation->id }} :<br/>
    @foreach($annotation->parsers as $parser)
    {{ $parser->id }} : <p>{{ $parser->name }}</p>
    @endforeach     
@endforeach 
-->

<!-- <button onclick="deleteRelations();" style="position:relative;">Clear</button>
<button onclick="modifyRelations();" style="position:relative;">Modify</button>
<button onclick="validateRelations();" style="position:relative;">Validate</button> -->
<div id="content" style="position:relative;">
    <div style="width:100%;height:0;visibility:hidden" id="width_block"></div>

</div>
<label>Font-size text :</label>
<input onchange="updateDraw();" id="font_size" type="number" value="17" style="position:relative;" /><br/>
<label>Font-size label :</label>
<input onchange="updateDraw();" id="font_size_label" type="number" value="10" style="position:relative;" /><br/>
<label>Inter-y :</label>
<input onchange="updateDraw();" id="inter_relation_y" type="number" value="15" style="position:relative;" /><br/>
<label>Inter-x :</label>
<input onchange="updateDraw();" id="inter_relation_x" type="number" value="12" style="position:relative;" /><br/>
<label>Width arrow :</label>
<input onchange="updateDraw();" id="width_arrow" type="number" value="2" style="position:relative;" /><br/>
<label>Height arrow :</label>
<input onchange="updateDraw();" id="height_arrow" type="number" value="6" style="position:relative;" /><br/>
<label>Width stroke :</label>
<input onchange="updateDraw();" id="stroke_width" type="number" value="1" style="position:relative;" /><br/>
<label>Decalage-y label :</label>
<input onchange="updateDraw();" id="margin_label" type="number" value="5" style="position:relative;" /><br/>
<label>Padding-x label :</label>
<input onchange="updateDraw();" id="padding_relation" type="number" value="5" style="position:relative;" /><br/>
<label>Inter words :</label>
<input onchange="updateDraw();" id="inter_word" type="number" value="15" style="position:relative;" /><br/>
<label>Height-min relation :</label>
<input onchange="updateDraw();" id="height_init" type="number" value="50" style="position:relative;" /><br/>
<label>Courbure relation :</label>
<input onchange="updateDraw();" id="courbure" type="number" value="10" style="position:relative;" /><br/>
<style>
.graph_svgh{
    position:relative;
}
.container_svg_sentence{
    position:relative;
    overflow-x: scroll;
}
.container_svg{
    position:relative;
}
#elements_html {
    display:none;
}
#myMarkerrelation_2_1{
    cursor: move;
}
select.select_relations{
    font-size: 12px;
}
#sentence {
    
}
#sentence .highlight {
    color : red;
}

use.normal {
    stroke-width: 0.75;
    stroke: #888;
}
text.normal {
    fill: black;
}
use.success {
    stroke-width: 1;
    stroke: #0f0;
}
text.success {
    fill: #0f0;
}
use.disabled {
    stroke-width: 0.75;
    stroke: #888;
}
text.disabled {
    font-weight: normal;
    fill: #888;
}
use.alert {
    stroke-width: 1.25;
    stroke: #F00;
}
text.alert {
    fill: #F00;
}
use.highlight {
    stroke-width: 1.5;
}
text.highlight {
    font-weight: normal;
    fill: blue;
}
use.unhighlight {
    stroke-width: 0.75;
    stroke: #888;
}
text.unhighlight {
    fill: #888;
}
text.selected {
    text-decoration: underline;
}
svg.graph_svg_view > text.label_relation {
    cursor : copy;
}
</style>

@stop
@section('scripts')
{!! Html::script('js/svg/jquery.svg.js') !!}
<script>
var graphs = [];
function updateDraw(){

    $(graphs).each(function(){
        this.draw();
    });
}

GraphSVGContainer = function(data) {
  this.init(data);
}

$.extend(GraphSVGContainer.prototype, {
    sentence : '',
    graphs : [],
    data : [],
    relations : [],
    widget_edit : null, 
    init: function(data) {
        this.data= data;
        this.sentence= data.sentence;
        this.parsers= data.parsers;
    },
    draw: function() {
        var self = this;
        this.widget_edit = new GraphSVG(self, 'edit');
        this.widget_edit.draw();

        graphs[this.widget_edit.svg_id] = this.widget_edit;  
        var widget = new GraphSVG(self, 'view','best');
        widget.draw();
        graphs[widget.svg_id] = widget;
        $(self.parsers).each(function(){
            var widget = new GraphSVG(self, 'view',this.id);
            widget.draw();
            graphs[widget.svg_id] = widget;   
        });
        $('.graph_svg_view > .label_relation').click(function(){
            console.log(this.id);
            var relation_id = $(this).attr('id').match(/\d+\_\d+$/)[0];
            var [id_origin, id_destination] = relation_id.split('_');
            console.log(id_destination);

            var annotation = {word_position:id_destination,governor_position:id_origin};
            annotation.distance_to_governor = Math.abs(id_destination - id_origin);
            annotation.deplacement_to_governor = id_destination-id_origin;
            annotation.relation_name = $(this).html();
            annotation.parsers = [];
            // self.widget_edit.annotations.push(annotation);
            self.widget_edit.saveAnnotation(annotation);
            self.widget_edit.updateRelations();        
            
        });
    },
});

GraphSVG = function(container, graph_id, parser_id) {
  this.init(container, graph_id, parser_id);
}

$.extend(GraphSVG.prototype, {
   // object variables
    container : null,
    all_annotations : [],
    annotations : [],
    words : [],
    relations : [],
    sentence : [],
    colors : ['green','blue'],
    origin:"",destination:"",
    annotationsSortByDistanceToGovernor:[],
    width_min_relation:0,
    font_size:15,
    font_size_label:6,
    inter_relation_y : 15,
    inter_relation_x : 12,
    height_arrow : 6,
    width_arrow : 3,
    stroke_width : 2,
    width_svg : 200,
    margin_label:5,
    height:0,
    width:0,
    padding_relation : 5,
    decalage_y : 5,
    inter_word : 10,
    padding_bottom : 15,
    height_init : 50,
    courbure : 10,
    orientation : 1,
    padding_top : 0,
    select_relation_open : false,
    svg:null, 
    svg_id: '',

    init: function(container,mode,parser_id = null) {
        // do initialization here
        this.all_annotations = container.data.annotations;
        this.sentence = container.data.sentence;
        this.container = container;
        this.mode = mode;
        this.parser_id = parser_id;
        var sentence_id = this.sentence.id;
        this.svg_id = sentence_id+'_'+mode;
        if(mode=='view')
            this.svg_id += '_'+parser_id;
        if($('#container_'+sentence_id).length==0){
            var container = $('<div id="container_'+sentence_id+'" class="container_svg"></div>');
            var sentence_hml = $('<div id="sentence_'+sentence_id+'"></div>').html(displaySentence(sentence.content,0,0));
            var container_svg_sentence = $('<div id="container_svg_'+sentence_id+'" class="container_svg_sentence"></div>');
            container.append(sentence_hml);
            container.append(container_svg_sentence);
            $('#content').append(container);
        } else {
            var container = $('#container_'+sentence_id);
        }
      
        var container_svg = $('<div id="container_'+this.svg_id+'" class="container_svg"></div>');
        var svg_html = $('<svg id="'+this.svg_id+'" class="graph_svg graph_svg_'+this.mode+'"></svg>');
        // var sentence_html = 
        if($('#sentence_'+sentence_id).length==0){

        }
        container_svg.append(svg_html);
        // container.append(container_svg);
        $('#container_svg_'+sentence_id).append(container_svg);
        $('#'+this.svg_id).svg();
        this.svg = $('#'+this.svg_id).svg('get');
        console.log('fin init');
    },

    draw: function() {
        width_svg=Math.floor($('#width_block').width());
        this.loadParams();
        // this.svg = (_svg)?_svg:svg;
        this.svg.clear();
        var x=0;
        var y=0;
        y+=this.padding_top;
        y+=this.font_size;
        var current_word = 0;
        var index = 0;

        width_min_relation = $('#select_relations').width() + 2*this.padding_relation ;
        var width_words =[];
        // margin_label=5;
        var self = this;
        $(this.all_annotations).each(function(i,annotation){
            if(annotation.word_position!=99999){
                

                all_annotations[i].distance_to_governor = Math.abs(annotation.word_position - annotation.governor_position);
                all_annotations[i].deplacement_to_governor = annotation.word_position-annotation.governor_position;
                if(annotation.word_position!=current_word){
                    current_word=annotation.word_position;
                    var word_id = self.svg_id+"_word_"+annotation.word_position;
                    self.words.push(annotation.word_position);
                    self.svg.text(x,y,annotation.word,{fontFamily: 'Verdana', fontSize: self.font_size, fill: 'black', id: word_id, class: "word" });
                    var elm = self.svg.getElementById(word_id);
                    x+=self.inter_word+elm.getComputedTextLength();

                    width_words[current_word] = elm.getComputedTextLength();
                    $(elm).hover(function(){
                        self.highlight(this);
                        $(this).attr('fill','red');
                    },
                    function(){
                        self.unhighlight($(this));
                        $(this).attr('fill','black');
                    });
                    if(self.mode=='edit')
                    $(elm).click(function(){
                        if(self.origin==""){
                            self.origin = this.id;
                            $(this).addClass('selected');
                        }
                        else if(self.origin==this.id){
                            $(this).removeClass('selected');
                            self.origin = "";
                        }
                        else {
                            $(this).addClass('selected');
                            self.destination=this.id;
                            var id_destination = self.destination.match(/\d+$/)[0];
                            var id_origin = self.origin.match(/\d+$/)[0];

                            var annotation = {word_position:id_destination,governor_position:id_origin};
                            annotation.distance_to_governor = Math.abs(id_destination - id_origin);
                            annotation.deplacement_to_governor = id_destination-id_origin;
                            annotation.parsers = [];
                            self.saveAnnotation(annotation);
                            // self.annotations.push(annotation);
                            self.updateRelations();
                            
                        }

                    });
                }
                self.annotations[index++]=self.all_annotations[i];
            } 

        });

        this.width = x;
        
        this.traceRelations();
        this.svg.configure({width: this.width+20, height: this.height+this.padding_top, id: this.svg_id, class:'graph_svg graph_svg_'+this.mode}, true);
        $('#'+this.svg_id+' use').attr('transform', 'translate(0 '+(this.height+this.padding_top)+') scale(1,-1)');
        $('#'+this.svg_id+' .word').attr('transform', 'translate(0 '+(this.height-this.font_size-this.padding_top)+')');
        this.placeLabelRelation();

    },
    updateRelations: function(){
        this.deleteRelationsSVG();
        this.traceRelations();
        this.destination="";
        this.origin="";
        $('#'+this.svg_id+' use').attr('transform', 'translate(0 '+(this.height+this.padding_top)+') scale(1,-1)');
        $('#'+this.svg_id+' .word').attr('transform', 'translate(0 '+(this.height-this.font_size-this.padding_top)+')');        
        this.placeLabelRelation();
        this.svg.configure({height: this.height+this.padding_top});
    },
    placeLabelRelation: function(){
        var self = this;
        $(this.relations).each(function(i,relation){
            var label_id = '#'+self.svg_id+'_label_'+relation.governor+'_'+relation.dependent;
            var new_y = parseInt($('#'+self.svg_id+' #'+self.svg_id+'_word_'+relation.dependent).attr('y'),10)+self.height-self.height_init-self.padding_top-self.margin_label-2*self.font_size-relation.level*self.inter_relation_y;
            $('#'+self.svg_id+' > '+label_id).attr('y',new_y);
        });
    },
    highlight: function(elm){

        var self = this;
        var word_position = parseInt(elm.id.match(/\d+$/)[0]);
        console.log(word_position);
        $('#'+this.svg_id+' .word').addClass('unhighlight');
        $(this.relations).each(function(i,relation){
            if(word_position==relation.governor || word_position==relation.dependent){
                $('#'+self.svg_id+' #'+self.svg_id+'_word_'+relation.governor).addClass('highlight');
                $('#'+self.svg_id+' #'+self.svg_id+'_word_'+relation.dependent).addClass('highlight');
                $('#'+self.svg_id+' #'+self.svg_id+'_word_'+relation.governor).removeClass('unhighlight');
                $('#'+self.svg_id+' #'+self.svg_id+'_word_'+relation.dependent).removeClass('unhighlight');
                $('#'+self.svg_id+' .relation_'+relation.governor+'_'+relation.dependent).addClass('highlight');
            } else {
                $('#'+self.svg_id+'  .relation_'+relation.governor+'_'+relation.dependent).addClass('unhighlight');            
            }
        });
    },
    unhighlight: function(elm){
        $('#'+this.svg_id+' .relation').removeClass('highlight');
        $('#'+this.svg_id+' text').removeClass('highlight');
        $('#'+this.svg_id+' .relation').removeClass('unhighlight');
        $('#'+this.svg_id+' text').removeClass('unhighlight');
    },
    deleteRelations: function(){
        $('#'+this.svg_id+' .relation').remove();
        this.relations=[];
        this.annotations=[];
        this.draw();
    },
    updateDraw: function(){
        this.loadParams();
        this.draw();
    },
    loadParams42: function(){
        this.font_size = parseInt($('#font_size').val(),10);
        this.font_size_label = parseInt($('#font_size_label').val(),10);
        this.inter_relation_x = parseInt($('#inter_relation_x').val(),10);
        this.inter_relation_y = parseInt($('#inter_relation_y').val(),10);
        this.height_arrow = parseInt($('#height_arrow').val(),10);
        this.width_arrow = parseInt($('#width_arrow').val(),10);
        this.stroke_width = parseFloat($('#stroke_width').val());
        this.margin_label = parseInt($('#margin_label').val(),10);
        this.padding_relation = parseInt($('#padding_relation').val(),10);
        this.inter_word = parseInt($('#inter_word').val(),10);
        this.height_init = parseInt($('#height_init').val(),10);
        this.courbure = parseInt($('#courbure').val(),10);
    },
    loadParams: function(){
        this.font_size = 10;
        this.font_size_label = 8;
        this.inter_relation_x = 3;
        this.inter_relation_y = 10;
        this.height_arrow = 6;
        this.width_arrow = 2;
        this.stroke_width = 1;
        this.margin_label = 3;
        this.padding_relation = -7;
        this.inter_word = 5;
        this.height_init = 15;
        this.courbure = 6;
        this.padding_top = 15;
    },
    loadParams2: function(){
        font_size = 15;
        font_size_label = 8;
        inter_relation_x = 6;
        inter_relation_y = 10;
        height_arrow = 6;
        width_arrow = 2;
        stroke_width = 1;
        margin_label = 3;
        padding_relation = -5;
        inter_word = 7;
        height_init = 15;
        courbure = 10;
    },

    validateRelation: function(){

        $('#container_'+this.svg_id+' > .select_relation').remove();
        $('#'+this.svg_id+' > .label_relation').show();

    },

    validateRelations: function(relation_id,_relation_name){
        var [id_origin, id_destination] = relation_id.split('_');
        var annotation = {word_position:id_destination,governor_position:id_origin,relation_name:_relation_name};
        this.saveAnnotation(annotation);
        $('#container_'+this.svg_id+' > .select_relation').remove();
        $('#'+this.svg_id+' > .label_relation').show();

    },
    saveAnnotation: function(annotation){
        var found = false;
        var self=this;
        $(this.annotations).each(function(i,annot){
            if(this.word_position==annotation.word_position || (this.governor_position==annotation.word_position && this.word_position==annotation.governor_position)){
                self.annotations[i]=annotation;
                this.relation_name=annotation.relation_name;
                this.governor_position=annotation.governor_position;
                found=true;
            }
        });
        if(!found)
            this.annotations.push(annotation);
    },
    modifyRelations: function(){
        var self = this;
        var margin_select_relation = 5;
        $('.label_relation').each(function(){
            var relation_name = $(this).html();
            var top = parseInt($(this).attr('y'),10)-margin_select_relation;
            var left = parseInt($(this).attr('x'),10) - ($('#select_relations').width()+25)/2;
            var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
            var id = 'select_relation_'+relation_id;
            var new_menu = $('#select_relations').clone().attr('id',id).css({'top':top+"px",'left':left+"px"});
            new_menu.prependTo("#container_"+self.svg_id);
            new_menu.addClass('select_relation relation relation_'+relation_id);
            new_menu.children().filter(function() {
                return this.text == relation_name; 
            }).attr('selected', true);
        });
        $('#'+this.svg_id+' .label_relation').hide();
        this.relations=[];
        this.annotations=[];    
    },

    modifyRelation: function(relation_id){
        console.log("enter modifyRelation");

        var self = this;
        var margin_select_relation = 5;
        $('#'+this.svg_id+' > #'+this.svg_id+'_label_'+relation_id).each(function(){
            var relation_name = $(this).html();
            var top = parseInt($(this).attr('y'),10)-margin_select_relation;
            var left = parseInt($(this).attr('x'),10) - ($('#select_relations').width()+25)/2;
            var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
            var id = 'select_relation_'+relation_id;
            var new_menu = $('#select_relations').clone().attr('id',id).css({'top':top+"px",'left':left+"px"});
            new_menu.prependTo("#container_"+self.svg_id);
            new_menu.addClass('select_relation relation relation_'+relation_id);
            new_menu.children().filter(function() {
                return this.text == relation_name; 
            }).attr('selected', true);

            new_menu.mouseup(function(){
                self.select_relation_open = true;
                new_menu.click(function(){
                    if(self.select_relation_open === false){
                        var relation_id = $(this).attr('id').match(/\d+\_\d+$/)[0];
                        var relation_name = $(this).find(":selected").text();
                        $('#'+self.svg_id+' > #'+self.svg_id+'_label_'+relation_id).html(relation_name);                     
                        self.validateRelations(relation_id,relation_name);
                    } else {
                        self.select_relation_open = false;
                    }
                });            
            });
            new_menu.blur(function(){
                var relation_id = $(this).attr('id').match(/\d+\_\d+$/)[0];
                $('#'+self.svg_id+' > #'+self.svg_id+'_label_'+relation_id).html($(this).find(":selected").text());                
                self.validateRelations();
            });
            new_menu.change(function(){
                console.log("Change !!!");
                var relation_id = $(this).attr('id').match(/\d+\_\d+$/)[0];
                console.log(relation_id);
                $('#'+self.svg_id+' > #'+self.svg_id+'_label_'+relation_id).html($(this).find(":selected").text());
                self.validateRelations();
            });
        });
        $('#'+self.svg_id+' > #'+self.svg_id+'_label_'+relation_id).hide();
        //relations=[];
        //annotations=[];    
    },

    deleteRelationsSVG: function(){
        $('#'+this.svg_id+' .relation').remove();
    },
    deleteRelation: function(governor,dependent){
        $('#'+this.svg_id+' .relation_'+governor+'_'+dependent).remove();
    },

    traceRelations: function(){
        // var annot = annotations;
        annotationsSortByDistanceToGovernor = this.annotations.sort(function(a,b){
            return a.distance_to_governor-b.distance_to_governor;
        });
        this.relations=[];
        var self = this;
        $(annotationsSortByDistanceToGovernor).each(function(index,annotation){
            var class_relation = "normal";
            if(annotation.relation_name=="ponct")
                return true;
            if(self.parser_id!='best' && self.parser_id!=null && annotation.parsers.length==0)
                return true;
            if(self.parser_id=='best' && annotation.best==0) {
                return true;
            }            
            else if(self.parser_id!=null && self.parser_id!='best'){
                var in_array = false;
                $(annotation.parsers).each(function(){
                    console.log(this.id);
                    if(self.parser_id==this.id)
                        in_array = true;
                });
                if(!in_array)
                    return true;
            }
            if(annotation.governor_position == 99999 || annotation.word_position == 99999)
                return true;
            if (annotation.parsers.length==1 && annotation.best==1){
                parser_id = 0;
                class_relation = "success";
            }
            else if(annotation.parsers.length==1){
                if(self.mode=='edit') return;
                parser_id = annotation.parsers[0].id;
                class_relation = "alert";
            }
            else if (annotation.parsers.length==0 && annotation.best==1){
                parser_id = 0;
                class_relation = "success";
            }
            else if (annotation.parsers.length==2){
                parser_id = 0;
                class_relation = "disabled";
            }
            else
                parser_id = null;

            if (annotation.source_id==2 && annotation.best!=1){
                return true;
            }

            if(annotation.governor_position>0){
                var level = self.getLevelRelation(annotation.governor_position,annotation.word_position);
                self.traceArrowRelation(level,annotation.governor_position,annotation.word_position,annotation.relation_name, parser_id,class_relation);
            }
        });
    },
    traceArrow: function(svg,relation){
       
    },
    removeAnnotation: function(governor,dependent){
        $(this.annotations).each(function(index,annotation){
            if(annotation.governor_position==governor && annotation.word_position==dependent){
                annotation.governor_position=0;
            }
        });
    },

    getLevelRelation: function(governor,dependent){

        // var dependents = getDependents(governor,dependent);
        var level_max = 0;
        var borne_inf = governor>dependent?dependent:governor;
        var borne_sup = governor<dependent?dependent:governor;
        $(this.relations).each(function(){

            if((this.governor>borne_inf&&this.governor<borne_sup)||(this.dependent>borne_inf&&this.dependent<borne_sup))
                if(this.level>level_max)
                level_max = this.level;
        });   
        return level_max+1;
    },
    getDependents: function(governor,dependent=null){
        var dependents = [];
        $(this.annotations).each(function(index){
            if(this.governor_position==governor && (dependent==null || this.word_position<dependent))
                dependents.push(this);
        });
        return dependents;
    },
    getNumberOfDependents: function(governor,dependent=null){
        var number = 0;
        $(this.annotations).each(function(index){
            // if(this.governor_position==0) continue;
            var same_side_of_governor = Math.sign((dependent-governor)/(this.word_position-governor))>0?true:false;
            if( (this.governor_position==governor && (dependent==null || (same_side_of_governor && this.word_position>dependent) || (!same_side_of_governor && this.word_position<dependent))))
                number++;
            var same_side_of_governor = Math.sign((dependent-governor)/(governor-this.governor_position))>0?true:false;
            if( (this.word_position==governor && this.governor_position!=0 && (dependent==null || (same_side_of_governor && this.governor_position<dependent) || (!same_side_of_governor && this.governor_position>dependent))))
                number++;
           
           
        });
        return number;
    },
    getDegree: function(governor,dependent=null){
        var number = 0;
        $(this.annotations).each(function(index){
            if(this.governor_position==governor || this.word_position==governor)
                number++;
        });
        return number;
    },
    // function getDependents(governor){
        // var dependents = {};
       
    // }
    getWordByPosition: function(id_word){
        var id_word = id_word.match(/\d+/)[0];
        var annotation;
        $(this.annotations).each(function(i){
            if(this.word_position==id_word){
                annotation = this;
            }
        });
        return annotation;
    },

    decalWords: function(_word,decalage){
        var current_word = 0;
        var self = this;
        $(this.all_annotations).each(function(i,annotation){

            if(annotation.word_position!=current_word && annotation.word_position>=_word){

                current_word=annotation.word_position;

                var elm = this.svg.getElementById(self.svg_id+"_word_"+annotation.word_position);

                var x=parseInt($(elm).attr('x'),10)+decalage;
                $(elm).attr('x',x);

                if(current_word==words.length){
                    var elm = this.svg.getElementById(self.svg_id+"_word_"+current_word);
                    x+=this.inter_word+elm.getComputedTextLength();
                    // svg.configure({width: x, height: 500}, true);
                    if(x>this.width)
                        this.width = x;
                }
            }
        });
    },

    traceArrowRelation: function(level,_origin,_destination,_relation_name, _parser_id, class_relation){
        var self = this;
        // if(class_relation==""){
        //     var color = "red";
        // } else if(_parser_id==1) {
        //     var color = "blue";
        // } else if(_parser_id==2) {
        //     var color = "green";
        // } else 
        var color = "black";
        var id_origin = self.svg_id+"_word_"+_origin;
        var id_destination = self.svg_id+"_word_"+_destination;
        var svg_word_origin = this.svg.getElementById(id_origin);
        var svg_word_destination = this.svg.getElementById(id_destination);

        var w_o = svg_word_origin.getComputedTextLength(); // width of the governor word
        var w_d = svg_word_destination.getComputedTextLength(); // width of the dependent word
        var y = parseInt($(svg_word_origin).attr("y"),10);
        var x_o = parseInt($(svg_word_origin).attr("x"),10);
        var x_d = parseInt($(svg_word_destination).attr("x"),10);
        
        var path = this.svg.createPath();
        //svg.path(defs,path.line([(x_o+w_o/2).toString(), y], [x_o+w_o/2, y-100], [x_d+w_d/2, y-100], [x_d+w_d/2, y]),{id: 'MyPath2'});
        var path_id = this.svg_id+'_'+_origin+'_'+_destination;
        class_relation += ' relation_'+_origin+'_'+_destination;
        var defs = this.svg.defs({'class':'relation '+path_id});
        // this.svg.path(defs,path.move(x_o+w_o/2,y).line(x_o+w_o/2, y+50).arc(3,3,45,1,0,x_o+w_o/2+3,y+50+3).line(x_d+w_d/2, y+50).line(x_d+w_d/2, y),{id: path_id});

        /* distance entre le texte et les extrémités des relations */
        var decalage_y = 5;

        var sens = Math.sign(_destination-_origin);

        var degree_origin = this.getDegree(_origin);
        var degree_destination = this.getDegree(_destination);
        var number_decalage_origin = this.getNumberOfDependents(_origin,_destination);
        var number_decalage_destination = this.getNumberOfDependents(_destination,_origin);
        var decalage_x_origin = -(degree_origin-1)*this.inter_relation_x/2+number_decalage_origin*this.inter_relation_x;
        var decalage_x_destination = -(degree_destination-1)*this.inter_relation_x/2+number_decalage_destination*this.inter_relation_x;

        this.svg.marker(defs, 'marker_'+path_id, 4, 5, 200, 200);
        // trace Arrows
        this.svg.path($('#'+'marker_'+path_id), 
            path.move(0,0)
                .line(0,5+this.width_arrow)
                .line(this.height_arrow,5)
                .line(0,5-this.width_arrow),
            {fill:color, class: class_relation});
        // this.svg.use('#myMarker', {fill: 'none', stroke: 'black','class':'relation '+path_id});


        var defs = this.svg.defs({'class':'relation '+path_id});
        var path = this.svg.createPath();
        var x_origin = decalage_x_origin+x_o+w_o/2;
        var x_destination = decalage_x_destination+x_d+w_d/2;
        if(Math.abs(x_destination-x_origin)<this.width_min_relation){
            this.decalWords(svg,Math.max(_origin,_destination),this.width_min_relation-Math.abs(x_destination-x_origin));
            x_o = parseInt($(svg_word_origin).attr("x"),10);
            x_d = parseInt($(svg_word_destination).attr("x"),10);
            decalage_x_origin = -(degree_origin-1)*this.inter_relation_x/2+number_decalage_origin*this.inter_relation_x;
            decalage_x_destination = -(degree_destination-1)*this.inter_relation_x/2+number_decalage_destination*this.inter_relation_x;
            x_origin = decalage_x_origin+x_o+w_o/2;
            x_destination = decalage_x_destination+x_d+w_d/2;
        }



        var x_label = (x_origin+x_destination)/2;
        var y_relation = y+this.height_init+this.inter_relation_y*level;

        if(y_relation>this.height) this.height=y_relation; 
        this.svg.path(defs,
            path.move(x_origin,y+this.decalage_y)
                .line(x_origin, y+this.height_init+this.inter_relation_y*level-this.courbure)
                .arc(sens*this.courbure,this.courbure, -1*sens*45,0,(sens>0)?0:1,x_origin+sens*this.courbure,y+this.height_init+this.inter_relation_y*level)
                .line(x_destination-sens*this.courbure, y_relation)
                .arc(this.courbure,-this.courbure,-45,0,(sens>0)?0:1,x_destination,y+this.height_init+this.inter_relation_y*level-this.courbure)
                .line(x_destination, y+this.decalage_y),{id: 'path_'+path_id, style: 'marker-end:url(#marker_'+path_id+')','class':'relation '+path_id});


        if(_relation_name){
            elm = this.svg.text(x_label,y+this.height_init+this.inter_relation_y*level-this.margin_label,_relation_name,{'class':class_relation+' relation label_relation '+path_id+' label_'+_origin+'_'+_destination, 'contentEditable':'true', 'style' :'text-anchor:middle;text-align:center', fontFamily: 'Verdana', fontSize: this.font_size_label, fill: color, id: self.svg_id+"_label_"+_origin+"_"+_destination});
        }
        else {
            elm = this.svg.text(x_label,y+this.height_init+this.inter_relation_y*level-this.margin_label,'unk',{'class':class_relation+' relation label_relation '+path_id+' label_'+_origin+'_'+_destination, 'contentEditable':'true', 'style' :'text-anchor:middle;text-align:center', fontFamily: 'Verdana', fontSize: this.font_size_label, fill: color, id: self.svg_id+"_label_"+_origin+"_"+_destination});
        }
        
        var relation = {governor:_origin,dependent:_destination,level:level,parser:_parser_id,relation:_relation_name,y:y_relation};
        this.relations.push(relation);

        if(self.mode=='edit')
        $(elm).click(function(){
            var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
            self.modifyRelation(relation_id);
        });

        var path_relation = this.svg.use('#path_'+path_id, {margin: 10, strokeWidth: parseFloat(this.stroke_width), fill: 'none', stroke: color,'class':class_relation+' relation '+path_id, id: path_id});
        
        if(!_relation_name)
            this.modifyRelation(_origin+'_'+_destination);

        if(self.mode=='edit')
        $('#'+path_id).click(function(i, elm){
            $('.'+path_id).remove();
            var rel = this.id.split('_');
            self.removeAnnotation(rel[0],rel[1]);
        });
        else
        $('#'+path_id).click(function(i, elm){
            // $('.'+path_id).remove();
            // var rel = this.id.split('_');
            // self.removeAnnotation(rel[0],rel[1]);
        });            
        $(path_relation).hover(function(){
            console.log($(this).attr('id'));
            var governor = $(this).attr('id').split('_')[2];
            var dependent = $(this).attr('id').split('_')[3];
            $('#'+self.svg_id+'_word_'+governor).addClass('highlight');
            $('#'+self.svg_id+'_word_'+dependent).addClass('highlight');
            var classname = $(this).attr('id');
            // $('.'+$(this).attr('id')).attr('cursor','copy');
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
    },

});


var t = '';
function gText(e) {
    t = (document.all) ? document.selection.createRange().text : document.getSelection();

    document.getElementById('input').innerHTML = t;
}

// document.onmouseup = gText;
// if (!document.all) document.captureEvents(Event.MOUSEUP);


var all_annotations = {!! $annotations->makeVisible(['word','word_position','governor_position','relation_name','source_id','parsers','score','best'])->toJson() !!};
var sentence = {!! $sentence->toJson() !!};
var parsers = {!! json_encode(array_values($parsers)) !!};
var data = {
    annotations : all_annotations,
    sentence : sentence,
    parsers : parsers
};

var graphs_container = new GraphSVGContainer(data);
graphs_container.draw();

</script>
@stop
@section('style')
{!! Html::style('css/jquery.svg.css') !!}
@stop