$(document).ready(function(){
    // $.ajaxSetup({
    //     global:false,
    //     data: {
    //         user_token: $.cookie('user_cookie')
    //     }
    // });

    $( "#form-change-password" ).submit(function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : $(this).attr('action'),
            data : $(this).serialize(),
            complete: function(e, xhr, settings){
                if(e.status === 422){
                    $('#modalChangePassword div.form-group').addClass('has-error');
                    $('#modalChangePassword div.modal-body').addClass('has-error');
                    $('#error-change-password').html('<small class="help-block">'+e.responseJSON.password[0]+'</small>');
                }else if(e.status === 200){
                    $('#modalChangePassword').modal('hide');
                }else{

                }
            }
        });      
    });

    $( "#form-change-email" ).submit(function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : $(this).attr('action'),
            data : $(this).serialize(),
            complete: function(e, xhr, settings){
                if(e.status === 422){
                    $('#modalChangeEmail div.form-group').addClass('has-error');
                    $('#modalChangeEmail div.modal-body').addClass('has-error');
                    $('#error-change-email').html('<small class="help-block">'+e.responseJSON.email[0]+'</small>');
                 }else if(e.status === 200){
                    $('#modalChangeEmail').modal('hide');
                }else{

                }
            }
        });      
    });

    $('#demandeAmi').on('touchend click', function(){
        $.ajax({
            method : 'GET',
            url : $(this).attr('url'),
            success : function(response){
                $('#result').html(response.html);
                $('#demandeAmi').remove();
            }
        });
    });

    $('#number-friends').on('touchend click', function(){
        $('#modalFriends').modal("show"); 
    });

    $('#changePassword').on('touchend click', function(){
        $('#error-change-password').html('');
        $('#modalChangePassword div.form-group').removeClass('has-error');
        $('#modalChangePassword div.modal-body').removeClass('has-error');    
        $('#modalChangePassword').modal("show");        
    });

    $('#changeEmail').on('touchend click', function(){
        $('#error-change-email').html('');
        $('#modalChangeEmail div.form-group').removeClass('has-error');
        $('#modalChangeEmail div.modal-body').removeClass('has-error');    
        $('#modalChangeEmail').modal("show");      
    });

    $('#delete-acc').on('click', function(){
        if (confirm(trans('site.confirm-delete-account'))) {
            $.ajax({
                method : 'GET',
                url : $(this).attr('url'),
                success: function(response){
                    window.location.href = response.href;
                }
            });
        }   
    });

    $('.accepter').on('click', function(){
        $.ajax({
            method : 'GET',
            url : $(this).attr('url'),
            success : function(response){

                $('.demande[user_id=' + response.id + ']').remove();
                var amis = $('#amis');
                var html =  '<a href="'+base_url+'user/'+response.id+'">'+response.username+'</a>&nbsp;';
                html += response.score +'&nbsp';
                html += '<img src="'+base_url+'img/cerveau_plein.png"/><br />';
                if(amis.length>0){
                    if(amis.attr('present') == 0){
                        $('#amis').html(html);
                    }else{
                        $('#amis').append(html);
                    }
                } else {
                    $('#resultAmi').html('<h3>' + response.username +' est maintenant ton ennemi.</h3>');                    
                }
            }
        });
    });

    $('.annuler').on('click', function(){
        $.ajax({
            method : 'GET',
            url : $(this).attr('url'),
            success : function(response){
                if(response == 'false'){
                    $('#resultAmi').html('<h3>Une erreur s\'est produite</h3>');
                }else{
                    $('.demande[user_id=' + response.id + ']').remove();
                    $('#resultAmi').html('<h3>' + trans('site.will-not-be-your-friend',{'username':response.username})+'</h3>');
                    if($('.demande').length == 0){
                        $('#soi').html('');
                    }
                }
            }
        });
    });
});
