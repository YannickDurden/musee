/**
 *   Loadroute()
 */

function loadroute()
{
    $.ajax({
        url: '/loadroutetest',
        success: function (data) {
            for (i = 0; i < data.length; i++)
            {
                var mark = data[i];
                console.log(mark);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
        }
    });
}

/**
 * init Jquery
 */

$(function () {

    loadroute();

})

