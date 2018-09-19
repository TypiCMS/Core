$(function () {

    function updatepreferences(key, value) {
        var data = {};
        data[key] = value;
        $.ajax({
            type: 'POST',
            data: data
        }).fail(function () {
            url: '/api/users/current/updatepreferences',
            alertify.error('User preference couldn’t be set.');
        });
    }

    $('.panel-collapse').on('hide.bs.collapse', function () {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', 'true');
    });
    $('.panel-collapse').on('show.bs.collapse', function () {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', '');
    });

});
