/*jslint browser: true*/
/*globals $, jQuery, angular, TypiCMS, alertify, top, window, console*/

(function (angular) {

    'use strict';

    angular.module('typicms').controller('ListController', ['$http', '$scope', '$location', '$api', '$cookies', function ($http, $scope, $location, $api, $cookies) {

        $scope.itemsByPage = 100;
        $scope.allChecked = false;
        $scope.deleteLimit = 1000;
        var url = $location.absUrl().split('?')[0],
            modulePath = url.split('/')[4],
            $params = {};

        $scope.TypiCMS = TypiCMS;

        if (TypiCMS.content_locale == null) {
            TypiCMS.content_locale = TypiCMS.locale;
        }

        // if we query menulinks menu_id value :
        if (modulePath === 'menus' && url.split('/')[5]) {
            $params.menu_id = url.split('/')[5];
            modulePath = 'menulinks';
        }

        if (modulePath === 'projects' && url.split('/')[5]) {
            modulePath = 'projects/categories';
        }

        if (modulePath === 'pages' && url.split('/')[5]) {
            $params.page_id = url.split('/')[5];
            modulePath = 'sections';
        }

        if (TypiCMS.models) {
            $scope.models = TypiCMS.models;
            $scope.displayedModels = [].concat($scope.models);
        } else {
            $api.query($params).$promise.then(function (all) {
                $scope.models = all;
                // Copy the references (you could clone ie angular.copy but then have to go through a dirty checking for the matches)
                $scope.displayedModels = [].concat($scope.models);
            });
        }

        /**
         * Empty object that will contain checked items
         */
        $scope.checked = {
            models: []
        };

        /**
         * Check all items
         */
        $scope.toggleCheckAll = function () {
            var countChecked = $scope.checked.models.length
            $scope.checked.models = [];
            if (countChecked < $scope.displayedModels.length) {
                this.checkAll();
            }
        };

        /**
         * Check all items
         */
        $scope.checkAll = function () {
            $scope.allChecked = true;
            $scope.checked.models = [];
            $scope.displayedModels.forEach(function (model) {
                $scope.checked.models.push(model);
            });
        };

        /**
         * Uncheck all items
         */
        $scope.uncheckAll = function () {
            $scope.allChecked = false;
            $scope.checked.models = [];
        };

        /**
         * Set all checked items published or unpublished
         */
        $scope.setItems = function (column, value, label) {

            var ids = [],
                models = $scope.checked.models,
                number = models.length,
                data = {};

            if (!window.confirm('Are you sure you want to set ' + number + ' items ' + label + '?')) {
                return false;
            }

            if (typeof value === 'object') {
                for (var key in value) {
                    if (value.hasOwnProperty(key)) {
                        data[column+'->'+key] = value[key];
                    }
                }
            } else {
                data[column] = value;
            }

            models.forEach(function (model) {
                ids.push(model.id);
                if (typeof value === 'object') {
                    for (var key in value) {
                        if (value.hasOwnProperty(key)) {
                            model[column][key] = value[key];
                            model[column+'_translated'] = value[key];
                        }
                    }
                } else {
                    model[column] = value;
                }
            });

            $api.update({id: ids.join()}, data).$promise.then(
                function (data) {
                    if (data.number < number) {
                        alertify.error((number - data.number) + ' items could not be set ' + label + '.');
                    } else {
                        alertify.success(data.number + ' items set ' + label + '.');
                    }
                },
                function (reason) {
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );

        };

        /**
         * Check all items that have a key equal to value
         */
        $scope.check = function (column, value) {
            $scope.allChecked = false;
            $scope.checked.models = [];
            $scope.models.forEach(function (model) {

                if (typeof value === 'object') {
                    for (var key in value) {
                        if (value.hasOwnProperty(key)) {
                            if (model[column][key] == value[key]) {
                                $scope.checked.models.push(model);
                            }
                        }
                    }
                } else {
                    if (model[column] == value) {
                        $scope.checked.models.push(model);
                    }
                }

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
                var index = $scope.models.indexOf(model);
                $scope.models.splice(index, 1);
            });

            $scope.checked.models = [];

            $scope.loading = true;

            $api.delete({id: ids.join()}).$promise.then(
                function (data) {
                    $scope.loading = false;
                    if (data.number < number) {
                        alertify.error((number - data.number) + ' items could not be deleted.');
                    }
                    if (data.number > 0) {
                        alertify.success(data.number + ' items deleted.');
                    }
                },
                function (reason) {
                    $scope.loading = false;
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );

        };

        /**
         * Set status = 0 or 1 for item
         */
        $scope.toggleStatus = function (model) {
            var status = parseInt(model.status[TypiCMS.content_locale]) || 0,
                newStatus = Math.abs(status - 1).toString(),
                data = {
                    status: {}
                },
                label = (newStatus === '1')
                    ? 'published'
                    : 'unpublished';
            model.status[TypiCMS.content_locale] = newStatus;
            model.status_translated = newStatus;
            data.status[TypiCMS.content_locale] = newStatus;
            $api.update({id: model.id}, data).$promise.then(
                function (response) {
                    alertify.success('Item is ' + label + '.');
                },
                function (reason) {
                    alertify.error(reason.data.error);
                }
            );
        };

        /**
         * Set homepage = 0 or 1 for item
         */
        $scope.toggleHomepage = function (model) {
            model.homepage = Math.abs(model.homepage - 1);
            $api.update({id: model.id}, model).$promise.then(
                function () {
                    alertify.success('Homepage is set.');
                },
                function (reason) {
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );
        };

        /**
         * Clear history
         */
        $scope.clearHistory = function () {
            if (window.confirm('Are you sure you want to clear history?')) {
                $api.delete().$promise.then(
                    function (data) {
                        if (data.error) {
                            console.log(data);
                        } else {
                            $scope.displayedModels = [];
                        }
                    },
                    function (reason) {
                        alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                    }
                );
            }
        };

        /**
         * Update model
         */
        $scope.update = function (model, column) {
            var data = {}
            data[column] = model[column];
            $api.update({id: model.id}, data).$promise.then(
                null,
                function (reason) {
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                }
            );
        };

        /**
         * Delete an item from a non-nested list
         */
        $scope.delete = function (model, title) {
            if (!title) {
                title = model.title[TypiCMS.content_locale];
            }
            if (!window.confirm('Do you want to delete « ' + title + ' » ?')) {
                return false;
            }
            var index = $scope.models.indexOf(model);
            $api.delete({id: model.id}, function (data) {
                if (index !== -1) {
                    $scope.models.splice(index, 1);
                }
                if (data.error) {
                    alertify.error('Error');
                }
            }, function (reason) {
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });
        };

        /**
         * Save the collapsed state of pages tree.
         */
        $scope.saveState = function (scope) {
            var pagesState = $cookies.getObject('pagesState') || {};
            pagesState[scope.$id] = scope.collapsed;
            $cookies.putObject('pagesState', pagesState);
        };

        /**
         * Delete an item from a nested list
         */
        $scope.deleteFromNested = function (scope, title) {
            if (!title) {
                title = scope.model.title[TypiCMS.content_locale];
            }
            if (scope.hasChild()) {
                alertify.error('This item cannot be deleted because it has children.');
                return false;
            }
            if (!window.confirm('Do you want to delete « ' + title + ' » ?')) {
                return false;
            }
            $api.delete({id: scope.model.id}, function (data) {
                scope.remove();
                if (data.error) {
                    alertify.error('Error');
                }
            }, function (reason) {
                alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            });
        };

        $scope.treeOptions = {
            collapsed: function(scope) {
                if (!scope.hasChild()) {
                    return false;
                }
                var pagesState = $cookies.getObject('pagesState') || {};
                if (pagesState[scope.$id] === undefined) {
                    return true;
                }
                return pagesState[scope.$id];
            },
            accept: function (sourceNodeScope, destNodesScope) {
                if (destNodesScope.model && destNodesScope.model.module) {
                    return false;
                }
                return true;
            },
            dropped: function (event) {

                var model = event.source.nodeScope.model,
                    parentId = null,
                    data = {},
                    nodes = event.dest.nodesScope,
                    currentList = nodes.$modelValue;

                // If there is a parent
                if (event.dest.nodesScope.$nodeScope) {
                    parentId = nodes.$nodeScope.model.id;
                }

                // do nothing when no move
                if (event.dest.index === event.source.index && model.parent_id === parentId) {
                    return false;
                }

                // If parent is private set current model to private
                if (event.dest.nodesScope.$nodeScope) {
                    if (nodes.$nodeScope.model.private == 1) {
                        model.private = 1;
                    }
                }

                data.moved = model.id;
                data.item = [];
                model.position = event.dest.index + 1;
                model.parent_id = parentId;

                angular.forEach(currentList, function (model) {
                    data.item.push({id: model.id, parent_id: model.parent_id, private: model.private});
                });

                $http.post('/admin/' + modulePath + '/sort', data).success(function (data) {
                    alertify.success(data.message);
                }).error(function (data) {
                    alertify.error(data.error.message);
                });

            }
        };

    }]);

}(angular));
