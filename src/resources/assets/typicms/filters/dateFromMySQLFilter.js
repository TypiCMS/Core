angular.module('typicms').filter('dateFromMySQL', [
    'dateFilter',
    function(dateFilter) {
        return function(date, format) {
            if (!date) {
                return;
            }
            date = new Date(date.replace(/-/g, '/'));
            return dateFilter(date, format);
        };
    },
]);
