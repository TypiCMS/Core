/**
 * jQuery
 */
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

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
import Swiper, { Navigation, Pagination, Autoplay, Parallax, EffectFade } from 'swiper';
Swiper.use([Navigation, Pagination, Autoplay, Parallax, EffectFade]);
window.Swiper = Swiper;

/**
 * Get files from /resources/js/public
 */
import enableAnchorTop from './public/anchor-top.js';
enableAnchorTop();
import enableOffCanvas from './public/offcanvas.js';
enableOffCanvas();
