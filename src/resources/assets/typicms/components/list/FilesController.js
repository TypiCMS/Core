/*jslint browser: true*/
/*globals $, jQuery, angular, TypiCMS, alertify, top, window, console*/

(function (angular) {

    'use strict';

    angular.module('typicms').controller('FilesController', ['$http', '$scope', '$location', '$api', function ($http, $scope, $location, $api) {

        var moduleName = 'files',
            $params = {};

        //shorctut function for testing whether a selection modifier is pressed
        function hasModifier(e)
        {
            return (e.ctrlKey || e.metaKey || e.shiftKey);
        }

        /**
         * Empty object that will contain checked items
         */
        $scope.checked = {
            models: []
        };

        $scope.path = null;

        $scope.folder = {id: ''};
        if (localStorage.getItem('folder')) {
            $scope.folder = JSON.parse(localStorage.getItem('folder'));
        }

        if (TypiCMS.content_locale == null) {
            TypiCMS.content_locale = TypiCMS.locale;
        }
        $scope.TypiCMS = TypiCMS;

        $http.get('/admin/files?folder_id='+$scope.folder.id).then(function (response) {
                $scope.models = response.data.models;
                $scope.path = response.data.path;
                //copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
                $scope.displayedModels = [].concat($scope.models);
            }, function (error) {
                console.log(error);
            });

        $scope.dropped = function(draggedModels, droppedModel) {

            let ids = [];
            draggedModels.forEach(function (model) {
                ids.push(model.id);
            });

            if (droppedModel.type !== 'f' || ids.indexOf(droppedModel.id) !== -1) {
                return;
            }

            for (var i = draggedModels.length - 1; i >= 0; i--) {
                let draggedModel = draggedModels[i];
                var index = $scope.models.indexOf(draggedModel);
                $scope.models.splice(index, 1);
            }

            let data = {
                folder_id: droppedModel.id
            }

            $api.update({id: ids}, data).$promise.then(
                function (data) {},
                function (reason) {
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );

            $scope.checked.models = [];

        }

        /**
         * Create a new folder
         */
        $scope.newFolder = function(folderId) {
            let name = window.prompt('What is the name of the new folder?');
            if (!name) {
                return;
            }
            let data = {
                folder_id: folderId,
                type: 'f',
                name: name,
                description: {},
                alt_attribute: {},
            }
            $api.save(data).$promise.then(
                function (data) {
                    $scope.models.push(data.model);
                },
                function (reason) {
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );
        }

        /**
         * Open folder
         */
        $scope.open = function (model) {
            if (model.type === 'f') {
                $http.get('/admin/files?folder_id='+model.id).then(function (response) {
                        $scope.models = response.data.models;
                        $scope.path = response.data.path;
                        //copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
                        $scope.displayedModels = [].concat($scope.models);
                    }, function (error) {
                        console.log(error);
                    });
                localStorage.setItem('folder', JSON.stringify(model));
                $scope.folder = model;
                $scope.checked.models = [];
            }
        };

        /**
         * Check an item
         */
        $scope.check = function (model, $event) {
            $event.stopPropagation();
            let indexOfLastCheckedItem = $scope.models.indexOf($scope.checked.models[$scope.checked.models.length-1]);
            let index = $scope.checked.models.indexOf(model);
            if (!hasModifier($event)) {
                $scope.checked.models = [];
            }
            if (index !== -1 && ($event.metaKey || $event.ctrlKey)) {
                $scope.checked.models.splice(index, 1);
            } else if ($scope.checked.models.indexOf(model) === -1) {
                $scope.checked.models.push(model);
            }
            if (index === -1) {
                if ($event.shiftKey) {
                    let currentItemIndex = $scope.models.indexOf(model);
                    $scope.models.forEach(function (model, index) {
                        if (currentItemIndex > indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem === -1) {
                                if (index <= currentItemIndex) {
                                    $scope.checked.models.push(model);
                                }
                            }
                            if (indexOfLastCheckedItem !== -1) {
                                if (index > indexOfLastCheckedItem && index < currentItemIndex) {
                                    if ($scope.checked.models.indexOf(model) === -1) {
                                        $scope.checked.models.push(model);
                                    }
                                }
                            }
                        }
                        if (currentItemIndex < indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem !== -1) {
                                if (index < indexOfLastCheckedItem && index > currentItemIndex) {
                                    if ($scope.checked.models.indexOf(model) === -1) {
                                        $scope.checked.models.push(model);
                                    }
                                }
                            }
                        }
                    });
                }
            }
        };

        /**
         * Uncheck all items
         */
        $scope.unCheckAll = function (model) {
            $scope.checked.models = [];
        };

        /**
         * Put items in parent folder
         */
        $scope.moveToParentFolder = function () {

            var ids = [],
                models = $scope.checked.models,
                number = models.length;

            if ($scope.checked.models.length > $scope.deleteLimit) {
                alertify.error('Too much elements (max ' + $scope.deleteLimit + ' items.)');
                return false;
            }

            models.forEach(function (model) {
                ids.push(model.id);
                var index = $scope.models.indexOf(model);
                $scope.models.splice(index, 1);
            });

            $scope.checked.models = [];

            $scope.loading = true;

            let data = {
                folder_id: $scope.path[$scope.path.length-2].id
            }

            $api.update({id: ids.join()}, data).$promise.then(
                function (data) {
                    $scope.loading = false;
                    if (data.number < number) {
                        alertify.error((number - data.number) + ' items could not be moved.');
                    }
                    if (data.number > 0) {
                        alertify.success(data.number + ' items moved.');
                    }
                },
                function (reason) {
                    $scope.loading = false;
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );

        };

        /**
         * Delete checked items
         */
        $scope.deleteChecked = function () {

            var ids = [],
                models = $scope.checked.models,
                number = models.length;

            if ($scope.checked.models.length > $scope.deleteLimit) {
                alertify.error('Impossible to delete more than ' + $scope.deleteLimit + ' items in one go.');
                return false;
            }
            if (!window.confirm('Are you sure you want to delete ' + number + ' items?')) {
                return false;
            }

            models.forEach(function (model) {
                ids.push(model.id);
            });

            $scope.loading = true;

            $api.delete({id: ids.join()}).$promise.then(
                function (data) {
                    $scope.loading = false;
                    if (data.number == 0) {
                        alertify.error(data.message);
                    } else if (data.number < number) {
                        alertify.error((number - data.number) + ' items could not be deleted.');
                    }
                    if (data.number == number) {
                        alertify.success(data.number + ' items deleted.');
                        models.forEach(function (model) {
                            var index = $scope.models.indexOf(model);
                            $scope.models.splice(index, 1);
                        });
                        $scope.checked.models = [];
                    }
                },
                function (reason) {
                    $scope.loading = false;
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );

        };

    }]);

}(angular));
