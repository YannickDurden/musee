$(function(){

    var nameField = $('#add_route_name');
    var categoryField = $('#add_route_category');


    function onInputTextKeyUp(){

        var text = nameField.val();

        if (text.length < 5 ) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    }
    nameField.on('keyup', onInputTextKeyUp);

    function onSelectChange(){

        var text = $('#add_route_category option:selected').val();

        console.log(text);

        if (text == 0 ) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    }
    categoryField.on('change', onSelectChange);
});