<template>
    <ul class="pagination item-list-pagination" v-if="data.total > data.per_page">
        <li class="page-item pagination-prev-nav" :class="{ disabled: !data.prev_page_url}">
            <button class="page-link" aria-label="Previous" @click.prevent="selectPage(--data.current_page)">
                <slot name="prev-nav">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">{{ $t('Previous') }}</span>
                </slot>
            </button>
        </li>
        <li class="page-item pagination-page-nav" v-for="page in getPages()" :class="{ disabled: page === '…', active: page == data.current_page }">
            <button class="page-link" @click.prevent="selectPage(page)">{{ page }}</button>
        </li>
        <li class="page-item pagination-next-nav" :class="{ disabled: !data.next_page_url}">
            <button class="page-link" aria-label="Next" @click.prevent="selectPage(++data.current_page)">
                <slot name="next-nav">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">{{ $t('Next') }}</span>
                </slot>
            </button>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default: function() {
                return {
                    current_page: 1,
                    data: [],
                    from: 1,
                    last_page: 1,
                    next_page_url: null,
                    per_page: 10,
                    prev_page_url: null,
                    to: 1,
                    total: 0,
                };
            },
        },
        limit: {
            type: Number,
            default: 4,
        },
    },
    methods: {
        selectPage: function(page) {
            if (page === '…') {
                return;
            }

            this.$emit('pagination-change-page', page);
        },
        getPages: function() {
            if (this.limit === -1) {
                return 0;
            }

            if (this.limit === 0) {
                return this.data.last_page;
            }

            let delta = this.limit,
                left = this.data.current_page - delta,
                right = this.data.current_page + delta + 1,
                range = [],
                pages = [],
                l;

            for (let i = 1; i <= this.data.last_page; i++) {
                if (i == 1 || i == this.data.last_page || (i >= left && i < right)) {
                    range.push(i);
                }
            }

            range.forEach(function(i) {
                if (l) {
                    if (i - l === 2) {
                        pages.push(l + 1);
                    } else if (i - l !== 1) {
                        pages.push('…');
                    }
                }
                pages.push(i);
                l = i;
            });

            return pages;
        },
    },
};
</script>
