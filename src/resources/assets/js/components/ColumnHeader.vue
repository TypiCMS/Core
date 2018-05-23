<template>
    <th :class="classes" @click="sort">
        <slot></slot>
    </th>
</template>

<script>
export default {
    props: {
        name: {
            type: String,
            required: true,
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
        classes() {
            let classes = [];
            classes.push(this.name);
            if (this.sortable) {
                classes.push('th-sort');
            }
            if (this.sortArray.indexOf(this.name) !== -1) {
                classes.push('th-sort-asc');
            }
            if (this.sortArray.indexOf('-' + this.name) !== -1) {
                classes.push('th-sort-desc');
            }
            return classes.join(' ');
        },
    },
    methods: {
        sort() {
            if (this.sortable) {
                let sort = [this.name];
                if (this.sortArray.indexOf(this.name) !== -1) {
                    sort = ['-' + this.name];
                }
                if (this.sortArray.indexOf('-' + this.name) !== -1) {
                    sort = [this.name];
                }
                this.$parent.$emit('sort', sort);
            }
        },
    },
};
</script>
