<template>
    <div>

        <div class="header">
            <h3 class="mr-2">
                {{ total }} {{ title }}
            </h3>
        </div>

        <div class="btn-toolbar">
            <slot name="add-button"></slot>
            <list-actions
                :number-of-checked-models="numberOfcheckedModels"
                :loading="loading"
                @destroy="destroy"
                @publish="publish"
                @unpublish="unpublish"
            ></list-actions>
            <div class="btn-group ml-2" aria-label="Page navigation">
                <button class="btn btn-light" :disabled="current_page === 1" @click="prevPage" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </button>
                <button class="btn btn-light" disabled>Page {{ current_page }} of {{ last_page }}, {{ filteredModels.length }} per page</button>
                <button class="btn btn-light" :disabled="current_page === last_page" @click="nextPage" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </button>
            </div>
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
    </div>
</template>

<script>
import ListSelector from './ListSelector';
import ListActions from './ListActions';
import TypiBtnStatus from './TypiBtnStatus';

export default {
    components: {
        ListSelector,
        ListActions,
        TypiBtnStatus,
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
            models: [],
            total: 0,
            current_page: null,
            last_page: null,
            checkedModels: [],
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
            return this.urlBase + '?' + 'sort=' + this.sortArray.join(',') + '&' + this.urlParameters + '&page=' + this.current_page;
        },
        filteredModels() {
            return this.models;
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
                    this.models = response.data.data;
                    this.total = response.data.total;
                    this.current_page = response.data.current_page;
                    this.last_page = response.data.last_page;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'An error occurred with the data fetch.');
                });
        },
        prevPage() {
            this.current_page -= 1;
            this.fetchData();
        },
        nextPage() {
            this.current_page += 1;
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
                        let index = this.models.indexOf(this.checkedModels[i]);
                        this.models[index].status_translated = status;
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
            this.current_page = 1;
            this.sortArray = object;
            this.fetchData();
        },
    },
};
</script>
