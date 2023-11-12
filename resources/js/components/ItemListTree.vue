<template>
    <div :class="{ 'sub-list': subList }" class="item-list-tree">
        <div class="item-list-header header">
            <h1 v-if="!subList" class="item-list-title">
                {{ $t(title) }}
            </h1>
            <h2 v-else class="item-list-subtitle">
                {{ $t(title) }}
            </h2>
            <div class="btn-toolbar item-list-toolbar header-toolbar">
                <slot name="buttons"></slot>
                <slot name="add-button"></slot>
                <div class="d-flex align-items-center ms-2">
                    <div v-if="loading" class="spinner-border spinner-border-sm text-dark" role="status">
                        <span class="visually-hidden">{{ $t('Loading…') }}</span>
                    </div>
                </div>
                <div v-if="translatable && locales.length > 1" class="btn-group btn-group-sm ms-auto">
                    <button id="dropdownLangSwitcher" aria-expanded="false" aria-haspopup="true" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
                        <span id="active-locale">{{ locales.find((item) => item.short === contentLocale).long }}</span>
                    </button>
                    <div aria-labelledby="dropdownLangSwitcher" class="dropdown-menu dropdown-menu-right">
                        <button v-for="locale in locales" :key="locale.short" :class="{ active: locale === contentLocale }" class="dropdown-item" type="button" @click="switchLocale(locale.short)">
                            {{ locale.long }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="item-list-content content">
            <sl-vue-tree ref="slVueTree" v-model="models" :allowMultiselect="false" @drop="drop" @toggle="toggle">
                <template slot="title" slot-scope="{ node }">
                    <button v-if="$can('delete ' + table)" class="btn btn-xs btn-link" type="button" @click="deleteFromNested(node)">
                        <i class="bi bi-x-lg fs-6 text-danger"></i>
                    </button>

                    <a v-if="$can('update ' + table)" :href="table + '/' + node.data.id + '/edit'" class="btn btn-light btn-xs ms-1 me-2">
                        {{ $t('Edit') }}
                    </a>

                    <button class="btn-status me-2" type="button" @click="toggleStatus(node)">
                        <span v-if="translatable" :class="node.data.status_translated === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'" class="btn-status-icon"></span>
                        <span v-else :class="node.data.status === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'" class="btn-status-icon"></span>
                    </button>
                    <i v-if="node.data.is_home" class="bi bi-house-door-fill text-secondary"></i>
                    <i v-if="node.data.private" class="bi bi-lock-fill text-secondary"></i>
                    <div class="title" v-html="translatable ? node.data.title_translated : node.data.title"></div>
                    <i v-if="node.data.redirect" class="bi bi-arrow-down-right-square text-secondary"></i>

                    <a v-if="node.data.module" :href="'/admin/' + node.data.module" class="btn btn-xs btn-secondary py-0 px-1 fw-bold">
                        {{ $t(node.data.module.charAt(0).toUpperCase() + node.data.module.slice(1)) }}
                    </a>
                </template>

                <template slot="toggle" slot-scope="{ node }">
                    <small v-if="node.children.length > 0 && node.isExpanded" class="bi bi-caret-down-fill"></small>
                    <small v-if="node.children.length > 0 && !node.isExpanded" class="bi bi-caret-right-fill"></small>
                </template>
            </sl-vue-tree>
        </div>
    </div>
</template>

<script>
import SlVueTree from 'sl-vue-tree';
import ItemListSelector from './ItemListSelector.vue';
import ItemListActions from './ItemListActions.vue';
import fetcher from '../admin/fetcher';

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
        table: {
            type: String,
            required: true,
        },
        fields: {
            type: String,
        },
        translatable: {
            type: Boolean,
            default: true,
        },
        subList: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loadingTimeout: null,
            locales: window.TypiCMS.locales,
            contentLocale: window.TypiCMS.locale,
            loading: false,
            models: [],
        };
    },
    created() {
        this.fetchData();
    },
    computed: {
        url() {
            let query = ['fields[' + this.table + ']=' + this.fields];

            if (this.translatable) {
                query.push('locale=' + this.contentLocale);
            }

            return this.urlBase + '?' + query.join('&');
        },
        filteredItems() {
            return this.models;
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
                this.models = await response.json();
                this.stopLoading();
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('An error occurred with the data fetch.'));
            }
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
                alertify.error(error.message);
            }
        },
        async deleteFromNested(node) {
            let model = node.data;
            let title = model.title_translated;
            if (
                !window.confirm(
                    this.$i18n.t('Are you sure you want to delete “{title}”?', {
                        title,
                    }),
                )
            ) {
                return false;
            }
            try {
                const response = await fetcher(this.urlBase + '/' + model.id, {
                    method: 'DELETE',
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                this.$refs.slVueTree.remove([node.path]);
                alertify.success(this.$i18n.t('Item successfully deleted.'));
            } catch (error) {
                console.log(error);
                alertify.error(this.$i18n.t(error.message) || this.$i18n.t('Sorry, an error occurred.'));
            }
        },

        async drop(draggingNodes, position) {
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
                        list = node.children.map((item) => {
                            item.data.parent_id = parentId;
                            if (node.data.private) {
                                item.data.private = true;
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
            try {
                const response = await fetcher(this.urlBase + '/sort', {
                    method: 'POST',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('Sorry, an error occurred.'));
            }
        },
        async toggle(node) {
            let data = {};
            data[this.title + '_' + node.data.id + '_collapsed'] = node.isExpanded;
            try {
                const response = await fetcher('/api/users/current/update-preferences', {
                    method: 'POST',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
            } catch (error) {
                alertify.error("User preference couldn't be set.");
            }
        },
        async toggleStatus(node) {
            let originalNode = JSON.parse(JSON.stringify(node)),
                status = this.translatable ? parseInt(node.data.status_translated) : parseInt(node.data.status) || 0,
                newStatus = Math.abs(status - 1),
                data = {
                    status: {},
                },
                label = newStatus === 1 ? 'published' : 'unpublished';

            if (this.translatable) {
                data.status[this.contentLocale] = newStatus;
                node.data.status_translated = newStatus;
            } else {
                data.status = newStatus;
                node.data.status = newStatus;
            }

            this.$refs.slVueTree.updateNode(node.path, node);
            try {
                const response = await fetcher(this.urlBase + '/' + node.data.id, {
                    method: 'PATCH',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                alertify.success(this.$i18n.t('Item is ' + label + '.'));
            } catch (error) {
                this.$refs.slVueTree.updateNode(node.path, originalNode);
                alertify.error(error.message || this.$i18n.t('Sorry, an error occurred.'));
            }
        },
    },
};
</script>
