<template>
    <button
        class="btn-status"
        :class="statusOn ? 'btn-status-on' : 'btn-status-off'"
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
    computed: {
        statusOn() {
            if (typeof this.model.status_translated === 'undefined') {
                return parseInt(this.model.status) === 1;
            } else {
                return this.model.status_translated === 1;
            }
        },
    },
};
</script>
