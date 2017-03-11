$(function () {
    $('#select-files').on('click', function(event) {
        event.preventDefault();
        $('html, body').addClass('noscroll');
        $('#filepicker').addClass('filepicker-open');
    });
    $('#filepicker').on('click', function(event) {
        event.preventDefault();
        $('html, body').removeClass('noscroll');
        $(this).removeClass('filepicker-open');
    });
    $('#btn-add-selected-files').on('click', function(event) {
        event.preventDefault();
        alert('add');
    });
});
