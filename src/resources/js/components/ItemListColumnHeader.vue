<template>
    <th :class="classes" @click="sort">
        <span>{{ label }}</span>
    </th>
</template>

<script>
export default {
    props: {
        name: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: '',
        },
        sortable: {
            type: Boolean,
            default: false,
        },
        sortArray: {
            type: Array,
            default: () => {
                return [];
            },
        },
    },
    computed: {
        column() {
            return this.name;
        },
        classes() {
            let classes = [];
            classes.push(this.name);
            if (this.sortable) {
                classes.push('th-sort');
            }
            if (this.sortArray.indexOf(this.column) !== -1) {
                classes.push('th-sort-asc');
            }
            if (this.sortArray.indexOf('-' + this.column) !== -1) {
                classes.push('th-sort-desc');
            }
            return classes.join(' ');
        },
    },
    methods: {
        sort() {
            if (this.sortable) {
                let sort = [this.column];
                if (this.sortArray.indexOf(this.column) !== -1) {
                    sort = ['-' + this.column];
                }
                if (this.sortArray.indexOf('-' + this.column) !== -1) {
                    sort = [this.column];
                }
                this.$parent.$emit('sort', sort);
            }
        },
    },
};
</script>
