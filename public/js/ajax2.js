$(function(){
    $('.border-desc').hide();
    function refreshDesc() {

        var param = $('.select-route').val();
        $.ajax({
            url:'http://localhost:8000/mymuseum/ajax/getdescription/' + param,
            type: 'GET',
        }).done(function(response){
            $('.border-desc').html(response).show("slow");
            console.log(response);
        });
    }

    refreshDesc();

    $('.select-route').change(function(){
        refreshDesc();
    });

});