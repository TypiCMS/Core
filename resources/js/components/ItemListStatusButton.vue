<template>
    <button class="btn-status" type="button" @click="emitter.emit('toggleStatus', model)">
        <span :class="statusOn ? 'btn-status-icon-on' : 'btn-status-icon-off'" class="btn-status-icon"></span>
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
