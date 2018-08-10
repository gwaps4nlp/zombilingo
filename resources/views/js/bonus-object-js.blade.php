$('document').ready(function(){


    $.ajaxSetup({
        data: {
            user_token: $.cookie('user_cookie')
        }
    });
    setTimeout(function(){$('#viande').remove()}, 3000);
    url = "{{ url('shop/objectWon') }}";
    $('#viande').on('click', function(){
        $.ajax({
            url : url,
            method : 'GET',
            success : function(response){
                $('#viande').remove();
                    var url = "{{ asset('img/objet') }}/"+ response.image;
  
                    $('body').append('<img src="' + url + '" id="loot" />');
                    setTimeout(function(){$('#loot').remove()}, 5000);
                    alert("Bien joué, tu as gagné l'objet suivant : " + response.name);
            }
        });
    });

});