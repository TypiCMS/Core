window.addEventListener('scroll', function (e) {
    var anchorTop = document.getElementById('anchor-top');
    if (window.scrollY > 300) {
        anchorTop.classList.remove('disabled');
    } else {
        anchorTop.classList.add('disabled');
    }
});
