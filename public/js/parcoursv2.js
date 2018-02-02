$(function() {

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
        var name = $('#form_route option:selected').text();
        if(name != 'Choisir le parcours à modifier')
        {
            $('#animation').show();
            $('#table-mark').fadeOut('slow');
            $.ajax({
                url: 'http://localhost:8000/ajax/getMarks',
                type: 'POST',
                data: {name : name}
            })
                .done(function( response ) {
                    //Masque l'animation et affiche le resultat dans un tableau
                    $('#animation').hide();
                    $('#table-mark').fadeIn('slow');
                    $('#table-mark > tbody:last').html(response);
                    $('#name').val($('#route_name').val());
                    $('#description').val($('#route_description').val());
                    $('#hours').val($('#route_hours').val());
                    $('#minutes').val($('#route_minutes').val());


                    $('.deleteMark').click(function(e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        $(this).parent().parent().remove();
                        removeMark(name);
                    });
                    $('.editMark').click(function(e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        editMark(name);

                    });

                });
        }

    });

    /**
     * Ajout d'un repère au parcours
     */
    $('#add_mark_add_save').click(function(e, update){
        e.preventDefault();
        addMarkToBdd();

        $('.editMark').click(function(e) {
            e.preventDefault();
            var name = $(this).attr('name');
            editMark(name);

        });

        $('.deleteMark').click(function(e) {
            e.preventDefault();
            var name = $(this).attr('name');
            $(this).parent().parent().remove();
            removeMark(name);
        });
    });

    /**
     *  Ajout du parcours en BDD
     */

    $('#submit-info-parcours').click(function(e){
        e.preventDefault();
        var $routeInfo = $('#add_route').serialize();
        var name = $('#form_route option:selected').text();
        $.ajax({
            url: 'http://localhost:8000/ajax/saveRoutetoBDD',
            type: 'POST',
            data: {routeInfo: $routeInfo, name: name}
        });
    });

    /**
     * Suppression d'une ligne du tableau et de sa correspondance dans le tableau d'id en session
     */
    function removeMark(name)
    {
        var decodedName = decodeURI(name);
        $.ajax({
            url: 'http://localhost:8000/ajax/deleteMarkFromSession',
            type: 'POST',
            data: {name: decodedName}
        })
    }

    /**
     * Modification d'un repère deja ajouté
     */
    function editMark(name)
    {
        $("#previousName").val(name);
        var decodedName = decodeURI(name);
        $.ajax({
            url: 'http://localhost:8000/ajax/getMarkInfo',
            type: 'POST',
            data: {name: decodedName}
        })
            .done(function(response){
                //$('#repere').html(response);
                $('#add_mark_add_name').val(response.name);
                $('#add_mark_add_coordinateX').val(response.coordinateX);
                $('#add_mark_add_coordinateY').val(response.coordinateY);

                $('#add_mark_add_descriptions_0_label').val(response.description1.label);
                $('#add_mark_add_descriptions_0_category').val(response.description1.category);
                $('#add_mark_add_descriptions_1_label').val(response.description2.label);
                $('#add_mark_add_descriptions_1_category').val(response.description2.category);

                $('#add_mark_add_questions_0_label').val(response.question1.label);
                $('#add_mark_add_questions_0_category').val(response.question1.category);
                $('#add_mark_add_questions_0_answers_goodAnswer').val(response.question1.answers.goodAnswer);
                $('#add_mark_add_questions_0_answers_answer1').val(response.question1.answers.answer1);
                $('#add_mark_add_questions_0_answers_answer2').val(response.question1.answers.answer2);
                $('#add_mark_add_questions_0_answers_answer3').val(response.question1.answers.answer3);

                $('#add_mark_add_questions_1_label').val(response.question2.label);
                $('#add_mark_add_questions_1_category').val(response.question2.category);
                $('#add_mark_add_questions_1_answers_goodAnswer').val(response.question2.answers.goodAnswer);
                $('#add_mark_add_questions_1_answers_answer1').val(response.question2.answers.answer1);
                $('#add_mark_add_questions_1_answers_answer2').val(response.question2.answers.answer2);
                $('#add_mark_add_questions_1_answers_answer3').val(response.question2.answers.answer3);


            });
    }

    /**
     *
     */
    function addMarkToBdd()
    {
        var $markInfo = $('[name =add_mark_add]').serialize();
        var file = new FormData(document.getElementById('add_mark_form'));
        var previousName = $("#previousName").val();
        var previousNameEncoded = encodeURI(previousName);
        $.ajax({
            url: 'http://localhost:8000/ajax/saveMarkToSession',
            type: 'POST',
            //processData: false,
            //cache: false,
            //dataType: false,
            data: {markInfo : $markInfo, update: previousName}
        });

        //On recupere le nom du nouveau repère pour le stocker dans le tableau de repères
        var newName = $('#add_mark_add_name').val();
        var encodedNewName = encodeURI(newName);
        $('[name =add_mark_add]')[0].reset();
        if(previousNameEncoded == 'false')
        {
            $("#previousName").val('false');
            //Recupère le nombre de ligne actuel pour numeroter la nouvelle insertion
            var nbreRows = $('#table-mark tbody tr').length;
            nbreRows++;
            var newRow = "<tr>\n" +
                "        <th scope=\"row\">"+nbreRows+"</th>\n" +
                "        <td name=\""+encodedNewName+"\">"+newName+"</td>\n" +
                "        <td><a href=\"#\" name=\""+encodedNewName+"\" class=\"editMark\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
                "        <td><a href=\"#\" name=\""+encodedNewName+"\" class=\"deleteMark\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
                "    </tr>";
            //Enfin on ajoute la nouvelle ligne au tableau
            $('#table-mark > tbody:last').append(newRow);
        }
        else
        {
            $('#table-mark td[name='+previousNameEncoded+']').html(newName).attr('name', encodedNewName);
            $('#table-mark a[name='+previousNameEncoded+']').each(function(){
                $(this).attr('name', encodedNewName);
            });

        }

    }


});
