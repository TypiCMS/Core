/**
 * jQuery and the Bootstrap jQuery plugin
 */
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * Axios HTTP library
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * API Token
 */
let apiToken = document.head.querySelector('meta[name="api-token"]');

if (apiToken) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken.content}`;
} else {
    console.error('API token not found.');
}

/**
 * Cropper.js
 */
import Cropper from 'cropperjs';
window.Cropper = Cropper;

/**
 * Vue
 */
import Vue from 'vue';
window.Vue = Vue;

/**
 * i18n
 */
import VueI18n from 'vue-i18n';
import fr from '../lang/fr.json';
import en from '../lang/en.json';
import es from '../lang/es.json';
const messages = { fr, en, es };
const i18n = new VueI18n({ locale: window.TypiCMS.locale, messages });

/**
 * Date Filter
 */
import date from './filters/Date.js';
Vue.filter('date', date);

/**
 * Lists
 */
import ItemListColumnHeader from './components/ItemListColumnHeader.vue';
import ItemList from './components/ItemList.vue';
import ItemListTree from './components/ItemListTree.vue';
import ItemListStatusButton from './components/ItemListStatusButton.vue';
import ItemListCheckbox from './components/ItemListCheckbox.vue';
import ItemListPositionInput from './components/ItemListPositionInput.vue';

/**
 * History
 */
import History from './components/History.vue';

/**
 * Files
 */
import Filepicker from './components/Filepicker.vue';
import Files from './components/Files.vue';
import FileField from './components/FileField.vue';

window.EventBus = new Vue({});

new Vue({
    i18n,
    components: {
        ItemListColumnHeader,
        ItemList,
        ItemListTree,
        ItemListStatusButton,
        ItemListCheckbox,
        ItemListPositionInput,
        Filepicker,
        Files,
        FileField,
        History,
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
 * All files in /reources/js/admin
 */
var req = require.context('./admin', true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key) {
    req(key);
});
