/**
$(document).ready(function(){

    $.ajax({
        url: '/Mark/test',
        type:'POST',
        dataType:'json',
        async: true,
        success: function(data, status)
        {
            if(typeof (data) == 'object') {
                for (i = 0; i < data.length; i++) {
                    parcour = data[i];
                    console.log(parcour);
                }

            }
        },
        error : function(xhr, textStatus, errorThrown) {

        }
    });
});

 **/