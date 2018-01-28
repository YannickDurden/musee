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
    $('#map').click(function(e){
       var coordX = e.pageX - $(this).offset().left;
       var coordY = e.pageY - $(this).offset().top;
       console.log(coordX);
       console.log(coordY);

       $('#add_mark_add_coordinateX').val(coordX);
       $('#add_mark_add_coordinateY').val(coordY);

        var p = document.createElement("div");
        p.setAttribute("id","repereMap");
        p.style.width = 10 + "px";
        p.style.height = 10 + "px";
        p.style.backgroundColor ="red";
        p.style.left= coordX + 'px';
        p.style.top= coordY + 'px';
        $('#map').append(p);
    });

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

    $('#add_mark_add_save').click(function(e){
        e.preventDefault();
        $markInfo = $('[name =add_mark_add]').serialize();
        $.ajax({
            url: 'http://localhost:8000/ajax/saveMarkToSession',
            type: 'POST',
            dataType: "json",
            data: {markInfo: $markInfo}
        });

        var newName = $('#add_mark_add_name').val();
        var nbreRows = $('#table-mark tbody tr').length;
        console.log(nbreRows);
        nbreRows++;
        var newRow = "<tr>\n" +
            "        <th scope=\"row\">"+nbreRows+"</th>\n" +
            "        <td>"+newName+"</td>\n" +
            "        <td><a href=\"#\" id=\"45\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
            "        <td><a href=\"#\" id=\"45\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
            "    </tr>";
        $('#table-mark > tbody:last').append(newRow);
    });

    $('#submit-info-parcours').click(function(e){
        e.preventDefault();
        $routeInfo = $('#add_mark').serialize();

        $.ajax({
            url: 'http://localhost:8000/ajax/saveMarkToSession',
            type: 'POST',
            data: {markInfo: $markInfo}
        });

    });



    // Ajout des sous formulaires de question et description
    var $container = $('div#add_mark_add_descriptions');
    var $containerQuestion = $('div#add_mark_add_questions');
    for(var i = 0; i<2; i++)
    {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Description n°'+(i+1))
            .replace(/__name__/g, i)
        ;
        var template2 = $containerQuestion.attr('data-prototype')
            .replace(/__name__label__/g, 'Question n°'+(i+1))
            .replace(/__name__/g, i)
        ;
        var $prototype = $(template);
        var $prototype2 = $(template2);
        $container.append($prototype);
        $containerQuestion.append($prototype2);
    }

});