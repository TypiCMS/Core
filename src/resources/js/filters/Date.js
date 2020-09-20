export default function (sqlDate) {
    if (!sqlDate) {
        return '';
    }
    let datetime = sqlDate.split('T');
    return datetime[0].split('-').reverse().join('.');
}
