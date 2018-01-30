$(function () {

    function coord() {
        var reperes = $('.repere-map');
        var height = $('#museum-map').height();
        var width = $('#museum-map').width();

        $.each(reperes, function () {

            var coordX = $(this).data('coordx');
            var coordY = $(this).data('coordy');
            var calcX = coordX * width;
            var calcY = coordY * height;

            console.log("X : " + calcX + " - Y : " + calcY);

            $(this).css('left', calcX);
            $(this).css('top', calcY);

        });
    }

    coord();

    $(window).resize(function(){
        coord();

    })

});