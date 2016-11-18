$('document').ready(function(){

    $.ajaxSetup({
        data: {
            user_token: $.cookie('user_cookie')
        }
    });


    $('#id_phrase').on('change', function(){
        $.ajax({
            method : 'POST',
            url : base_url + 'administration/getPhrase',
            data : {
                user_token : $.cookie('user_cookie'),
                id_phrase : $(this).val().trim()
            },
            success : function(response){
                if(response == ''){
                    $('#resultat').html('');
                }else{
                    
                    var json = JSON.parse(response);
                    
                    if(json.nontrouve != undefined){
                        $('#resultat').html("La phrase n'a pas été trouvée");
                    }else{
                        var resultat = json.contenu;
                        resultat += '<br />Sentid : ' + json.id_conll;
                        resultat += '<br />Difficulté : ' + json.difficulte;
                        resultat += '<br />';
                        if(json.refPhrase == 1){
                            resultat += "C'est une phrase de référence";
                        }else{
                            resultat += "Ce n'est pas une phrase de référence";
                        }
                        $('#resultat').html(resultat);

                    }
                }
            }
        });
    });

    $('.supprimer_news').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url : $(this).attr('href'),
            method : 'POST',
            data : {
                id_news : $(this).attr('id_news'),
                user_token: $.cookie('user_cookie')
            },
            success : function(response){
                var result = JSON.parse(response);
                $('.news[id_news=' + result.id_news + ']').remove();
                if($('.news').length == 0){
                    $('#liste_news').html('<h1>Aucune news</h1>');
                }
                $('#resultat').html('');
            }
        });
        return false;
    });

});