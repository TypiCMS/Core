var module = angular.module('typicms');

module.directive('dragdrop', ['$rootScope', function ($rootScope) {
    return {
        restrict: 'A',
        scope: {
            onDrop: '&'
        },
        link: function (scope, el, attrs, controller) {
            angular.element(el).attr('draggable', 'true');

            el.bind('dragstart', function (e) {
                e.originalEvent.dataTransfer.setData('text', this.id);
                $rootScope.$emit('LVL-DRAG-START', this);
            });

            el.bind('dragend', function (e) {
                $rootScope.$emit('LVL-DRAG-END', this);
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
                var data = e.originalEvent.dataTransfer.getData('text');
                var draggedModel = angular.element($('#'+data)).scope().model;
                var droppedModel = angular.element($('#'+this.id)).scope().model;
                scope.onDrop({draggedModel: draggedModel, droppedModel: droppedModel});
                scope.$apply('drop()');
            });

            $rootScope.$on('LVL-DRAG-START', function (e, el) {
                angular.element(el).addClass('lvl-target');
            });

            $rootScope.$on('LVL-DRAG-END', function (e, el) {
                angular.element(el).removeClass('lvl-target');
                angular.element(el).removeClass('lvl-over');
            });
        }
    };
}]);
