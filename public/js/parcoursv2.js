$(function() {

    /**
     * Gestion des coordonnées des repères
     */
    $('#map').click(function(e){

        //Recupere les coordonnées du clique par rapport a la div #map
        var coordX = e.pageX - $(this).offset().left;
        var coordY = e.pageY - $(this).offset().top;
        var mapWidth = $('#map').width();
        var mapHeight = $('#map').height();

        //Pour les afficher plus facilement par la suite on stock les coordonnées
        //en % de la hauteur et largeur
        coordX = (coordX / mapWidth).toFixed(3);
        coordY = (coordY / mapHeight).toFixed(3);

        //Affiche les coordonnées dans le formulaire
        $('#add_mark_add_coordinateX').val(coordX);
        $('#add_mark_add_coordinateY').val(coordY);

        //Affiche le repere sur la map
        var p = document.createElement("div");
        p.setAttribute("id","repereMap");
        p.style.width = 10 + "px";
        p.style.height = 10 + "px";
        p.style.backgroundColor ="red";
        //Sans oublier de convertir le % en valeur en pixel
        p.style.left= (coordX*mapWidth) + 'px';
        p.style.top= (coordY*mapHeight) + 'px';
        $('#map').append(p);
    });

    /**
     * Gestion de l'affichage de la liste des repères si un parcours pré-existant est selectionné
     */
    $('#form_route').change(function(){

        //Affiche l'animation de chargment
        $('#animation').show();
        $('#liste-reperes').fadeOut('slow');

        var id = $('#form_route').val()
        $.ajax({
            url: 'http://localhost:8000/ajax/getMarks',
            type: 'POST',
            data: {id : id}
        })
            .done(function( response ) {
                //Masque l'animation et affiche le resultat dans un tableau
                $('#animation').hide();
                $('#liste-reperes').fadeIn('slow');
                $('#liste-reperes').html(response);
            });
    });


    /**
     * Ajout d'un repère au parcours
     */
    $('#add_mark_add_save').click(function(e){
        e.preventDefault();
        $markInfo = $('[name =add_mark_add]').serialize();
        $.ajax({
            url: 'http://localhost:8000/ajax/saveMarkToSession',
            type: 'POST',
            dataType: "json",
            data: {markInfo: $markInfo}
        });

        //On recupere le nom du nouveau repère pour le stocker dans le tableau de repères
        var newName = $('#add_mark_add_name').val();

        //Recupère le nombre de ligne actuel pour numeroter la nouvelle insertion
        var nbreRows = $('#table-mark tbody tr').length;
        console.log(nbreRows);
        nbreRows++;
        var newRow = "<tr>\n" +
            "        <th scope=\"row\">"+nbreRows+"</th>\n" +
            "        <td>"+newName+"</td>\n" +
            "        <td><a href=\"#\" id=\"45\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
            "        <td><a href=\"#\" id=\"45\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
            "    </tr>";
        //Enfin on ajoute la nouvelle ligne au tableau
        $('#table-mark > tbody:last').append(newRow);
    });

    /**
     *  Ajout du parcours en BDD
     */
    /*
    $('#submit-info-parcours').click(function(e){
        e.preventDefault();
        $routeInfo = $('#add_mark').serialize();

        $.ajax({
            url: 'http://localhost:8000/ajax/saveMarkToSession',
            type: 'POST',
            data: {markInfo: $markInfo}
        });

    });
    */



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