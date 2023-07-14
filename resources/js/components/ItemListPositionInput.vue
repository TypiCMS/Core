<template>
    <input
        class="form-control form-control-sm"
        min="0"
        type="number"
        v-model="model.position"
        :id="'position-input-' + model.id"
        @change="$parent.$emit('update-position', model)"
    />
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
