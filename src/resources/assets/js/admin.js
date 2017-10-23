/**
 * jQuery
 */
window.$ = window.jQuery = require('jquery')

/**
 * Bootstrap
 */
require('bootstrap-sass');

/**
 * Dropzone
 */
window.Dropzone = require('dropzone');

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
var req = require.context("../../../resources/assets/typicms", true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key){
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
var req = require.context("../../../resources/assets/js/admin", true, /^(.*\.(js$))[^.]*$/im);
req.keys().forEach(function(key){
    req(key);
});
