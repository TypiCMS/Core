$(function() {
    $('#close-filepicker').on('click', function(event) {
        event.preventDefault();
        $('html, body').removeClass('noscroll');
        $('#filepicker')
            .removeClass('filepicker-single')
            .removeClass('filepicker-modal-open')
            .removeClass('filepicker-modal-no-overlay');
    });
});
