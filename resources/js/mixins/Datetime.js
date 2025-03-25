export default {
    methods: {
        formatDateTime(datetime) {
            if (datetime === null) {
                return '';
            }
            return new Date(datetime).toLocaleString(window.TypiCMS.locale_region);
        },
    },
};
