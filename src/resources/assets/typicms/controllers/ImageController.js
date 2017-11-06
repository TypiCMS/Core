/*jslint browser: true*/
/*globals $, jQuery, angular, TypiCMS, alertify, top, window, console*/

(function (angular) {

    'use strict';

    angular.module('typicms').controller('ImageController', ['$http', '$scope', '$rootScope', '$attrs', '$api', function ($http, $scope, $rootScope, $attrs, $api) {

        $scope.model = TypiCMS.model;

        $scope.$on('fileAdded', function (event, model) {
            $scope.model.image_id = model.id;
            $scope.model.image = model;
            $('#image_id').val(model.id);
        });

        /**
         * Remove image
         */
        $scope.removeImage = function (model) {
            if (!window.confirm('Remove this image?')) {
                return
            }
            $scope.model.image_id = null;
            $scope.model.image = null;
            $('#image_id').val('');
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
