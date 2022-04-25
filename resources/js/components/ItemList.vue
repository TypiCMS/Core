<template>
    <div class="item-list" :class="{ 'sub-list': subList }">
        <div class="item-list-header header">
            <slot name="back-button"></slot>
            <h1 class="item-list-title" v-if="!subList">
                {{ $t(title.charAt(0).toUpperCase() + title.slice(1)) }}
            </h1>
            <h2 class="item-list-subtitle" v-else>
                {{ $t(title.charAt(0).toUpperCase() + title.slice(1)) }}
            </h2>
            <div class="btn-toolbar header-toolbar">
                <item-list-selector
                    v-if="selector && ($can('update ' + table) || $can('delete ' + table))"
                    class="me-2"
                    :filtered-models="filteredItems"
                    :all-checked="allChecked"
                    :loading="loading"
                    :publishable="publishable"
                    @check-all="checkAll"
                    @check-none="checkNone"
                    @check-published="checkPublished"
                    @check-unpublished="checkUnpublished"
                ></item-list-selector>
                <item-list-actions
                    v-if="actions && ($can('update ' + table) || $can('delete ' + table))"
                    class="me-2"
                    :number-of-checked-models="numberOfCheckedItems"
                    :loading="loading"
                    :publishable="publishable"
                    :table="table"
                    @destroy="destroy"
                    @publish="publish"
                    @unpublish="unpublish"
                ></item-list-actions>
                <item-list-per-page
                    v-if="pagination && this.data.total > 10 && $can('read ' + table)"
                    class="me-2"
                    :loading="loading"
                    :per-page="parseInt(data.per_page)"
                    @change-per-page="changeNumberOfItemsPerPage"
                ></item-list-per-page>
                <slot name="buttons"></slot>
                <slot name="add-button"></slot>
                <div class="d-flex align-items-center ms-2">
                    <div class="spinner-border spinner-border-sm text-dark" role="status" v-if="loading">
                        <span class="visually-hidden">{{ $t('Loading…') }}</span>
                    </div>
                </div>
                <small class="text-muted align-self-center" v-if="!loading">
                    {{ $tc('# ' + title, data.total, { count: data.total }) }}
                </small>
                <div class="d-flex ms-auto">
                    <div class="filters form-inline" v-if="searchable.length > 0">
                        <div class="input-group input-group-sm mb-0">
                            <div class="input-group-text">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 1792 1792"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"
                                    />
                                </svg>
                            </div>
                            <input
                                class="form-control"
                                type="text"
                                id="search"
                                v-model="searchString"
                                @input="onSearchStringChanged"
                            />
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm ms-2" v-if="translatable && locales.length > 1">
                        <button
                            class="btn btn-secondary dropdown-toggle"
                            type="button"
                            id="dropdownLangSwitcher"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span id="active-locale">{{
                                locales.find((item) => item.short === contentLocale).long
                            }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLangSwitcher">
                            <button
                                class="dropdown-item"
                                :class="{ active: locale === contentLocale }"
                                type="button"
                                v-for="locale in locales"
                                @click="switchLocale(locale.short)"
                            >
                                {{ locale.long }}
                            </button>
                        </div>
                    </div>
                    <a :href="this.exportUrl" class="btn btn-sm btn-secondary ms-2" v-if="exportable">
                        <svg
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                            class="bi bi-table"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"
                            />
                        </svg>
                        Export
                    </a>
                </div>
            </div>
        </div>

        <div class="item-list-content content">
            <div class="table-responsive" v-if="filteredItems.length">
                <table class="table item-list-table">
                    <thead>
                        <tr>
                            <slot :sort-array="sortArray" name="columns"></slot>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="model in filteredItems">
                            <slot
                                :model="model"
                                :checked-models="checkedItems"
                                :loading="loading"
                                name="table-row"
                            ></slot>
                        </tr>
                    </tbody>
                </table>
            </div>

            <item-list-pagination
                :data="data"
                @pagination-change-page="changePage"
                v-if="pagination"
            ></item-list-pagination>
        </div>
    </div>
</template>

<script>
import ItemListSelector from './ItemListSelector.vue';
import ItemListActions from './ItemListActions.vue';
import ItemListPerPage from './ItemListPerPage.vue';
import ItemListStatusButton from './ItemListStatusButton.vue';
import ItemListPagination from './ItemListPagination.vue';

export default {
    components: {
        ItemListSelector,
        ItemListActions,
        ItemListPerPage,
        ItemListStatusButton,
        ItemListPagination,
    },
    props: {
        urlBase: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        sorting: {
            type: Array,
            default: () => ['-id'],
        },
        selector: {
            type: Boolean,
            default: true,
        },
        actions: {
            type: Boolean,
            default: true,
        },
        pagination: {
            type: Boolean,
            default: true,
        },
        searchable: {
            type: Array,
            default: () => [],
        },
        publishable: {
            type: Boolean,
            default: true,
        },
        exportable: {
            type: Boolean,
            default: false,
        },
        translatable: {
            type: Boolean,
            default: true,
        },
        table: {
            type: String,
            required: true,
        },
        include: {
            type: String,
            default: '',
        },
        fields: {
            type: String,
            default: '',
        },
        subList: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loadingTimeout: null,
            searchString: null,
            sortArray: this.sorting,
            searchableArray: this.searchable,
            locales: window.TypiCMS.locales,
            contentLocale: window.TypiCMS.content_locale,
            loading: false,
            total: 0,
            last_page: null,
            checkedItems: [],
            data: {
                current_page: 1,
                data: [],
                from: 1,
                last_page: 1,
                next_page_url: null,
                per_page: 50,
                prev_page_url: null,
                to: 1,
                total: 0,
            },
        };
    },
    created() {
        this.fetchData();
    },
    mounted() {
        this.$on('sort', this.sort);
        this.$on('toggle-status', this.toggleStatus);
        this.$on('update-position', this.updatePosition);
    },
    computed: {
        searchQuery() {
            if (this.searchString === null) {
                return '';
            }
            return this.searchableArray.map((item) => 'filter[' + item + ']=' + this.searchString).join('&');
        },
        exportUrl() {
            let query = ['sort=' + this.sortArray.join(',')];

            let fields = {};
            let fieldsArray = this.fields.split(',');
            fieldsArray.forEach((element) => {
                let key = this.table;
                let value = element;
                if (element.indexOf('.') !== -1) {
                    [key, value] = element.split('.');
                }
                if (!Array.isArray(fields[key])) {
                    fields[key] = [];
                }
                fields[key].push(value);
            });

            for (const table in fields) {
                query.push('fields[' + table + ']=' + fields[table].join(','));
            }

            if (this.translatable) {
                query.push('locale=' + this.contentLocale);
            }
            if (this.searchQuery !== '') {
                query.push(this.searchQuery);
            }

            return this.urlBase.replace('api/', 'admin/') + '/export?' + query.join('&');
        },
        url() {
            let query = ['sort=' + this.sortArray.join(',')];

            let fields = {};
            let fieldsArray = this.fields.split(',');
            fieldsArray.forEach((element) => {
                let key = this.table;
                let value = element;
                if (element.indexOf('.') !== -1) {
                    [key, value] = element.split('.');
                }
                if (!Array.isArray(fields[key])) {
                    fields[key] = [];
                }
                fields[key].push(value);
            });

            for (const table in fields) {
                query.push('fields[' + table + ']=' + fields[table].join(','));
            }

            if (this.include !== '') {
                query.push('include=' + this.include);
            }
            if (this.translatable) {
                query.push('locale=' + this.contentLocale);
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
        allChecked() {
            return this.filteredItems.length > 0 && this.filteredItems.length === this.checkedItems.length;
        },
        numberOfCheckedItems() {
            return this.checkedItems.length;
        },
    },
    methods: {
        fetchData() {
            this.startLoading();
            axios
                .get(this.url)
                .then((response) => {
                    this.data = response.data;
                    this.stopLoading();
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
        startLoading() {
            this.loadingTimeout = setTimeout(() => {
                this.loading = true;
            }, 300);
        },
        stopLoading() {
            clearTimeout(this.loadingTimeout);
            this.loading = false;
        },
        switchLocale(locale) {
            this.startLoading();
            this.contentLocale = locale;
            axios.get('/admin/_locale/' + locale).then((response) => {
                this.stopLoading();
                this.fetchData();
            });
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
        changeNumberOfItemsPerPage(per_page) {
            this.data.current_page = 1;
            this.data.per_page = per_page;
            this.fetchData();
        },
        checkAll() {
            this.checkedItems = this.filteredItems.filter(() => true);
        },
        checkNone() {
            this.checkedItems = [];
        },
        checkPublished() {
            let statusVar = 'status';
            if (this.translatable) {
                statusVar = 'status_translated';
            }
            this.checkedItems = this.filteredItems.filter((model) => model[statusVar] === 1);
        },
        checkUnpublished() {
            let statusVar = 'status';
            if (this.translatable) {
                statusVar = 'status_translated';
            }
            this.checkedItems = this.filteredItems.filter((model) => model[statusVar] === 0);
        },
        destroy() {
            this.data.current_page = 1;
            const deleteLimit = 100;

            if (this.checkedItems.length > deleteLimit) {
                alertify.error(
                    this.$i18n.t('Impossible to delete more than # items in one go.', {
                        deleteLimit,
                    })
                );
                return false;
            }
            if (
                !window.confirm(
                    this.$i18n.tc('Are you sure you want to delete # items?', this.numberOfCheckedItems, {
                        count: this.numberOfCheckedItems,
                    })
                )
            ) {
                return false;
            }

            this.startLoading();

            axios
                .all(
                    this.checkedItems.map((model) =>
                        axios
                            .delete(this.urlBase + '/' + model.id)
                            .catch((error) =>
                                alertify.error(
                                    this.$i18n.tc(error.response.data.message) ||
                                        this.$i18n.t('Sorry, an error occurred.')
                                )
                            )
                    )
                )
                .then((responses) => {
                    let successes = responses.filter((response) => response.statusText === 'OK');
                    if (successes.length > 0) {
                        alertify.success(
                            this.$i18n.tc('# items deleted', successes.length, {
                                count: successes.length,
                            })
                        );
                    }
                    this.checkNone();
                    this.stopLoading();
                    this.fetchData();
                });
        },
        publish() {
            if (
                !window.confirm(
                    this.$i18n.tc('Are you sure you want to publish # items?', this.checkedItems.length, {
                        count: this.checkedItems.length,
                    })
                )
            ) {
                return false;
            }
            this.setStatus(1);
        },
        unpublish() {
            if (
                !window.confirm(
                    this.$i18n.tc('Are you sure you want to unpublish # items?', this.checkedItems.length, {
                        count: this.checkedItems.length,
                    })
                )
            ) {
                return false;
            }
            this.setStatus(0);
        },
        setStatus(status) {
            let data = {
                    status: {},
                },
                label = status === 1 ? 'published' : 'unpublished',
                statusVar = 'status';

            if (this.translatable) {
                statusVar = 'status_translated';
                data.status[this.contentLocale] = status;
            } else {
                data.status = status;
            }

            this.startLoading();

            axios
                .all(
                    this.checkedItems.map((model) =>
                        axios
                            .patch(this.urlBase + '/' + model.id, data)
                            .catch((error) =>
                                alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'))
                            )
                    )
                )
                .then((responses) => {
                    let successes = responses.filter((response) => response.statusText === 'OK');
                    if (successes.length > 0) {
                        alertify.success(
                            this.$i18n.tc('# items ' + label, successes.length, {
                                count: successes.length,
                            })
                        );
                    }
                    for (let i = this.checkedItems.length - 1; i >= 0; i--) {
                        let index = this.data.data.indexOf(this.checkedItems[i]);
                        this.data.data[index][statusVar] = status;
                    }
                    this.checkNone();
                    this.stopLoading();
                });
        },
        toggleStatus(model) {
            let translatable = typeof model.status_translated !== 'undefined' ? this.translatable : false,
                status = translatable ? model.status_translated : model.status,
                newStatus = Math.abs(status - 1),
                data = {
                    status: {},
                },
                label = newStatus === 1 ? 'published' : 'unpublished';

            if (translatable) {
                model.status_translated = newStatus;
                data.status[this.contentLocale] = newStatus;
            } else {
                model.status = newStatus;
                data.status = newStatus;
            }
            axios
                .patch(this.urlBase + '/' + model.id, data)
                .then((response) => {
                    alertify.success(this.$i18n.t('Item is ' + label + '.'));
                })
                .catch((error) => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
        updatePosition(model) {
            let data = {
                position: model.position,
            };
            axios.patch(this.urlBase + '/' + model.id, data).catch((error) => {
                alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
            });
        },
        sort(object) {
            this.data.current_page = 1;
            this.checkNone();
            this.sortArray = object;
            this.fetchData();
        },
    },
};
</script>
