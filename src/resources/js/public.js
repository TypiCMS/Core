/**
 * jQuery
 */
window.$ = window.jQuery = require('jquery');

/**
 * Bootstrap
 */
require('bootstrap');

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
req.keys().forEach(function(key) {
    req(key);
});
