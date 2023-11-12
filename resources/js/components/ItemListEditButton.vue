<template>
    <a :href="url" class="btn btn-light btn-xs">{{ $t('Edit') }}</a>
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
