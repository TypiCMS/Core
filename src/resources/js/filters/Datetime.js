export default function (sqlDatetime, withSeconds = false) {
    if (!sqlDatetime) {
        return '';
    }
    let datetime = sqlDatetime.split(' ');
    const date = datetime[0].split('-').reverse().join('.');
    const time = datetime[1].split(':');
    if (!withSeconds) {
        time.pop();
    }

    return date + ' ' + time.join(':');
}
