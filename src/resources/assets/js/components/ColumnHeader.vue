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
        translate: {
            type: Boolean,
            default: false,
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
            if (this.translate) {
                return this.name + '->' + TypiCMS.content_locale;
            }
            return this.name;
        },
        classes() {
            let classes = [];
            classes.push(this.column);
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
