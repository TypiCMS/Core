export default function (datetime) {
    if (datetime === null) {
        return '';
    }
    return new Date(datetime).toLocaleString(window.TypiCMS.locale_country);
}
