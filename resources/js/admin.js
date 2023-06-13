/**
 * Bootstrap
 */
import Dropdown from 'bootstrap/js/dist/dropdown';
import Tab from 'bootstrap/js/dist/tab';
import Collapse from 'bootstrap/js/dist/collapse';
import Alert from 'bootstrap/js/dist/alert';
import Offcanvas from 'bootstrap/js/dist/offcanvas';

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
import en from '../../lang/en.json';
import es from '../../lang/es.json';
const messages = { fr, en, es };
const i18n = new VueI18n({ locale: window.TypiCMS.locale, messages });

/**
 * Permissions mixin
 */
import Permissions from './mixins/Permissions.js';
Vue.mixin(Permissions);

/**
 * Date Filter
 */
import date from './filters/Date.js';
Vue.filter('date', date);

/**
 * Datetime Filter
 */
import datetime from './filters/Datetime.js';
Vue.filter('datetime', datetime);

/**
 * Lists
 */
import ItemListColumnHeader from './components/ItemListColumnHeader.vue';
import ItemList from './components/ItemList.vue';
import ItemListTree from './components/ItemListTree.vue';
import ItemListStatusButton from './components/ItemListStatusButton.vue';
import ItemListEditButton from './components/ItemListEditButton.vue';
import ItemListShowButton from './components/ItemListShowButton.vue';
import ItemListCheckbox from './components/ItemListCheckbox.vue';
import ItemListPositionInput from './components/ItemListPositionInput.vue';

/**
 * History
 */
import History from './components/History.vue';

/**
 * Files
 */
import FileManager from './components/FileManager.vue';
import FileField from './components/FileField.vue';
import FilesField from './components/FilesField.vue';

window.EventBus = new Vue({});

new Vue({
    i18n,
    components: {
        ItemListColumnHeader,
        ItemList,
        ItemListTree,
        ItemListStatusButton,
        ItemListEditButton,
        ItemListShowButton,
        ItemListCheckbox,
        ItemListPositionInput,
        FileManager,
        FilesField,
        FileField,
        History,
    },
}).$mount('#app');

/**
 * Alertify
 */
import alertify from 'alertify.js';
window.alertify = alertify;

/**
 * TomSelect
 */
import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

/**
 * Preview window
 */
import enablePreviewWindow from './admin/preview-window';
enablePreviewWindow();

/**
 * Set content locale
 */
import enableSetContentLocale from './admin/set-content-locale';
enableSetContentLocale();

/**
 * Enable sidebar section collapse
 */
import enableSidebarPanelCollapse from './admin/enable-sidebar-panel-collapse';
enableSidebarPanelCollapse();

/**
 * Enable delete attachment
 */
import enableDeleteAttachment from './admin/enable-delete-attachment';
enableDeleteAttachment();

/**
 * Enable checkboxes for the roles’ permissions.
 */
import enableCheckboxesPermissions from './admin/enable-checkboxes-permissions';
enableCheckboxesPermissions();

/**
 * Enable tag field with TomSelect.
 */
import enableTagsField from './admin/enable-tags-field';
enableTagsField();

/**
 * Slug plugin.
 */
import Slug from './admin/slug';
const items = document.querySelectorAll('[data-slug]').forEach((item) => {
    new Slug(item);
});
