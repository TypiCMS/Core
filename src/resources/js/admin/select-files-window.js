$(function() {
    var closeFilepicker = function() {
        $('html, body').removeClass('noscroll');
        $(this).removeClass('filepicker-modal-open');
    };
    $('#select-files').on('click', function(event) {
        event.preventDefault();
        $('html, body').addClass('noscroll');
        $('#filepicker').addClass('filepicker-modal-open');
    });
    $('#close-filepicker').on('click', function(event) {
        event.preventDefault();
        $('html, body').removeClass('noscroll');
        $('#filepicker')
            .removeClass('filepicker-modal-open')
            .removeClass('filepicker-modal-no-overlay');
    });
});
