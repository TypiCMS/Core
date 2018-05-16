import TypiBtnStatus from './TypiBtnStatus.vue';

export default {
    el: '#itemList',
    components: {
        TypiBtnStatus,
    },
    data() {
        return {
            models: TypiCMS.models,
        };
    },
};
