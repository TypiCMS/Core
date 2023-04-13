<template>
    <a class="btn btn-light btn-xs" :href="url">{{ $t('Show') }}</a>
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
