var destination = '';
var user_id=0;
var position_help=0;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
$(document).ready(function(){

    if($('#onglet-duel').length>0){
        var height = ($('#onglet-duel').height()-5)+'px';
        $('#count_pending_duel').css('bottom',height);
        $('#count_pending_duel').fadeIn();
    }
    $( window ).resize(function() {
        var height = ($('#onglet-duel').height()-5)+'px';
        $('#count_pending_duel').css('bottom',height);
    });
    $(document).on('mouseover', '.aideTool', function(e){
        var position = $('#helpRelation').position();
        var position_savant = $(this).offset();
        var offset = $('#helpRelation').offset();
        var scroll = $(document).scrollTop();
        var diff = position.top+scroll-offset.top+70;
        if(offset.top-scroll<70){
            position_help=scroll;
            $('#helpRelation').css({'top':diff,'position':'relative'});
        }
        else if(position_help>70){
            $('#helpRelation').css({'top':'','bottom':0,'position':'absolute'});
            adjustPositionHelp();
        }
    });
    $(document).on('scroll', function(e){
        adjustPositionHelp();
    });

    function adjustPositionHelp(){
        if($('#helpRelation').length>0){
            var position = $('#helpRelation').position();
            var position_savant = $("#savant").offset();
            var offset = $('#helpRelation').offset();
            var scroll = $(document).scrollTop();
            var diff = position.top+scroll-offset.top;
            if(scroll>70){
                position_help=scroll;
                $('#helpRelation').css({'top':diff,'position':'relative'});
            }
        }
    }
    /*
	$(document).on('click', 'a.connected', function(e){
        if($('#connected').val()!=0){
			return true;
        }
		e.preventDefault();
        destination = $(this).attr("href");
		$.ajax({
            url: base_url+"auth/login",
            success: function(result){
				$("#containerModal").html(result);
				$('#modalLogin').modal();
            }
        });
    });
    */
    $(document).on('submit', "#form-login" ,function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : url_site('auth/login'),
            data : $(this).serialize(),
            complete: function(e, xhr, settings){
                if(e.status === 422){
                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');                
                    $.each(e.responseJSON,function(key,error){
                        var elm = $('#'+key);
                        var parent = elm.parent('.form-group').get(0);
                        $(parent).addClass('has-error');
                        elm.after('<small class="help-block">'+error+'</small>');                        
                    });
                 }else if(e.status === 200){
                    window.location.href=destination;
                }else{

                }
            }
        });          
    }); 
    if($('#detail_user').length<1){
        var html = '<div id="detail_user" style="display:none;"><br/><span class="link">ajouter à mes ennemis</span></div>';
        $('body').append(html);
    }

    $('.rank').hover(
        
       function (event) {
            var html = $(this).html();
            user_id = $(this).attr('user_id');
            if($('#connected').val()!='0'&&$('#connected').val()!=user_id){
                user_id = parseInt(user_id,10);
                if(jQuery.inArray( user_id, enemies ) > -1){
                    html+='<br/>est un de vos ennemis';
                } else if(jQuery.inArray( user_id, pending_enemies ) > -1) {
                    html+='<br/><span>demande d\'ennemi en attente</span><br/><span class="link cancelEnemy">annuler la demande</span>';
                    var url_action = url_site('user/cancel-friend/');
                } else if(jQuery.inArray( user_id, ask_enemies ) > -1) {
                    html+='<br/><span>t\'as demandé en ennemi</span><br/><span class="link acceptEnemy">accepter la demande</span>';
                    var url_action = url_site('user/accept-friend/');
                } else {
                    html+='<br/><span class="link askEnemy">ajouter à mes ennemis</span>';
                }
                
            }

            var position = $(this).position();
            var left = position.left - 230;
            var top = position.top - 10;
            $('#detail_user').css("left",left+"px");
            $('#detail_user').css("top",top+"px");
            $('#detail_user').html(html);

            $(this).append($('#detail_user'));

            $('#detail_user').show();
            $('.askEnemy').on('touchend click', function(e){
                e.preventDefault();
                $.ajax({
                    method : 'GET',
                    url : url_site('user/ask-friend/') + user_id,
                    success : function(response){
                        $(e.currentTarget).html(response.html);
                        $(e.currentTarget).removeClass('link');
                        pending_enemies.push(user_id);
                    }
                });
                return false;
            }); 
            $('.cancelEnemy').on('touchend click', function(e){
                e.preventDefault();
                $.ajax({
                    method : 'GET',
                    url : url_site('user/cancel-friend/') + user_id,
                    success : function(response){
                        $(e.currentTarget).html("");
                        $(e.currentTarget).removeClass('link');
                        delete pending_enemies[jQuery.inArray( user_id, pending_enemies )];
                    }
                });
                return false;
            });  
            $('.acceptEnemy').on('touchend click', function(e){
                e.preventDefault();
                $.ajax({
                    method : 'GET',
                    url : url_site('user/accept-friend/') + user_id,
                    success : function(response){
                        $(e.currentTarget).html('');
                        $(e.currentTarget).removeClass('link');
                        delete ask_enemies[jQuery.inArray( user_id, ask_enemies )];
                        enemies.push(user_id);
                    }
                });
                return false;
            });            
       }, 
        
       function (event) {
          $('#detail_user').hide();
       }
    );
 
	$(document).on('click', '#register42', function(e){
        if($('#connected').val()=='1')
			return true;
		e.preventDefault();
		// 
		$("#modalLogin").animate({left: '100px'}, "slow");
		$("#modalLogin").hide(500,function(){

			$.ajax({
				url: base_url+"auth/register",
				success: function(result){
					$("#containerModal").append(result);
					$('#modalRegister').modal("show");
					// $('#modalRegister').slideDown(2000);
				}
			});		
			$('#modalLogin').modal('hide');			
		});
		
    });

    var periode = $('#periode').val();
	var type_score=$('#type_score').val();
	
    $('.periode-choice').on('click', function(){

        $('.periode-choice').removeClass('focus');
        $(this).addClass('focus');
        $('.rank').hide();
        $('.rank_neighbor').hide();
        $('.rank-'+type_score+'.'+this.id).show();
        periode=this.id;
    });

    $('#toggleScore').on('click', function(){
        if($(this).hasClass('points')){
            $(this).removeClass('points').addClass('annotations');
            $(this).html('annotations');
            type_score='annotations';
        } else if($(this).hasClass('annotations')) {
            $(this).removeClass('annotations').addClass('points');
            type_score='points';
            $(this).html('points');
        }
        
        $('.rank').hide();
        $('.rank-'+type_score+'.'+periode).show();

    });

    $('#my-position').on('click', function(){

        if($('.rank_neighbor.'+periode).length>0){
    		$('.rank').hide();
            $('.rank_neighbor.'+periode).show();
        }	
    });


});

