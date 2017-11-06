/*jslint browser: true*/
/*globals $, jQuery, angular, TypiCMS, alertify, top, window, console*/

(function (angular) {

    'use strict';

    angular.module('typicms').controller('DocumentController', ['$http', '$scope', '$rootScope', '$attrs', '$api', function ($http, $scope, $rootScope, $attrs, $api) {

        $scope.model = TypiCMS.model;

        $scope.$on('fileAdded', function (event, model) {
            $scope.model.document_id = model.id;
            $scope.model.document = model;
            $('#document_id').val(model.id);
        });

        /**
         * Remove document
         */
        $scope.removeDocument = function (model) {
            if (!window.confirm('Remove this document?')) {
                return
            }
            $scope.model.document_id = null;
            $scope.model.document = null;
            $('#document_id').val('');
        };

        /**
         * Open Filepicker
         */
        $scope.openFilepicker = function () {
            $('html, body').addClass('noscroll');
            $('#filepicker').addClass('filepicker-modal-open');
        }

    }]);

}(angular));
