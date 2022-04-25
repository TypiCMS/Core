<template>
    <a class="btn btn-secondary btn-xs" :href="url">{{ $t('Edit') }}</a>
</template>

<script>
export default {
    props: {
        url: {
            type: String,
            required: true,
        },
    },
};
</script>
