
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
//var markerWidth = 47,
//    markerHeight = 75;

var height = $('#map-map').height();
var width = $('#map-map').width();
  // markerEdit = true;


/**
 * Create a map marker on a specific map
 *
 * @param mapId [string] a valid DOM element ID
 * @param x [number] the x coordinate of the point
 * @param y [number] the y coordinate of the point
 * @param number [number] the count number of the point ID
 *
 */

function create_map_marker (mapId, x, y, number) {

    var x;
    var y;

    var point = $("<div></div>",
        {
            "id" :"New_pointer_" + number,
            "class" :"pointeur"
        }).css(
        {
            "left":x,
            "top":y,
        });
    $(mapId).append(point);
}

/**
 * Dipslay Mark map
 */

function coord() {

    var reperes = $('.repere-map');

    $.each(reperes, function (key)
    {
        var coordX = $(this).data('coordx');
        var coordY = $(this).data('coordy');

        var calcX = coordX * width;
        var calcY = coordY * height;
        create_map_marker ('#map',calcX, calcY, key);
    });

}







$(document).ready(function() {



    /**
     *  Gestion des questions
     */


    var $container = $('div#add_mark_add_questions');

    var count = $container.find(':input').length;

    $('#add_question').click(function(e) {
        addQuestion($container);

        e.preventDefault();
        return false;
    });

    if (count == 0)
    {
        addQuestion($container);
    } else
    {
        $container.children('div').each(function()
        {
            addDeleteLink($(this));
        });
    }

    function addQuestion($container)
    {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Question n°' + (count+1))
            .replace(/__name__/g, count)
        ;

        var $prototype = $(template);

        addDeleteLink($prototype);

        $container.append($prototype);

        count++;
    }

    function addDeleteLink($prototype)
    {

        var $delete = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        $prototype.append($delete);

        $delete.click(function(e)
        {
            count--;
            $prototype.remove();

            e.preventDefault();
            return false;
        });
    }


    /**
     * Gestion des descriptions
     */

    var $descriptionContainer = $('div#add_mark_add_descriptions');
    var countDescription = $descriptionContainer.find(':input').length;


    $('#add_description').click(function(e) {
        addDescription($descriptionContainer);

        e.preventDefault();
        return false;
    });


    function addDescription($container)
    {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Description' + (countDescription+1))
            .replace(/__name__/g, countDescription)
        ;

        var $prototype = $(template);

        addDeleteLink($prototype);

        $descriptionContainer.append($prototype);

        countDescription++;
    }
});