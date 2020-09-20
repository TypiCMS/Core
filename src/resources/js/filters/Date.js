export default function (date) {
    return new Date(date).toLocaleDateString(window.TypiCMS.locale);
}
