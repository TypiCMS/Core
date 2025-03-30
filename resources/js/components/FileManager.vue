<template>
    <div id="filepicker" ref="filepicker" :class="classes" class="filepicker">
        <div class="wrapper">
            <div class="filemanager-header header">
                <a v-if="path.length > 1" class="btn-back" href="#" @click="openFolder(path[path.length - 2])">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span class="btn-back-label">
                        {{ path[path.length - 2].name }}
                    </span>
                </a>
                <h1 v-if="path.length > 0" class="filemanager-title header-title">
                    {{ path[path.length - 1].name }}
                </h1>
                <div class="header-toolbar btn-toolbar">
                    <button class="btn btn-sm btn-light me-2" type="button" @click="newFolder(folder.id)">
                        <i class="bi bi-folder-fill text-black-50 me-1"></i>
                        {{ t('New folder') }}
                    </button>
                    <div class="btn-group btn-group-sm me-2">
                        <button id="dropdown-action-button" aria-expanded="true" aria-haspopup="true" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
                            {{ t('Action') }}
                        </button>

                        <div aria-labelledby="dropdown-action-button" class="dropdown-menu">
                            <button :disabled="selectedItems.length === 0" class="dropdown-item" type="button" @click="deleteSelected">
                                {{ t('Delete') }}
                            </button>
                            <button :disabled="!folder.id || selectedFiles.length === 0" class="dropdown-item" type="button" @click="moveToParentFolder()">
                                {{ t('Move to parent folder') }}
                            </button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item" disabled="disabled" type="button">
                                {{
                                    t('# items selected', selectedItems.length, {
                                        count: selectedItems.length,
                                    })
                                }}
                            </button>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm me-2">
                        <button :class="{ active: view === 'grid' }" class="btn btn-light" type="button" @click="switchView('grid')">
                            <i class="bi bi-grid-3x2-gap-fill text-black-50 me-1"></i>
                            {{ t('Grid') }}
                        </button>
                        <button :class="{ active: view === 'list' }" class="btn btn-light" type="button" @click="switchView('list')">
                            <i class="bi bi-list-ul text-black-50 me-1"></i>
                            {{ t('List') }}
                        </button>
                    </div>
                    <div class="d-flex align-items-center ms-2">
                        <div v-if="loading" class="spinner-border spinner-border-sm text-dark" role="status">
                            <span class="visually-hidden">{{ t('Loading…') }}</span>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm me-2 ms-auto">
                        <button
                            v-if="options.multiple"
                            id="add-selected-files-button"
                            :disabled="selectedFiles.length < 1"
                            class="btn btn-primary filemanager-btn-add btn-add-multiple"
                            type="button"
                            @click="addSelectedFiles()"
                        >
                            {{ t('Add selected files') }}
                        </button>

                        <button
                            v-if="options.single"
                            id="add-selected-file-button"
                            :disabled="selectedFiles.length !== 1"
                            class="btn btn-primary filemanager-btn-add btn-add-single"
                            type="button"
                            @click="addSingleFile(selectedFiles[0])"
                        >
                            {{ t('Add selected file') }}
                        </button>
                    </div>
                    <button v-if="dropzone" id="upload-files-button" class="btn btn-sm btn-light header-btn-add" type="button">
                        <i class="bi bi-cloud-upload-fill me-1"></i>
                        {{ t('Upload files') }}
                    </button>
                </div>
            </div>

            <button v-if="modal" :aria-label="t('Close window')" class="filemanager-btn-close" type="button" @click="closeModal">
                <span aria-hidden="true">×</span>
            </button>

            <div class="filemanager-body">
                <Dashboard
                    :plugins="['ImageEditor']"
                    :props="{
                        inline: false,
                        trigger: '#upload-files-button',
                        proudlyDisplayPoweredByUppy: false,
                    }"
                    :uppy="uppy"
                />
                <div :class="{ 'filemanager-view-list': view === 'list' }" class="filemanager-list" @click="checkNone()">
                    <div
                        v-for="item in filteredItems"
                        :key="item.id"
                        :id="'item_' + item.id"
                        :class="{
                            'filemanager-item-selected': selectedItems.indexOf(item) !== -1,
                            'filemanager-item-folder': item.type === 'f',
                            'filemanager-item-file': item.type !== 'f',
                            'filemanager-item-dragging-source': dragging && selectedItems.indexOf(item) !== -1,
                        }"
                        class="filemanager-item filemanager-item-with-name filemanager-item-editable"
                        draggable="true"
                        @click="check(item, $event)"
                        @dblclick="onDoubleClick(item)"
                        @dragend="dragEnd($event)"
                        @dragenter="dragEnter($event)"
                        @dragleave="dragLeave($event)"
                        @dragover="dragOver($event)"
                        @dragstart="dragStart(item, $event)"
                        @drop="drop(item, $event)"
                    >
                        <div class="filemanager-item-wrapper">
                            <div v-if="item.type === 'i'" class="filemanager-item-icon">
                                <div class="filemanager-item-image-wrapper">
                                    <img :src="item.thumb_sm" class="filemanager-item-image" alt="" />
                                </div>
                            </div>
                            <div v-else :class="'filemanager-item-icon-' + item.type" class="filemanager-item-icon">
                                <i v-if="item.type === 'a'" class="bi bi-file-earmark-music"></i>
                                <i v-if="item.type === 'v'" class="bi bi-file-earmark-play"></i>
                                <i v-if="item.type === 'd'" class="bi bi-file-earmark"></i>
                                <i v-if="item.type === 'f'" class="bi bi-folder"></i>
                            </div>
                            <div class="filemanager-item-name">
                                {{ item.name }}
                            </div>
                            <a :href="'/admin/files/' + item.id + '/edit'" class="filemanager-item-editable-button">
                                <span class="filemanager-item-editable-button-icon"></span>
                                <span class="visually-hidden">{{ t('Edit') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Uppy from '@uppy/core';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import DropTarget from '@uppy/drop-target';
import ImageEditor from '@uppy/image-editor';
import '@uppy/image-editor/dist/style.min.css';
import es from '@uppy/locales/lib/es_ES';
import fr from '@uppy/locales/lib/fr_FR';
import nl from '@uppy/locales/lib/nl_NL';
import { Dashboard } from '@uppy/vue';
import XHRUpload from '@uppy/xhr-upload';
import { computed, nextTick, ref } from 'vue';
import fetcher from '../admin/fetcher';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const uppyLocales = { fr, nl, es };

const props = defineProps({
    modal: {
        type: Boolean,
        default: true,
    },
    modalIsInFront: {
        type: Boolean,
        default: false,
    },
    dropzone: {
        type: Boolean,
        default: true,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    single: {
        type: Boolean,
        default: false,
    },
    open: {
        type: Boolean,
        default: false,
    },
    overlay: {
        type: Boolean,
        default: true,
    },
});
const loadingTimeout = ref(null);
const dragging = ref(false);
const loading = ref(false);
const view = ref('grid');
const selectedItems = ref([]);
const deleteLimit = ref(100);
const urlBase = ref('/api/files');
const options = ref({
    dropzone: props.dropzone,
    modal: props.modal,
    modalIsInFront: props.modalIsInFront,
    multiple: props.multiple,
    open: props.open,
    overlay: props.overlay,
    single: props.single,
});
const maxFilesize = ref(window.TypiCMS.max_file_upload_size);
const folder = ref({ id: '' });
const data = ref({ models: [], path: [] });

if (sessionStorage.getItem('view')) {
    view.value = JSON.parse(sessionStorage.getItem('view'));
}

emitter.on('openFilePickerForCKEditor', (opts) => {
    document.body.classList.add('noscroll');
    options.value = opts;
    window.ckEditorDialogBlured = true;
    nextTick().then(() => {
        document.querySelector('.filemanager-btn-close').focus();
    });
});
emitter.on('openFilePicker', (opts) => {
    document.body.classList.add('noscroll');
    options.value = opts;
});

document.addEventListener('keydown', (event) => {
    if (event.code === 'Escape') {
        if (props.modal && options.value.modalIsInFront) {
            closeModal();
        } else {
            if (CKEDITOR.dialog.getCurrent() !== null) {
                CKEDITOR.dialog.getCurrent().hide();
            }
        }
    }
});

const uppy = computed(() => {
    return new Uppy({
        locale: uppyLocales[TypiCMS.locale],
        restrictions: {
            maxFileSize: maxFilesize.value * 1024,
            allowedFileTypes: [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'application/vnd.openxmlformats-officedocument.presentationml.slide',
                'application/msword',
                'application/vnd.ms-powerpoint',
                'application/vnd.ms-excel',
                'application/pdf',
                'application/postscript',
                'application/zip',
                'application/json',
                'text/plain',
                'image/svg+xml',
                'image/tiff',
                'image/jpeg',
                'image/gif',
                'image/png',
                'image/bmp',
                'image/gif',
                'audio/*',
                'video/*',
            ],
        },
    })
        .use(DropTarget, {
            target: document.body,
        })
        .use(XHRUpload, {
            endpoint: '/api/files',
            formData: true,
            fieldName: 'name',
            allowedMetaFields: ['folder_id'],
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + document.head.querySelector('meta[name="api-token"]').content,
            },
        })
        .use(ImageEditor, { quality: 0.8 })
        .on('file-added', (file) => {
            uppy.value.setFileMeta(file.id, {
                folder_id: folder.value.id,
            });
        })
        .on('dashboard:modal-open', () => {
            options.value.modalIsInFront = false;
        })
        .on('dashboard:modal-closed', () => {
            options.value.modalIsInFront = true;
        })
        .on('complete', (result) => {
            const fails = result.failed;
            if (fails.length > 0) {
                alertify.error(
                    t('# files could not be uploaded.', fails.length, {
                        count: fails.length,
                    }),
                );
            }

            const successes = result.successful;
            if (successes.length > 0) {
                alertify.success(
                    t('# files uploaded.', successes.length, {
                        count: successes.length,
                    }),
                );
                fetchData();
            }
        });
});
const classes = computed(() => {
    return {
        'filemanager-modal': options.value.modal,
        'filemanager-multiple': options.value.multiple,
        'filemanager-single': options.value.single,
        'filemanager-modal-open': options.value.open,
        'filemanager-modal-no-overlay': !options.value.overlay,
    };
});

const url = computed(() => {
    let url = urlBase.value;
    if (sessionStorage.getItem('folder')) {
        folder.value = JSON.parse(sessionStorage.getItem('folder'));
    }
    if (folder.value.id !== '') {
        url += '?folder_id=' + folder.value.id;
    }
    return url;
});

const filteredItems = computed(() => {
    return data.value.models;
});

const path = computed(() => {
    return data.value.path;
});

const selectedFiles = computed(() => {
    return selectedItems.value.filter((item) => item.type !== 'f').sort((a, b) => b.name.localeCompare(a.name));
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
        data.value = await response.json();
    } catch (error) {
        alertify.error(error.message || t('Sorry, a network error occurred.'));
    }
    stopLoading();
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
function dragStart(item, event) {
    event.dataTransfer.setData('text', '');
    dragging.value = true;
    if (selectedItems.value.indexOf(item) === -1) {
        checkNone();
        selectedItems.value.push(item);
    }
}
function dragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
}
function dragEnd() {
    dragging.value = false;
}
function dragEnter(event) {
    if (event.target.classList.contains('filemanager-item-folder')) {
        event.target.classList.add('filemanager-item-over');
    }
}
function dragLeave(event) {
    event.target.classList.remove('filemanager-item-over');
}
async function drop(targetItem, event) {
    event.target.classList.remove('filemanager-item-over');
    dragging.value = false;

    const ids = [];
    selectedItems.value.forEach((item) => {
        ids.push(item.id);
    });

    if (targetItem.type !== 'f' || ids.indexOf(targetItem.id) !== -1) {
        return;
    }

    for (let i = selectedItems.value.length - 1; i >= 0; i--) {
        const draggedItem = selectedItems[i];
        const index = data.value.models.indexOf(draggedItem);
        data.value.models.splice(index, 1);
    }

    try {
        const response = await fetcher('/api/files/' + ids.join(), {
            method: 'PATCH',
            body: JSON.stringify({ folder_id: targetItem.id }),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        await fetchData();
    } catch (error) {
        alertify.error(error.message || t('Sorry, an error occurred.'));
    }

    checkNone();
}
async function newFolder(folderId) {
    const name = window.prompt(t('Enter a name for the new folder.'));
    if (!name) {
        return;
    }
    const data = {
        folder_id: folderId,
        type: 'f',
        name: name,
    };
    try {
        const response = await fetcher('/api/files', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        const responseData = await response.json();
        if (!response.ok) {
            throw new Error(responseData.message);
        }
        await fetchData();
    } catch (error) {
        alertify.error(error.message || t('Sorry, an error occurred.'));
    }
}
function check(item, $event) {
    $event.stopPropagation();
    const indexOfLastCheckedItem = data.value.models.indexOf(selectedItems.value[selectedItems.value.length - 1]);
    const index = selectedItems.value.indexOf(item);
    if (!($event.ctrlKey || $event.metaKey || $event.shiftKey)) {
        checkNone();
    }
    if (index !== -1 && ($event.metaKey || $event.ctrlKey)) {
        selectedItems.value.splice(index, 1);
    } else if (selectedItems.value.indexOf(item) === -1) {
        selectedItems.value.push(item);
    }
    if (index === -1) {
        if ($event.shiftKey) {
            const currentItemIndex = data.value.models.indexOf(item);
            data.value.models.forEach((item, index) => {
                if (currentItemIndex > indexOfLastCheckedItem) {
                    if (indexOfLastCheckedItem === -1) {
                        if (index <= currentItemIndex) {
                            selectedItems.value.push(item);
                        }
                    }
                    if (indexOfLastCheckedItem !== -1) {
                        if (index > indexOfLastCheckedItem && index < currentItemIndex) {
                            if (selectedItems.value.indexOf(item) === -1) {
                                selectedItems.value.push(item);
                            }
                        }
                    }
                }
                if (currentItemIndex < indexOfLastCheckedItem) {
                    if (indexOfLastCheckedItem !== -1) {
                        if (index < indexOfLastCheckedItem && index > currentItemIndex) {
                            if (selectedItems.value.indexOf(item) === -1) {
                                selectedItems.value.push(item);
                            }
                        }
                    }
                }
            });
        }
    }
}
async function moveToParentFolder() {
    if (!folder.value.id) {
        return;
    }

    const ids = [],
        models = selectedItems.value,
        number = models.length;

    if (selectedItems.value.length > deleteLimit.value) {
        alertify.error('Too much elements (max ' + deleteLimit.value + ' items.)');
        return false;
    }

    models.forEach((item) => {
        ids.push(item.id);
        const index = data.value.models.indexOf(item);
        data.value.models.splice(index, 1);
    });

    checkNone();

    startLoading();

    try {
        const response = await fetcher('/api/files/' + ids.join(), {
            method: 'PATCH',
            body: JSON.stringify({ folder_id: path.value[path.value.length - 2].id }),
        });
        const responseData = await response.json();
        if (!response.ok) {
            throw new Error(responseData.message);
        }
        if (responseData.number < number) {
            alertify.error(
                t('# files could not be moved.', number - responseData.number, {
                    count: number - responseData.number,
                }),
            );
        }
        if (responseData.number > 0) {
            alertify.success(
                t('# files moved.', responseData.number, {
                    count: responseData.number,
                }),
            );
        }
    } catch (error) {
        alertify.error(error.message || t('Sorry, an error occurred.'));
    }
    stopLoading();
}
function addSingleFile(item) {
    emitter.emit('fileAdded', item);
    const filepicker = document.getElementById('filepicker');
    const CKEditorCleanUpFuncNum = filepicker.dataset.CKEditorCleanUpFuncNum,
        CKEditorFuncNum = filepicker.dataset.CKEditorFuncNum;
    if (!!CKEditorFuncNum || !!CKEditorCleanUpFuncNum) {
        parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, item.url);
        parent.CKEDITOR.tools.callFunction(CKEditorCleanUpFuncNum);
    }
    closeModal();
}
function addSelectedFiles() {
    const files = [];

    if (selectedFiles.value.length === 0) {
        closeModal();
        return;
    }

    selectedFiles.value.forEach((file) => {
        files.push(file);
    });

    emitter.emit('filesAdded', selectedFiles.value);
    closeModal();
    checkNone();
}
function closeModal() {
    document.body.classList.remove('noscroll');
    options.value.open = false;
    options.value.modalIsInFront = false;
    window.ckEditorDialogBlured = false;
}
function switchView(viewType) {
    view.value = viewType;
    sessionStorage.setItem('view', JSON.stringify(viewType));
}
function openFolder(folderToOpen) {
    folder.value = folderToOpen;
    sessionStorage.setItem('folder', JSON.stringify(folder.value));
    fetchData();
    checkNone();
}
function onDoubleClick(item) {
    if (item.type === 'f') {
        openFolder(item);
        return;
    }
    if (props.modal) {
        if (options.value.multiple) {
            addSelectedFiles();
        } else {
            addSingleFile(item);
        }
    }
}
function checkNone() {
    selectedItems.value = [];
}
async function deleteSelected() {
    if (selectedItems.value.length > deleteLimit.value) {
        alertify.error(
            t('Impossible to delete more than # items in one go.', {
                deleteLimit: deleteLimit.value,
            }),
        );
        return false;
    }
    if (
        !window.confirm(
            t('Are you sure you want to delete # items?', selectedItems.value.length, {
                count: selectedItems.value.length,
            }),
        )
    ) {
        return false;
    }

    startLoading();
    const deletePromises = selectedItems.value.map(async (model) => {
        try {
            const response = await fetcher(urlBase.value + '/' + model.id, { method: 'DELETE' });
            if (!response.ok) {
                const responseData = await response.json();
                throw new Error(responseData.message);
            }
            return response;
        } catch (error) {
            alertify.error(t(error.message) || t('Sorry, an error occurred.'));
        }
    });

    const responses = await Promise.all(deletePromises);
    const successes = responses.filter((response) => response && response.ok);
    if (successes.length > 0) {
        alertify.success(
            t('# files deleted.', successes.length, {
                count: successes.length,
            }),
        );
    }
    checkNone();
    stopLoading();
    await fetchData();
}
</script>
