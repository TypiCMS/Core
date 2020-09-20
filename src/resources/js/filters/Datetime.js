export default function (datetime) {
    return new Date(datetime).toLocaleString(window.TypiCMS.locale);
}
