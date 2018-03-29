GraphSVGContainer = function(data,mode) {
  this.init(data,mode);
}
function toggleNav() {
    var width_sidenav = document.getElementById("mySidenav").style.width;
    if(width_sidenav=="0px" || width_sidenav==''){
        document.getElementById("mySidenav").style.width = "30%";
        document.getElementById("main").style.marginRight = "30%";
    } else {
        document.getElementById("mySidenav").style.width = "0";
        document.getElementById("main").style.marginRight= "0";
    }
}
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginRight = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginRight= "0";
}
$.extend(GraphSVGContainer.prototype, {
    sentence : '',
    graphs : [],
    data : [],
    relations : [],
    parsers : [],
    main_widget : null, 
    mode : '', 

    init: function(data,mode) {
        this.data= data;
        this.sentence= data.sentence;
        this.parsers= data.parsers;
        this.graphs= [];
        this.mode= mode;
        this.config = this.loadConfig();
    },
    draw: function() {

        var container = $('<div id="container_'+this.sentence.id+'" class="container_svg"></div>');
        var sentence_hml = $('<div id="sentence_'+this.sentence.id+'"></div>').html(displaySentence(this.sentence.content,0,0));
        var container_svg_sentence = $('<div id="container_svg_'+this.sentence.id+'" class="container_svg_sentence"></div>');
        container.append(sentence_hml);
        container.append(container_svg_sentence);
        $('#content').append(container);      
            var self = this;
            if(this.mode=='correction' || this.mode=='adjudication' || this.mode=='reference'){
                this.main_widget = new GraphSVG(self, 'edit');
            } else if (this.mode=='diff') {
                this.main_widget = new GraphSVG(self, 'view','gold');
            } else if (this.mode=='view') {
                this.main_widget = new GraphSVG(self, 'view','game');
            }
            this.main_widget.draw();
            self.graphs.push(this.main_widget);

            self.graphs[this.main_widget.svg_id] = this.main_widget;  
            if (this.mode!='view' && this.mode!='reference') {
                var widget = new GraphSVG(self, 'view','game');
                widget.draw();
                self.graphs.push(widget);
            }
            $(self.parsers).each(function(){
                var widget = new GraphSVG(self, 'view',this.id);
                widget.draw();
                self.graphs.push(widget);
            });
            $('#btn_save').click(function(){
                self.save();
            });
            $('#btn_save_config').click(function(){
                self.saveConfig();
            });

        $('.param').change(function(){
            self.updateConfig();
            $(self.graphs).each(function(){
                this.my_svg.positionWords();
                this.drawRelationSVG();                 
            });
        });
        $('.word').click(function(elm){
            var data_word_position = parseInt($(this).attr("data-word-position"),10);
            var width_container = container_svg_sentence.width();
            var pos_x_svg = self.main_widget.my_svg.words[data_word_position].x-width_container/2+self.main_widget.my_svg.words[data_word_position].element.getComputedTextLength()/2;

            container_svg_sentence.stop().animate({scrollLeft:pos_x_svg}, '500', 'swing', function() {

                self.highlightNew(word_position);
                setTimeout(function(){ self.unhighlightNew(); }, 5000);
            });

        });

    },
    loadConfig: function(){

        var config = {};
        config.zoom = 1;
        config.font_size = 14;
        config.font_size_label = 8;
        config.font_size_pos = 8;
        config.inter_relation_x = 3;
        config.inter_relation_y = 9;
        config.height_arrow = 6;
        config.width_arrow = 2;
        config.stroke_width = 1;
        config.margin_label = 3;
        config.padding_relation = 0;
        config.inter_word = 5;
        config.padding_top_relation = 2;
        config.height_init = 5;
        config.courbure = 6;
        config.padding_top = 20;
        config.padding_top_pos = 15;
        config.orientation = 0;
        config.show_punctuations = 0;
        config.margin_left = 10;
        if(typeof config_user!=='undefined'){
            for(var key in config_user) { 
                var value = config_user[key];
                config[key]=parseInt(value,10);
            }             
        }        
        for(var key in config) { 
            var value = config[key];
            var s = `<div class="form-group row">
                    <label class="col-6 col-form-label col-form-label-sm">`+key+` :</label>
                      <div class="col-6">
                      <input class="form-control form-control-sm param" id="`+key+`" type="number" value="`+value+`" />
                      </div>
                    </div>`;
            var div = document.createElement('div');
            div.innerHTML = s;
            document.getElementById('menu-params').appendChild(div);
        }        

        return config;
    },
    updateConfig: function(){
        this.config.zoom = parseInt($('#zoom').val(),10);
        this.config.font_size = parseInt($('#font_size').val(),10);
        this.config.font_size_label = parseInt($('#font_size_label').val(),10);
        this.config.font_size_pos = parseInt($('#font_size_pos').val(),10);
        this.config.inter_relation_x = parseInt($('#inter_relation_x').val(),10);
        this.config.inter_relation_y = parseInt($('#inter_relation_y').val(),10);
        this.config.height_arrow = parseInt($('#height_arrow').val(),10);
        this.config.width_arrow = parseInt($('#width_arrow').val(),10);
        this.config.stroke_width = parseFloat($('#stroke_width').val());
        this.config.margin_label = parseInt($('#margin_label').val(),10);
        this.config.padding_relation = parseInt($('#padding_relation').val(),10);
        this.config.inter_word = parseInt($('#inter_word').val(),10);
        this.config.padding_top_relation = parseInt($('#padding_top_relation').val(),10);
        this.config.height_init = parseInt($('#height_init').val(),10);
        this.config.courbure = parseInt($('#courbure').val(),10);
        this.config.padding_top = parseInt($('#padding_top').val(),10);
        this.config.padding_top_pos = parseInt($('#padding_top_pos').val(),10);        
        this.config.orientation = parseInt($('#orientation').val(),10)%2;
        this.config.show_punctuations = parseInt($('#show_punctuations').val(),10)%2;

    },    
    highlightNew: function(elm,_classname='highlight'){
        $(this.graphs).each(function(){
            this.highlightNew(elm,_classname);
        });
    },
    unhighlightNew: function(_classname='highlight'){
        $(this.graphs).each(function(){
            this.unhighlightNew(_classname);
        });
    },
    save: function(){
        var mydata = [];
        var self = this;

        this.main_widget.my_svg.words.forEach(function(annotation,i ){
            if(annotation!==undefined){
                mydata[annotation.word_position]=annotation.filterAttributes();
                var relation = this.main_widget.my_svg.findRelation(annotation.word_position);
                if(relation!==undefined){
                    mydata[annotation.word_position].relation_name=relation.name;
                    mydata[annotation.word_position].governor_position=relation.governor;
                } else {
                    mydata[annotation.word_position].relation_name="";
                    mydata[annotation.word_position].governor_position=0;                    
                }
                // mydata[annotation.word_position].governor_position=annotation;
            }
        },this);
        $('#btn_save').attr("disabled","");
        $.ajax({
          type:  "POST",
          url:  base_url + "annotator/save",
          data: { 'sentence' : self.sentence, 'annotations' : mydata },
          success: function(msg){
            $('#btn_save').removeClass('btn-warning btn-alert btn-primary').addClass('btn-success');
          }
        });
      
    },
    saveConfig: function(){
        $.ajax({
          type:  "POST",
          url:  base_url + "annotator/save-config",
          data: { 'config' : this.config },
          success: function(msg){
            $('#btn_save_config').removeClass('btn-warning btn-alert btn-primary').addClass('btn-success');
          }
        });
      
    },
});