function modal(text){
    var html ='<div class="modal fade" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal">'+text+'</div><div class="modal-footer"><button type="button" class="btn btn-lg btn-success" data-dismiss="modal" id="close-modal">'+trans('site.close')+'</button></div></div></div></div></div>';
    if($('#modalSimple').length>0){
        $('#modalSimple').remove();
    }
    $('body').append(html);
    $('#modalSimple').modal("show");
}

function newModal(){
    var html ='<div class="modal fade" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal"></div><div class="modal-footer"><button type="button" class="btn btn-lg btn-success" data-dismiss="modal" id="close-modal">'+trans('site.close')+'</button></div></div></div></div></div>';
    if($('modalSimple').length>0){
        $('modalSimple').remove();
    }
    $('body').append(html);
}
function newModalSimple(class_name){
    class_name = class_name || "";
    var html ='<div class="modal fade '+class_name+'" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal"></div><div class="modal-footer" id="modalFooter"></div></div></div></div></div>';
    if($('modalSimple').length>0){
        $('modalSimple').remove();
    }
    $('body').append(html);
}

function bonusObject(){
    setTimeout(function(){$('#bonus-object').remove()}, 3000);
    url = base_url + 'shop/objectWon';
    $('#bonus-object').on('click', function(){

        $.ajax({
            url : url,
            method : 'GET',
            success : function(response){
                $('#bonus-object').remove();
                var url = base_url + "img/object/"+ response.image;
                newModal();
                $('#contentModal').html('<div class="text-center">Bien joué, tu as gagné l\'objet : ' + response.name + '<br/><img src="' + url + '" class="bonus-object" /></div>');
                $('#modalSimple').modal('show');
            }
        });
    });    
}

