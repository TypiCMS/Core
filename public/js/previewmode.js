/**
 * Appends preview=true query parameter to all same-domain links.
 * Maintains preview mode when navigating between pages.
 */
'use strict';

const searchParams = new URLSearchParams(window.location.search);
if (searchParams.has('preview')) {
    document.querySelectorAll('a[href]').forEach((link) => {
        const url = new URL(link.href);
        if (url.hostname === window.location.hostname) {
            url.searchParams.set('preview', 'true');
            link.href = url.toString();
        }
    });
}
