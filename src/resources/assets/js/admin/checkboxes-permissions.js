function enablePermissionsCheckboxes() {
    var checkbox = $('<input>')
        .attr('type', 'checkbox')
        .click(function(){
            var checkedStatus = this.checked;
            $(this).closest('.permissions-modules-item').find('input:checkbox:not(:disabled)').prop('checked', checkedStatus);
        });
    $('.permissions-modules-item-title').prepend(checkbox).wrap('<div class="checkbox" />').wrap('<label />');
}

!function( $ ){

    "use strict";

    $(function () {

        enablePermissionsCheckboxes();

    });

}( window.jQuery || window.ender );