const xmlns = "http://www.w3.org/2000/svg";

class WordSVG {
    constructor(x,y,annotation,_root) {
        this.x = x;
        this.y = y||0;
        this.word = annotation.word;
        this.pos = '';
        this.lemma = '';
        this.features = '';
        this.word_position = annotation.word_position;
        this.classes = '';
        this.fontSize = 14;
        this.width = 0;
        this.nb_different_gov = 1;
        var elm = document.createElementNS(xmlns, 'text');
        elm.setAttributeNS(null, 'x', this.x);
        elm.setAttributeNS(null, 'y', this.y);
        elm.innerHTML = this.word;
        elm.data=this;
        elm.addEventListener('click', this.myAlert, false);
        this.element = elm;  
        this.width = elm.getComputedTextLength();
        this.xf = this.x + this.width;
    }
}

WordSVG.prototype.draw = function() {
    this.element.setAttributeNS(null, 'x', this.x);
    this.element.setAttributeNS(null, 'y', this.y);    
    this.width = this.element.getComputedTextLength();
    this.xf = this.x + this.width;
}

WordSVG.prototype.filterAttributes = function() {
    return {word_position: this.word_position, word: this.word, pos: this.pos, features: this.features, lemma: this.lemma};
}
WordSVG.prototype.myAlert = function(event,element,arr) {
    console.log(event.target.data.word);
    console.log(event.target.data.pos);
}

