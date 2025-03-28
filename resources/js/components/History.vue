<template>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            {{ $t('Latest changes') }}
            <button v-if="filteredItems.length > 0 && clearButton" id="clear-history" class="btn-clear-history" @click="clearHistory">
                {{ $t('Clear') }}
            </button>
        </div>

        <div v-if="filteredItems.length" class="history table-responsive">
            <table class="history-table table table-main mb-0">
                <thead>
                    <tr>
                        <slot :sort-array="sortArray" name="columns"></slot>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="model in filteredItems">
                        <td>
                            <small class="text-muted text-nowrap">{{ formatDateTime(model.created_at) }}</small>
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
                                <span :class="'icon-' + model.action" class="icon"></span>
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

        <div v-else class="card-body">
            <div v-if="loading">
                <span class="text-muted">{{ $t('Loading…') }}</span>
            </div>
            <div v-else>
                <span class="text-muted">{{ searchString !== '' ? $t('Nothing found.') : $t('History is empty.') }}</span>
            </div>
        </div>
        <div v-if="filteredItems.length > 0 && data.total > data.per_page" class="card-footer">
            <item-list-pagination v-if="pagination" :data="data" class="justify-content-center" @pagination-change-page="changePage"></item-list-pagination>
        </div>
    </div>
</template>

<script>
import ItemListPagination from './ItemListPagination.vue';
import fetcher from '../admin/fetcher';

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
        this.emitter.on('sort', (object) => {
            this.sort(object);
        });
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
            if (this.translatable) {
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
        async fetchData() {
            this.loading = true;
            try {
                const response = await fetcher(this.url);
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                this.data = await response.json();
                this.loading = false;
            } catch (error) {
                alertify.error(error.message || this.$t('An error occurred with the data fetch.'));
            }
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
        async clearHistory() {
            if (!window.confirm(this.$t('Do you want to clear history?'))) {
                return false;
            }
            this.loading = true;
            try {
                const response = await fetcher(this.url, { method: 'DELETE' });
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                this.data.data = [];
                this.loading = false;
            } catch (error) {
                alertify.error(this.$t(error.message) || this.$t('Sorry, an error occurred.'));
            }
        },
        sort(object) {
            this.data.current_page = 1;
            this.sortArray = object;
            this.fetchData();
        },
    },
};
</script>
