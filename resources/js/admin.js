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
import { createApp } from 'vue/dist/vue.esm-bundler';

/**
 * i18n
 */
import { createI18n } from 'vue-i18n';
import fr from '../../lang/fr.json';
import en from '../../lang/en.json';
import es from '../../lang/es.json';

const messages = { fr, en, es };
const i18n = new createI18n({
    legacy: false,
    locale: window.TypiCMS.locale,
    messages,
});

/**
 * Mixins
 */
import Permissions from './mixins/Permissions.js';
import Date from './mixins/Date.js';
import DateTime from './mixins/Datetime.js';

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

/**
 * Event bus
 */
import mitt from 'mitt';

const emitter = mitt();
window.emitter = emitter;

const app = createApp();
app.component('ItemListColumnHeader', ItemListColumnHeader);
app.component('ItemList', ItemList);
app.component('ItemListTree', ItemListTree);
app.component('ItemListStatusButton', ItemListStatusButton);
app.component('ItemListEditButton', ItemListEditButton);
app.component('ItemListShowButton', ItemListShowButton);
app.component('ItemListCheckbox', ItemListCheckbox);
app.component('ItemListPositionInput', ItemListPositionInput);
app.component('History', History);
app.component('FileManager', FileManager);
app.component('FileField', FileField);
app.component('FilesField', FilesField);
app.mixin(Permissions);
app.mixin(DateTime);
app.mixin(Date);
app.use(i18n);
app.config.globalProperties.emitter = emitter;
app.mount('#app');

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
import enablePreviewWindow from './admin/preview-window.ts';

enablePreviewWindow();

/**
 * Set content locale
 */
import enableSetContentLocale from './admin/set-content-locale.ts';

enableSetContentLocale();

/**
 * Enable sidebar section collapse
 */
import enableSidebarPanelCollapse from './admin/enable-sidebar-panel-collapse.ts';

enableSidebarPanelCollapse();

/**
 * Enable delete attachment
 */
import enableDeleteAttachment from './admin/enable-delete-attachment.ts';

enableDeleteAttachment();

/**
 * Enable checkboxes for the roles’ permissions.
 */
import enableCheckboxesPermissions from './admin/enable-checkboxes-permissions.ts';

enableCheckboxesPermissions();

/**
 * Enable tag field with TomSelect.
 */
import enableTagsField from './admin/enable-tags-field.ts';

enableTagsField();

/**
 * Slug plugin.
 */
import Slug from './admin/slug.ts';

document.querySelectorAll('[data-slug]').forEach((item) => new Slug(item));
