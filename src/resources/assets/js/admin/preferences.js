/**
 * Set user preferences
 *
 * @param  {string} key
 * @param  {string} value
 * @return {void}
 */
export default function (key, value) {
    var data = {};
    data[key] = value;
    $.ajax({
        type: 'POST',
        url: '/admin/users/current/updatepreferences',
        data: data
    }).fail(function () {
        alertify.error('User preference couldn’t be set.');
    });
}
