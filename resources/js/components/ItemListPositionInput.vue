<template>
    <input :id="'position-input-' + model.id" v-model="model.position" class="form-control form-control-sm" min="0" type="number" @change="$parent.$emit('update-position', model)" />
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
