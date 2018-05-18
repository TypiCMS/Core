<template>
    <button class="btn btn-xs btn-link btn-status" @click="toggleStatus">
        <span class="fa btn-status-switch" :class="model.status[locale] === '1' ? 'fa-toggle-on' : 'fa-toggle-off'"></span>
    </button>
</template>

<script>
export default {
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            class: '',
            locale: TypiCMS.content_locale,
        };
    },
    created() {},
    methods: {
        toggleStatus() {
            alert('Toggle status of ' + this.model.id + '?');
            // this.$emit('delete', item);
        },
    },
};
</script>
