/**
 * jQuery
 */
window.$ = window.jQuery = require('jquery');

/**
 * Bootstrap
 */
import 'bootstrap/js/dist/dropdown';
import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/alert';

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
