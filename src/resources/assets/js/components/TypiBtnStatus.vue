<template>
    <button class="btn btn-xs btn-link btn-status" @click="$parent.$emit('toggle-status', model)">
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
            locale: TypiCMS.content_locale,
        };
    },
};
</script>
