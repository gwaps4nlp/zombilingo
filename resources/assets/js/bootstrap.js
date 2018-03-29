window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js').default;
window.autosize = require('autosize');

require('bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ajaxError(function(jqXHR, textStatus, errorThrown) {
    pending_request = false;
    if(textStatus.status==403)
        modal(textStatus.responseJSON.error);
    if(textStatus.status==401){
        window.location.href = base_url + 'login';        
    }
}); 