// ====================================================================================================
function displaySentence(sentence, id_highlight, id_highlight2,_mode){
    var words = sentence.split(' ');
    var retour = '';
    var word;
    var id = 1;
    var nbQuote = 0;
    var regexp = /(_(?!\s))/g;

    for(var i = 0 ; i < words.length ; i++){
        word = words[i];
        word = word.replace(regexp , ' ');
        if(id == id_highlight || (id_highlight2 != undefined && id == id_highlight2)){
            var classe = 'highlight';
        }else{
            if(_mode=='special')
                var classe = 'disabled-word';
            else if(_mode=='upl')
                var classe = 'upl-word';
            else
                var classe = 'word';
        }
        retour += '<span class="'+classe+'" data-word-position="' + id + '" id="word_index_'+ id +'">';
        retour += word;
        retour += '</span>';
        if(i < words.length - 1){
            switch(words[i + 1]){
                case ',':
                    break;
                case '.':
                    break;
                case '"':
                    if(nbQuote % 2 == 0){
                        retour += ' ';
                    }
                    break;
                case '-il':
                    break;
                case ')':
                    break;
                default:
                    if(word[word.length - 1] == "'" ||  word == '('  || (word == '"' && nbQuote % 2 == 0 ) )
                        retour+='';
                    else {
                        retour += ' ';
                    }
                    break;
            }
            if(word=='"') nbQuote++;
        }
        id++;
    }
    return retour;
}
// ====================================================================================================
function displaySentenceUpl(sentence){
    var words = sentence.split(/[\s_]/);
    var retour = '';
    var word;
    var id = 1;
    var nbQuote = 0;
    var regexp = /(_(?!\s))/g;

    for(var i = 0 ; i < words.length ; i++){

        var compound_word = words[i].split('-');


        for (var index_word=0;index_word<compound_word.length;index_word++){
            var word = compound_word[index_word];
            word = word.replace(regexp , ' ');
            //Test trait d'union sur le premier word
            // if(compound_word.length>1 && index_word<(compound_word.length-1)){
            //     id++;
            //     word+='-';
            // }
            if(index_word>0){
                id++;
                retour += '<span class="upl-word" data-word-position="' + id + '" id="word_index_'+ id +'">-</span>';                
                id++;
            }
            retour += '<span class="upl-word" data-word-position="' + id + '" id="word_index_'+ id +'">'+word+'</span>';
        }

        if(i < words.length - 1){
            switch(words[i + 1]){
                case ',':
                    break;
                case '.':
                    break;
                case '"':
                    if(nbQuote % 2 == 0){
                        retour += ' ';
                    }
                    break;
                case '-il':
                    break;
                case ')':
                    break;
                default:
                    if(word[word.length - 1] == "'" ||  word == '('  || (word == '"' && nbQuote % 2 == 0 ) )
                        retour+='';
                    else {
                        retour += '<span> </span> ';
                    }
                    break;
            }
            if(word=='"') nbQuote++;
        }
        id++;
    }
    return retour;
}
function displaySentenceStats(sentence, id_highlight, user_answer){
    console.log ('[jeu.js] ENTER displaySentenceStats');
    var words = sentence.split(' ');
    var retour = '';
    var word;
    var id = 1;
    var nbQuote = 0;
    var regexp = /(_(?!\s))/g;

    for(var i = 0 ; i < words.length ; i++){
        word = words[i];
        word = word.replace(regexp , ' ');
        if(id == id_highlight){
            var classe = 'highlight';
        }else if(id==user_answer){
            var classe = 'user_answer';
        }else{
            var classe = 'word';
        }
        retour += '<span class="'+classe+'" data-word-position="' + id + '">';
        retour += word;
        retour += '</span>';
        if(i < words.length - 1){
            switch(words[i + 1]){
                case ',':
                    break;
                case '.':
                    break;
                case '"':
                    if(nbQuote % 2 == 0){
                        retour += ' ';
                    }
                    break;
                case '-il':
                    break;
                case ')':
                    break;
                default:
                    if(word[word.length - 1] == "'" ||  word == '('  || (word == '"' && nbQuote % 2 == 0 ) )
                        retour+='';
                    else {
                        retour += ' ';
                    }
                    break;
            }
            if(word=='"') nbQuote++;
        }
        id++;
    }

    return retour;
}

function url_site(path){
    return base_url + path;
}

function trans(key,attributes){
    try {
        var translation = translations[key];
        if(attributes){
            for(attribute in attributes){
                translation = translation.replace(':'+attribute,attributes[attribute]);
            }
        }
    }
    catch(err) {
        var translation = key;
    }

    return translation;
}

