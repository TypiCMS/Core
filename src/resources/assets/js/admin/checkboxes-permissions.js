$(function () {

    var checkbox = $('<input>')
        .attr('type', 'checkbox')
        .addClass('permissions-modules-item-title-checkbox')
        .click(function(){
            var checkedStatus = this.checked;
            $(this).closest('.permissions-modules-item').find('input:checkbox:not(:disabled)').prop('checked', checkedStatus);
        });
    $('.permissions-modules-item-title').prepend(checkbox).wrap('<div class="checkbox" />').wrap('<label />');
    $('.permissions-modules-item-title-checkbox').each(function(index, el) {
        var container = $(this).closest('.permissions-modules-item').find('.permissions-modules-item-checkbox'),
            all = container.find('input:checkbox').length,
            checked = container.find('input:checkbox:checked').length;
        if (all === checked) {
            $(this).prop('checked', true);
        }
    });

});
