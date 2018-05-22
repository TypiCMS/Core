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
        sortObject: {
            type: Object,
            default: () => { return { column: null, direction: null }},
        },
    },
    computed: {
        classes() {
            let classes = [];
            classes.push(this.name);
            if (this.sortable) {
                classes.push('th-sort');
            }
            if (this.sortObject.column === this.name) {
                if (this.sortObject.direction === 1) {
                    classes.push('th-sort-asc');
                } else {
                    classes.push('th-sort-desc');
                }
            }
            return classes.join(' ');
        }
    },
    methods: {
        sort() {
            if (this.sortable) {
                let direction = 1;
                if (this.sortObject.column === this.name) {
                    direction = -this.sortObject.direction;
                }
                this.$parent.$emit('sort', { column: this.name, direction: direction });
            }
        },
    },
};
</script>
