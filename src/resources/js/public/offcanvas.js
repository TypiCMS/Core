(function () {
    const sidebar = document.getElementById('navigation');
    const items = document.querySelectorAll('[data-toggle="offcanvas"]');
    for (i = 0; i < items.length; i++) {
        items[i].addEventListener('click', function (event) {
            const element = event.target || event.srcElement;
            sidebar.classList.toggle('active');
            const isOpen = sidebar.classList.contains('active');
            for (j = 0; j < items.length; j++) {
                items[j].setAttribute('aria-expanded', isOpen);
            }
            event.preventDefault();
        });
    }
})();
