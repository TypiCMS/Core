<template>

    <div>

        <div class="btn-toolbar">
            <list-selector
                :filtered-models="filteredModels"
                :all-checked="allChecked"
                @toggle="toggle"
                @check="check"
                @uncheck="uncheck"
                @check-published="checkPublished"
                @check-unpublished="checkUnpublished"
            ></list-selector>
            <list-actions
                :number-of-checked-models="checkedModels.length"
                :loading="loading"
                @destroy="destroy"
                @publish="publish"
                @unpublish="unpublish"
            ></list-actions>
            <slot name="buttons" v-if="!loading"></slot>
        </div>

        <sl-vue-tree v-model="models" :allowMultiselect="false">

            <template slot="title" slot-scope="{ node }">

                <div @click="deleteFromNested(data)" class="btn btn-xs btn-link">
                    <span class="fa fa-remove"></span>
                </div>

                <a class="btn btn-light btn-xs" :href="'pages/'+node.data.id+'/edit'">Edit</a>

                <div class="btn btn-xs btn-link btn-status" @click="toggleStatus(node.data)">
                    <span class="fa btn-status-switch" :class="node.data.status[locale] == '1' ? 'fa-toggle-on' : 'fa-toggle-off'"></span>
                </div>

                <div v-if="node.data.private" class="fa fa-lock"></div>

                <div class="title">{{ node.title[locale] }}</div>

                <div class="badge badge-info" :href="node.data.module" v-if="node.data.module">{{ node.data.module }}</div>

            </template>

            <template slot="toggle" slot-scope="{ node }">
                <div class="disclose fa fa-fw" :class="{'fa-caret-right': !node.isExpanded, 'fa-caret-down': node.isExpanded, 'd-none': !node.children.length}"></div>
            </template>

        </sl-vue-tree>

    </div>

</template>

<script>
import SlVueTree from 'sl-vue-tree';
import ListSelector from './ListSelector';
import ListActions from './ListActions';

export default {
    components: {
        SlVueTree,
        ListSelector,
        ListActions,
    },
    props: {
        url: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            loading: true,
            models: [],
            checkedModels: [],
            locale: TypiCMS.content_locale,
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
        }
    },
    methods: {
        fetchData() {
            axios.get(this.url)
                .then((response) => {
                    this.models = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'An error occurred with the data fetch.');
                });
        },
        toggle() {
            if (this.allChecked === true) {
                this.uncheck();
            } else {
                this.check();
            }
        },
        check() {
            this.checkedModels = this.models;
        },
        uncheck() {
            this.checkedModels = [];
        },
        checkPublished() {
            alert('check published');
        },
        checkUnpublished() {
            alert('check unpublished');
        },
        destroy() {
            alert('destroy');
        },
        publish() {
            alert('publish');
        },
        unpublish() {
            alert('unpublish');
        },
    }
};
</script>
