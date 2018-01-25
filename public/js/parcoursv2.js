$(function() {


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