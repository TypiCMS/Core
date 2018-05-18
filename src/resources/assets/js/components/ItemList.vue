<template>
    <div>
        <div class="btn-toolbar">
            <list-selector
                :filtered-models="filteredModels"
                :all-checked="allChecked"
                @toggle="toggle"
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
            <slot name="buttons" v-if="!loading"></slot>
        </div>
        <div class="table-responsive">
            <table class="table table-main">
                <thead>
                    <tr>
                        <th class="checkbox"></th>
                        <slot name="columns"></slot>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="model in filteredModels">
                        <td>
                            <input type="checkbox" :id="model.id" :value="model" v-model="checkedModels">
                        </td>
                        <slot :model="model"></slot>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import ListSelector from './ListSelector';
import ListActions from './ListActions';

export default {
    components: {
        ListSelector,
        ListActions,
    },
    props: {
        url: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            loading: true,
            models: [],
            checkedModels: [],
        };
    },
    created() {
        this.fetchData();
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
        toggle() {
            if (this.allChecked === true) {
                this.checkNone();
            } else {
                this.checkAll();
            }
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
            const deleteLimit = 500;

            if (this.checkedModels.length > deleteLimit) {
                alertify.error('Impossible to delete more than ' + deleteLimit + ' items in one go.');
                return false;
            }
            if (!window.confirm('Are you sure you want to delete ' + this.numberOfcheckedModels + ' items?')) {
                return false;
            }

            this.loading = true;

            let ids = this.checkedModels.map(model => model.id).join();

            axios
                .delete(this.url + '/' + ids)
                .then(response => {
                    this.loading = false;

                    for (var i = this.checkedModels.length - 1; i >= 0; i--) {
                        let index = this.models.indexOf(this.checkedModels[i]);
                        this.models.splice(index, 1);
                    }
                    this.checkedModels = [];

                    if (response.data.number < this.numberOfcheckedModels) {
                        alertify.error(this.numberOfcheckedModels - response.data.number + ' items could not be deleted.');
                    }
                    if (response.data.number > 0) {
                        alertify.success(response.data.number + ' items deleted.');
                    }
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'An error occurred.');
                });
        },
        publish() {
            alert('publish');
        },
        unpublish() {
            alert('unpublish');
        },
    },
};
</script>
