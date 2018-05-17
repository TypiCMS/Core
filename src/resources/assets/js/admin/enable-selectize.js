$(function() {
    $('select#category_id').selectize();
    $('select#page_id').selectize();
    $('select#target').selectize();

    /**
     * Selectize for tags
     */
    if ($('#tags').length) {
        $.getJSON({
            url: '/admin/tags',
        })
            .done(function(data) {
                var tags = data.map(function(x) {
                    return { item: x.tag };
                });
                var oldTags = $('#tags')
                    .val()
                    .split(',');
                for (var i = oldTags.length - 1; i >= 0; i--) {
                    if (oldTags[i] !== '') {
                        tags.push({ item: oldTags[i] });
                    }
                }
                $('#tags').selectize({
                    persist: false,
                    create: true,
                    options: tags,
                    searchField: 'item',
                    labelField: 'item',
                    valueField: 'item',
                    createOnBlur: true,
                });
            })
            .fail(function() {
                alertify.error('An error occurred while getting tags.');
            });
    }
});
