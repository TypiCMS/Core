import updatepreferences from './preferences';

$(function () {

    $('.panel-collapse').on('hide.bs.collapse', function () {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', 'true');
    });
    $('.panel-collapse').on('show.bs.collapse', function () {
        updatepreferences('menus_' + $(this).attr('id') + '_collapsed', '');
    });

});
