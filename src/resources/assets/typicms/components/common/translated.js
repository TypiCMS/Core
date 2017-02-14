angular.module('typicms').filter('translated', function() {
    return function(input) {
        return input[TypiCMS.content_locale];
    };
});
