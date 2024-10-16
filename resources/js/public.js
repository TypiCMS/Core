/**
 * Bootstrap
 */
import Dropdown from 'bootstrap/js/dist/dropdown';
import Collapse from 'bootstrap/js/dist/collapse';
import Alert from 'bootstrap/js/dist/alert';

/**
 * Swiper
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade, Parallax } from 'swiper/modules';
import enablePhotoSwipeLightbox from './public/photo-swipe-lightbox.ts';
import enableAnchorTop from './public/anchor-top.ts';
import enableOffCanvas from './public/offcanvas.ts';

Swiper.use([Navigation, Pagination, Autoplay, Parallax, EffectFade]);
window.Swiper = Swiper;

enablePhotoSwipeLightbox();
enableAnchorTop();
enableOffCanvas();

import.meta.glob(['../images/**']);

/**
 * For TypiCMS’s Places module
 */
// import initMap from './public/map';
// window.initMap = initMap;
