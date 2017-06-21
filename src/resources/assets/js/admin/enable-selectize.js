$(function () {

    $('select#galleries').selectize();
    $('select#category_id').selectize();
    $('select#page_id').selectize();
    $('select#target').selectize();

    /**
     * Selectize for tags
     */
    $('#tags').selectize({
        persist: false,
        create: true,
        options: TypiCMS.tags,
        searchField: ['item'],
        labelField: 'item',
        valueField: 'item',
        createOnBlur: true
    });

});
