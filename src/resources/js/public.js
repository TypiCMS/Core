/**
 * jQuery and the Bootstrap jQuery plugin
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * Fancybox
 */
require('@fancyapps/fancybox');

/**
 * Swiper
 */
require('swiper');

/**
 * Get files from /resources/js/public
 */
var req = require.context('./public', true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function (key) {
    req(key);
});
