<template>

    <div>

        <div class="header">
            <h3 class="mr-2">
                {{ data.total }} {{ title }}
            </h3>
        </div>

        <div class="btn-toolbar">
            <slot name="add-button"></slot>
            <list-actions
                class="mr-2"
                :number-of-checked-models="numberOfcheckedItems"
                :loading="loading"
                @destroy="destroy"
                @publish="publish"
                @unpublish="unpublish"
            ></list-actions>
            <list-items-per-page
                class="mr-2"
                :loading="loading"
                :per-page="parseInt(data.per_page)"
                @change-per-page="changeNumberOfItemsPerPage"
            ></list-items-per-page>
            <typi-search-bar class="mr-2" @search="search"></typi-search-bar>
            <div class="d-flex align-items-center ml-2">
                <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
            </div>
            <slot name="buttons"></slot>
        </div>

        <div class="table-responsive" v-if="filteredItems.length">
            <table class="table table-main">
                <thead>
                    <tr>
                        <th class="checkbox">
                            <list-selector
                                :filtered-models="filteredItems"
                                :all-checked="allChecked"
                                :loading="loading"
                                @check-all="checkAll"
                                @check-none="checkNone"
                                @check-published="checkPublished"
                                @check-unpublished="checkUnpublished"
                            ></list-selector>
                        </th>
                        <slot :sort-array="sortArray" name="columns"></slot>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="model in filteredItems">
                        <slot :model="model" :checked-models="checkedItems" :loading="loading" name="table-row"></slot>
                    </tr>
                </tbody>
            </table>
        </div>

        <pagination :data="data" @pagination-change-page="changePage"></pagination>

    </div>

</template>

<script>
import ListSelector from './ListSelector';
import ListActions from './ListActions';
import ListItemsPerPage from './ListItemsPerPage';
import TypiBtnStatus from './TypiBtnStatus';
import TypiSearchBar from './TypiSearchBar';
import Pagination from './Pagination';

export default {
    components: {
        ListSelector,
        ListActions,
        ListItemsPerPage,
        TypiBtnStatus,
        TypiSearchBar,
        Pagination,
    },
    props: {
        urlBase: {
            type: String,
            required: true,
        },
        urlParameters: {
            type: String,
        },
        title: {
            type: String,
            required: true,
        },
        locale: {
            type: String,
            required: true,
        },
        sorting: {
            type: Array,
            default: ['-id'],
        },
        searchable: {
            type: Array,
            default: false,
        },
    },
    data() {
        return {
            searchString: null,
            sortArray: this.sorting,
            searchableArray: this.searchable,
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
    },
    computed: {
        searchQuery() {
            if (this.searchString === null) {
                return '';
            }
            return '&' + this.searchableArray.map(item => 'filter[' + item + ']=' + this.searchString).join('&');
        },
        url() {
            return (
                this.urlBase +
                '?' +
                'sort=' +
                this.sortArray.join(',') +
                '&' +
                this.urlParameters +
                '&locale=' +
                this.locale +
                this.searchQuery +
                '&page=' +
                this.data.current_page +
                '&per_page=' +
                this.data.per_page
            );
        },
        filteredItems() {
            return this.data.data;
        },
        allChecked() {
            return this.filteredItems.length > 0 && this.filteredItems.length === this.checkedItems.length;
        },
        numberOfcheckedItems() {
            return this.checkedItems.length;
        },
    },
    methods: {
        fetchData() {
            this.loading = true;
            axios
                .get(this.url)
                .then(response => {
                    this.data = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('An error occurred with the data fetch.'));
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
            this.checkedItems = this.filteredItems;
        },
        checkNone() {
            this.checkedItems = [];
        },
        checkPublished() {
            this.checkedItems = this.filteredItems.filter(model => model.status_translated === '1');
        },
        checkUnpublished() {
            this.checkedItems = this.filteredItems.filter(model => model.status_translated === '0');
        },
        destroy() {
            this.data.current_page = 1;
            const deleteLimit = 100;

            if (this.checkedItems.length > deleteLimit) {
                alertify.error(this.$i18n.t('Impossible to delete more than # items in one go.', { deleteLimit }));
                return false;
            }
            if (!window.confirm(this.$i18n.tc('Are you sure you want to delete # items?', this.numberOfcheckedItems, { count: this.numberOfcheckedItems }))) {
                return false;
            }

            this.loading = true;

            axios
                .all(this.checkedItems.map(model => axios.delete(this.urlBase + '/' + model.id)))
                .then(responses => {
                    let successes = responses.filter(response => response.data.error === false);
                    this.loading = false;
                    alertify.success(this.$i18n.tc('# items deleted', successes.length, { count: successes.length }));
                    this.fetchData();
                    this.checkedItems = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
        publish() {
            if (!window.confirm(this.$i18n.tc('Are you sure you want to publish # items?', this.checkedItems.length, { count: this.checkedItems.length }))) {
                return false;
            }
            this.setStatus('1');
        },
        unpublish() {
            if (!window.confirm(this.$i18n.tc('Are you sure you want to unpublish # items?', this.checkedItems.length, { count: this.checkedItems.length }))) {
                return false;
            }
            this.setStatus('0');
        },
        setStatus(status) {
            let data = {
                    status: {},
                },
                label = status === '1' ? 'published' : 'unpublished';
            data.status[TypiCMS.content_locale] = status;

            this.loading = true;

            axios
                .all(this.checkedItems.map(model => axios.patch(this.urlBase + '/' + model.id, data)))
                .then(responses => {
                    this.loading = false;
                    alertify.success(this.$i18n.tc('# items ' + label, responses.length, { count: responses.length }));
                    for (let i = this.checkedItems.length - 1; i >= 0; i--) {
                        let index = this.data.data.indexOf(this.checkedItems[i]);
                        this.data.data[index].status_translated = status;
                    }
                    this.checkedItems = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
        toggleStatus(model) {
            let status = parseInt(model.status_translated) || 0,
                newStatus = Math.abs(status - 1).toString(),
                data = {
                    status: {},
                },
                label = newStatus === '1' ? 'published' : 'unpublished';
            model.status_translated = newStatus;
            data.status[TypiCMS.content_locale] = newStatus;
            axios
                .patch(this.urlBase + '/' + model.id, data)
                .then(responses => {
                    alertify.success(this.$i18n.t('Item is ' + label + '.'));
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
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
