<template>
    <button
        class="btn-status"
        :class="model.status_translated === 1 ? 'btn-status-on' : 'btn-status-off'"
        type="button"
        @click="$parent.$emit('toggle-status', model)"
    ></button>
</template>

<script>
export default {
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
};
</script>
