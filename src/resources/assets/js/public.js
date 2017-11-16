/**
 * jQuery
 */
window.$ = window.jQuery = require('jquery')

/**
 * Bootstrap
 */
require('bootstrap-sass');

/**
 * Fancybox
 */
require('@fancyapps/fancybox');

/**
 * Swiper
 */
require('swiper');

/**
 * Get files from /resources/assets/js/public
 */
var req = require.context("../../../resources/assets/js/public", true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key){
    req(key);
});
