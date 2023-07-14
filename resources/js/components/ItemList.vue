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
                    v-if="
                        selector &&
                        ($can('update ' + table) || $can('delete ' + table))
                    "
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
                    v-if="
                        actions &&
                        ($can('update ' + table) || $can('delete ' + table))
                    "
                    class="me-2"
                    :number-of-checked-models="numberOfCheckedItems"
                    :loading="loading"
                    :publishable="publishable"
                    :deletable="deletable"
                    :table="table"
                    @destroy="destroy"
                    @publish="publish"
                    @unpublish="unpublish"
                ></item-list-actions>
                <item-list-per-page
                    v-if="
                        data.total > perPage &&
                        pagination &&
                        $can('read ' + table)
                    "
                    class="me-2"
                    :loading="loading"
                    :per-page="parseInt(data.per_page)"
                    @change-per-page="changeNumberOfItemsPerPage"
                ></item-list-per-page>
                <slot name="buttons"></slot>
                <slot name="add-button"></slot>
                <div class="d-flex align-items-center ms-2">
                    <div
                        class="spinner-border spinner-border-sm text-dark"
                        role="status"
                        v-if="loading"
                    >
                        <span class="visually-hidden">{{
                            $t('Loading…')
                        }}</span>
                    </div>
                </div>
                <small class="text-muted align-self-center" v-if="!loading">
                    {{ $tc('# ' + title, data.total, { count: data.total }) }}
                </small>
                <div class="d-flex ms-auto">
                    <div
                        class="filters form-inline"
                        v-if="searchable.length > 0"
                    >
                        <div class="input-group input-group-sm mb-0">
                            <div class="input-group-text">
                                <i class="bi bi-search"></i>
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
                    <div
                        class="btn-group btn-group-sm ms-2"
                        v-if="translatable && locales.length > 1"
                    >
                        <button
                            class="btn btn-light dropdown-toggle"
                            type="button"
                            id="dropdownLangSwitcher"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span id="active-locale">{{
                                locales.find(
                                    (item) => item.short === contentLocale,
                                ).long
                            }}</span>
                        </button>
                        <div
                            class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownLangSwitcher"
                        >
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
                    <a
                        :href="this.exportUrl"
                        class="btn btn-sm btn-light ms-2"
                        v-if="exportable"
                    >
                        <i class="bi bi-table me-1"></i>
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
import fetcher from '../admin/fetcher';

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
        deletable: {
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
        perPage: {
            type: Number,
            default: 100,
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
                per_page: this.perPage,
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
            return this.searchableArray
                .map((item) => 'filter[' + item + ']=' + this.searchString)
                .join('&');
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

            return (
                this.urlBase.replace('api/', 'admin/') +
                '/export?' +
                query.join('&')
            );
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
            return (
                this.filteredItems.length > 0 &&
                this.filteredItems.length === this.checkedItems.length
            );
        },
        numberOfCheckedItems() {
            return this.checkedItems.length;
        },
    },
    methods: {
        async fetchData() {
            this.startLoading();
            try {
                const response = await fetcher(this.url);
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                this.data = await response.json();
            } catch (error) {
                alertify.error(
                    error.message ||
                        this.$i18n.t('An error occurred with the data fetch.'),
                );
            }
            this.stopLoading();
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
        async switchLocale(locale) {
            this.startLoading();
            this.contentLocale = locale;
            try {
                const response = await fetcher('/admin/_locale/' + locale);
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                this.stopLoading();
                await this.fetchData();
            } catch (error) {
                this.stopLoading();
                alertify.error(error.message);
            }
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
            this.checkedItems = this.filteredItems.filter(
                (model) => model[statusVar] === 1,
            );
        },
        checkUnpublished() {
            let statusVar = 'status';
            if (this.translatable) {
                statusVar = 'status_translated';
            }
            this.checkedItems = this.filteredItems.filter(
                (model) => model[statusVar] === 0,
            );
        },
        async destroy() {
            this.data.current_page = 1;
            const deleteLimit = 100;

            if (this.checkedItems.length > deleteLimit) {
                alertify.error(
                    this.$i18n.t(
                        'Impossible to delete more than # items in one go.',
                        {
                            deleteLimit,
                        },
                    ),
                );
                return false;
            }
            if (
                !window.confirm(
                    this.$i18n.tc(
                        'Are you sure you want to delete # items?',
                        this.numberOfCheckedItems,
                        {
                            count: this.numberOfCheckedItems,
                        },
                    ),
                )
            ) {
                return false;
            }

            this.startLoading();
            const deletePromises = this.checkedItems.map(async (model) => {
                try {
                    const response = await fetcher(
                        this.urlBase + '/' + model.id,
                        { method: 'DELETE' },
                    );
                    if (!response.ok) {
                        const responseData = await response.json();
                        throw new Error(responseData.message);
                    }
                } catch (error) {
                    alertify.error(
                        this.$i18n.tc(error.message) ||
                            this.$i18n.t('Sorry, an error occurred.'),
                    );
                }
            });

            const responses = await Promise.all(deletePromises);
            let successes = responses.filter(
                (response) => response && response.status === 200,
            );
            if (successes.length > 0) {
                alertify.success(
                    this.$i18n.tc('# items deleted', successes.length, {
                        count: successes.length,
                    }),
                );
            }
            this.checkNone();
            this.stopLoading();
            await this.fetchData();
        },
        publish() {
            if (
                !window.confirm(
                    this.$i18n.tc(
                        'Are you sure you want to publish # items?',
                        this.checkedItems.length,
                        {
                            count: this.checkedItems.length,
                        },
                    ),
                )
            ) {
                return false;
            }
            this.setStatus(1);
        },
        unpublish() {
            if (
                !window.confirm(
                    this.$i18n.tc(
                        'Are you sure you want to unpublish # items?',
                        this.checkedItems.length,
                        {
                            count: this.checkedItems.length,
                        },
                    ),
                )
            ) {
                return false;
            }
            this.setStatus(0);
        },
        async setStatus(status) {
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

            const updatePromises = this.checkedItems.map(async (model) => {
                try {
                    const response = await fetcher(
                        this.urlBase + '/' + model.id,
                        {
                            method: 'PATCH',
                            body: JSON.stringify(data),
                        },
                    );
                    if (!response.ok) {
                        const responseData = await response.json();
                        throw new Error(responseData.message);
                    }
                } catch (error) {
                    alertify.error(
                        error.message ||
                            this.$i18n.t('Sorry, an error occurred.'),
                    );
                }
            });

            const responses = await Promise.all(updatePromises);
            let successes = responses.filter(
                (response) => response && response.status === 200,
            );
            if (successes.length > 0) {
                alertify.success(
                    this.$i18n.tc('# items ' + label, successes.length, {
                        count: successes.length,
                    }),
                );
            }
            for (let i = this.checkedItems.length - 1; i >= 0; i--) {
                let index = this.data.data.indexOf(this.checkedItems[i]);
                this.data.data[index][statusVar] = status;
            }
            this.checkNone();
            this.stopLoading();
        },
        async toggleStatus(model) {
            let translatable =
                    typeof model.status_translated !== 'undefined'
                        ? this.translatable
                        : false,
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
            try {
                const response = await fetcher(this.urlBase + '/' + model.id, {
                    method: 'PATCH',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                alertify.success(this.$i18n.t('Item is ' + label + '.'));
            } catch (error) {
                alertify.error(
                    error.message || this.$i18n.t('Sorry, an error occurred.'),
                );
            }
        },
        async updatePosition(model) {
            let data = {
                position: model.position,
            };
            try {
                const response = await fetcher(this.urlBase + '/' + model.id, {
                    method: 'PATCH',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
            } catch (error) {
                alertify.error(
                    error.message || this.$i18n.t('Sorry, an error occurred.'),
                );
            }
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
