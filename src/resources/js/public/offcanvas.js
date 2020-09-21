$(function () {
    $('[data-toggle="offcanvas"]').click(function (e) {
        $('.site-header-offcanvas').toggleClass('active');
        e.preventDefault();
    });
});
