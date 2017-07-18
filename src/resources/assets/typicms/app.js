/*jslint browser: true*/
/*globals $, jQuery, angular, window*/

(function (angular) {

    'use strict';

    angular.module('typicms', ['ngResource', 'ngCookies', 'smart-table', 'ui.tree', 'ng-sortable', 'checklist-model']);

    // Creating an 'update' method (PUT)
    angular.module('typicms').factory('$api', ['$resource', function ($resource) {

        var pathSegments = window.location.pathname.split('/'),
            modulePath = pathSegments[2];

        if (modulePath === 'galleries' && pathSegments[4] === 'edit') {
            modulePath = 'galleries/'+pathSegments[3]+'/files';
        }
        if (modulePath === 'menus' && pathSegments[4] === 'edit') {
            modulePath = 'menulinks';
        }
        if (modulePath === 'dashboard') {
            modulePath = 'history';
        }
        if (modulePath === 'projects' && pathSegments[3] === 'categories') {
            modulePath = 'projects/categories';
        }
        if (modulePath === 'pages' && pathSegments[4] === 'edit') {
            modulePath = 'sections';
        }

        return $resource('/admin/' + modulePath + '/:id', null, {
            update: {
                method: 'PATCH'
            }
        });
    }]);

}(angular));
