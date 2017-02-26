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
                element.closest('.filemanager-item-folder').addClass('lvl-over');
            });

            el.bind('dragleave', function (e) {
                let element = angular.element(e.target);
                if (!element.hasClass('filemanager-item-folder')) {
                    element.closest('.filemanager-item-folder').removeClass('lvl-over');
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
                angular.element(el).addClass('lvl-target');
            });

            $rootScope.$on('DRAG-END', function (e, el) {
                angular.element(el).removeClass('lvl-target');
                angular.element(el).removeClass('lvl-over');
            });
        }
    };
}]);
