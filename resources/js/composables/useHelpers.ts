export default function useHelpers() {
    function formatDate(date: string) {
        if (date === null) {
            return '';
        }
        return new Date(date).toLocaleDateString(TypiCMS.locale_region);
    }

    function formatDateRange(startDate: string, endDate: string) {
        const start = new Date(startDate).toLocaleDateString(TypiCMS.locale_region);
        const end = new Date(endDate).toLocaleDateString(TypiCMS.locale_region);
        if (start === end) {
            return start;
        }
        return start + ' → ' + end;
    }

    function formatDateTime(date: string) {
        if (date === null) {
            return '';
        }
        return new Date(date).toLocaleString(TypiCMS.locale_region);
    }

    function $can(permissionName: string) {
        return TypiCMS.permissions.includes('all') || TypiCMS.permissions.includes(permissionName);
    }

    return {
        formatDate,
        formatDateRange,
        formatDateTime,
        $can,
    };
}
