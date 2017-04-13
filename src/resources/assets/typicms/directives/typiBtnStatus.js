angular.module('typicms').directive('typiBtnStatus', function() {
    return {
        scope: {
            model: '=',
            locale: '=locale',
            action: '&'
        },
        template: '<div class="btn btn-xs btn-link btn-status" ng-click="action()">' +
                '<span class="fa btn-status-switch" ng-class="model.status.'+TypiCMS.content_locale+' == \'1\' ? \'fa-toggle-on\' : \'fa-toggle-off\'"></span>' +
                '<span class="sr-only" ng-show="model.status.'+TypiCMS.content_locale+' == \'1\'" translate>Published</span>' +
                '<span class="sr-only" ng-hide="model.status.'+TypiCMS.content_locale+' == \'0\'" translate>Unpublished</span>' +
            '</div>'
    };
});
