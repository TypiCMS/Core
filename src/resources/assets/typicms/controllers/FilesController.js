/*jslint browser: true*/
/*globals $, jQuery, angular, TypiCMS, alertify, top, window, console*/

(function (angular) {

    'use strict';

    angular.module('typicms').controller('FilesController', ['$http', '$scope', '$rootScope', '$location', '$attrs', function ($http, $scope, $rootScope, $location, $attrs) {

        $scope.url = $attrs.url;

        /**
         * Empty object that will contain checked items.
         */
        $scope.checked = {
            models: []
        };

        $scope.path = null;

        $scope.model = {};

        $scope.view = 'grid';
        if (sessionStorage.getItem('view')) {
            $scope.view = JSON.parse(sessionStorage.getItem('view'));
        }

        $scope.folder = {id: ''};
        if (sessionStorage.getItem('folder')) {
            $scope.folder = JSON.parse(sessionStorage.getItem('folder'));
        }
        if ($scope.folder.id) {
            $scope.url += '?folder_id=' + $scope.folder.id;
        }

        if (TypiCMS.content_locale == null) {
            TypiCMS.content_locale = TypiCMS.locale;
        }
        $scope.TypiCMS = TypiCMS;

        $http.get($scope.url).then(function (response) {
            $scope.models = response.data.models;
            $scope.model.models = response.data.models;
            $scope.path = response.data.path;
            //copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
            $scope.displayedModels = [].concat($scope.models);
        }, function (error) {
            console.log(error);
        });

        $scope.$on('filesAdded', function (event, models) {
            $scope.model.models = models;
        });

        $scope.sortableOptions = {
            animation: 100,
            onSort: function (evt){
                $http({
                    method: 'POST',
                    url: '/admin/files/sort',
                    data: evt.models
                }).then(function successCallback(response) {
                }, function errorCallback(response) {
                    alertify.error('Error ' + response.status + ' ' + response.statusText);
                });
            }
        };

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

            $http.patch('/admin/files/'+ids.join(), data).then(function (response) {
            }, function (reason) {
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });

            $scope.checked.models = [];

        }

        /**
         * Create a new folder.
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

            $http.post('/admin/files', data).then(function (response) {
                $scope.models.push(response.data.model);
            }, function (reason) {
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });
        }

        /**
         * Select an item.
         */
        $scope.check = function (model, $event) {
            $event.stopPropagation();
            let indexOfLastCheckedItem = $scope.models.indexOf($scope.checked.models[$scope.checked.models.length-1]);
            let index = $scope.checked.models.indexOf(model);
            if (!($event.ctrlKey || $event.metaKey || $event.shiftKey)) {
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
            if (!$scope.folder.id) {
                return;
            }

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

            $http.patch('/admin/files/'+ids.join(), data).then(function (response) {
                $scope.loading = false;
                if (response.data.number < number) {
                    alertify.error((number - response.data.number) + ' items could not be moved.');
                }
                if (response.data.number > 0) {
                    alertify.success(response.data.number + ' items moved.');
                }
            }, function (reason) {
                $scope.loading = false;
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });

        };

        /**
         * Remove a file attached to a model
         */
        $scope.remove = function (model) {
            var segments = $location.absUrl().split('?')[0].split('/').reverse(),
                modelId = segments[1],
                module = segments[2],
                index = $scope.model.models.indexOf(model);

            $scope.model.models.splice(index, 1);
            $scope.loading = true;

            $http.patch('/admin/' + module + '/' + modelId, {remove: model.id}).then(function (response) {
                $scope.loading = false;
            }, function (reason) {
                $scope.loading = false;
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });

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

            $http.delete('/admin/files/'+ids.join()).then(function (response) {
                $scope.loading = false;
                if (response.data.number == 0) {
                    alertify.error(response.data.message);
                } else if (response.data.number < number) {
                    alertify.error((number - response.data.number) + ' items could not be deleted.');
                }
                if (response.data.number == number) {
                    alertify.success(response.data.number + ' items deleted.');
                    models.forEach(function (model) {
                        var index = $scope.models.indexOf(model);
                        $scope.models.splice(index, 1);
                    });
                    $scope.checked.models = [];
                }
            }, function (reason) {
                $scope.loading = false;
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });

        };

        /**
         * Add selected items to model
         */
        $scope.addSelectedFiles = function () {
            var ids = [],
                models = $scope.checked.models,
                data = {},
                segments = $location.absUrl().split('?')[0].split('/').reverse(),
                modelId = segments[1],
                module = segments[2];

            if (models.length === 0) {
                $('html, body').removeClass('noscroll');
                $('#filepicker').removeClass('filepicker-modal-open');
                return;
            }

            models.forEach(function (model) {
                ids.push(model.id);
            });
            data.files = ids;


            $http.patch('/admin/' + module + '/' + modelId, data).then(function (response) {

                $scope.checked.models = [];

                $rootScope.$broadcast('filesAdded', response.data.models);
                $('html, body').removeClass('noscroll');
                $('#filepicker').removeClass('filepicker-modal-open');

                if (response.data.number == 0) {
                    alertify.error(response.data.message);
                } else {
                    alertify.success(response.data.message);
                }

            }, function (reason) {
                console.log(reason);
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });

        }

        /**
         * Switch view.
         */
        $scope.switchView = function (view) {
            $scope.view = view;
            sessionStorage.setItem('view', JSON.stringify(view));
        }

        /**
         * Handle a double click on a file or folder.
         */
        $scope.handle = function (model) {
            if (model.type === 'f') {
                $http.get('/admin/files?folder_id='+model.id).then(function (response) {
                        $scope.models = response.data.models;
                        $scope.path = response.data.path;
                        //copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
                        $scope.displayedModels = [].concat($scope.models);
                    }, function (error) {
                        console.log(error);
                    });
                sessionStorage.setItem('folder', JSON.stringify(model));
                $scope.folder = model;
                $scope.checked.models = [];
            } else {
                var CKEditorCleanUpFuncNum = $('#filepicker').data('CKEditorCleanUpFuncNum'),
                    CKEditorFuncNum = $('#filepicker').data('CKEditorFuncNum');
                if (!!CKEditorFuncNum || !!CKEditorCleanUpFuncNum) {
                    parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, '/storage/' + model.path);
                    parent.CKEDITOR.tools.callFunction(CKEditorCleanUpFuncNum);
                } else {
                    $rootScope.$broadcast('fileAdded', model);
                    $('html, body').removeClass('noscroll');
                    $('#filepicker').removeClass('filepicker-modal-open');
                }
            }
        };

        /**
         * Add selected item
         */
        $scope.addSelectedFile = function () {
            $rootScope.$broadcast('fileAdded', $scope.checked.models[0]);
            $('html, body').removeClass('noscroll');
            $('#filepicker').removeClass('filepicker-modal-open');
        }

    }]);

}(angular));