class MySVG {
  constructor(container,sentence, mode, _parent) {
    this.width = 0;
    this.current_x = 0;
    this.config = _parent.config;
    this.parent = _parent;
    var elm = document.createElementNS(xmlns, 'svg');
    elm.setAttributeNS(null, 'width', this.width);
    var group_sentence = document.createElementNS(xmlns, 'g');
    group_sentence.setAttributeNS(null, 'class', 'group_sentence');
    group_sentence.setAttributeNS(null, 'font-size', this.config.font_size);
    var group_relations = document.createElementNS(xmlns, 'g');
    group_relations.setAttributeNS(null, 'class', 'group_relations');
    var group_relations_name = document.createElementNS(xmlns, 'g');
    group_relations_name.setAttributeNS(null, 'class', 'group_relations_name');
    var group_pos = document.createElementNS(xmlns, 'g');
    group_pos.setAttributeNS(null, 'class', 'group_pos');
    group_pos.setAttributeNS(null, 'font-size', this.config.font_size_pos);

    var path_marker = document.createElementNS(xmlns, 'path');
    path_marker.setAttributeNS(null, 'd', 'M0,0L0,'+(5+this.config.width_arrow)+'L'+this.config.height_arrow+',5L0,'+(5-this.config.width_arrow));

    var label_parser = document.createElementNS(xmlns, 'text');
    label_parser.setAttributeNS(null, 'font-size', '10');

    var marker = document.createElementNS(xmlns, 'marker');
    marker.setAttributeNS(null, 'orient', 'auto');
    marker.setAttributeNS(null, 'refX', 4);
    marker.setAttributeNS(null, 'refY', 5);
    marker.setAttributeNS(null, 'markerWidth', 200);
    marker.setAttributeNS(null, 'markerHeight', 200);
    marker.setAttributeNS(null, 'id', 'my_marker');
    marker.appendChild(path_marker);
    elm.appendChild(marker);
    elm.appendChild(group_sentence);
    elm.appendChild(label_parser);
    elm.appendChild(group_relations);
    elm.appendChild(group_relations_name);
    elm.appendChild(group_pos);
    this.element = elm;
    this.group_sentence = group_sentence;
    this.label_parser = label_parser;
    this.group_relations = group_relations;
    this.group_relations_name = group_relations_name;
    this.group_pos = group_pos;
    this.id = sentence.id+'_'+mode;
    this.words = [];
    this.relations = [];
    container.appendChild(this.element);
  }
}

MySVG.prototype.addWord = function(word) {
    this.words[word.word_position]=word;
    word.x = this.current_x;
    this.group_sentence.appendChild(word.element);
    word.draw();
    this.current_x += this.config.inter_word+word.width;
    this.width = this.current_x;

}
MySVG.prototype.positionWords = function() {
    this.group_sentence.setAttributeNS(null,'font-size',this.config.font_size);
    current_x = 0;
    this.words.forEach(function(word){
        word.element.setAttributeNS(null,'x',current_x);
        word.element.setAttributeNS(null,'y',word.element.getBBox().height);
        current_x+=word.element.getComputedTextLength()+this.config.inter_word;
    },this);
    this.width = current_x;
    // this.element.setAttributeNS(null,'width',this.width);
}

MySVG.prototype.addRelation = function(relation) {
    function findDependent(_relation) {
        if(_relation!==undefined)
            return (_relation.dependent == relation.dependent) || (_relation.governor == relation.dependent && _relation.dependent == relation.governor  );
        else return false;        
    }
    var existing_relation = this.relations.find(findDependent);  

    if(existing_relation!=undefined){
        existing_relation.name = relation.name;
        existing_relation.governor = relation.governor;
        existing_relation.dependent = relation.dependent;
        existing_relation.class = relation.class;
    } else {
        this.parent.createRelationSVG(relation);        
        this.relations.push(relation);
    }

}
MySVG.prototype.hasRelation = function(dependent) {

    function findDependent(relation) {
        if(relation!==undefined)
      return relation.dependent == dependent;
        else return false;
    }
    var relation = this.relations.find(findDependent);
    return relation!==undefined;
    // return this.relations[dependent]!==undefined;

}
MySVG.prototype.hasAnnotation = function(annotation) {

    function findDependent(relation) {
        if(relation!==undefined)
            return relation.dependent == annotation.word_position && relation.governor == annotation.governor_position && relation.name == annotation.relation_name;
        else 
            return false;
    }
    var relation = this.relations.find(findDependent);
    return relation!==undefined;

}
MySVG.prototype.findRelation = function(dependent) {

    function findDependent(relation) {
        if(relation!==undefined)
      return relation.dependent == dependent;
        else return false;
    }
    var relation = this.relations.find(findDependent);
    return relation;
}

MySVG.prototype.attr = function(attr,value) {

    this.element.setAttributeNS(null, attr, value);
    var current_x = 0;

    for(key in this.words)
    {
        var word = this.words[key];
        word.x = current_x;
        word.draw();
        current_x += this.config.inter_word+word.width;
    }

}

class RelationSVG {
  constructor(dependent, governor, name=undefined) {
    this.dependent  = dependent;
    this.governor   = governor;
    this.name       = name;
    this.level      = 1;
    this.id = name+'_'+governor+'_'+dependent;
  }
}

RelationSVG.prototype.addWord = function(word) {

    this.words[word.word_position]=word;
    word.x = this.current_x;
    this.element.appendChild(word.element);
    word.draw();
    this.current_x += this.config.inter_word+word.width;

}

GraphSVG = function(container, graph_id, parser_id) {
  this.init(container, graph_id, parser_id);
}