function trans_choice(key,attributes){
    var translation = translations[key].split("|");
    if(attributes){
        for(attribute in attributes){
            if(parseInt(attributes[attribute],10)<=1)
            translation = translation[0].replace(':'+attribute,attributes[attribute]);
            else
            translation = translation[1].replace(':'+attribute,attributes[attribute]);
        }
    }
    return translation;
}
/* Gestion of threads of discussion */
$(document).on('focus', '.message', function(){
    $(this).closest("form.form-message").find(".submitMessage").removeAttr("disabled");
});
$('.message-button').click(showThread);
$('#message-button').click(showThread);

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

function showThread(event){
    event.preventDefault();
    console.log("show thread");
    var entity_id = $(this).attr('data-id');
    var entity_type = $(this).attr('data-type');
    if(!$("#thread_"+entity_id).is(":visible")){
        $("#thread_"+entity_id).load(url_site('discussion/thread')+"?entity_id="+entity_id+"&entity_type="+entity_type, function(responseTxt, statusTxt, xhr){
            if(statusTxt == "success"){
                $("#thread_"+entity_id).slideDown(200,function(){
                    if($("#sentence").length>0){
                        var target_offset = $("#thread_"+entity_id).offset();
                        var target_top = target_offset.top-75;
                        $('.parallax').animate({
                            scrollTop: target_top
                        }, 500);
                    }                    
                });

            }
            if(statusTxt == "error")
                alert("Error: " + xhr.status + ": " + xhr.statusText);
        });
    } else {
        $("#thread_"+entity_id).slideUp(200);
    }
}
function followThread(){
    var annotation_id = $(this).attr('data-id');
    var $element = $(this);
    $.ajax({
        method : 'GET',
        url : base_url + 'discussion/follow-thread',
        data : {'id':annotation_id,'type':'annotation'},
        complete: function(response){
            $element.hide();
            $('span.unfollow-thread-button[data-id='+annotation_id+']').show();
        }
    });
}
function unFollowThread(){
    var annotation_id = $(this).attr('data-id');
    var $element = $(this);
    $.ajax({
        method : 'GET',
        url : base_url + 'discussion/un-follow-thread',
        data : {'id':annotation_id,'type':'annotation'},
        complete: function(response){
            $element.hide();
            $('span.follow-thread-button[data-id='+annotation_id+']').show();
        }
    });
}

$(document).on('click','.cancelReport', function(event){
    event.preventDefault();
    $(this).closest('.thread').slideUp();
});
$(document).on('click','.cancelAnswer', function(event){
    event.preventDefault();
    $(this).closest('.form-message').slideUp();
});

$(document).on('submit', ".form-message" ,function( event ) {
    event.preventDefault();
    var text = $.trim($(this).find("textarea").val());
    if(text!=""){
        var entity_id = $(this).attr('data-id');
        $.ajax({
            method : 'POST',
            url : base_url + 'discussion/new',
            data : $(this).serialize(),
            complete: function(response){
                $("#thread_"+entity_id).html(response.responseText);
            }
        });
    } else {
        alert("Veuillez saisir un message");
    }   
});
$(document).on('click', ".delete-message" ,function( event ) {
    event.preventDefault();
    if(confirm('Are you sure ?')){
        var message_id = $(this).attr('data-message-id');
        var entity_id = $(this).attr('data-entity-id');
        var entity_type = $(this).attr('data-type');
        $.ajax({
            method : 'GET',
            url : base_url + 'discussion/delete?entity_id='+entity_id+'&message_id='+message_id+'&entity_type='+entity_type,
            complete: function(response){
                $("#thread_"+entity_id).html(response.responseText);
            }
        });
    }
});
$(document).on('change', "#per-page" ,function( event ) {
    event.preventDefault();
    $('#discussions-selection').submit();
});
function scrollTo(id){
    $('html, body').animate({
        scrollTop: $("#"+id).offset().top
    }, 0);    
}
function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    var arrFrames = parent.parent.document.getElementsByTagName("IFRAME");
    for (var i = 0; i < arrFrames.length; i++) {
      if (arrFrames[i].name != obj.name) {
        resizeIframe(arrFrames[i]);
        }
    }
}
function resizeIframe2(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    var arrFrames = parent.parent.parent.document.getElementsByTagName("IFRAME");
    for (var i = 0; i < arrFrames.length; i++) {
      if (arrFrames[i].name != obj.name) {
        resizeIframe2(arrFrames[i]);
        }
    }
}
function resizeIframeAgain(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    var arrFrames = parent.document.getElementsByTagName("IFRAME");
    for (var i = 0; i < arrFrames.length; i++) {
      if (arrFrames[i].name != obj.name) {
        resizeIframeAgain(arrFrames[i]);
        }
    }
}

