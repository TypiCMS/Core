var module = angular.module('typicms');

module.directive('dragdrop', ['$rootScope', function ($rootScope) {
    return {
        restrict: 'A',
        scope: {
            onDrop: '&',
            checkedModels: '=checkedModels'
        },
        link: function (scope, el, attrs, controller) {
            angular.element(el).attr('draggable', 'true');

            el.bind('dragstart', function (e) {
                e.originalEvent.dataTransfer.setData('text', ''); // Firefox compatibility
                $rootScope.$emit('DRAG-START', this);
                let model = angular.element(this).scope().model;
                if (scope.checkedModels.indexOf(model) === -1) {
                    scope.checkedModels = [];
                    scope.checkedModels.push(model);
                    scope.$apply();
                }
            });

            el.bind('dragend', function (e) {
                $rootScope.$emit('DRAG-END', this);
            });

            el.bind('dragover', function (e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                return false;
            });

            el.bind('dragenter', function (e) {
                let element = angular.element(e.target);
                element.closest('.filemanager-item-folder').addClass('filemanager-item-over');
            });

            el.bind('dragleave', function (e) {
                let element = angular.element(e.target);
                if (!element.hasClass('filemanager-item-folder')) {
                    element.closest('.filemanager-item-folder').removeClass('filemanager-item-over');
                }
            });

            el.bind('drop', function (e) {
                e.preventDefault();
                var draggedModels = scope.checkedModels;
                var droppedModel = angular.element($('#'+this.id)).scope().model;
                scope.onDrop({draggedModels: draggedModels, droppedModel: droppedModel});
                scope.$apply('drop()');
                $rootScope.$emit('DRAG-END', this);
            });

            $rootScope.$on('DRAG-START', function (e, el) {
                angular.element(el).addClass('filemanager-item-target');
            });

            $rootScope.$on('DRAG-END', function (e, el) {
                angular.element(el).removeClass('filemanager-item-target');
                angular.element(el).removeClass('filemanager-item-over');
            });
        }
    };
}]);
