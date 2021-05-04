export default function (date) {
    if (date === null) {
        return '';
    }
    return new Date(date).toLocaleDateString(window.TypiCMS.locale_country);
}
