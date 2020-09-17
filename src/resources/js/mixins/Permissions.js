export default {
    methods: {
        $can(permissionName) {
            if (TypiCMS.permissions.indexOf('all') !== -1) {
                return true;
            }
            return TypiCMS.permissions.indexOf(permissionName) !== -1;
        },
    },
};
