<template>

    <div class="item-list-tree">

        <div class="item-list-header">
            <h1 class="item-list-title">
                {{ $t(title) }}
            </h1>
            <slot name="add-button"></slot>
        </div>

        <div class="btn-toolbar">
            <slot name="buttons" v-if="!loading"></slot>
        </div>

        <sl-vue-tree v-model="models" :allowMultiselect="false" ref="slVueTree" @drop="drop" @toggle="toggle">

            <template slot="title" slot-scope="{ node }">

                <div @click="deleteFromNested(node)" class="btn btn-xs btn-link">
                    <span class="fa fa-remove"></span>
                </div>

                <a class="btn btn-light btn-xs" :href="'pages/'+node.data.id+'/edit'">Edit</a>

                <div class="btn btn-xs btn-link btn-status" @click="toggleStatus(node)">
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
import ItemListSelector from './ItemListSelector';
import ItemListActions from './ItemListActions';

export default {
    components: {
        SlVueTree,
        ItemListSelector,
        ItemListActions,
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        urlBase: {
            type: String,
            required: true,
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
        },
    },
    methods: {
        fetchData() {
            axios
                .get(this.urlBase)
                .then(response => {
                    this.models = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(error.response.data.message || 'An error occurred with the data fetch.');
                });
        },
        deleteFromNested(node) {
            let model = node.data;
            let title = model.title[this.locale];
            if (node.children && node.children.length > 0) {
                alertify.error('This item cannot be deleted because it has children.');
                return false;
            }
            if (!window.confirm('Do you want to delete « ' + title + ' » ?')) {
                return false;
            }
            axios
                .delete(this.urlBase + '/' + model.id)
                .then(data => {
                    this.$refs.slVueTree.remove([node.path]);
                })
                .catch(error => {
                    alertify.error('Error ' + error.response.status + ' ' + error.response.statusText);
                });
        },
        drop(draggingNodes, position) {
            let list = [];
            let draggedNode = draggingNodes[0];
            let parentId = position.node.data.parent_id;
            if (position.placement === 'inside') {
                parentId = position.node.data.id;
            }

            this.$refs.slVueTree.traverse((node, nodeModel, path) => {
                if (node.data.id === draggedNode.data.id) {
                    node.data.parent_id = parentId;
                }
                if (parentId !== null) {
                    if (node.data.id === parentId) {
                        list = node.children.map(item => {
                            item.data.parent_id = parentId;
                            if (node.data.private === 1) {
                                item.data.private = 1;
                            }
                            return item.data;
                        });
                    }
                } else {
                    if (node.data.parent_id === null) {
                        list.push(node.data);
                    }
                }
            });

            let data = {
                moved: draggedNode.data.id,
                item: list,
            };

            axios.post(this.urlBase + '/sort', data).catch(error => {
                alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
            });
        },
        toggle(node) {
            let data = {};
            data[this.title + '_' + node.data.id + '_collapsed'] = node.isExpanded;
            axios.post('/api/users/current/updatepreferences', data).catch(error => {
                alertify.error('User preference couldn’t be set.');
            });
        },
        toggleStatus(node) {
            let originalNode = JSON.parse(JSON.stringify(node)),
                status = parseInt(node.data.status[this.locale]) || 0,
                newStatus = Math.abs(status - 1).toString(),
                data = {
                    status: {},
                },
                label = newStatus === '1' ? 'published' : 'unpublished';
            data.status[this.locale] = newStatus;
            node.data.status[this.locale] = newStatus;
            this.$refs.slVueTree.updateNode(node.path, node);
            axios
                .patch(this.urlBase + '/' + node.data.id, data)
                .then(response => {
                    alertify.success(this.$i18n.t('Item is ' + label + '.'));
                })
                .catch(error => {
                    this.$refs.slVueTree.updateNode(node.path, originalNode);
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
    },
};
</script>
