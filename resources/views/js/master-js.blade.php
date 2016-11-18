//Beginning of the real master
@if(Auth::check())
var enemies = {!! Auth::user()->getListAcceptedFriends()->toJson() !!};
var pending_enemies = {!! Auth::user()->getListPendingFriendRequests()->toJson() !!};
var ask_enemies = {!! Auth::user()->getListAskFriendRequests()->toJson() !!};
@else
var enemies = [];
var pending_enemies = [];
var ask_enemies = [];
@endif
var destination = '';
var user_id=0;
var position_help=0;
var translations = {
    'site.close' : "{{ trans('site.close') }}"
};
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
        var diff = position.top+scroll-offset.top;
        if(offset.top-scroll<0){
            position_help=scroll
            console.log ('[jeu.js] test'+offset.top-scroll);
            $('#helpRelation').css({'top':diff,'position':'relative'});
        }
        else if(position_help>0){
            $('#helpRelation').css({'top':'','bottom':0,'position':'absolute'});
            adjustPositionHelp();
        }
    });

    function adjustPositionHelp(){
        var position = $('#helpRelation').position();
        var position_savant = $(this).offset();
        var offset = $('#helpRelation').offset();
        var scroll = $(document).scrollTop();
        var diff = position.top+scroll-offset.top;
        if(offset.top-scroll<0){
            position_help=scroll
            console.log ('[jeu.js] test'+offset.top-scroll);
            $('#helpRelation').css({'top':diff,'position':'relative'});
        }
    }

	$(document).on('click', 'a.connected', function(e){
    return true;
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
    $(document).on('submit', "#form-login" ,function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : "{{ url('auth/login') }}",
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
        var html = '<div id="detail_user"><br/><span class="link">ajouter à mes ennemis</span></div>';
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
                    var url_action = "{{ url('user/cancel-friend/') }}/";
                } else if(jQuery.inArray( user_id, ask_enemies ) > -1) {
                    html+='<br/><span>t\'as demandé en ennemi</span><br/><span class="link acceptEnemy">accepter la demande</span>';
                    var url_action = "{{ url('user/accept-friend/') }}/";
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
                    url : "{{ url('user/ask-friend/') }}/" + user_id,
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
                    url : "{{ url('user/cancel-friend/') }}/" + user_id,
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
                    url : "{{ url('user/accept-friend/') }}/" + user_id,
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
 
	$(document).on('click', '#register', function(e){
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
		// alert($(this).attr('href'));
        // alert('ok');
		
    });
    var $classement = [];
    $classement['semaine'] = $('#classementSemaine');
    $classement['mois'] = $('#classementMois');
    $classement['total'] = $('#classementTotal');

    var backgroundClair = '#9BC5AA';
    var backgroundFonce = '#0E7F3C';

    var couleurClair = '#FFF';
    var couleurFonce = '#4a1710';

    var periode = "week";
	var type_score="points";
	
    $('#semaine').css('color', couleurFonce);

    $('#semaine').on('click', function(){
        for(x in $classement){
            $classement[x].hide();
        }
        $classement['semaine'].show();
        $('#semaine').css({
            'background-color' : backgroundClair,
            'color' : couleurFonce
        });
        $('#mois').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
        $('#total').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
    });

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


    $('#mois').on('click', function(){
        for(x in $classement){
            $classement[x].hide();
        }
        $classement['mois'].show();
        $('#mois').css({
            'background-color' : backgroundClair,
            'color' : couleurFonce
        });
        $('#total').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
        $('#semaine').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
    });
    $('#total').on('click', function(){
		if($('#total').hasClass('periode-choice'))
			return;
        for(x in $classement){
            $classement[x].hide();
        }
        $classement['total'].show();
        $('#total').css({
            'background-color' : backgroundClair,
            'color' : couleurFonce
        });
        $('#mois').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
        $('#semaine').css({
            'background-color' : backgroundFonce,
            'color' : couleurClair
        });
    });

});

function modal(text){
    var html ='<div class="modal fade" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal">'+text+'</div><div class="modal-footer"><button type="button" class="btn btn-lg btn-success" data-dismiss="modal">'+trans('site.close')+'</button></div></div></div></div></div>';
    if($('#modalSimple').length>0){
        $('#modalSimple').remove();
    }
    $('body').append(html);
    $('#modalSimple').modal("show");
}

function newModal(){

    var html ='<div class="modal fade" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal"></div><div class="modal-footer"><button type="button" class="btn btn-lg btn-success" data-dismiss="modal">'+trans('site.close')+'</button></div></div></div></div></div>';
    if($('modalSimple').length>0){
        $('modalSimple').remove();
    }
    $('body').append(html);
    $('#modalSimple').modal("show");
}
function newModalSimple(){

    var html ='<div class="modal fade" id="modalSimple" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal">&times;</button><div id="contentModal"></div><div class="modal-footer"></div></div></div></div></div>';
    if($('modalSimple').length>0){
        $('modalSimple').remove();
    }
    $('body').append(html);
    $('#modalSimple').modal("show");
}

// ====================================================================================================
function displaySentence(sentence, id_highlight, id_highlight2,_mode){
    console.log ('[jeu.js] ENTER displaySentence');
    var mots = sentence.split(' ');
    var retour = '';
    var mot;
    var id = 1;
    var nbQuote = 0;
    var regexp = /(_(?!\s))/g;

    for(var i = 0 ; i < mots.length ; i++){
        mot = mots[i];
        mot = mot.replace(regexp , ' ');
        if(id == id_highlight || (id_highlight2 != undefined && id == id_highlight2)){
            var classe = 'highlight';
        }else{
            if(_mode=='special')
                var classe = 'disabled-word';
            else
                var classe = 'mot';
        }
        retour += '<span class="'+classe+'" word_position="' + id + '" id="index_' + id + '">';
        retour += mot;
        retour += '</span>';
        if(i < mots.length - 1){
            switch(mots[i + 1]){
                case ',':
                    break;
                case '.':
                    break;
                case '"':
                    if(nbQuote % 2 != 1){
                        retour += ' ';
                    }
                    break;
                case '-il':
                    break;
                case ')':
                    break;
                default:
                    if(mot[mot.length - 1] != "'" && mot != '(' && mot != '"'){
                        retour += ' ';
                    }
                    break;
            }
        }
        id++;
    }
    return retour;
}
function displaySentenceStats(sentence, id_highlight, user_answer){
    console.log ('[jeu.js] ENTER displaySentence');
    var mots = sentence.split(' ');
    var retour = '';
    var mot;
    var id = 1;
    var nbQuote = 0;
    var regexp = /(_(?!\s))/g;

    for(var i = 0 ; i < mots.length ; i++){
        mot = mots[i];
        mot = mot.replace(regexp , ' ');
        if(id == id_highlight){
            var classe = 'highlight';
        }else if(id==user_answer){
            var classe = 'user_answer';
        }else{
            var classe = 'mot';
        }
        retour += '<span class="'+classe+'" word_position="' + id + '" id="word_' + id + '">';
        retour += mot;
        retour += '</span>';
        if(i < mots.length - 1){
            switch(mots[i + 1]){
                case ',':
                    break;
                case '.':
                    break;
                case '"':
                    if(nbQuote % 2 != 1){
                        retour += ' ';
                    }
                    break;
                case '-il':
                    break;
                case ')':
                    break;
                default:
                    if(mot[mot.length - 1] != "'" && mot != '(' && mot != '"'){
                        retour += ' ';
                    }
                    break;
            }
        }
        id++;
    }
    return retour;
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

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
}; 
Number.prototype.formatScore = function() {
@if(app()->getLocale()=='fr')
    return this.format(0,3,'&#8239;',',');
@else
    return this.format(0,3,',','.');
@endif
};  
String.prototype.formatScore = function() {
return parseInt(this,10).formatScore();
}; 


