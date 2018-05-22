<template>
    <div>
        <div class="heading">
            <slot name="add-button"></slot>
            <h1>{{ title }}</h1>
            <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
        </div>
        <div class="btn-toolbar">
            <list-selector
                :filtered-models="filteredModels"
                :all-checked="allChecked"
                @check-all="checkAll"
                @check-none="checkNone"
                @check-published="checkPublished"
                @check-unpublished="checkUnpublished"
            ></list-selector>
            <list-actions
                :number-of-checked-models="numberOfcheckedModels"
                :loading="loading"
                @destroy="destroy"
                @publish="publish"
                @unpublish="unpublish"
            ></list-actions>
            <slot name="buttons"></slot>
        </div>
        <div class="table-responsive">
            <table class="table table-main">
                <thead>
                    <tr>
                        <slot :sort-object="sortObject" name="columns"></slot>
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
        url: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        sortDefault: {
            type: Object,
        },
    },
    data() {
        return {
            sortObject: this.sortDefault,
            loading: true,
            models: [],
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
            axios
                .get(this.url)
                .then(response => {
                    this.models = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'An error occurred with the data fetch.');
                });
        },
        checkAll() {
            this.checkedModels = this.filteredModels;
        },
        checkNone() {
            this.checkedModels = [];
        },
        checkPublished() {
            this.checkedModels = this.filteredModels.filter(model => model.status[TypiCMS.content_locale] === '1');
        },
        checkUnpublished() {
            this.checkedModels = this.filteredModels.filter(model => model.status[TypiCMS.content_locale] === '0');
        },
        destroy() {
            const deleteLimit = 1500;

            if (this.checkedModels.length > deleteLimit) {
                alertify.error('Impossible to delete more than ' + deleteLimit + ' items in one go.');
                return false;
            }
            if (!window.confirm('Are you sure you want to delete ' + this.numberOfcheckedModels + ' items?')) {
                return false;
            }

            this.loading = true;

            axios
                .all(this.checkedModels.map(model => axios.delete(this.url + '/' + model.id)))
                .then(responses => {
                    let successes = responses.filter(response => response.data.error === false);
                    this.loading = false;
                    alertify.success(successes.length + ' items deleted.');
                    for (let i = this.checkedModels.length - 1; i >= 0; i--) {
                        let index = this.models.indexOf(this.checkedModels[i]);
                        this.models.splice(index, 1);
                    }
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
                .all(this.checkedModels.map(model => axios.patch(this.url + '/' + model.id, data)))
                .then(responses => {
                    this.loading = false;
                    alertify.success(responses.length + ' items ' + label + '.');
                    for (let i = this.checkedModels.length - 1; i >= 0; i--) {
                        let index = this.models.indexOf(this.checkedModels[i]);
                        this.models[index].status[TypiCMS.content_locale] = status;
                        this.models[index].status_translated = status;
                    }
                    this.checkedModels = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'Sorry, an error occurred.');
                });
        },
        toggleStatus(model) {
            let status = parseInt(model.status[TypiCMS.content_locale]) || 0,
                newStatus = Math.abs(status - 1).toString(),
                data = {
                    status: {},
                },
                label = newStatus === '1' ? 'published' : 'unpublished';
            model.status[TypiCMS.content_locale] = newStatus;
            model.status_translated = newStatus;
            data.status[TypiCMS.content_locale] = newStatus;
            axios
                .patch(this.url + '/' + model.id, data)
                .then(responses => {
                    alertify.success('Item is ' + label + '.');
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'Sorry, an error occurred.');
                });
        },
        sort(object) {
            this.sortObject = object;
        },
    },
};
</script>
