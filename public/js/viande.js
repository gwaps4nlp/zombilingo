$('document').ready(function(){


    $.ajaxSetup({
        data: {
            user_token: $.cookie('user_cookie')
        }
    });
    // setTimeout(function(){$('#viande').remove()}, 3000);
    url = "public/shop/objectWon";
    if(window.location.pathname.split('/').length == 4){
        url = '../' + url;
    }
    $('#viande').on('click', function(){
        $.ajax({
            url : url,
            method : 'GET',
            success : function(response){
                $('#viande').remove();
                // if(response != ''){
                    // var result = JSON.parse(response);
                    var url = '../assets/img/object/' + response.image;
                    
                    $('body').append('<img src="' + url + '" id="loot" />');
                    setTimeout(function(){$('#loot').remove()}, 5000);
                    alert("Bien joué, tu as gagné l'objet suivant : " + response.name);
                // }
            }
        });
    });

});
