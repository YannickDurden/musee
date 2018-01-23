$(function() {

    $('#form_route').change(function(){

        var id = $('#form_route').val()
        $.ajax({
            url: 'http://localhost:8000/ajax/edit-route',
            type: 'POST',
            data: {id : id}
        })
            .done(function( response ) {
                $('#selected_route').html(response);
            });
    });

    $(document).on("click", "#add_route_save", function(e){
        e.preventDefault();
        var data = $("#add_route_form").serialize();
        var id = $('#form_route').val()
        var request = $.ajax({
            url: 'http://localhost:8000/ajax/route/add',
            type: 'POST',
            dataType: "json",
            data: {form: data, id: id }
        });
        var modifSuccess =
            "    <div class=\"alert alert-success\" role=\"alert\">\n" +
            "        <h4 class=\"alert-heading\">Modifications enregistrées</h4>\n" +
            "        <p class=\"mb-5\">Vos modifications ont bien été enregistrées</p>\n" +
            "    </div>";
        $('#selected_route').html(modifSuccess);

    });
});