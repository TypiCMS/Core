<template>
    <a class="btn btn-light btn-xs" :href="url">{{ $t('Edit') }}</a>
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
