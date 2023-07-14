/**
 * Add query preview=true on every <a href>
 * Required for previewing in admin side
 */
'use strict';

var params = {};
window.location.search.replace(
    /[?&]+([^=&]+)=([^&]*)/gi,
    function (str, key, value) {
        params[key] = value;
    },
);
if (params.preview) {
    document.querySelectorAll('[href]').forEach((link) => {
        var chunks = link.href.split('#');
        if (chunks[0] !== '') {
            chunks[0] =
                chunks[0] +
                (chunks[0].indexOf('?') !== -1
                    ? '&preview=true'
                    : '?preview=true');
        }
        link.href = chunks.join('#');
    });
}
