$(function () {
    /**
     * Build modal window
     */
    $('<div>', {
        id: 'preview-window',
        class: 'typicms-modal',
    }).appendTo('body');

    $('<div>', {
        id: 'preview-window-wrapper',
        class: 'typicms-modal-wrapper',
    }).appendTo('#preview-window');

    $('<iframe>', {
        src: this.href,
        id: 'preview-content',
        class: 'typicms-modal-content',
        frameborder: 0,
    }).prependTo('#preview-window-wrapper');

    $('<button>', {
        id: 'close-preview',
        class: 'typicms-modal-btn-close',
    }).appendTo('#preview-window');

    $(
        '<svg width="20" height="20" viewBox="0 0 1792 1792" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>'
    ).appendTo('#close-preview');

    /**
     * Open preview window
     */
    $('.btn-preview').on('click', function (event) {
        event.preventDefault();
        $('#preview-content').attr({ src: this.href });
        $('html, body').addClass('noscroll');
        $('#preview-window').addClass('typicms-modal-open');
    });

    /**
     * Close preview window
     */
    $('body').on('click', '#close-preview', function (event) {
        event.preventDefault();
        $('#preview-content').attr({ src: '' });
        $('html, body').removeClass('noscroll');
        $('#preview-window').removeClass('typicms-modal-open');
    });
});
