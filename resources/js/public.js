/**
 * jQuery
 */
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

/**
 * Smooth scroll polyfill
 */
import smoothscroll from 'smoothscroll-polyfill';
smoothscroll.polyfill();
import 'smoothscroll-anchor-polyfill';

/**
 * Bootstrap
 */
import Dropdown from 'bootstrap/js/dist/dropdown';
import Collapse from 'bootstrap/js/dist/collapse';
import Alert from 'bootstrap/js/dist/alert';

/**
 * Fancybox
 */
require('@fancyapps/fancybox');

/**
 * Swiper
 */
import Swiper, { Navigation, Pagination } from 'swiper';
Swiper.use([Navigation, Pagination]);
window.Swiper = Swiper;

/**
 * Get files from /resources/js/public
 */
var req = require.context('./public', true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function (key) {
    req(key);
});
