$(function(){

    //Affichage description de chaque parcours pour le template
    //select-route.html.twig
    $('.border-desc').hide();
    function refreshDesc() {

        var param = $('.select-route').val();
        $.ajax({
            url:'http://localhost:8000/mymuseum/admin-ajax/getdescription/' + param,
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