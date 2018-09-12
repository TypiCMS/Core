$(function() {
    var closeFilepicker = function() {
        $('html, body').removeClass('noscroll');
        $(this).removeClass('filepicker-modal-open');
    };
    $('#select-files').on('click', function(event) {
        event.preventDefault();
        $('html, body').addClass('noscroll');
        $('#filepicker.filepicker-multiple').addClass('filepicker-modal-open');
    });
    $('.filepicker-btn-close').on('click', function(event) {
        event.preventDefault();
        $('html, body').removeClass('noscroll');
        $(this)
            .closest('.filepicker-modal')
            .removeClass('filepicker-modal-open')
            .removeClass('filepicker-modal-no-overlay');
    });
});
