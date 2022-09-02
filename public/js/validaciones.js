$(document).ready(function(){
    $('.codigo-input').keypress(function(e){
        var keyCode = (e.which) ? e.which : event.keyCode
        return !(keyCode > 31 && (keyCode < 48 || keyCode > 90) && (keyCode < 97 || keyCode > 122));
    });
    $('#product_price').attr('type', 'number');
})