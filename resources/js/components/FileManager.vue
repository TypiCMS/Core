<template>
    <div class="filepicker" :class="classes" id="filepicker" ref="filepicker">
        <div class="wrapper">
            <div class="filemanager-header header">
                <a v-if="path.length > 1" class="btn-back" @click="openFolder(path[path.length - 2])" href="#">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span class="btn-back-label">
                        {{ path[path.length - 2].name }}
                    </span>
                </a>
                <h1 class="filemanager-title header-title" v-if="path.length > 0">{{ path[path.length - 1].name }}</h1>
                <div class="header-toolbar btn-toolbar">
                    <button class="btn btn-sm btn-light me-2" @click="newFolder(folder.id)" type="button">
                        <i class="bi bi-folder-fill text-black-50 me-1"></i>
                        {{ $t('New folder') }}
                    </button>
                    <div class="btn-group btn-group-sm me-2">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdown-action-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            {{ $t('Action') }}
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdown-action-button">
                            <button class="dropdown-item" type="button" @click="deleteSelected" :disabled="selectedItems.length === 0">
                                {{ $t('Delete') }}
                            </button>
                            <button class="dropdown-item" type="button" @click="moveToParentFolder()" :disabled="!folder.id || selectedFiles.length === 0">
                                {{ $t('Move to parent folder') }}
                            </button>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item" type="button" disabled="disabled">
                                {{
                                    $tc('# items selected', selectedItems.length, {
                                        count: selectedItems.length,
                                    })
                                }}
                            </button>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light" :class="{ active: view === 'grid' }" type="button" @click="switchView('grid')">
                            <i class="bi bi-grid-3x2-gap-fill text-black-50 me-1"></i>
                            {{ $t('Grid') }}
                        </button>
                        <button class="btn btn-light" :class="{ active: view === 'list' }" type="button" @click="switchView('list')">
                            <i class="bi bi-list-ul text-black-50 me-1"></i>
                            {{ $t('List') }}
                        </button>
                    </div>
                    <div class="d-flex align-items-center ms-2">
                        <div class="spinner-border spinner-border-sm text-dark" role="status" v-if="loading">
                            <span class="visually-hidden">{{ $t('Loading…') }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary header-btn-add ms-auto" id="upload-files-button" v-if="dropzone">
                        <i class="bi bi-cloud-upload-fill me-1"></i>
                        {{ $t('Upload files') }}
                    </button>
                </div>
            </div>

            <button class="filemanager-btn-close" type="button" v-if="this.modal" @click="closeModal">
                <i class="bi bi-x fs-3"></i>
            </button>

            <div class="content">
                <Dashboard
                    :uppy="uppy"
                    :props="{
                        inline: false,
                        trigger: '#upload-files-button',
                        proudlyDisplayPoweredByUppy: false,
                    }"
                    :plugins="['ImageEditor']"
                />
                <div @click="checkNone()" class="filemanager-list" :class="{ 'filemanager-view-list': view === 'list' }">
                    <div
                        class="filemanager-item filemanager-item-with-name filemanager-item-editable"
                        v-for="item in filteredItems"
                        @click="check(item, $event)"
                        :id="'item_' + item.id"
                        :class="{
                            'filemanager-item-selected': selectedItems.indexOf(item) !== -1,
                            'filemanager-item-folder': item.type === 'f',
                            'filemanager-item-file': item.type !== 'f',
                            'filemanager-item-dragging-source': dragging && selectedItems.indexOf(item) !== -1,
                        }"
                        draggable="true"
                        @drop="drop(item, $event)"
                        @dragstart="dragStart(item, $event)"
                        @dragover="dragOver($event)"
                        @dragenter="dragEnter($event)"
                        @dragleave="dragLeave($event)"
                        @dragend="dragEnd($event)"
                        @dblclick="onDoubleClick(item)"
                    >
                        <div class="filemanager-item-wrapper">
                            <div class="filemanager-item-icon" v-if="item.type === 'i'">
                                <div class="filemanager-item-image-wrapper">
                                    <img class="filemanager-item-image" :src="item.thumb_sm" />
                                </div>
                            </div>
                            <div class="filemanager-item-icon" :class="'filemanager-item-icon-' + item.type" v-else>
                                <i class="bi bi-file-earmark-music" v-if="item.type === 'a'"></i>
                                <i class="bi bi-file-earmark-play" v-if="item.type === 'v'"></i>
                                <i class="bi bi-file-earmark" v-if="item.type === 'd'"></i>
                                <i class="bi bi-folder" v-if="item.type === 'f'"></i>
                            </div>
                            <div class="filemanager-item-name">{{ item.name }}</div>
                            <a class="filemanager-item-editable-button" :href="'/admin/files/' + item.id + '/edit'">
                                <span class="filemanager-item-editable-button-icon"></span>
                                <span class="visually-hidden">{{ $t('Edit') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <button
                class="btn btn-success filemanager-btn-add btn-add-multiple"
                type="button"
                @click="addSelectedFiles()"
                id="add-selected-files-button"
                v-if="options.multiple"
                :disabled="selectedFiles.length < 1"
            >
                {{ $t('Add selected files') }}
            </button>

            <button
                class="btn btn-success filemanager-btn-add btn-add-single"
                type="button"
                @click="addSingleFile(selectedFiles[0])"
                id="add-selected-file-button"
                v-if="options.single"
                :disabled="selectedFiles.length !== 1"
            >
                {{ $t('Add selected file') }}
            </button>
        </div>
    </div>
</template>

<script>
import fetcher from '../admin/fetcher';
import { Dashboard } from '@uppy/vue';
import Uppy from '@uppy/core';
import XHRUpload from '@uppy/xhr-upload';
import ImageEditor from '@uppy/image-editor';
import DropTarget from '@uppy/drop-target';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import '@uppy/image-editor/dist/style.min.css';
import fr from '@uppy/locales/lib/fr_FR';
import nl from '@uppy/locales/lib/nl_NL';
import es from '@uppy/locales/lib/es_ES';

const uppyLocales = { fr, nl, es };

export default {
    components: {
        Dashboard,
    },
    props: {
        modal: {
            type: Boolean,
            default: true,
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
    },
    data() {
        return {
            loadingTimeout: null,
            dragging: false,
            loading: false,
            total: 0,
            view: 'grid',
            selectedItems: [],
            urlBase: '/api/files',
            locale: TypiCMS.content_locale,
            options: {
                modal: this.modal,
                dropzone: this.dropzone,
                multiple: this.multiple,
                single: this.single,
                open: this.open,
                overlay: this.overlay,
            },
            maxFilesize: window.TypiCMS.max_file_upload_size,
            folder: {
                id: '',
            },
            data: {
                models: [],
                path: [],
            },
        };
    },
    created() {
        this.fetchData();
    },
    mounted() {
        if (sessionStorage.getItem('view')) {
            this.view = JSON.parse(sessionStorage.getItem('view'));
        }
        window.EventBus.$on('openFilepickerForCKEditor', (options) => {
            document.documentElement.classList.add('noscroll');
            document.body.classList.add('noscroll');
            this.options = options;
        });
        this.$root.$on('openFilepicker', (options) => {
            document.documentElement.classList.add('noscroll');
            document.body.classList.add('noscroll');
            this.options = options;
        });
    },
    computed: {
        uppy() {
            return new Uppy({
                locale: uppyLocales[TypiCMS.locale],
                restrictions: {
                    maxFileSize: this.maxFilesize * 1024,
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
                    this.uppy.setFileMeta(file.id, {
                        folder_id: this.folder.id,
                    });
                })
                .on('complete', (result) => {
                    const fails = result.failed;
                    if (fails.length > 0) {
                        alertify.error(
                            this.$i18n.tc('# files could not be uploaded.', fails.length, {
                                count: fails.length,
                            })
                        );
                    }

                    const successes = result.successful;
                    if (successes.length > 0) {
                        alertify.success(
                            this.$i18n.tc('# files uploaded.', successes.length, {
                                count: successes.length,
                            })
                        );
                        successes.forEach((success) => {
                            this.data.models.push(success.response.body.model);
                            this.data.models.sort((a, b) => a.id - b.id);
                        });
                    }
                });
        },
        classes() {
            return {
                'filemanager-modal': this.options.modal,
                'filemanager-multiple': this.options.multiple,
                'filemanager-single': this.options.single,
                'filemanager-modal-open': this.options.open,
                'filemanager-modal-no-overlay': !this.options.overlay,
            };
        },
        url() {
            let url = this.urlBase;
            if (sessionStorage.getItem('folder')) {
                this.folder = JSON.parse(sessionStorage.getItem('folder'));
            }
            if (this.folder.id !== '') {
                url += '?folder_id=' + this.folder.id;
            }
            return url;
        },
        filteredItems() {
            return this.data.models;
        },
        path() {
            return this.data.path;
        },
        allChecked() {
            return this.filteredItems.length > 0 && this.filteredItems.length === this.selectedItems.length;
        },
        numberOfselectedItems() {
            return this.selectedItems.length;
        },
        selectedFiles() {
            return this.selectedItems.filter((item) => item.type !== 'f').sort((a, b) => b.name.localeCompare(a.name));
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
                this.data = await response.json();
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('Sorry, a network error occurred.'));
            }
            this.stopLoading();
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
        dragStart(item, event) {
            event.dataTransfer.setData('text', '');
            this.dragging = true;
            if (this.selectedItems.indexOf(item) === -1) {
                this.checkNone();
                this.selectedItems.push(item);
            }
        },
        dragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
        },
        dragEnd() {
            this.dragging = false;
        },
        dragEnter(event) {
            if (event.target.classList.contains('filemanager-item-folder')) {
                event.target.classList.add('filemanager-item-over');
            }
        },
        dragLeave(event) {
            event.target.classList.remove('filemanager-item-over');
        },
        async drop(targetItem, event) {
            event.target.classList.remove('filemanager-item-over');
            this.dragging = false;

            let ids = [];
            this.selectedItems.forEach((item) => {
                ids.push(item.id);
            });

            if (targetItem.type !== 'f' || ids.indexOf(targetItem.id) !== -1) {
                return;
            }

            for (let i = this.selectedItems.length - 1; i >= 0; i--) {
                const draggedItem = this.selectedItems[i];
                const index = this.data.models.indexOf(draggedItem);
                this.data.models.splice(index, 1);
            }

            let data = {
                folder_id: targetItem.id,
            };
            try {
                const response = await fetcher('/api/files/' + ids.join(), {
                    method: 'PATCH',
                    body: JSON.stringify(data),
                });
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message);
                }
                await this.fetchData();
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('Sorry, an error occurred.'));
            }

            this.checkNone();
        },
        async newFolder(folderId) {
            let name = window.prompt(this.$i18n.t('Enter a name for the new folder.'));
            if (!name) {
                return;
            }
            let data = {
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
                this.data.models.push(responseData.model);
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('Sorry, an error occurred.'));
            }
        },
        check(item, $event) {
            $event.stopPropagation();
            let indexOfLastCheckedItem = this.data.models.indexOf(this.selectedItems[this.selectedItems.length - 1]);
            let index = this.selectedItems.indexOf(item);
            if (!($event.ctrlKey || $event.metaKey || $event.shiftKey)) {
                this.checkNone();
            }
            if (index !== -1 && ($event.metaKey || $event.ctrlKey)) {
                this.selectedItems.splice(index, 1);
            } else if (this.selectedItems.indexOf(item) === -1) {
                this.selectedItems.push(item);
            }
            if (index === -1) {
                if ($event.shiftKey) {
                    let currentItemIndex = this.data.models.indexOf(item);
                    this.data.models.forEach((item, index) => {
                        if (currentItemIndex > indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem === -1) {
                                if (index <= currentItemIndex) {
                                    this.selectedItems.push(item);
                                }
                            }
                            if (indexOfLastCheckedItem !== -1) {
                                if (index > indexOfLastCheckedItem && index < currentItemIndex) {
                                    if (this.selectedItems.indexOf(item) === -1) {
                                        this.selectedItems.push(item);
                                    }
                                }
                            }
                        }
                        if (currentItemIndex < indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem !== -1) {
                                if (index < indexOfLastCheckedItem && index > currentItemIndex) {
                                    if (this.selectedItems.indexOf(item) === -1) {
                                        this.selectedItems.push(item);
                                    }
                                }
                            }
                        }
                    });
                }
            }
        },
        async moveToParentFolder() {
            if (!this.folder.id) {
                return;
            }

            let ids = [],
                models = this.selectedItems,
                number = models.length;

            if (this.selectedItems.length > this.deleteLimit) {
                alertify.error('Too much elements (max ' + this.deleteLimit + ' items.)');
                return false;
            }

            models.forEach((item) => {
                ids.push(item.id);
                const index = this.data.models.indexOf(item);
                this.data.models.splice(index, 1);
            });

            this.checkNone();

            this.startLoading();

            let data = {
                folder_id: this.path[this.path.length - 2].id,
            };
            try {
                const response = await fetcher('/api/files/' + ids.join(), {
                    method: 'PATCH',
                    body: JSON.stringify(data),
                });
                const responseData = await response.json();
                if (!response.ok) {
                    throw new Error(responseData.message);
                }
                if (responseData.number < number) {
                    alertify.error(
                        this.$i18n.tc('# files could not be moved.', number - responseData.number, {
                            count: number - responseData.number,
                        })
                    );
                }
                if (responseData.number > 0) {
                    alertify.success(
                        this.$i18n.tc('# files moved.', responseData.number, {
                            count: responseData.number,
                        })
                    );
                }
            } catch (error) {
                alertify.error(error.message || this.$i18n.t('Sorry, an error occurred.'));
            }
            this.stopLoading();
        },
        addSingleFile(item) {
            this.$root.$emit('fileAdded', item);
            const filepicker = document.getElementById('filepicker');
            const CKEditorCleanUpFuncNum = filepicker.dataset.CKEditorCleanUpFuncNum,
                CKEditorFuncNum = filepicker.dataset.CKEditorFuncNum;
            if (!!CKEditorFuncNum || !!CKEditorCleanUpFuncNum) {
                parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, item.url);
                parent.CKEDITOR.tools.callFunction(CKEditorCleanUpFuncNum);
            }
            this.closeModal();
        },
        addSelectedFiles() {
            let files = [];

            if (this.selectedFiles.length === 0) {
                this.closeModal();
                return;
            }

            this.selectedFiles.forEach((file) => {
                files.push(file);
            });

            this.$root.$emit('filesAdded', this.selectedFiles);
            this.closeModal();
            this.checkNone();
        },
        closeModal() {
            document.documentElement.classList.remove('noscroll');
            document.body.classList.remove('noscroll');
            this.options.open = false;
        },
        switchView(view) {
            this.view = view;
            sessionStorage.setItem('view', JSON.stringify(view));
        },
        openFolder(folder) {
            this.folder = folder;
            sessionStorage.setItem('folder', JSON.stringify(folder));
            this.fetchData();
            this.checkNone();
        },
        onDoubleClick(item) {
            if (item.type === 'f') {
                this.openFolder(item);
                return;
            }
            if (this.modal) {
                if (this.options.multiple) {
                    this.addSelectedFiles();
                } else {
                    this.addSingleFile(item);
                }
            }
        },
        checkNone() {
            this.selectedItems = [];
        },
        async deleteSelected() {
            const deleteLimit = 100;

            if (this.selectedItems.length > deleteLimit) {
                alertify.error(
                    this.$i18n.t('Impossible to delete more than # items in one go.', {
                        deleteLimit,
                    })
                );
                return false;
            }
            if (
                !window.confirm(
                    this.$i18n.tc('Are you sure you want to delete # items?', this.selectedItems.length, {
                        count: this.selectedItems.length,
                    })
                )
            ) {
                return false;
            }

            this.startLoading();
            const deletePromises = this.selectedItems.map(async (model) => {
                try {
                    const response = await fetcher(this.urlBase + '/' + model.id, { method: 'DELETE' });
                    if (!response.ok) {
                        const responseData = await response.json();
                        throw new Error(responseData.message);
                    }
                    return response;
                } catch (error) {
                    alertify.error(this.$i18n.tc(error.message) || this.$i18n.t('Sorry, an error occurred.'));
                }
            });

            const responses = await Promise.all(deletePromises);
            let successes = responses.filter((response) => response && response.ok);
            if (successes.length > 0) {
                alertify.success(
                    this.$i18n.tc('# files deleted.', successes.length, {
                        count: successes.length,
                    })
                );
            }
            this.checkNone();
            this.stopLoading();
            await this.fetchData();
        },
    },
};
</script>
