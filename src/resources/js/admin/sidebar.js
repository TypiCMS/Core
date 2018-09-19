$(function() {
    function updatepreferences(key, value) {
        var data = {};
        data[key] = value;
        $.ajax({
            type: 'POST',
            url: '/api/users/current/updatepreferences',
            data: data,
        }).fail(function() {
            alertify.error('User preference couldn’t be set.');
        });
    }

    $('.panel-collapse').on('hide.bs.collapse', function() {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', 'true');
    });
    $('.panel-collapse').on('show.bs.collapse', function() {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', '');
    });
});
