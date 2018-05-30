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
                :number-of-checked-models="numberOfcheckedModels"
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
            <div class="d-flex align-items-center ml-2">
                <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
            </div>
            <slot name="buttons"></slot>
        </div>

        <div class="table-responsive" v-if="filteredModels.length">
            <table class="table table-main">
                <thead>
                    <tr>
                        <th class="checkbox">
                            <list-selector
                                :filtered-models="filteredModels"
                                :all-checked="allChecked"
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
                    <tr v-for="model in filteredModels">
                        <slot :model="model" :checked-models="checkedModels" name="table-row"></slot>
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
import Pagination from './Pagination';

export default {
    components: {
        ListSelector,
        ListActions,
        ListItemsPerPage,
        TypiBtnStatus,
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
        sorting: {
            type: Array,
            default: ['-id'],
        },
    },
    data() {
        return {
            sortArray: this.sorting,
            loading: false,
            total: 0,
            last_page: null,
            checkedModels: [],
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
        url() {
            return (
                this.urlBase +
                '?' +
                'sort=' +
                this.sortArray.join(',') +
                '&' +
                this.urlParameters +
                '&page=' +
                this.data.current_page +
                '&per_page=' +
                this.data.per_page
            );
        },
        filteredModels() {
            return this.data.data;
        },
        allChecked() {
            return this.filteredModels.length > 0 && this.filteredModels.length === this.checkedModels.length;
        },
        numberOfcheckedModels() {
            return this.checkedModels.length;
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
                    alertify.error(error.response.data.message || 'An error occurred with the data fetch.');
                });
        },
        changePage(page = 1) {
            this.data.current_page = page;
            this.fetchData();
        },
        changeNumberOfItemsPerPage(per_page) {
            this.data.per_page = per_page;
            this.fetchData();
        },
        checkAll() {
            this.checkedModels = this.filteredModels;
        },
        checkNone() {
            this.checkedModels = [];
        },
        checkPublished() {
            this.checkedModels = this.filteredModels.filter(model => model.status_translated === '1');
        },
        checkUnpublished() {
            this.checkedModels = this.filteredModels.filter(model => model.status_translated === '0');
        },
        destroy() {
            const deleteLimit = 500;

            if (this.checkedModels.length > deleteLimit) {
                alertify.error('Impossible to delete more than ' + deleteLimit + ' items in one go.');
                return false;
            }
            if (!window.confirm('Are you sure you want to delete ' + this.numberOfcheckedModels + ' items?')) {
                return false;
            }

            this.loading = true;

            axios
                .all(this.checkedModels.map(model => axios.delete(this.urlBase + '/' + model.id)))
                .then(responses => {
                    let successes = responses.filter(response => response.data.error === false);
                    this.loading = false;
                    alertify.success(successes.length + ' items deleted.');
                    this.fetchData();
                    this.checkedModels = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'Sorry, an error occurred.');
                });
        },
        publish() {
            if (!window.confirm('Are you sure you want to publish ' + this.checkedModels.length + ' items?')) {
                return false;
            }
            this.setStatus('1');
        },
        unpublish() {
            if (!window.confirm('Are you sure you want to unpublish ' + this.checkedModels.length + ' items?')) {
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
                .all(this.checkedModels.map(model => axios.patch(this.urlBase + '/' + model.id, data)))
                .then(responses => {
                    this.loading = false;
                    alertify.success(responses.length + ' items ' + label + '.');
                    for (let i = this.checkedModels.length - 1; i >= 0; i--) {
                        let index = this.data.data.indexOf(this.checkedModels[i]);
                        this.data.data[index].status_translated = status;
                    }
                    this.checkedModels = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'Sorry, an error occurred.');
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
                    alertify.success('Item is ' + label + '.');
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'Sorry, an error occurred.');
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
