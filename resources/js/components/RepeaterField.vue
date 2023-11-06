<template>
    <div class="mb-3">
        <label :for="name" class="form-label">{{ $t(title) }}</label>
        <input
            v-if="type === 'number' || type === 'text' || type === 'email' || type === 'date' || type === 'time' || type === 'datetime-local' || type === 'url'"
            :id="name"
            v-model="item[name]"
            :name="fieldName + '[' + index + '][' + name + ']'"
            :placeholder="placeholder"
            :type="type"
            class="form-control"
        />
        <textarea
            v-if="type === 'textarea'"
            :id="name"
            v-model="item[name]"
            :name="fieldName + '[' + index + '][' + name + ']'"
            :placeholder="placeholder"
            :rows="rows"
            class="form-control"
        ></textarea>
        <select v-if="type === 'select'" :id="name" v-model="item[name]" :name="fieldName + '[' + index + '][' + name + ']'" class="form-select">
            <option v-for="(item, key) in items" :value="key">{{ item }}</option>
        </select>
    </div>
</template>

<script>
export default {
    props: {
        field: {
            type: Object,
        },
        fieldName: {
            type: String,
        },
        index: {
            type: Number,
        },
        item: {
            type: Object,
        },
    },
    data() {
        return {
            name: this.field.name,
            title: this.field.title,
            type: this.field.type,
            rows: this.field.rows,
            items: this.field.items,
            placeholder: this.field.placeholder,
        };
    },
    created() {
        // console.log(this.config);
    },
    computed: {},
    methods: {},
};
</script>
