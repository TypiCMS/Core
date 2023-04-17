<template>
    <div class="item-list-tree" :class="{ 'sub-list': subList }">
        <div class="item-list-header header">
            <h1 class="item-list-title" v-if="!subList">
                {{ $t(title) }}
            </h1>
            <h2 class="item-list-subtitle" v-else>
                {{ $t(title) }}
            </h2>
            <div class="btn-toolbar item-list-toolbar header-toolbar">
                <slot name="buttons"></slot>
                <slot name="add-button"></slot>
                <div class="d-flex align-items-center ms-2">
                    <div class="spinner-border spinner-border-sm text-dark" role="status" v-if="loading">
                        <span class="visually-hidden">{{ $t('Loading…') }}</span>
                    </div>
                </div>
                <div class="btn-group btn-group-sm ms-auto" v-if="translatable && locales.length > 1">
                    <button
                        class="btn btn-light dropdown-toggle"
                        type="button"
                        id="dropdownLangSwitcher"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span id="active-locale">{{ locales.find((item) => item.short === contentLocale).long }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLangSwitcher">
                        <button
                            class="dropdown-item"
                            :class="{ active: locale === contentLocale }"
                            type="button"
                            v-for="locale in locales"
                            @click="switchLocale(locale.short)"
                            :key="locale.short"
                        >
                            {{ locale.long }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="item-list-content content">
            <sl-vue-tree v-model="models" :allowMultiselect="false" ref="slVueTree" @drop="drop" @toggle="toggle">
                <template slot="title" slot-scope="{ node }">
                    <button
                        class="btn btn-xs btn-link"
                        type="button"
                        @click="deleteFromNested(node)"
                        v-if="$can('delete ' + table)"
                    >
                        <svg
                            width="12"
                            height="12"
                            viewBox="0 0 1792 1792"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"
                            />
                        </svg>
                    </button>

                    <a
                        class="btn btn-light btn-xs ms-1 me-2"
                        :href="table + '/' + node.data.id + '/edit'"
                        v-if="$can('update ' + table)"
                    >
                        {{ $t('Edit') }}
                    </a>

                    <button class="btn-status me-2" type="button" @click="toggleStatus(node)">
                        <span
                            class="btn-status-icon"
                            :class="node.data.status_translated === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'"
                            v-if="translatable"
                        ></span>
                        <span
                            class="btn-status-icon"
                            :class="node.data.status === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'"
                            v-else
                        ></span>
                    </button>
                    <i class="bi bi-house-door-fill text-secondary" v-if="node.data.is_home === 1"></i>
                    <i class="bi bi-lock-fill text-secondary" v-if="node.data.private === 1"></i>
                    <div class="title" v-html="translatable ? node.data.title_translated : node.data.title"></div>
                    <i class="bi bi-arrow-down-right-square text-secondary" v-if="node.data.redirect === 1"></i>

                    <a
                        class="btn btn-xs btn-secondary py-0 px-1 fw-bold"
                        :href="'/admin/' + node.data.module"
                        v-if="node.data.module"
                    >
                        {{ $t(node.data.module.charAt(0).toUpperCase() + node.data.module.slice(1)) }}
                    </a>
                </template>

                <template slot="toggle" slot-scope="{ node }">
                    <svg
                        v-if="node.children.length > 0 && node.isExpanded"
                        width="0.8em"
                        height="0.8em"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"
                        />
                    </svg>
                    <svg
                        v-if="node.children.length > 0 && !node.isExpanded"
                        width="0.8em"
                        height="0.8em"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M12.14 8.753l-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"
                        />
                    </svg>
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
            if (!window.confirm(this.$i18n.t('Are you sure you want to delete “{title}”?', { title }))) {
                return false;
            }
            try {
                const response = await fetcher(this.urlBase + '/' + model.id, { method: 'DELETE' });
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
