export default () => {
    const navigation = document.getElementById('navigation') as HTMLElement;
    const items = document.querySelectorAll('[data-toggle="offcanvas"]');

    items.forEach((item) => {
        item.addEventListener('click', (event) => {
            navigation.classList.toggle('active');
            const isOpen: string = navigation.classList.contains('active') ? 'true' : 'false';

            items.forEach((item) => {
                item.setAttribute('aria-expanded', isOpen);
            });

            event.preventDefault();
        });
    });
};
