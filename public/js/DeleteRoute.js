function deleteRoute(id) {

    $.ajax({
        url: '/Ajax/deleteRoute',
        type: 'POST',
        data: { tableId : id},
        success: function (data) {

        },
        error: function (xhr, textStatus, errorThrown) {

        }
    });

}
/**
 * display all Route
 */
function displayAllRoute()
{
    $.ajax({
        url: '/Mark/displayAllroute',
        type: 'POST',
        success: function (data) {
            var table = $('#table-parcours tbody');
            var html = '';
            for (i = 0; i < data.length; i++) {
                var mark = data[i];
                html +='<tr id="'+ mark.id + '">' +
                    '<td>'+ mark.id + '</td>' +
                    '<td>'+mark.name+'</td>' +
                    '<td>' +
                        '<a href="#" class="deleteButton" data-id="'+mark.id+'" id="idTabDelete-'+mark.id+'">' +
                            '<i class="fa fa-trash" aria-hidden="true"></i>' +
                        '</a>' +
                    '</td>' +
                '</tr>';


            }
            table.html(html);
            $(".deleteButton").on('click', function () {

               var idTable= $(this).data('id');
               deleteRoute(idTable);

            })
        },

        error: function (xhr, textStatus, errorThrown) {

        }

    });

}


/**
 * init Jquery
 */

$(function ()
{

    displayAllRoute();

})

