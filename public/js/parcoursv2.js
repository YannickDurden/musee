/**
 *    Marker map add
 *
 *************************************************************************/

/**
 *
 * @type markerWidth  {number} the width marker point
 * @type markerHeight  {number} the height marker point
 *
 */

var height = $("#map").height();
var width = $("#map").width();
var mapId = $("#map");
var markerWidth = 47,
    markerHeight = 75;


/**
 *  Mousshover active
 * @param mapId [string] a valid DOM element ID
 */

function mousehover(MarkID) {
    var info = $("<div><div>",
        {
            "class": "info"
        }
    ).css
    (
        {
            "width": 200,
            "height": 200,
        }
    ).html("hello");
    MarkID.append(info);
}

/**
 * delete_icon
 *  @param MapID [string] a valid DOM element ID
 *  @param MarkID [string] a valid DOM element ID of Mark
 */
function delete_icon(MarkID, RouteID, ValMarkId) {
    $(MarkID).text("delete point ?");
    $(MarkID).remove();

    /**
     *  appel de la fonction pph pour suppirmer en seesion
     */
    $.ajax
    (
        {
            url: '/Mark/DeleteIcon',
            type: 'POST',
            data:
                {
                    RouteID: RouteID,
                    ValMarkId: ValMarkId
                }
        })
        .done(function (data) {

            console.log(data);
        });

}

/**
 * Display a map marker on a specific map
 * @param mapId [string] a valid DOM element ID
 * @param x [number] the x coordinate of the point
 * @param y [number] the y coordinate of the point
 * @param number [number] the count number of the point ID
 *
 */
function Display_map_marker(mapId, x, y, number, RouteID, ValMarkId) {
    var x;
    var y;

    var delete_icon_map = $("<div></div>",
        {
            "id": "delete_icon_map",
            "class": "delete_icon_map"
        }).click(function () {

    });

    var point = $("<div></div>",
        {
            "id": "New_pointer_" + number,
            "class": "pointeur"
        }).on(
        /**
         'mouseover',function()
         {
             var MarkID=$(this);
             mousehover(MarkID);
         },
         **/

        'click', function () {
            var MarkId = $(this);
            delete_icon(MarkId, RouteID, ValMarkId);
            mousehover(MarkId);
        }).css(
        {
            "width": markerWidth,
            "height": markerHeight,
            "left": x,
            "top": y,
        }).append(delete_icon_map);

    $(mapId).append(point);

}

/**
 *  Route on page load
 */
function ajax_load_map_route(RouteID) {
    $.ajax({
        url: '/Mark/LoadIcon',
        type: 'POST',
        data: {RouteID: RouteID},
        success: function (data) {
            var count = 0;
            for (i = 0; i < data.length; i++) {
                count++;
                var mark = data[i];
                var ValMarkId = mark.id;
                var coordX = mark.coordinateX * height;
                var coordY = mark.coordinateY * width;

                Display_map_marker(mapId, coordX, coordY, i, RouteID, ValMarkId);
            }
        },
        error: function (xhr, textStatus, errorThrown) {

        }

    });
}

/**
 * Create repere
 * @param num [integer] the count number of the point ID
 */
function Create_Map_Marker(mapId, number) {
    //Recupere les coordonnées du clique par rapport a la div #map

    var coordX = e.pageX - mapId.offset().left;
    var coordY = e.pageY - mapId.offset().top;

    //Pour les afficher plus facilement par la suite on stock les coordonnées
    //en % de la hauteur et largeur

    coordX = (coordX / width);
    coordY = (coordY / height);

    //Affiche les coordonnées dans le formulaire

    $('#add_mark_add_coordinateX').val(coordX);
    $('#add_mark_add_coordinateY').val(coordY);

    //Affichage du pointeur en fonction de l'incone de pointeur

    var pointeurX = (coordX * width) - (markerWidth / 2);
    var pointeurY = (coordY * height) - (markerHeight);

    Display_map_marker(mapId, pointeurX, pointeurY, number);
}


/**
 * init Jquery
 */

