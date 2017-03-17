$(function () {
    var closeFilepicker = function() {
        $('html, body').removeClass('noscroll');
        $(this).removeClass('filepicker-modal-open');
    }
    $('#select-files').on('click', function(event) {
        event.preventDefault();
        $('html, body').addClass('noscroll');
        $('#filepicker').addClass('filepicker-modal-open');
    });
    // $('#filepicker').on('click', function(event) {
    //     event.preventDefault();
    //     $('html, body').removeClass('noscroll');
    //     $(this).removeClass('filepicker-modal-open');
    // });
    // $('.filepicker-content').on('click', function(event) {
    //     event.stopPropagation();
    // });
});
