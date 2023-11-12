<template>
    <input :id="model.id" v-model="checkedModels" :disabled="loading" :value="model" class="form-check-input" type="checkbox" @click="check" />
</template>

<script>
export default {
    props: {
        model: {
            type: Object,
            required: true,
        },
        checkedModelsProp: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
    },
    computed: {
        checkedModels() {
            return this.checkedModelsProp;
        },
    },
    methods: {
        check() {
            let index = this.checkedModels.indexOf(this.model);
            if (index === -1) {
                this.checkedModels.push(this.model);
            } else {
                this.checkedModels.splice(index, 1);
            }
        },
    },
};
</script>
