<template>
    <a :href="url" class="btn btn-light btn-xs">{{ $t('Edit') }}</a>
</template>

<script setup lang="ts">
defineProps({
    url: {
        type: String,
        required: true,
    },
});
</script>
