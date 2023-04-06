export default function enableSetContentLocale() {
    /**
     * Change content locale when user change tab in admin forms
     *
     * @param  {string} locale
     * @return {void}
     */

    function setLocale(locale) {
        axios.get('/admin/_locale/' + locale).catch(function () {
            alertify.error('Content locale couldn’t be set to ' + locale);
        });
    }

    document.querySelectorAll('.btn-lang-js').forEach((button) => {
        button.addEventListener(
            'click',
            (event) => {
                const clickedItem = event.target,
                    locale = clickedItem.dataset.locale,
                    label = clickedItem.textContent;
                [...clickedItem.parentElement.children].forEach((item) => {
                    item.classList.remove('active');
                });
                clickedItem.classList.add('active');
                if (locale === 'all') {
                    document.querySelectorAll('.form-group-translation').forEach((element) => {
                        element.style.display = 'block';
                    });
                } else {
                    document.querySelectorAll('.form-group-translation').forEach((element) => {
                        element.style.display = element.querySelector('[data-language="' + locale + '"]')
                            ? 'block'
                            : 'none';
                    });
                }
                const activeLocale = document.getElementById('active-locale');
                if (activeLocale) {
                    activeLocale.textContent = label;
                }
                setLocale(locale);
                event.preventDefault();
            },
            false
        );
    });
    const currentLocaleButton = document.querySelector('.btn-lang-js.active');
    if (currentLocaleButton) {
        currentLocaleButton.click();
    }
}
