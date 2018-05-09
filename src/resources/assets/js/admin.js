/**
 * jQuery
 */
window.$ = window.jQuery = require('jquery');

/**
 * Bootstrap
 */
require('bootstrap');

/**
 * Dropzone
 */
window.Dropzone = require('dropzone');

/**
 * Vue
 */
import Vue from 'vue';
window.Vue = Vue;
import { DraggableTree } from 'vue-draggable-nested-tree';
window.DraggableTree = DraggableTree;
import SlVueTree from 'sl-vue-tree';
window.SlVueTree = SlVueTree;

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

// import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

/**
 * Angular
 */
require('angular');
require('angular-cookies');
require('angular-resource');
require('checklist-model');
require('angular-smart-table/dist/smart-table.js');
require('angular-ui-tree');
require('sortablejs');
require('sortablejs/ng-sortable');
var req = require.context('../../../resources/assets/typicms', true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key) {
    req(key);
});

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
