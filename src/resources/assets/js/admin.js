// window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * jQuery and the Bootstrap jQuery plugin
 */
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (e) {}

/**
 * Axios HTTP library
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF Token
 */
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Dropzone
 */
window.Dropzone = require('dropzone');

/**
 * Vue
 */
import Vue from 'vue';
window.Vue = Vue;

/**
 * DraggableTree
 */
import { DraggableTree } from 'vue-draggable-nested-tree';
window.DraggableTree = DraggableTree;
import SlVueTree from 'sl-vue-tree';
window.SlVueTree = SlVueTree;

/**
 * i18n
 */
import VueI18n from 'vue-i18n';
import fr from '../../lang/fr.json';
const messages = { fr };
const i18n = new VueI18n({ locale: 'fr', messages });

/**
 * Date filter
 */
import date from './filters/Date.js';
Vue.filter('date', date);

import TypiBtnStatus from './components/TypiBtnStatus.vue';

/**
 * Lists
 */
import ItemList from './components/ItemList.vue';
new Vue({
    i18n,
    components: {
        ItemList,
        TypiBtnStatus,
    },
}).$mount('#app');

/**
 * Alertify
 */
window.alertify = require('alertify.js');

/**
 * Selectize
 */
require('selectize');

/**
 * All files in /reources/assets/js/admin
 */
var req = require.context('../../../resources/assets/js/admin', true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key) {
    req(key);
});