$(function () {

    //var count;
    //$('#map').click(function (e) {
    //  count++;
    // Create_Map_Marker('$(this)',count);
    //});

    //Ajout de la classe active dans le menu de navigation pour la page en cours
    $('#add-route').addClass("active");

    // Ajout des sous formulaires de question et description
    var $container = $('div#add_mark_add_descriptions');
    var $containerQuestion = $('div#add_mark_add_questions');
    for (var i = 0; i < 2; i++) {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Description n°' + (i + 1))
            .replace(/__name__/g, i)
        ;
        var template2 = $containerQuestion.attr('data-prototype')
            .replace(/__name__label__/g, 'Question n°' + (i + 1))
            .replace(/__name__/g, i)
        ;
        var $prototype = $(template);
        var $prototype2 = $(template2);
        $container.append($prototype);
        $containerQuestion.append($prototype2);
    }

    /**
     * Gestion de l'affichage de la liste des repères si un parcours pré-existant est selectionné
     */

    $('#form_route').change(function () {
        $("#map").html("");
        var RouteID = $(this).val();

        ajax_load_map_route(RouteID);

        //Affiche l'animation de chargment
        var name = $('#form_route option:selected').text();
        if (name != 'Choisir le parcours à modifier') {
            $('#animation').show();
            $('#table-mark').fadeOut('slow');
            $.ajax({
                url: 'http://localhost:8000/ajax/getMarks',
                type: 'POST',
                data: {name: name}
            })
                .done(function (response) {
                    //Masque l'animation et affiche le resultat dans un tableau
                    $('#animation').hide();
                    $('#table-mark').fadeIn('slow');
                    $('#table-mark > tbody:last').html(response);
                    $('#name').val($('#route_name').val());
                    $('#description').val($('#route_description').val());
                    $('#hours').val($('#route_hours').val());
                    $('#minutes').val($('#route_minutes').val());


                    $('.deleteMark').click(function (e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        $(this).parent().parent().remove();
                        removeMark(name);
                    });
                    $('.editMark').click(function (e) {
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

    $('#add_mark_add_save').click(function (e, update) {
        e.preventDefault();
        addMarkToBdd();

        $('.editMark').click(function (e) {
            e.preventDefault();
            var name = $(this).attr('name');
            editMark(name);

        });

        $('.deleteMark').click(function (e) {
            e.preventDefault();
            var name = $(this).attr('name');
            $(this).parent().parent().remove();
            removeMark(name);
        });
    });

    /**
     *  Ajout du parcours en BDD
     */


    $('#submit-info-parcours').click(function (e) {
        e.preventDefault();
        var $routeInfo = $('#add_route').serialize();
        var name = $('#form_route option:selected').text();
        $.ajax({
            url: 'http://localhost:8000/ajax/saveRoutetoBDD',
            type: 'POST',
            data: {routeInfo: $routeInfo, name: name}
        });
    });

});

/**
 * Suppression d'une ligne du tableau et de sa correspondance dans le tableau d'id en session
 */
function removeMark(name) {
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
function editMark(name) {
    $("#previousName").val(name);
    var decodedName = decodeURI(name);
    $.ajax({
        url: 'http://localhost:8000/ajax/getMarkInfo',
        type: 'POST',
        data: {name: decodedName}
    })
        .done(function (response) {

            //Remplissage manuel des differents champs sur les 3 onglets
            $('#add_mark_add_name').val(response.name);
            $('#add_mark_add_coordinateX').val(response.coordinateX);
            $('#add_mark_add_coordinateY').val(response.coordinateY);
            $('#add_mark_add_medias').val(response.medias);

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
 * Ajout ou modification d'un repère en BDD
 */
function addMarkToBdd() {
    var $markInfo = $('[name =add_mark_add]').serialize();
    //On recupere la valeur de l'input caché qui determine si on a affaire
    // à une modification ou un nouvel ajout
    var previousName = $("#previousName").val();

    //Dans le but de le comparer à un nom encodé il faut encoder cette variable
    var previousNameEncoded = encodeURI(previousName);

    //On affiche la div permettant de desactiver le formulaire
    //Elle reste active 3s avant de fadeOut
    $("#hideForm").css('z-index', 3000);
    $("#hideForm").show();
    $("#hideForm").delay(3000).fadeOut(800);


    $.ajax({
        url: 'http://localhost:8000/ajax/saveMarkToSession',
        type: 'POST',
        //processData: false,
        //cache: false,
        //dataType: false,
        data: {markInfo: $markInfo, update: previousName}
    }).done(function () {

        //En cas de réussite on affiche le message de succès
        $("#validationMessage").css('z-index', 3001);
        $("#validationMessage").show();
        $("#validationMessage").delay(3000).fadeOut(800);

        //Puis on "recache" les divs
        setTimeout(function () {
            $("#hideForm").css('z-index', -1);
            $("#validationMessage").css('z-index', -1);
        }, 3800);


        //On recupere le nom du nouveau repère pour le stocker dans le tableau de repères
        var newName = $('#add_mark_add_name').val();

        //Pour pouvoir ajouter ce nom dans un attribut [name] il faut encoder le nouveau nom pour convertir
        // les espaces et caractères spéciaux
        var encodedNewName = encodeURI(newName);

        $('[name =add_mark_add]')[0].reset();
        if (previousNameEncoded == 'false') {
            $("#previousName").val('false');
            //Recupère le nombre de ligne actuel pour numeroter la nouvelle insertion
            var nbreRows = $('#table-mark tbody tr').length;
            nbreRows++;
            var newRow = "<tr>\n" +
                "        <th scope=\"row\">" + nbreRows + "</th>\n" +
                "        <td name=\"" + encodedNewName + "\">" + newName + "</td>\n" +
                "        <td><a href=\"#\" name=\"" + encodedNewName + "\" class=\"editMark\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
                "        <td><a href=\"#\" name=\"" + encodedNewName + "\" class=\"deleteMark\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
                "    </tr>";
            //Enfin on ajoute la nouvelle ligne au tableau
            $('#table-mark > tbody:last').append(newRow);
        }
        else {
            $('#table-mark td[name=' + previousNameEncoded + ']').html(newName).attr('name', encodedNewName);
            $('#table-mark a[name=' + previousNameEncoded + ']').each(function () {
                $(this).attr('name', encodedNewName);
            });

        }
    }).fail(function () {
        $("#errorMessage").css('z-index', 3001);
        $("#errorMessage").show();
        $("#errorMessage").delay(3000).fadeOut(800);
        setTimeout(function () {
            $("#hideForm").css('z-index', -1);
            $("#errorMessage").css('z-index', -1);
        }, 3800);
    })
}

