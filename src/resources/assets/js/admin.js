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
import TypiCheckbox from './components/TypiCheckbox.vue';

/**
 * Lists
 */
import ColumnHeader from './components/ColumnHeader.vue';
import ItemList from './components/ItemList.vue';
import ItemListTree from './components/ItemListTree.vue';
new Vue({
    i18n,
    components: {
        ColumnHeader,
        ItemList,
        ItemListTree,
        TypiBtnStatus,
        TypiCheckbox,
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
