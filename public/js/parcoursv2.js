$(function() {
    /*
    var mapWidth = $('#map').width();
    var mapHeight = $('#map').height();
    console.log("Largeur :" + mapWidth);
    console.log("Hauteur :" + mapHeight);
    $('#map').append("<div id='pointeur'>test</div>");
    var pointeur = $('#pointeur');
    //pointeur[0].style.top = 396 + 'px';
    //pointeur[0].style.left = 332 + 'px';
    pointeur[0].style.top = 365/1400 * mapHeight + 'px';
    pointeur[0].style.left = 469/2336 * mapWidth + 'px';
    pointeur[0].style.width= 10 + "px";
    pointeur[0].style.height= 10 + "px";
    pointeur[0].style.backgroundColor="red";
    //365 x
    //469 y

    //705/1034.5 * mapWidth
    //193/667 * mapHeight

    //1034.5 largeur 0
    //667 hauteur 0
    //largeur 1 : 421
    //hauteur 1: 24

    */

    $('#form_route').change(function(){

        $('#animation').show();
        $('#liste-reperes').fadeOut('slow');





        var id = $('#form_route').val()
        $.ajax({
            url: 'http://localhost:8000/ajax/getMarks',
            type: 'POST',
            data: {id : id}
        })
            .done(function( response ) {
                $('#animation').hide();
                $('#liste-reperes').fadeIn('slow');
                $('#liste-reperes').html(response);
            });
    });
});