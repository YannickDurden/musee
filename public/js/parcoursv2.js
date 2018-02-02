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

var height =$("#map").height();
var width =$("#map").width();
var mapId =$("#map");
var markerWidth =47,
    markerHeight =75;


/**
 *  Mousshover active
 * @param mapId [string] a valid DOM element ID
 */

function mousehover(mapId)
{
    var inside =$("<div><div>",
        {
            "class":"inside"
        }
        ).css(

    );
    $("#map").on
    (
        'click',function ()
        {
            $(this).toggleClass("acive");
        },
        'onmouseenter()',function()
        {
            $(this).toggleClass("inside");

        },
        'mouseleave', function()
        {
            $(this).removeClass("inside");
        }
    );
}

/**
 * delete_icon
 *  @param MapID [string] a valid DOM element ID
 *  @param MarkID [string] a valid DOM element ID of Mark
 */
function delete_icon(MarkID,RouteID,ValMarkId)
{
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
                    RouteID : RouteID,
                    ValMarkId:ValMarkId
                }
            })
     .done(function(data)
     {

                  console.log(data.name);


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
function Display_map_marker (mapId, x, y, number,RouteID,ValMarkId)
{
    var x;
    var y;

    var delete_icon_map=$("<div></div>",
        {
            "id" :"delete_icon_map",
            "class" :"delete_icon_map"
        }).click(function ()
        {

        });

    var point = $("<div></div>",
        {
            "id" :"New_pointer_" + number,
            "class" :"pointeur"
    }).on('click',function ()
        {
              var MarkId=$(this);
               delete_icon(MarkId,RouteID,ValMarkId);
        }).css(
        {
            "width": markerWidth,
            "height": markerHeight,
            "left":x,
            "top":y,
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
        data: {RouteID:RouteID},
        success: function(data) {
                var count = 0;
                for(i = 0; i < data.length; i++) {
                    count ++;
                    var mark = data[i];
                    var ValMarkId= mark.id;
                    var coordX = mark.coordinateX * height;
                    var coordY = mark.coordinateY * width;

                    Display_map_marker (mapId, coordX, coordY, i,RouteID,ValMarkId);
                }
        },
        error : function(xhr, textStatus, errorThrown) {

        }

    });
}
/**
 * Create repere
 * @param num [integer] the count number of the point ID
 */
function Create_Map_Marker(mapId,number)
{
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

        var pointeurX=(coordX * width) - (markerWidth / 2);
        var pointeurY=(coordY * height) - (markerHeight);

        Display_map_marker (mapId, pointeurX, pointeurY, number);
}

/**
 * init Jquery
 */

$(function() {

    //var count;
    //$('#map').click(function (e) {
      //  count++;
       // Create_Map_Marker('$(this)',count);
    //});

    /**
     * Gestion de l'affichage de la liste des repères si un parcours pré-existant est selectionné
     */

    $('#form_route').change(function()
    {
        $("#map").html("");
        var RouteID =$(this).val();

        ajax_load_map_route(RouteID);

    });

    /**
     * Ajout d'un repère au parcours
     */

    $('#add_mark_add_save').click(function(e){
        e.preventDefault();
        $markInfo = $('[name =add_mark_add]').serialize();
        $.ajax({
            url: '/ajax/saveMarkToSession',
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