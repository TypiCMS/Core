<template>
    <button class="btn-status" type="button" @click="$parent.$emit('toggle-status', model)">
        <span class="btn-status-icon" :class="statusOn ? 'btn-status-icon-on' : 'btn-status-icon-off'"></span>
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
