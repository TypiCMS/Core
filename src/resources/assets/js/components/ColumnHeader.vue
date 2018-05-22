<template>
    <th :class="thClasses" @click="sort">
        <slot>{{ name }}</slot>
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
        sortDefault: {
            type: String,
        },
    },
    computed: {
        thClasses() {
            let classes = [];
            if (this.sortable) {
                classes.push('th-sort');
            }
            if (this.sortDefault === 'asc') {
                classes.push('th-sort-asc');
            }
            if (this.sortDefault === 'desc') {
                classes.push('th-sort-desc');
            }
            return classes.join(' ');
        }
    },
    methods: {
        sort() {
            if (this.sortable) {
                this.$parent.$emit('sort', this.name);
            }
        },
    },
};
</script>