$.extend(GraphSVG.prototype, {

    container : null,
    all_annotations : [],
    annotations : [],
    words : [],
    relations : [],
    sentence : [],
    origin:"",destination:"",
    width_min_relation:0,

    width_svg : 200,

    height:0,
    width:0,

    select_relation_open : false,
    svg:null, 
    my_svg:null, 
    svg_id: '',

    init: function(container,mode,parser_id = null) {

        this.all_annotations = container.data.annotations;
        this.addRoot();
        this.sentence = container.data.sentence;
        this.container = container;
        this.config = container.config;
        this.mode = mode;
        this.words = [];
        this.parser_id = parser_id;
        var sentence_id = this.sentence.id;
        this.svg_id = sentence_id+'_'+mode;

        if(mode=='view')
            this.svg_id += '_'+parser_id;
      
        var container_svg = document.createElement('div');
        container_svg.setAttribute('id','container_'+this.svg_id);
        container_svg.setAttribute('class','container_svg');      
        var svg_html = document.createElement('div');
        svg_html.setAttribute('id',this.svg_id);
        svg_html.setAttribute('class','graph_svg graph_svg_'+this.mode);

        this.my_svg = new MySVG(container_svg, this.sentence, this.mode, this);
        if(mode=='edit')
            this.my_svg.element.addEventListener('click', function(e){
                if(e.target.id==this.id && this.origin!='')
                    alert(e.target.id);
                // self.modifyRelationNew(relation);
            }, false);
        document.getElementById('container_svg_'+sentence_id).appendChild(container_svg);
        document.getElementById('container_svg_'+sentence_id).appendChild(svg_html);
        this.container_svg = container_svg;

    },
    addRoot: function() {
        var root_json = {word_position:0, governor_position: 0, relation_name: '', word: '&#216;', pos: 'ROOT', source_id: 1, user_id: 0, parsers: [] };
        this.all_annotations.unshift(root_json);
    },
    draw: function() {

        width_svg=Math.floor($('#width_block').width());
        var x=20;
        var current_x=20;
        var y=0;
        var current_word = -1;
        var index = 0;

        this.width_min_relation = $('#select_relations').width() + 2*this.config.padding_relation ;
        this.width_min_relation = 0;
        var width_words =[];

        var self = this;

        this.all_annotations.forEach(function(annotation,i){
            if(!(annotation.word_position==99999 || annotation.governor_position==99999 || (annotation.source_id==2 && annotation.best!=1))){
                if(annotation.word_position!=current_word){
                    current_word=annotation.word_position;
                    var new_word = new WordSVG(current_x,y,annotation);
                    self.my_svg.addWord(new_word);
                    var y = new_word.element.getBBox().height;
                    console.log(y);
                    console.log(new_word);
                    new_word.element.setAttributeNS(null,'y',y);
                    self.addWordListener(new_word);
                    var pos_svg = document.createElementNS(xmlns, 'text');
                    pos_svg.innerHTML = new_word.pos;
                    self.my_svg.group_pos.appendChild(pos_svg);
                    new_word.posSVG = pos_svg;
                } else {
                    if(annotation.relation_name!='_')
                    if(self.my_svg.words[annotation.word_position].governor_position != annotation.governor_position
                        || self.my_svg.words[annotation.word_position].relation_name != annotation.relation_name)
                    self.my_svg.words[annotation.word_position].nb_different_gov++;           
                }               
                self.annotations[index++]=self.all_annotations[i];               
            }
        },this);
        this.initRelationsWidget();
        this.drawRelationSVG();
        this.my_svg.element.setAttributeNS(null,'id',this.svg_id);
        this.my_svg.element.setAttributeNS(null,'class','graph_svg graph_svg_'+this.mode);

    },
    createRelationSVG: function(relation) {
        var self=this;
        var path = document.createElementNS(xmlns, 'path');
        path.setAttributeNS(null, 'fill', 'none');
        path.setAttributeNS(null, 'style', 'marker-end:url(#my_marker)');
        relation.arcSVG = path;
        path.addEventListener("mouseenter", function( event ) {
            // highlight the mouseenter target
            self.container.highlightNew(relation.dependent);
        }, false); 
        path.addEventListener("mouseleave", function( event ) {
            // highlight the mouseenter target
            self.container.unhighlightNew();
        }, false); 
        var name_relation = document.createElementNS(xmlns, 'text');
        name_relation.setAttributeNS(null, 'content-editable', 'false');
        name_relation.setAttributeNS(null, 'text-anchor', 'middle');
        name_relation.setAttributeNS(null, 'text-align', 'center');
        name_relation.setAttributeNS(null, 'font-family', 'Verdana');
        this.my_svg.group_relations_name.appendChild(name_relation);
        name_relation.addEventListener("mouseenter", function( event ) {   
        // highlight the mouseenter target
            self.container.highlightNew(relation.dependent);
        }, false);
        name_relation.addEventListener("mouseleave", function( event ) {   
        // unhighlight the target
            self.container.unhighlightNew();
        }, false); 
        if(self.mode=='edit')
            name_relation.addEventListener('click', function(e){
                self.modifyRelationNew(relation);
            }, false);
        else if(self.container.mode=='correction')
            name_relation.addEventListener('click', function(e){
                self.validateRelationNew(e,relation);
            }, false);
        relation.labelSVG = name_relation;

    },    
    addWordListener: function(word){
        var self = this;
        word.element.addEventListener('mouseover',function(event){
            self.container.highlightNew(word.word_position);
        },false);
        word.element.addEventListener('mouseout',function(event){
            self.container.unhighlightNew();
        },false);
        if(this.mode=='edit')
        word.element.addEventListener('click',function(event){
            if(self.origin==""){
                self.origin = word;
                word.element.classList.add('selected');
            }
            else if(self.origin==word){
                word.element.classList.remove('selected');
                self.origin = "";
            }
            else {
                word.element.classList.add('selected');
                // self.origin=="";
                // $('.select_relation').remove();
                var new_relation = new RelationSVG(word.word_position, self.origin.word_position);
                new_relation.class = "default";
                self.my_svg.addRelation(new_relation);
                self.drawRelationSVG();
            }
        },false);

    },
    placeLabelRelationNew: function(){

        var height_relations = this.my_svg.group_relations.getBBox().height;
        this.my_svg.relations.forEach(function(relation){
            if(relation.governor==0) return true;
            var new_y = parseInt(height_relations-relation.y_relation,10);
            
            relation.labelSVG.setAttributeNS(null,'y',new_y);

        },this);
    },
    highlightNew: function(word_position,_classname='highlight'){
        var self = this;

        this.my_svg.relations.forEach(function(relation){

                        if(relation===undefined) return;
            if(word_position==relation.governor || word_position==relation.dependent){

                relation.arcSVG.classList.add(_classname);

                if(word_position==relation.dependent){
                    this.my_svg.words[word_position].element.classList.add("hl-success");
                    this.my_svg.words[relation.governor].element.classList.add("hl-alert");
                    relation.labelSVG.classList.add("hl-alert");
                }
                else if(word_position==relation.governor){

                    this.my_svg.words[relation.governor].element.classList.add("hl-success");
                    this.my_svg.words[relation.dependent].element.classList.add("hl-primary");
                    relation.labelSVG.classList.add("hl-primary");
                }

            } else {
                relation.arcSVG.classList.add('un'+_classname);
                relation.labelSVG.classList.add('un'+_classname);                
            }
        },this);
    },
    unhighlightNew: function(_classname='highlight'){
        
        this.my_svg.relations.forEach(function(relation){

            relation.arcSVG.classList.remove(_classname);
            relation.labelSVG.classList.remove(_classname);
            relation.arcSVG.classList.remove('un'+_classname);
            relation.labelSVG.classList.remove('un'+_classname);
            ["hl-alert","hl-warning",'hl-info',"hl-success","hl-primary"].forEach(function(className){
                relation.labelSVG.classList.remove(className);
            });

  
        });
        this.my_svg.words.forEach(function(word){

            ["hl-alert","hl-warning",'hl-info',"hl-success","hl-primary"].forEach(function(className){
                word.element.classList.remove(className);
            });
  
        });
    },
    deleteRelations: function(){
        $('#'+this.svg_id+' .relation').remove();
        this.relations=[];
        this.annotations=[];
        this.draw();
    },

    validateRelationNew: function(e,relation){
        var new_relation = new RelationSVG(relation.dependent, relation.governor, relation.name);
        new_relation.class = "success";
        this.container.main_widget.my_svg.addRelation(new_relation);
        this.container.main_widget.drawRelationSVG();
    },

    drawRelationSVG: function(){

        this.traceRelationsNew();
        this.tracePosNew();
        var y = 0;
        var x = this.config.margin_left;
        this.my_svg.label_parser.innerHTML = this.parser_id;    
        y_parser = this.my_svg.label_parser.getBBox().height;
        this.my_svg.label_parser.setAttributeNS(null,'y',y_parser);
        this.my_svg.label_parser.setAttributeNS(null,'x',0);
        if(this.config.orientation==1){
            //  Arcs vers le bas
            this.my_svg.group_sentence.setAttributeNS(null, 'transform', '');
            y+=this.my_svg.group_sentence.getBBox().height+1;
            this.my_svg.group_pos.setAttributeNS(null, 'transform', 'translate(0 '+y+')');
            y+=this.my_svg.group_pos.getBBox().height+1;+ +this.config.padding_top_relation;
            this.my_svg.group_relations_name.setAttributeNS(null, 'transform', 'translate(0 '+y+')');
            this.my_svg.group_relations.setAttributeNS(null, 'transform', 'translate(0 '+y+')');
            y+=this.my_svg.group_relations.getBBox().height+1;
            this.height =  y;
        } else {
            //  Arcs vers le haut
            this.my_svg.group_relations.setAttributeNS(null, 'transform', 'translate('+x+' '+(this.my_svg.group_relations.getBBox().height+this.config.padding_top+this.config.font_size_label+this.config.margin_label)+') scale(1,-1)');
            this.my_svg.group_relations_name.setAttributeNS(null, 'transform', 'translate('+x+' '+(this.my_svg.group_relations.getBBox().height+this.config.padding_top+this.config.font_size_label+this.config.margin_label)+')');
            y+=this.my_svg.group_relations.getBBox().height+this.config.padding_top+this.config.margin_label+this.config.font_size_label;
            this.my_svg.group_sentence.setAttributeNS(null, 'transform', 'translate('+x+' '+(y+this.config.padding_top_relation)+')');
            y+=this.my_svg.group_sentence.getBBox().height+this.config.padding_top_relation;
            this.my_svg.group_pos.setAttributeNS(null, 'transform', 'translate('+x+' '+(y)+')');
            y+=this.my_svg.group_pos.getBBox().height   ;
           this.height =  y+10 ;
        }
        var zoom = 1+(this.config.zoom-1)/10;
        this.my_svg.element.setAttributeNS(null,'width',(this.my_svg.width+20+x)*zoom);
        this.my_svg.element.setAttributeNS(null,'height',(this.height+this.config.padding_top)*zoom);        
        this.my_svg.element.setAttributeNS(null,'viewBox',"0 0 "+parseInt((this.my_svg.width+20))+" "+parseInt((this.height+this.config.padding_top)));

    },

    modifyRelationNew: function(relation){

        var self = this;
        var margin_select_relation = 15;
        // self.highlight(relation_id,'selected');

        // this.my_svg.relation.labelSVG

        var top = this.my_svg.group_relations.getBBox().height - relation.level*this.config.inter_relation_y-margin_select_relation; 
        // parseInt(relation.labelSVG.getAttribute('y'),10)-margin_select_relation;
        var left = parseInt(relation.labelSVG.getAttribute('x'),10) - ($('#select_relations').width()+15)/2;

        var id = 'select_relation_'+relation.id;

        var new_menu = document.getElementById("select_relations").cloneNode(true);
        new_menu.style.top= top+"px";
        new_menu.style.left= left+"px";
        new_menu.setAttribute('id',id);
        new_menu.setAttribute('class','select_relation relation relation_'+relation.id);
        this.container_svg.appendChild(new_menu);

        for (var i = 0; i < new_menu.children.length; i++) {
            if(new_menu.children[i].innerHTML==relation.name)
                new_menu.children[i].setAttribute('selected',true);
        }

        new_menu.addEventListener('mouseup', function(){
            self.select_relation_open = true;
            new_menu.addEventListener('click', function(){
                if(self.select_relation_open === false){

                    self.saveSelectedRelationNew(this,relation);

                } else {
                    self.select_relation_open = false;
                }
            },false);
        }, false);

        new_menu.addEventListener('blur', function(){
            self.saveSelectedRelationNew(this,relation);
        }, false);     
        new_menu.addEventListener('change', function(){
            self.saveSelectedRelationNew(this,relation);
        }, false);
        relation.labelSVG.style.display = 'none';
  
    },
    deleteRelationsSVG: function(){
        $('#'+this.svg_id+' .relation').remove();
    },
    deleteRelation: function(governor,dependent){
        $('#'+this.svg_id+' .relation_'+governor+'_'+dependent).remove();
    },

    saveSelectedRelationNew: function(elm_select, relation){
        var relation_id = $(elm_select).attr('id').match(/\d+\_\d+$/)[0];
        var relation_name = $(elm_select).find(":selected").text();
        relation.name = relation_name;
        relation.labelSVG.innerHTML = relation_name;
        relation.labelSVG.style.display = "";
        elm_select.remove();
        this.my_svg.words.forEach(function(word){
            word.element.classList.remove('selected');
        },false);
    },
    /*
    Selection of the annotatons to display in the widget
    */
    initRelationsWidget: function(){
        this.annotations.forEach(function(annotation, key, arr){

            if(annotation.governor_position==0 && annotation.relation_name!="root" && annotation.pos!="ROOT")
                return true;
            // if(annotation.governor_position==0)
 
            if(!this.config.show_punct && annotation.relation_name=="ponct")
                return true;

            if(this.my_svg.hasRelation(annotation.word_position)){
                return true;
            }

            if(this.mode=='view'){

                if(this.parser_id=='game') {
                    if(annotation.best==0)
                        return true;
                } 
                else if(this.parser_id=='gold') {
                    if(annotation.user_id!='gold')
                        return true;
                }
                else {
                    if (typeof annotation.parsers === 'undefined')
                        return true;
                    else {
                        var in_array = false;
                        annotation.parsers.forEach(function(parser){
                            if(this.parser_id==parser.id)
                                in_array = true;
                        },this);
                        if(!in_array)
                            return true;
                    }
                }
            }
            var class_relation = "default";

            if(this.container.mode=="correction"){
                if (typeof annotation.parsers === 'undefined')
                    return true;
                if (annotation.parsers.length<this.container.parsers.length && annotation.best==1){
                    parser_id = 0;
                    class_relation = "success";
                }
                else if (annotation.parsers.length==0 && annotation.best==1){
                    class_relation = "success";
                }            
                else if(annotation.parsers.length==1){
                    if(this.my_svg.words[annotation.word_position].nb_different_gov==1) class_relation = "warning";
                    else if (this.mode=='edit') class_relation = "alert";
                    else class_relation = "alert";
                    parser_id = annotation.parsers[0].id;
                }
                else if (annotation.parsers.length==this.container.parsers.length){
                    parser_id = 0;
                    class_relation = "disabled";
                }
            } else if(this.container.mode=="diff"){
                if(this.container.main_widget.my_svg.hasAnnotation(annotation)){

                    class_relation = "success";
                }
                else {

                    class_relation = "alert";
                }
            }
            this.my_svg.words[annotation.word_position].class = "default";
            this.my_svg.words[annotation.word_position].lemma = annotation.lemma;
            this.my_svg.words[annotation.word_position].pos = annotation.pos;
            this.my_svg.words[annotation.word_position].posSVG.innerHTML = annotation.pos;
            this.my_svg.words[annotation.word_position].features = annotation.features;
            if((annotation.word_position!=0 && annotation.governor_position!=0)||annotation.relation_name=="root"){

                var new_relation = new RelationSVG(annotation.word_position, annotation.governor_position, annotation.relation_name);
                new_relation.class = class_relation;
                this.my_svg.addRelation(new_relation);
            }
        },this);
    },
    traceRelationsNew: function(){

        this.my_svg.relations.sort(function(a,b){
            return Math.abs(a.dependent - a.governor) - Math.abs(b.dependent - b.governor);
        });

        this.my_svg.relations.forEach(function(relation){
            relation.level=0;
        },this); 
        this.my_svg.relations.forEach(function(relation){
            this.traceArrowNew(relation);
        },this);         

    }, 
    tracePosNew: function(){
        this.my_svg.group_pos.setAttributeNS(null,'font-size',this.config.font_size_pos);
        this.my_svg.words.forEach(function(word){
            var pos_svg = word.posSVG;
            var x_d = parseFloat(word.element.getAttribute('x'));
            var w_d = word.element.getComputedTextLength();
            var w_pos = pos_svg.getComputedTextLength();         
            pos_svg.setAttributeNS(null, 'x', x_d+w_d/2-w_pos/2);
            pos_svg.setAttributeNS(null, 'y', pos_svg.getBBox().height);
            this.height_pos = pos_svg.getBBox().height;                

        },this);         

    },    

    removeAnnotation: function(governor,dependent){

    },

    getLevelRelationNew: function(_relation){

        var level_relation = 0;
        var governor = parseInt(_relation.governor,10);
        var dependent = parseInt(_relation.dependent,10);
        var borne_inf = governor>dependent?dependent:governor;
        var borne_sup = governor<dependent?dependent:governor;
        this.my_svg.relations.forEach(function(relation){
            if((relation.governor>borne_inf&&relation.governor<borne_sup&&_relation.governor>relation.governor)||(relation.dependent>borne_inf&&relation.dependent<borne_sup))
                if(relation.level>level_relation){
                    level_relation = relation.level;
                }
        });
        _relation.level = level_relation+1;
        return level_relation+1;
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
        var governor = parseInt(governor,10);
        var dependent = parseInt(dependent,10);
        $(this.my_svg.relations).each(function(index){
            // if(this.governor_position==0) continue;
            var same_side_of_governor = Math.sign((dependent-governor)/(this.dependent-governor))>0?true:false;
            if( (this.governor==governor && (dependent==null || (same_side_of_governor && this.dependent>dependent) || (!same_side_of_governor && this.dependent<dependent))))
                number++;
            var same_side_of_governor = Math.sign((dependent-governor)/(governor-this.governor))>0?true:false;
            if( (this.dependent==governor && (this.governor!=0 || this.name=='root') && (dependent==null || (same_side_of_governor && this.governor<dependent) || (!same_side_of_governor && this.governor>dependent))))
                number++;
           
           
        });
        return number;
    },
    getDegree: function(governor,dependent=null){
        var governor = parseInt(governor,10);
        var dependent = parseInt(dependent,10);        
        var number = 0;
        $(this.my_svg.relations).each(function(index){
            if(this.governor==governor || this.dependent==governor)
                number++;
        });
        return number;
    },

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
        var self = this;
        
        this.my_svg.words.forEach(function(word,i,arr){

            if(word.word_position>=_word){

                var x=parseInt($(word.element).attr('x'),10)+decalage;
                $(word.element).attr('x',x);

                if(word.word_position==arr.length-1){
                    x+=this.config.inter_word+word.element.getComputedTextLength();
                    if(x>this.my_svg.width)
                        this.my_svg.width = x;
                }
            }
        },this);
    },

    traceArrowNew: function(relation){

        var self = this;
        // if(relation.governor==0) return;
        var dependent = this.my_svg.words[relation.dependent];    
        var governor = this.my_svg.words[relation.governor];  
        _origin = governor.word_position;    
        _destination = dependent.word_position;    
        _relation_name = relation.name;    
        var class_relation = relation.class;

        var color = "black";
        var id_origin = this.svg_id+"_word_"+_origin;
        var id_destination = this.svg_id+"_word_"+_destination;
        var svg_word_origin = governor.element;
        var svg_word_destination = dependent.element;

        var w_o = svg_word_origin.getComputedTextLength(); // width of the governor
        var w_d = svg_word_destination.getComputedTextLength(); // width of the dependent

        var y = parseFloat(svg_word_origin.getAttribute('y'));
        var x_o = parseFloat(svg_word_origin.getAttribute('x'));
        var x_d = parseFloat(svg_word_destination.getAttribute('x'));

        var level = this.getLevelRelationNew(relation);
        relation.level = level;
        var path_id = this.svg_id+'_'+_origin+'_'+_destination;        

        
        var path_relation = relation.arcSVG;
        var name_relation = relation.labelSVG;


        class_relation += ' relation_'+_origin+'_'+_destination;
        
        var decalage_y = 5;

        var sens = Math.sign(_destination-_origin);

        var degree_origin = this.getDegree(_origin);
        var degree_destination = this.getDegree(_destination);
        var number_decalage_origin = this.getNumberOfDependents(_origin,_destination);
        var number_decalage_destination = this.getNumberOfDependents(_destination,_origin);
        var decalage_x_origin = -(degree_origin-1)*this.config.inter_relation_x/2+number_decalage_origin*this.config.inter_relation_x;
        var decalage_x_destination = -(degree_destination-1)*this.config.inter_relation_x/2+number_decalage_destination*this.config.inter_relation_x;

        var x_origin = decalage_x_origin+x_o+w_o/2;
        var x_destination = decalage_x_destination+x_d+w_d/2;

        if(true && Math.abs(x_destination-x_origin)<Math.max(this.width_min_relation,2*this.config.courbure)){
            var decalage = Math.max(this.width_min_relation,2*this.config.courbure);
            this.decalWords(Math.max(_origin,_destination),decalage-Math.abs(x_destination-x_origin));
            x_o = parseInt($(svg_word_origin).attr("x"),10);
            x_d = parseInt($(svg_word_destination).attr("x"),10);
            decalage_x_origin = -(degree_origin-1)*this.config.inter_relation_x/2+number_decalage_origin*this.config.inter_relation_x;
            decalage_x_destination = -(degree_destination-1)*this.config.inter_relation_x/2+number_decalage_destination*this.config.inter_relation_x;
            x_origin = decalage_x_origin+x_o+w_o/2;
            x_destination = decalage_x_destination+x_d+w_d/2;
        }



        var x_label = (x_origin+x_destination)/2;
        var y_relation = this.config.height_init+this.config.inter_relation_y*level;
        
        if(y_relation>this.height) 
            this.height=y_relation;

        var chemin = '';
        chemin+='M'+x_origin+',0';
        chemin+='L'+x_origin+','+(y_relation-this.config.courbure);
        chemin+='A'+sens*this.config.courbure+','+this.config.courbure+' '+(-1*sens*45)+' 0,'+((sens>0)?0:1)+' '+(x_origin+sens*this.config.courbure)+','+y_relation;
        chemin+='L'+(x_destination-sens*this.config.courbure)+','+y_relation;
        chemin+='A'+this.config.courbure+',-'+this.config.courbure+' '+(-45)+' 0,'+((sens>0)?0:1)+' '+x_destination+','+(y_relation-this.config.courbure);
        chemin+='L'+x_destination+',0';

        path_relation.setAttributeNS(null, 'd', chemin);
        // path_relation.setAttributeNS(null, 'stroke-width', parseFloat(this.config.stroke_width));
        path_relation.setAttributeNS(null, 'class', class_relation);
        
        this.my_svg.group_relations.appendChild(path_relation);

        // this.svg.rect(x_origin-5,y+this.decalage_y,11,this.height_init+this.inter_relation_y*level,0,0,
        //     {fill:'transparent','class':'relation '+path_id, id: 'rect_gov_'+path_id});
        // this.svg.rect(x_destination-5,y+this.decalage_y,11,this.height_init+this.inter_relation_y*level,0,0,
        //     {fill:'transparent','class':'relation '+path_id, id: 'rect_dep_'+path_id});
        // this.svg.rect(((x_origin<x_destination)?x_origin:x_destination)-5,y+this.decalage_y+this.height_init+this.inter_relation_y*level-10,Math.abs(x_destination-x_origin),11,0,0,
        //     {fill:'transparent','class':'relation '+path_id, id: 'rect_top_'+path_id});


        name_relation.setAttributeNS(null, 'font-size', this.config.font_size_label);
        name_relation.setAttributeNS(null, 'class', class_relation);
        name_relation.setAttributeNS(null, 'x', x_label);
        if(this.config.orientation==1)
            var y_label = y_relation-this.config.margin_label;
        else
            var y_label = -y_relation-this.config.margin_label;
        name_relation.setAttributeNS(null, 'y', y_label);
        if(relation.name)
            name_relation.innerHTML = relation.name;
        else
            name_relation.innerHTML = "";
                   
        if(self.mode=='edit' && !_relation_name){
            self.modifyRelationNew(relation);
        } 

        return true;

        if(self.mode=='edit')
        $(elm).click(function(){
            var relation_id = $(this).attr('id').match(/\d+\_\d+/)[0];
            self.modifyRelation(relation_id);
        });
        
        if(!_relation_name)
            this.modifyRelation(_origin+'_'+_destination);

        if(self.mode=='edit')
        $('#'+path_id).click(function(i, elm){
            $('.'+path_id).remove();
            var rel = this.id.split('_');
            self.removeAnnotation(rel[2],rel[3]);
        });
        else
        $('#'+path_id).click(function(i, elm){
            // $('.'+path_id).remove();
            // var rel = this.id.split('_');
            // self.removeAnnotation(rel[0],rel[1]);
        });            
        $(path_relation).hover(function(){
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
    myAlert: function(){
        console.log("marker");        
    },

});