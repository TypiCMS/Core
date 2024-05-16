export default {
    methods: {
        $can(permissionName) {
            return TypiCMS.permissions.includes('all') || TypiCMS.permissions.includes(permissionName);
        },
    },
};
