<template>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            {{ $t('Latest changes') }}
            <button
                class="btn-clear-history"
                id="clear-history"
                @click="clearHistory"
                v-if="filteredItems.length > 0 && clearButton"
            >
                {{ $t('Clear') }}
            </button>
        </div>

        <div class="history table-responsive" v-if="filteredItems.length">
            <table class="history-table table table-main mb-0">
                <thead>
                    <tr>
                        <slot :sort-array="sortArray" name="columns"></slot>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="model in filteredItems">
                        <td>
                            <small class="text-muted text-nowrap">{{ model.created_at | datetime }}</small>
                        </td>
                        <td>
                            <a v-if="model.href" :href="model.href + '?locale=' + model.locale">{{ model.title }}</a>
                            <span v-if="!model.href">{{ model.title }}</span>
                            <span v-if="model.locale">({{ model.locale }})</span>
                        </td>
                        <td>
                            {{ model.historable_type.substring(model.historable_type.lastIndexOf('\\') + 1) }}
                        </td>
                        <td class="action">
                            <small class="action-content">
                                <span class="icon" :class="'icon-' + model.action"></span>
                                {{ model.action }}
                            </small>
                        </td>
                        <td>
                            <small class="user-name">{{ model.user_name }}</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-body" v-else>
            <div v-if="loading">
                <span class="text-muted">{{ $t('Loading…') }}</span>
            </div>
            <div v-else>
                <span class="text-muted">{{
                    searchString !== '' ? $t('Nothing found.') : $t('History is empty.')
                }}</span>
            </div>
        </div>
        <div class="card-footer" v-if="filteredItems.length > 0 && data.total > data.per_page">
            <item-list-pagination
                class="justify-content-center"
                :data="data"
                @pagination-change-page="changePage"
                v-if="pagination"
            ></item-list-pagination>
        </div>
    </div>
</template>

<script>
import ItemListPagination from './ItemListPagination.vue';

export default {
    components: {
        ItemListPagination,
    },
    props: {
        clearButton: {
            type: Boolean,
            default: false,
        },
        pagination: {
            type: Boolean,
            default: true,
        },
        sorting: {
            type: Array,
            default: () => ['-created_at'],
        },
        searchable: {
            type: Array,
            default: () => [],
        },
        fields: {
            type: String,
            default: '',
        },
        include: {
            type: String,
            default: '',
        },
        appends: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            urlBase: '/api/history',
            searchString: null,
            sortArray: this.sorting,
            searchableArray: this.searchable,
            loading: false,
            total: 0,
            last_page: null,
            data: {
                current_page: 1,
                data: [],
                from: 1,
                last_page: 1,
                next_page_url: null,
                per_page: 100,
                prev_page_url: null,
                to: 1,
                total: 0,
            },
        };
    },
    mounted() {
        this.$on('sort', this.sort);
    },
    computed: {
        searchQuery() {
            if (this.searchString === null) {
                return '';
            }
            return this.searchableArray.map((item) => 'filter[' + item + ']=' + this.searchString).join('&');
        },
        url() {
            let query = ['sort=' + this.sortArray.join(','), 'fields[history]=' + this.fields];

            if (this.include !== '') {
                query.push('include=' + this.include);
            }
            if (this.appends !== '') {
                query.push('append=' + this.appends);
            }
            if (this.multilingual) {
                query.push('locale=' + this.currentLocale);
            }
            if (this.pagination) {
                query.push('page=' + this.data.current_page);
                query.push('per_page=' + this.data.per_page);
            }
            query.push(this.searchQuery);

            return this.urlBase + '?' + query.join('&');
        },
        filteredItems() {
            return this.data.data;
        },
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            this.loading = true;
            axios
                .get(this.url)
                .then((response) => {
                    this.data = response.data;
                    this.loading = false;
                })
                .catch((error) => {
                    alertify.error(
                        error.response.data.message || this.$i18n.t('An error occurred with the data fetch.')
                    );
                });
        },
        onSearchStringChanged() {
            clearTimeout(this.fetchTimeout);
            this.fetchTimeout = setTimeout(() => {
                this.fetchData();
            }, 200);
        },
        search(string) {
            this.data.current_page = 1;
            this.searchString = string;
            this.fetchData();
        },
        changePage(page = 1) {
            this.data.current_page = page;
            this.fetchData();
        },
        clearHistory() {
            if (!window.confirm(this.$i18n.t('Do you want to clear history?'))) {
                return false;
            }
            this.loading = true;
            axios
                .delete(this.url)
                .then((response) => {
                    this.data.data = [];
                    this.loading = false;
                })
                .catch((error) => {
                    alertify.error(
                        error.response.data.message || this.$i18n.t('An error occurred with the data fetch.')
                    );
                });
        },
        sort(object) {
            this.data.current_page = 1;
            this.sortArray = object;
            this.fetchData();
        },
    },
};
</script>
