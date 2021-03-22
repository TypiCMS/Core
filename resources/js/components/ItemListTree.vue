<template>
    <div class="item-list-tree">
        <div class="item-list-header header">
            <h1 class="item-list-title header-title">{{ $t(title) }}</h1>
            <div class="item-list-toolbar header-toolbar btn-toolbar">
                <slot name="add-button"></slot>
            </div>
        </div>

        <div class="btn-toolbar item-list-actions">
            <slot name="buttons"></slot>
            <div class="d-flex align-items-center ms-2">
                <div class="spinner-border spinner-border-sm text-secondary" role="status" v-if="loading">
                    <span class="visually-hidden">{{ $t('Loading…') }}</span>
                </div>
            </div>
            <div class="btn-group btn-group-sm ms-auto" v-if="multilingual && locales.length > 1">
                <button
                    class="btn btn-light dropdown-toggle"
                    type="button"
                    id="dropdownLangSwitcher"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <span id="active-locale">{{ locales.find((item) => item.short === currentLocale).long }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLangSwitcher">
                    <button
                        class="dropdown-item"
                        :class="{ active: locale === currentLocale }"
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

                <div
                    class="btn btn-xs btn-link btn-status me-1"
                    :class="node.data.status_translated === 1 ? 'btn-status-on' : 'btn-status-off'"
                    @click="toggleStatus(node)"
                    v-if="multilingual"
                ></div>
                <div
                    class="btn btn-xs btn-link btn-status me-1"
                    :class="node.data.status === 1 ? 'btn-status-on' : 'btn-status-off'"
                    @click="toggleStatus(node)"
                    v-else
                ></div>

                <svg
                    class="text-muted"
                    v-if="node.data.is_home === 1"
                    width="1em"
                    height="1em"
                    viewBox="0 0 16 16"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M6.5 10.995V14.5a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .146-.354l6-6a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 .146.354v7a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5V11c0-.25-.25-.5-.5-.5H7c-.25 0-.5.25-.5.495z"
                    />
                    <path fill-rule="evenodd" d="M13 2.5V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                </svg>

                <svg
                    class="text-muted"
                    v-if="node.data.private === 1"
                    width="1em"
                    height="1em"
                    viewBox="0 0 16 16"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M2.5 9a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V9z" />
                    <path fill-rule="evenodd" d="M4.5 4a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z" />
                </svg>

                <div class="title" v-html="multilingual ? node.data.title_translated : node.data.title"></div>

                <svg
                    class="text-muted"
                    v-if="node.data.redirect === 1"
                    :title="$t('Redirect to first child')"
                    width="1em"
                    height="1em"
                    fill="currentColor"
                    viewBox="0 0 1792 1792"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M416 256h704q13 0 22.5 9.5t9.5 23.5v863h192q40 0 58 37t-9 69l-320 384q-18 22-49 22t-49-22l-320-384q-26-31-9-69 18-37 58-37h192v-640h-320q-14 0-25-11l-160-192q-13-14-4-34 9-19 29-19z"
                    />
                </svg>

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
</template>

<script>
import SlVueTree from 'sl-vue-tree';
import ItemListSelector from './ItemListSelector.vue';
import ItemListActions from './ItemListActions.vue';

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
        locale: {
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
        appends: {
            type: String,
            default: '',
        },
        multilingual: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            loadingTimeout: null,
            locales: window.TypiCMS.locales,
            currentLocale: this.locale,
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

            if (this.appends !== '') {
                query.push('append=' + this.appends);
            }
            if (this.multilingual) {
                query.push('locale=' + this.currentLocale);
            }

            return this.urlBase + '?' + query.join('&');
        },
        filteredItems() {
            return this.models;
        },
    },
    methods: {
        fetchData() {
            this.startLoading();
            axios
                .get(this.url)
                .then((response) => {
                    this.models = response.data;
                    this.stopLoading();
                })
                .catch((error) => {
                    alertify.error(
                        error.response.data.message || this.$i18n.t('An error occurred with the data fetch.')
                    );
                });
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
        switchLocale(locale) {
            this.startLoading();
            this.currentLocale = locale;
            axios.get('/admin/_locale/' + locale).then((response) => {
                this.stopLoading();
                this.fetchData();
            });
        },
        deleteFromNested(node) {
            let model = node.data;
            let title = model.title_translated;
            if (!window.confirm(this.$i18n.t('Are you sure you want to delete “{title}”?', { title }))) {
                return false;
            }
            axios
                .delete(this.urlBase + '/' + model.id)
                .then((data) => {
                    this.$refs.slVueTree.remove([node.path]);
                })
                .catch((error) => {
                    alertify.error(
                        this.$i18n.t(error.response.data.message) || this.$i18n.t('Sorry, an error occurred.')
                    );
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

            axios.post(this.urlBase + '/sort', data).catch((error) => {
                alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
            });
        },
        toggle(node) {
            let data = {};
            data[this.title + '_' + node.data.id + '_collapsed'] = node.isExpanded;
            axios.post('/api/users/current/updatepreferences', data).catch((error) => {
                alertify.error('User preference couldn’t be set.');
            });
        },
        toggleStatus(node) {
            let originalNode = JSON.parse(JSON.stringify(node)),
                status = this.multilingual ? parseInt(node.data.status_translated) : parseInt(node.data.status) || 0,
                newStatus = Math.abs(status - 1),
                data = {
                    status: {},
                },
                label = newStatus === 1 ? 'published' : 'unpublished';

            if (this.multilingual) {
                data.status[this.currentLocale] = newStatus;
                node.data.status_translated = newStatus;
            } else {
                data.status = newStatus;
                node.data.status = newStatus;
            }

            this.$refs.slVueTree.updateNode(node.path, node);
            axios
                .patch(this.urlBase + '/' + node.data.id, data)
                .then((response) => {
                    alertify.success(this.$i18n.t('Item is ' + label + '.'));
                })
                .catch((error) => {
                    this.$refs.slVueTree.updateNode(node.path, originalNode);
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
    },
};
</script>
