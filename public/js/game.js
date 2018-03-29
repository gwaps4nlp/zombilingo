var $blockGame;
var inventaireAffiche = false;
var special = false;
var inGame = false;
var idAnnotation = 0;
var annotation_id = 0;
var sentence_id = 0;
var word_position = 0;
var progression = 0;
var turn = 0;
var spell = null;
var essayer = false;
var mode,relation_id,object_id;
var pending_request=false;

$(document).ajaxError(function(jqXHR, textStatus, errorThrown) {
    pending_request = false;
    if(textStatus.status==403)
        modal(textStatus.responseJSON.error);
    if(textStatus.status==401){
        alert(textStatus.responseJSON.error);
        window.location.href = base_url + 'auth/login';         
    }
}); 

$(document).ready(function(){

    $blockGame = $('#block-game');

    $( window ).resize(function() {
        console.log("32");
        resizeProgressBar();  
        console.log("32");     
    });

    $(document).on('click', '.link-level', function(e){

        if($('modalEndGame').hasClass('in')){
            $('modalEndGame').modal('hide');
        }
        e.preventDefault();
        if(pending_request)
            return false;
        console.log ('[jeu.js] CLICK->.btn, id_phenomene=' + $(this).attr('id_phenomene') + ' action=' + $(this).attr('action'));
        if ($(this).hasClass('buy')||$(this).hasClass('mwe')||$(this).hasClass('disabled-mwe')){
            return false;
        }
        if ($(this).hasClass('change')||$(this).hasClass('link')){
            window.location.href=$(this).attr('href');
        }
        if ($(this).hasClass('close-modal')){
            $(this).parents('.modal').each(function() {
            $(this).modal("hide");
          });
        }

        guest = false;
        
        
        if($(this).attr('action')==undefined)
            return false;
        
        mode = $(this).attr('action');    
        inGame = true;
        relation_id = $(this).attr('id_phenomene');
        turn=0;

        ajaxLoadContent();
    });

    $blockGame.on('change', '#corpus_id', function(e){
        e.preventDefault();
        $('#corpusChoice').submit();
    });

    // ====================================================================================================
    // Click sur un des boutons (mode "plusieurs alternatives")
    $blockGame.on('click', '.reponse', function(e){
        $('.reponse').addClass('disabled-reponse').removeClass('reponse');
        console.log ('[jeu.js] CLICK->.reponse');
        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/answer',
            data : {
                relation_id : $(this).attr('id_phenomene'),
                sentence_id : $('#sentence').attr('sentence_id')
            },
            success : function(response){
                processAfterResponse(response);
            }
        });
    });

    // ====================================================================================================
    // Click sur un word (mode "phenomène")
    $blockGame.on('click', '.word', function(){
        $(this).addClass('hover');
        word_position = $(this).attr('data-word-position');
        startLoader();    
        $('.word').addClass('disabled-word').removeClass('word');
        if(special){
            // en mode choix multiple, le click sur un word ne fait rien
            return;
        }
        $(this).addClass('solution');
        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/answer',
            data : {
                'word' : $(this).text(),
                'word_position' : $(this).attr('data-word-position'),
                'sentence_id' : $('#sentence').attr('data-sentence-id')
            },
            success : function(response){
                processAfterResponse(response);
            }
        });
    });

    // ====================================================================================================
    // Click sur refuse
    // On réutilise le code de réponse pour mettre à jour la BDD
    // et pour passer à la suite du jeu.
    $blockGame.on('click', '.refuse', function(){
        $('.word').addClass('disabled-word').removeClass('word');
        startLoader();
        if(mode=='special')
            var data = {
                'relation_id' : id_relation_refused,
                'sentence_id' : $('#sentence').attr('sentence_id')
            };
        else 
            var data = {
                'word' : '__refuse__',
                'word_position' : 99999,
                'sentence_id' : $('#sentence').attr('sentence_id')
            }
        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/answer',
            data : data,
            success : function(response){
                processAfterResponse(response);
            }
        });
    }); 
    
    $(document).on('click', '.closeNextLevel', function(){
        $('#modalEndGame').modal("show");
        incCerveaux();
        incPiece(); 
    });
    
    $(document).on('click','#report-button', function(){
        $("#form-report")[0].reset();
        $('#submitReport').attr("disabled","disabled");
        $('body').append($('#modalReport'));
        $('#modalReport').modal("show");
        
    }); 

    $(document).on('click','.pending-duel', function(e){
        $('#submitJoinDuel').removeAttr("disabled");
        $('.help-block').remove();
        $('.form-group').removeClass('has-error');
        $('#modalConfirmJoin').find('form').attr('action', $(this).data('href'));
        $('#modalConfirmJoin').modal("show"); 
    });
    
    $(document).on('click','.duel-completed', function(e){
        newModalSimple();
        $('#contentModal').load(base_url+'duel/compare-results/'+$(this).attr("id_phenomene"));
        $('#modalSimple').modal('show');
    });

    $(document).on('click', '.checkboxReport', function(){
        var enable_submit=false;
        $('.checkboxReport').each(function(){
            enable_submit|=$(this).is( ":checked" );
        });
        if(enable_submit) $('#submitReport').removeAttr("disabled");
        else $('#submitReport').attr("disabled","disabled");
    });
    $(document).on('click', '#openNewDuel', function(event){
        
        event.preventDefault();
        if($('#modalNewDuel').length==0)
            $.ajax({
                url : base_url +'duel/modal-new',
                success : function(response){
                    $('body').append(response);
                    if($('#modalEndGame').hasClass('show')){
                        $('#modalEndGame').modal("hide");
                        $('#modalEndGame').on('hidden.bs.modal', function (e) {
                          $('#modalNewDuel').modal("show");  
                        })                        
                    } else
                        $('#modalNewDuel').modal("show");  
                    
                    $("#submitNewDuel").removeAttr("disabled");
                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');
                            
                }
            });        
        else {
            $("#submitNewDuel").removeAttr("disabled");
            $('.help-block').remove();
            $('.form-group').removeClass('has-error');
            if($('#modalEndGame').hasClass('show')){
                $('#modalEndGame').modal("hide");
                $('#modalEndGame').on('hidden.bs.modal', function (e) {
                  $('#modalNewDuel').modal("show");  
                })                        
            } else
                $('#modalNewDuel').modal("show"); 
        }

    });

    $(document).on('focus', '#freeReportArea', function(){
        $('#free-report').prop("checked",true);
        $('#submitReport').removeAttr("disabled");
    });

    $(document).on('submit', "#form-report" ,function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : base_url + 'report/send',
            data : $(this).serialize()+ '&annotation_id=' + annotation_id+ '&relation_id=' + relation_id+ '&mode=' + mode+ '&user_answer=' + word_position+ '&word=' + $('.disabled-word[word_position=' + word_position + ']').text(),
            complete: function(e, xhr, settings){
                if(e.status === 422){

                 }else if(e.status === 200){
                    $('#modalReport').modal('hide');
                    $('#report-button').html('<span style="color:#298A08" class="glyphicon glyphicon-check"></span>&nbsp;'+e.responseJSON.html);
                    $('#report-button').attr('id','report-button-disabled');
                }else{

                }
            }
        });          
    });

    $(document).on('submit', "#form-new-duel" ,function( event ) {
        event.preventDefault();
        $('#submitNewDuel').attr("disabled","disabled");
        $.ajax({
            method : 'POST',
            url : base_url + 'duel/new',
            data : $(this).serialize(),
            complete: function(e, xhr, settings){
                if(e.status === 422){
                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');           
                    $.each(e.responseJSON.errors,function(key,error){
                        var elm = $('#form-new-duel #'+key);
                        var parent = elm.parent('.form-group').get(0);
                        $(parent).addClass('has-error');
                        elm.after('<small class="help-block">'+error+'</small>');                        
                    });
                    $("#submitNewDuel").removeAttr("disabled");
                 }else if(e.status === 200){
                    window.location.href=e.responseJSON.href;
                    //$('#modalNewDuel').modal('hide');

                }else{

                }
            }
        });          
    });
    $(document).on('submit', "#form-join-duel" ,function( event ) {
        event.preventDefault();
        $('#submitJoinDuel').attr("disabled","disabled");
        $.ajax({
            method : 'POST',
            url : $(this).attr("action"),
            data : $(this).serialize(),
            complete: function(e, xhr, settings){
                if(e.status === 422){
                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');     
                    $.each(e.responseJSON.errors,function(key,error){
                        var elm = $('#form-join-duel #'+key);
                        var parent = elm.parent('.form-group').get(0);
                        $(parent).addClass('has-error');
                        elm.after('<small class="help-block">'+error+'</small>');                        
                    });
                    $("#submitJoinDuel").removeAttr("disabled");
                 }else if(e.status === 200){
                    window.location.href=e.responseJSON.href;
                }else{

                }
            }
        });          
    });

    $blockGame.on('click', '#next-sentence', function(){
        console.log ('[jeu.js] CLICK->.#next-sentence');
        $('.parallax').animate({
            scrollTop: 0
        }, 500);
        suivant();
    });

    $blockGame.on('click', '#signaler', function(){
        console.log ('[jeu.js] CLICK->.#signaler');
        $.ajax({
            url : base_url + 'jeu/signalerAnnotation',
            method : 'POST',
            data : {
                id_annotation : annotation_id
            },
            success : function(response){
                suivant();
            }
        });
    });
    // ====================================================================================================
    $blockGame.on('mouseover', '#menuObject', function(){
        console.log ('[jeu.js] MOUSEOVER->.#menuObject');
        $.ajax({
            method : 'GET',
            url : base_url + 'game/inventaire',
            success : function(response){
                processInventaire(response);
            }
        });
    });

    // ====================================================================================================
    $blockGame.on('click', '#menuObject', function(){
        console.log ('[jeu.js] CLICK->.#menuObject');
        $.ajax({
            method : 'GET',
            url : base_url + 'game/inventaire',
            success : function(response){
                processInventaire(response);
            }
        });
    });

    // ====================================================================================================
    $blockGame.on('mouseover', '#sentence', function(){
        console.log ('[jeu.js] MOUSEOVER->.#sentence');
        $('#inventory').css("visibility","hidden");
        $('#menuObject').css({
            'background-color' : 'inherit'
        });
    });
  $("*").dblclick(function(e){
    e.preventDefault();
  });
    // ====================================================================================================
    $blockGame.on('click', '.buy', function(){
        object_id = $(this).attr('data-object-id');
        $.ajax({
            url : base_url + 'game/buyObject/' + object_id,
            success : processInventaire
        });
    });

    // ====================================================================================================
    $blockGame.on('click', '.object', function(){
        console.log ('[jeu.js] CLICK->.object');
        $.ajax({
            method : 'GET',
            url : base_url + 'shop/' + mode + '/useObject/'+$(this).attr('data-object-id'),
            success : function(response){
                processInventaire(response);
            }
        });
    });

    // ====================================================================================================
    $blockGame.on('click', '.mwe', function(){
        $('.mwe').addClass('disabled-mwe').removeClass('mwe');
        $.ajax({
            url : base_url + 'game/' + mode + '/answer',
            data : {
                'frozen'  : $(this).attr('value'),
                'mwe_id' : $('#mwe_id').text()
            },
            success : loadContentMwe
        });
    }); 

    // ====================================================================================================

    $(document).on('click', '#compare_results', function(){
         $('.game-element').css({'visibility':'hidden'});
        $('#content').html('');
        $('#infos').remove();
        $('#block-profil').html('');
        $('#block-profil').attr('class','col-2');
        $('#block-profil').append($('#block-replay'));
        $('#content').append($('#results'));
        $('#results').show();
        $('.parallax').animate({
            scrollTop: 0
        }, 500);
        embedBratVisualizations();
        $('.follow-thread-button').click(followThread);
        $('.unfollow-thread-button').click(unFollowThread);
        $('.message-button').click(showThread);
    });
});
    // ====================================================================================================
    function startLoader(){
        if($('#loader').length>0){
            $('#loader-container').show();
            $('#loader').show();
        }
        else
            $('#sentence').append('<div id="loader-container"><div id="loader"></div></div>');
    }
    function hideLoader(){
        $('#loader').hide();
    } 
    function stopLoader(){
        $('#loader-container').remove();
    } 

    function checkHelpObjectAsSeen(object_id){
        $.ajax({
            url : base_url + 'object/checkHelpAsSeen/' + object_id
        });
    }

    function delay(time){
        setTimeout(function(){ suivant() }, time);
    }

    function suivant(){
        console.log ('[jeu.js] ENTER suivant');
        if(suivant){
            $('#menuObject').show();
            $('.reponse').each(function(){
                $(this).css({
                    color : '#4a1710'
                })
            })
        }
        $.ajax({
            method : 'GET',
            url : base_url + 'game/'+ mode + '/jsonContent',
            success : function(response){
                $('.loot').remove();
                if(response != ''){
                    $('#resultat').html('');
                    $('#message-object').html('');
                    processResponse(response);
                }else{
                    $('#next-sentence').attr('disabled', 'disabled');
                }
            }
        });
    }

    function initGame(_mode,_relation){
        turn=0;
        mode=_mode;
        relation_id=_relation;
        inGame=true;
        if(typeof(tour) !== 'undefined' && mode=='training'){
            tour.init();
            tour.start();
        } else if(typeof(tourA) !== 'undefined' && mode=='game'){
            tourA.init();
            tourA.start();
        }
        ajaxLoadContent();
    }

    function initMwe(){
        mode='mwe';
        $.ajax({
            method : 'GET',
            url : base_url +'game/'+ mode + '/begin/0',
            dataType : 'json',
            success : function(json){
                $blockGame.html(json.html);
                loadContentMwe(json);
            }
        })
    }

    function ajaxLoadContent(){
        
        $('body').attr('style', "cursor: url('" +base_url +'img/curseur.png'+"'), pointer; ");
        $('#coccinelle').hide();
        pending_request = true;
        $.ajax({
            method : 'GET',
            url : base_url + 'game/'+ mode + '/begin/' + relation_id,
            dataType : 'json',
            success : loadContent,
        });
    }

    function loadContent(json){
        console.log ('[jeu.js] CLICK->.phenomene SUB.1');
        $blockGame.removeClass('center-duel');
        if(mode=='duel' || mode=='demo') {
            $('.container-site').addClass('container-game').removeClass('container-site');
        }

        if($('#sentence').length==0){
            $('.parallax').animate({
                scrollTop: 0
            }, 500);            
        }
        $blockGame.html(json.html);
        resizeProgressBar();
        $('.refuse').popover({
            trigger: 'hover'
        });

        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/jsonContent',
            dataType : 'json',
            success : function(response){
                pending_request = false;
                console.log ('[jeu.js] CLICK->.phenomene SUB.1.1');
                    processResponse(response);
                    resizeProgressBar();
                    if (mode == 'training' && typeof tour != 'undefined') {
                            tour.start();
                    }else if (mode == 'demo' && typeof tour != 'undefined' ) {    
                            tour.start();
                    }else if(mode == 'game' && typeof tourA != 'undefined'){
                            tourA.start();
                    }

            }
        });
    }
    function loadContentMwe(json){
        if(json.error){
            alert(json.error);
            var href=window.location.href;
            window.location.href = href;
            return;
        }   
        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/jsonContent',
            dataType : 'json',
            success : function(json){
                    if(json.html){
                        $blockGame.html(json.html);
                    } else {
                         $('#mwe_content').html(json.mwe.content);
                         $('#mwe_id').html(json.mwe.id);
                    }
                    $('.disabled-mwe').addClass('mwe').removeClass('disabled-mwe');
                    
            },  
        });
    }
    
    function updateProgression(turn,nb_turns){
        progression = Math.round( turn / nb_turns * 100 );

        $('#progress-container').css({height: 0.98*parseInt($('#progressBar').height(),10)+'px',lineHeight: $('#progressBar').height()+'px'});
        $('#progress').css({height: $('#progressBar').height()+'px',lineHeight: 0.92*parseInt($('#progressBar').height(),10)+'px'});
        $('#progress').text(progression + '%');
        $('#phaseBar').css({
            width : progression/100*$('#progressBar').width() + 'px'
        });        
    }

    // ====================================================================================================
    function processResponse(json){

        $('#thread').remove();

        if(json.score){
            $('.score').html(json.score);
        }  
        
        updateProgression(json['turn'], json['nb_turns']);

        if(json.html){
            hideLoader();
            $("#containerModal").html(json.html);
            if($("#modalNextLevel").length>0){
                if($("#img_level").length>0)
                    $("#img_level").attr('src',url_site('/img/level/')+'level-'+json.user.level.id+'.gif');
                $("#modalNextLevel").modal("show");
            } else {
                $('#modalEndGame').modal("show");
                incCerveaux();
                incPiece();     
            }
            return true;
        }
        // en cas d'erreur
        if(json.href){
            window.location.href = json.href;
        }

        if(json.erreur != undefined && json.erreur){
            if(json.message != undefined){
                alert(json.message);
            }else{
                alert("Une erreur s'est produite");
            }
            return;
        }

        if(json.mode != 'demo'){
            $('.refuse').show();
        }
     
        if(json.mode != undefined && json.mode == 'special'){
            special = true;
        }else{
            special = false;
        }

        $('#label-phenomenon').text(json.description);

        var afficherClassement = true;
        if(json.user){
            var profil = '<div style="text-align:left;"><img src="'+base_url+'img/level/thumbs/' + json.user.level.image + '"><br />';
            
            //Progression
            profil += 'niveau '+json.user.level.id+'</div>';
            $('#profil').html(profil);
            var score_html="";
            if (json.user.level.id == 7) {
                score_html += '<img src="'+base_url+'img/cerveau_plein.png"/>'+trans('game.max-level')+'<br />';
            } else{
                var score_user = json.user.score;
                var score_next_level = json.user.next_level.required_score;
                var score_level = json.user.level.required_score;
                var progress_score = 100*(score_user-score_level)/(score_next_level-score_level);
                var score = json.user.score.formatScore() + " / " + json.user.next_level.required_score.formatScore();
                score_html += '<div class="progress" style="margin-bottom:10px;"><div style="padding-left:5px;height:20px;line-height:17px;color:#888;position:absolute;">'+score+'</div><div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211F;width:'+progress_score+'%"></div><div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:'+(100-progress_score)+'%"></div></div>';
            }
            $('.score').html(json.user.score.formatScore());

            if(json.user.money != undefined){
                $('.money').html(json.user.money.formatScore());
                score_html += '<img src="'+base_url+'img/piece.png"/><span id="argent" class="money" style="color:#4a1710;">' + json.user.money.formatScore() + '</span>';
            }

            score_html += '<br/>';
            $('#progress_score').html(score_html);
        }

        // var i;

        if(json.annotation){
            if(!json.annotation.sentence){
                $blockGame.html('<h1 class="text-center">'+trans('game.no-more-sentences')+'</h1>');
            }else{
                if(json.annotation.governor_position && json.annotation.word_position){
                    $('#sentence').html(displaySentence(json.annotation.sentence.content, json.annotation.governor_position, json.annotation.word_position, mode));
                } else{
                    $('#sentence').html(displaySentence(json.annotation.sentence.content, json.annotation.focus, null, mode));
                }
            }
            if(json.annotation.explanation){
                $('#indication').show();
                $('#indication').html(json.annotation.explanation);
            } else {
                $('#indication').hide();
                $('#indication').html("");
            }
            annotation_id = json.annotation.id;
            var scores = '';
            if(json.neighbors && json.neighbors.sup.length && afficherClassement){
                scores += trans('game.relation-ahead-you')+'<br /><ul>';
                for(i in json.neighbors.sup){
                    var score = json.neighbors.sup[i];
                    scores += '<li>•&nbsp;' + score.username + ' → ' + score.points.formatScore() + ' <img width="3%" src="'+base_url+'img/cerveau_plein.png" />&nbsp;'+trans('game.points-ahead')+'</li>';
                }
                scores += '</ul>';
            }
            if(json.neighbors && json.neighbors.inf.length && afficherClassement){
                scores += trans('game.relation-behind-you')+'<br /><ul>';
                for(i in json.neighbors.inf){
                    var score = json.neighbors.inf[i];
                    scores += '<li>•&nbsp;' + score.username + ' → ' + score.points.formatScore() + ' <img width="3%" src="'+base_url+'img/cerveau_plein.png" />&nbsp;'+trans('game.points-behind')+'</li>';
                }
                scores += '</ul>';
            }

            if(afficherClassement){
                $('#infos').html(scores);
            }
            annotation_id = json.annotation.id;

        }
        spell = json.spell;
        if(json.spell == 'vanish'){
            $('#sentence').prop('disabled', true);
            $('.word').prop('disabled', true);
            $('#sentence span.word').animate({
                'opacity' : '0'
            }, 6000);
            $('#sentence span.highlight').animate({
                'opacity' : '0'
            }, 6000);
            showHelp('glasses');
        } else if(json.spell == 'shrink'){
            $('#sentence').prop('disabled', true);
            $('.word').prop('disabled', true);
            $('#sentence').animate({
                'font-size' : '1px'
            }, 6000);
            showHelp('telescope');
        }

        if(mode!='training'){
            $('#resultat').html('<img src="'+base_url+'img/cerveau_plein.png" title="'+trans('game.to-won-sentence')+'"/> <span id="gain">' + json['gain'] + '</span>');
        }

        if(mode=='special'){
            $('.disabled-reponse').addClass('reponse').removeClass('disabled-reponse');
            $('.word').each(function(){
                if(!$(this).hasClass('highlight')){
                    $(this).addClass('special');
                }
            });
            $('.reponse').css('color', 'black');
        }

        turn++;
    }

    // ====================================================================================================
    function incCerveaux(){
        console.log ('[jeu.js] ENTER incCerveaux');
        var $totalCerveaux = $('#totalCerveaux span');
        var goal = parseInt($totalCerveaux.attr('goal'),10);
        var value  = parseInt($totalCerveaux.attr('value'),10);
        var diff = goal-value ;
        if( diff > 0){
            if(diff < 10){
                var new_value = value+1;
            }else if(diff < 100){
                var new_value = value+10;
            }else{
                var new_value = value+100;
            }
            $totalCerveaux[0].innerHTML = new_value.formatScore();
            $totalCerveaux.attr("value",new_value);             
            $('#totalCerveaux').append('<img class="volatile" src="'+base_url+'img/cerveau_plein.png" />');
            var rand = Math.floor(Math.random() * 300 - 150);
            $('.volatile').each(function(){
                $(this).animate({
                    opacity : 0,
                    top : '-200px',
                    left : rand + 'px'
                }, 1000, function(){
                    $(this).remove();
                });
            });
            setTimeout(function(){incCerveaux()}, 50);
        }
    }

    // ====================================================================================================
    function incPiece(){
        console.log ('[jeu.js] ENTER incPiece');
        var $totalPiece = $('#totalPiece span');
        var goal = parseInt($totalPiece.attr('goal'),10);
        var value  = parseInt($totalPiece.attr('value'),10);
        var diff = goal-value ; 
        if( diff > 0){
            if(diff < 10){
                var new_value = value+1;
            }else if(diff < 100){
                var new_value = value+10;
            }else{
                var new_value = value+100;
            }
            $totalPiece[0].innerHTML = new_value.formatScore();
            $totalPiece.attr("value",new_value);  
            $('#totalPiece').append('<img class="volatile" src="'+base_url+'img/piece.png" />');
            var rand = Math.floor(Math.random() * 300 - 150);
            $('.volatile').each(function(){
                $(this).animate({
                    opacity : 0,
                    top : '-200px',
                    left : rand + 'px'
                }, 1000, function(){
                    $(this).remove();
                });
            });
            setTimeout(function(){incPiece()}, 50);
        }
    }
    function showHelp(slug){
        $('.help_object').remove();
        $.ajax({
            method : 'GET',
            url : base_url + 'game/inventaire',
            success : function(response){
                $.each(response.inventaire,function(index,object){
                    if(object.slug==slug && object.help_seen==0){
                        if(slug=='telescope'){
                $('.aideTip').after('<div class="help_object" style="display:none;">Quand la phrase rapetisse, utilise la longue-vue <img src="'+base_url+'img/object/thumbs/longue_vue.png" style="width:50px"/> qui est dans ton sac <img src="'+base_url+'img/sac.png" style="width:50px"/> pour la faire réapparaître.<span id="arrow_border"></span><span id="arrow_inner"></span></div>');      
                        } else if(slug=='glasses'){
                $('.aideTip').after('<div class="help_object" style="display:none;">Quand la phrase disparaît, utilise les lunettes <img src="'+base_url+'img/object/thumbs/lunettes.png" style="width:50px"/> qui sont dans ton sac <img src="'+base_url+'img/sac.png" style="width:50px"/> pour la faire réapparaître.<span id="arrow_border"></span><span id="arrow_inner"></span></div>');                        
                        }
                        $('.help_object').fadeIn();
                        checkHelpObjectAsSeen(object.id);
                    }
                });
            }
        });
    }

    function colorizeWordsSentence(words, color, sentence_id){
        sentence_id = sentence_id || null;
        var container_sentence_id = (sentence_id)?'#sentence_'+sentence_id : "#sentence";
        for(var i=0;i<words.length;i++){
            $(container_sentence_id+' #word_index_'+words[i]).css({'color':color});
        }
    }

    function processAfterResponse(json){
        console.log ('[jeu.js] ENTER processAfterResponse');

        if(json.error){
            alert(json.error);
            var href=window.location.href;
            window.location.href = href;
            return;
        }
        if(json.href){
            window.location.href = json.href;
        }

        var time = 0;
        var addPasser = false;
        if(json.gain != undefined && $("#points_earned").length>0)
            $("#points_earned").html(json.gain);
        if((json.reference&&json.errors)||mode=='training'||mode=='demo'){
            if(jQuery.inArray( json.answer, json.expected_answers ) < 0){
                hideLoader();
                if(json.mode=='special')
                    var attribute = '.disabled-reponse[id_phenomene';
                else
                    var attribute = '.disabled-word[data-word-position';
                if(json.answer=='99999'){
                    var answer_user = img_croix_os;
                } else {
                    var reponse = $(attribute+'=' + json.answer + ']');
                    reponse.removeClass('hover').addClass('not_solution');
                    var answer_user ='<span class="not_solution">' + reponse.text() + '</span>';
                }
                var right = [];
                $.each(json.expected_answers,function(index,expected_answer){
                    if(expected_answer=="99999")
                        right.push(img_croix_os);
                    else {
                        var juste = $(attribute+'=' + expected_answer + ']');
                        juste.addClass('solution');
                        right.push('<span class="solution">' + juste.text() + '</span>');
                    }
                });
                var resultat = '<h4>'+trans('game.bad-answer',{'answer':answer_user,'response':right.join( trans('game.or') )})+'</h4>';
                // if(json.explication)
                    // resultat+='<br/>('+json.explication+')';
                $('#resultat').html(resultat);
                $('#message-object').html('');
                $('#menuObject').hide();
                $('.refuse').hide();
                if (json.errors == 3) {
                    $('#resultat').append('<h4>'+trans('game.no-more-attempt')+'</h4>');
                } else if (json.errors != 3 && json.errors) {
                    var remaining_trials = 3-json.errors;
                    $('#resultat').append('<h4>'+ trans_choice('game.remaining-trials',{'remaining_trials':remaining_trials})+'</h4>');
                }
                if(mode!='demo'){

                    var button = $('<button id="message-button" data-id="'+json.annotation.id+'" data-type="App\\Models\\Annotation" style="position:relative;" class="btn btn-small btn-faded btn-outline btn-green message-button">Discuter de la réponse <span class="badge">'+json.nb_messages+'</button>');
                    // $('#resultat').append('<div><span style="position:relative;"><span id="report-button" style="position:relative;" class="margin-right btn btn-small btn-faded btn-outline btn-green"><span style="color:#B43104" class="glyphicon glyphicon-warning-sign"></span>&nbsp;Je ne suis pas d\'accord</span></div>');
                    $('#resultat').append(button);
                    $('#bottom').after('<div class="row" id="thread" style="position:relative;top:60px;"><div class="col-12 col-lg-10 mx-lg-auto"><span style="display:none;" class="thread" id="thread_'+json.annotation.id+'"></span></div></div>');
                    button.click(showThread);
                    // $('#resultat').append('<div><span style="margin-right:20px;position:relative;"><span id="message-button" data-id="'+json.annotation.id+'" data-type="Annotation" style="position:relative;" class="btn btn-small btn-faded btn-outline btn-green message-button">Discuter de la réponse <span class="badge">'+json.nb_messages+'</span></span></div>');
                    
                }
                if (json.errors == 3) {
                    $('#resultat').append('<a class="link btn btn-small btn-green" href="'+ base_url + 'game'+'"  id="retourMenu" title="'+ trans('game.back-menu')+'">'+trans('game.back-menu')+'</a>');
                }else {
                    $('#resultat').append('<button class="link btn btn-small btn-green" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</button>');
                }

                $('#sentence').append('<div id="erreur"></div>');
                $('#erreur').fadeOut(800, function(){
                    $('#erreur').remove();
                });
            }else{
                suivant();
            }
        }else{
            if(jQuery.inArray( json.answer, json.expected_answers ) >= 0){
    
                $('#sentence').finish();
                $('#sentence .word').finish();
                $('#sentence').css({
                    'font-size': '1.7em',
                    'opacity' : '1'
                });
                
                if(json.loot && json.loot.id){
                    $('#resultat').html('<h3>' + trans('game.you-found-object',{'name':json.loot.name})+'</h3>');
                    $blockGame.append('<div class="loot"><img src="'+base_url+'img/object/' + json.loot.image + '" /></div>');
                    time += 1600;
                }

                if(!addPasser){
                    if(time == 0){
                        suivant();
                    }else{
                        delay(time);
                    }
                }else{
                    $('#resultat').append('<br/><a class="link" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</a>');
                }
            }else{
                if(json.guest != undefined){
                    var reponse = $('.word[word_position=' + json.word_position + ']');
                    reponse.addClass('not_solution');
                    var juste = $('.word[word_position=' + json.expected_answer + ']');
                    juste.addClass('solution');
                    $('#resultat').html('<h4>' + trans('game.bad-answer',{'answer':'<span class="not_solution">' + reponse.text() + '</span>','response':'<span class="solution">' + juste.text() + '</span>'})+'</h4>');
                    $('#resultat').append('<a class="link" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</a>');
                    $('#sentence').append('<div id="erreur"></div>');
                    $('#erreur').fadeOut(800, function(){
                        $('#erreur').remove();
                    });
                }else{
                    suivant();
                }
            }
        }
        $('.word').each(function(){
            $(this).removeClass('word');
        });

    }

    // ====================================================================================================
    function processInventaireShop(json){
        if(json.money){
            $('.money').html(json.money.formatScore());
        }
        if(json.message)
            $( "span.error[data-object-id|='"+object_id+"']" ).html(json.message);
        $.each(json.inventaire,function(index,object){
            $( "span.owned[data-object-id|='"+object.id+"']" ).html(object.quantity);
        });
    }

    function resizeProgressBar(){
        $('#progress-container').css({height: 0.98*parseInt($('#progressBar').height(),10)+'px',lineHeight: $('#progressBar').height()+'px'});
        $('#progress').css({height: $('#progressBar').height()+'px',lineHeight: 0.92*parseInt($('#progressBar').height(),10)+'px'});
        $('#phaseBar').css({
            width : progression/100*$('#progressBar').width() + 'px'
        });
    }

    function processInventaire(json){
        $('#message-object').show();
        if(!inGame) {
            processInventaireShop(json);
            return;
        }
        console.log ('[jeu.js] ENTER processInventaire');
        if(json.money){
            $('.money').html(json.money.formatScore());
        }
            
        var $inventaire = $('#inventory');
        
            if(json.inventaire == 0){
                $inventaire.html('Pas d\'objet');
            }else{
                var inventaire = json.inventaire;
                var htmlInventaire = '';
                var object;
                for(i in inventaire){
                    object = inventaire[i];
                    htmlInventaire += '<div class="contentObject">';
                    htmlInventaire += '<div class="object tool" id="use-object-'+object.id+'" data-object-id="' + object.id + '">';
                    htmlInventaire += '<span class="tip">' + object.description + '</span>';
                    htmlInventaire += '<img src="'+base_url+'img/object/thumbs/'+ object.image + '" /><span class="nombre">' + object.quantity + '</span><br />';
                    htmlInventaire += '</div>';
                    htmlInventaire += '<img src="'+base_url+'img/piece.png"/>' + object.price_ingame + '<br />';
                    htmlInventaire += '<button class="btn btn-success buy"  id="btn-buy-object-'+object.id+'" data-object-id="'+object.id+'">'+trans('game.buy')+'</button>';
                    htmlInventaire += '</div>';
                }   
                if(json.reappear_sentence){
                    $('.help_object').fadeOut();
                    $('#sentence .word').finish();
                    $('#sentence .highlight').finish();
                    $('#sentence .word').css({
                        'opacity' : '100',
                    });
                    $('#sentence .highlight').css({
                        'opacity' : '100',
                    });
                    $('.word').prop('disabled', false);
                    $('#sentence').prop('disabled', false);
                    $('#sentence .highlight').css({
                        'color' : '#0e7f3c'
                    });
                }
                if(json.midas != undefined && json.midas == 1){
                    $('#resultat img').attr('src', base_url + 'img/piece.png');
                }
                if(json.increase_sentence){
                    $('.help_object').fadeOut();
                    $('.word').prop('disabled', false);
                    $('#sentence').prop('disabled', false);
                    $('#sentence').finish().css({
                        'font-size' : '1.7em'
                    }, 6000);

                }
                if(json.gain != undefined){
                    $('#gain').html(json.gain);
                }
                if(json.message != undefined){
                    $('#message-object').html(json.message);
                }

                $inventaire.html(htmlInventaire);
            }
            $inventaire.css("visibility","visible");
            inventaireAffiche = true;


        $('#menuObject').css({
            'background-color' : '#9bc5aa'
        });
    } 

