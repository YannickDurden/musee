$(function() {

    $('#form_route').change(function(){

        var id = $('#form_route').val()
        $.ajax({
            url: 'http://localhost:8000/route/edit/'+id,
        })
            .done(function( response ) {
                $('#selected_route').html(response);
            });
    })
    $(document).on("click", "#add_route_save", function(e){
        e.preventDefault();
        $.fn.serializeObject = function()
        {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };
        // get the properties and values from the form
        var data = $("#add_route_form").serializeObject();
        var id = $('#form_route').val()
        $.ajax({
            url: 'http://localhost:8000/route/add',
            type: 'POST',
            dataType: 'json',
            data: {data: data}
        });

    });






});