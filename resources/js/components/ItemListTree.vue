<template>
    <div :class="{ 'sub-list': subList }" class="item-list">
        <div class="item-list-top">
            <h1 v-if="!subList" class="item-list-title header-title">
                {{ t(title.charAt(0).toUpperCase() + title.slice(1)) }}
            </h1>
            <h2 v-else class="item-list-subtitle">
                {{ t(title.charAt(0).toUpperCase() + title.slice(1)) }}
            </h2>
            <slot name="top-buttons"></slot>
        </div>
        <div :class="{ 'item-list-content': !subList }">
            <div :class="{ header: !subList }" class="item-list-header">
                <div class="btn-toolbar item-list-toolbar header-toolbar">
                    <slot name="buttons"></slot>
                    <div class="d-flex align-items-center">
                        <div v-if="loading" class="spinner-border spinner-border-sm text-dark" role="status">
                            <span class="visually-hidden">{{ t('Loading…') }}</span>
                        </div>
                    </div>
                    <small v-if="!loading && total" class="text-muted align-self-center">
                        {{ t('# ' + title, total, { count: total }) }}
                    </small>
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
            <div class="content">
                <sl-vue-tree-next ref="slVueTree" v-model="models" :allowMultiselect="false" @drop="drop" @toggle="toggle">
                    <template #title="{ node }">
                        <button v-if="$can('delete ' + table)" class="btn btn-xs btn-link" type="button" @click="deleteFromNested(node)">
                            <x-icon class="text-danger" :size="18" stroke-width="2" />
                        </button>

                        <a v-if="$can('update ' + table)" :href="table + '/' + node.data.id + '/edit'" class="btn btn-light btn-xs me-2 ms-1">
                            {{ t('Edit') }}
                        </a>

                        <button class="btn-status me-2" type="button" @click="toggleStatus(node)">
                            <span v-if="translatable" :class="node.data.status_translated === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'" class="btn-status-icon"></span>
                            <span v-else :class="node.data.status === 1 ? 'btn-status-icon-on' : 'btn-status-icon-off'" class="btn-status-icon"></span>
                        </button>
                        <house-icon v-if="node.data.is_home" class="text-secondary" size="16" />
                        <lock-icon v-if="node.data.private" class="text-secondary" size="16" />
                        <div class="title" v-html="translatable ? node.data.title_translated : node.data.title"></div>
                        <corner-right-down-icon v-if="node.data.redirect" class="text-secondary" size="16" />

                        <a v-if="node.data.module" :href="'/admin/' + node.data.module" class="btn btn-xs btn-secondary fw-bold px-1 py-0">
                            {{ t(node.data.module.charAt(0).toUpperCase() + node.data.module.slice(1)) }}
                        </a>
                    </template>

                    <template #toggle="{ node }">
                        <chevron-down-icon v-if="node.children.length > 0 && node.isExpanded" size="16" />
                        <chevron-right-icon v-if="node.children.length > 0 && !node.isExpanded" size="16" />
                        <small v-else />
                    </template>
                </sl-vue-tree-next>
            </div>
        </div>
    </div>
</template>

<script setup>
import alertify from 'alertify.js';
import { ChevronDownIcon, ChevronRightIcon, CornerRightDownIcon, HouseIcon, LockIcon, XIcon } from 'lucide-vue-next';
import { SlVueTreeNext } from 'sl-vue-tree-next';
import { computed, ref, useTemplateRef } from 'vue';
import { useI18n } from 'vue-i18n';

import fetcher from '../admin/fetcher';

const { t } = useI18n();

const props = defineProps({
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
});

const loadingTimeout = ref(null);
const locales = ref(window.TypiCMS.locales);
const contentLocale = ref(window.TypiCMS.content_locale);
const loading = ref(false);
const models = ref([]);
const total = ref(null);
const slVueTree = useTemplateRef('slVueTree');

const url = computed(() => {
    const query = ['fields[' + props.table + ']=' + props.fields];

    if (props.translatable) {
        query.push('locale=' + contentLocale.value);
    }

    return props.urlBase + '?' + query.join('&');
});

fetchData();

async function fetchData() {
    startLoading();
    try {
        const response = await fetcher(url.value);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        const data = await response.json();
        models.value = data.models;
        total.value = data.total;
        stopLoading();
    } catch (error) {
        alertify.error(t(error.message) || t('An error occurred with the data fetch.'));
    }
}

function startLoading() {
    loadingTimeout.value = setTimeout(() => {
        loading.value = true;
    }, 300);
}

function stopLoading() {
    clearTimeout(loadingTimeout.value);
    loading.value = false;
}

async function switchLocale(locale) {
    startLoading();
    contentLocale.value = locale;
    try {
        const response = await fetcher('/admin/_locale/' + locale);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        stopLoading();
        await fetchData();
    } catch (error) {
        alertify.error(t(error.message));
    }
}

async function deleteFromNested(node) {
    const model = node.data;
    const title = model.title_translated;
    if (
        !window.confirm(
            t('Are you sure you want to delete “{title}”?', {
                title,
            }),
        )
    ) {
        return false;
    }
    try {
        const response = await fetcher(props.urlBase + '/' + model.id, {
            method: 'DELETE',
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        slVueTree.value.remove([node.path]);
        alertify.success(t('Item successfully deleted.'));
    } catch (error) {
        console.log(error);
        alertify.error(t(error.message) || t('Sorry, an error occurred.'));
    }
}

async function drop(draggingNodes, position) {
    let list = [];
    const draggedNode = draggingNodes[0];
    let parentId = position.node.data.parent_id;
    if (position.placement === 'inside') {
        parentId = position.node.data.id;
    }

    slVueTree.value.traverse((node) => {
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

    const data = {
        moved: draggedNode.data.id,
        item: list,
    };
    try {
        const response = await fetcher(props.urlBase + '/sort', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
    } catch (error) {
        alertify.error(t(error.message) || t('Sorry, an error occurred.'));
    }
}

async function toggle(node) {
    const data = {};
    data[props.title + '_' + node.data.id + '_collapsed'] = node.isExpanded;
    try {
        const response = await fetcher('/api/users/current/update-preferences', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
    } catch {
        alertify.error(t('User preferences couldn’t be set.'));
    }
}

async function toggleStatus(node) {
    const originalNode = JSON.parse(JSON.stringify(node)),
        status = props.translatable ? parseInt(node.data.status_translated) : parseInt(node.data.status) || 0,
        newStatus = Math.abs(status - 1),
        data = {
            status: {},
        },
        label = newStatus === 1 ? 'published' : 'unpublished';

    if (props.translatable) {
        data.status[contentLocale.value] = newStatus;
        node.data.status_translated = newStatus;
    } else {
        data.status = newStatus;
        node.data.status = newStatus;
    }

    slVueTree.value.updateNode({ path: node.path, patch: node });
    try {
        const response = await fetcher(props.urlBase + '/' + node.data.id, {
            method: 'PATCH',
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        alertify.success(t('Item is ' + label + '.'));
    } catch (error) {
        slVueTree.value.updateNode({ path: node.path, patch: originalNode });
        alertify.error(t(error.message) || t('Sorry, an error occurred.'));
    }
}
</script>
