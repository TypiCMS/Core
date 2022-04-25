<template>
    <div class="filepicker" :class="classes" id="filepicker" ref="filepicker">
        <div class="wrapper">
            <div class="filemanager-header header">
                <a v-if="path.length > 1" class="btn-back" @click="openFolder(path[path.length - 2])" href="#">
                    <svg
                        class="btn-back-icon"
                        height="1em"
                        viewBox="0 0 24 24"
                        width="1em"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill="currentColor"
                            d="m16.133.427-11.572 11.573 11.572 11.573 3.306-3.311-8.267-8.262 8.267-8.267z"
                            fill-rule="evenodd"
                        />
                    </svg>
                    <span class="btn-back-label">
                        {{ path[path.length - 2].name }}
                    </span>
                </a>
                <h1 class="filemanager-title header-title" v-if="path.length > 0">{{ path[path.length - 1].name }}</h1>
                <div class="header-toolbar btn-toolbar">
                    <button class="btn btn-sm btn-secondary me-2" @click="newFolder(folder.id)" type="button">
                        <svg
                            class="me-1 text-muted"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3zm-8.322.12C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139z"
                            />
                        </svg>
                        {{ $t('New folder') }}
                    </button>
                    <div class="btn-group btn-group-sm me-2">
                        <button
                            class="btn btn-secondary dropdown-toggle"
                            :class=""
                            type="button"
                            id="dropdown-action-button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="true"
                        >
                            {{ $t('Action') }}
                        </button>

                        <div class="dropdown-menu" aria-labelledby="dropdown-action-button">
                            <button
                                class="dropdown-item"
                                type="button"
                                @click="deleteSelected"
                                :disabled="selectedItems.length === 0"
                            >
                                {{ $t('Delete') }}
                            </button>
                            <button
                                class="dropdown-item"
                                type="button"
                                @click="moveToParentFolder()"
                                :disabled="!folder.id || selectedFiles.length === 0"
                            >
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
                        <button
                            class="btn btn-secondary"
                            :class="{ active: view === 'grid' }"
                            type="button"
                            @click="switchView('grid')"
                        >
                            <svg
                                class="text-muted"
                                width="1em"
                                height="1em"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M1 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4zM1 9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V9z"
                                />
                            </svg>
                            {{ $t('Grid') }}
                        </button>
                        <button
                            class="btn btn-secondary"
                            :class="{ active: view === 'list' }"
                            type="button"
                            @click="switchView('list')"
                        >
                            <svg
                                class="text-muted"
                                width="1em"
                                height="1em"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"
                                />
                            </svg>
                            {{ $t('List') }}
                        </button>
                    </div>
                    <div class="d-flex align-items-center ms-2">
                        <div class="spinner-border spinner-border-sm text-dark" role="status" v-if="loading">
                            <span class="visually-hidden">{{ $t('Loading…') }}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary header-btn-add ms-auto"
                        id="upload-files-button"
                        v-if="dropzone"
                    >
                        <svg
                            class="me-1"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H7.5V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11h4.188C14.502 11 16 9.57 16 7.773c0-1.636-1.242-2.969-2.834-3.194C12.923 1.999 10.69 0 8 0zm-.5 14.5V11h1v3.5a.5.5 0 0 1-1 0z"
                            />
                        </svg>

                        {{ $t('Upload files') }}
                    </button>
                </div>
            </div>

            <button class="filemanager-btn-close" type="button" v-if="this.modal" @click="closeModal">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 1792 1792"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"
                    />
                </svg>
            </button>

            <div class="content">
                <vue-dropzone
                    id="dropzone"
                    ref="dropzone"
                    :options="dropOptions"
                    @vdropzone-success="dropzoneSuccess"
                    @vdropzone-sending="dropzoneSending"
                    @vdropzone-complete="dropzoneComplete"
                    @vdropzone-error="dropzoneError"
                    v-if="dropzone"
                >
                </vue-dropzone>

                <div
                    @click="checkNone()"
                    class="filemanager-list"
                    :class="{ 'filemanager-view-list': view === 'list' }"
                >
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
                                    <img
                                        class="filemanager-item-image"
                                        :src="item.thumb_sm"
                                        :alt="item.alt_attribute[locale]"
                                    />
                                </div>
                            </div>
                            <div class="filemanager-item-icon" :class="'filemanager-item-icon-' + item.type" v-else>
                                <svg
                                    v-if="item.type === 'a'"
                                    width="80px"
                                    height="80px"
                                    viewBox="0 0 16 16"
                                    class="bi bi-file-earmark-music"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M4 0h5.5v1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h1V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"
                                    />
                                    <path d="M9.5 3V0L14 4.5h-3A1.5 1.5 0 0 1 9.5 3z" />
                                    <path
                                        fill-rule="evenodd"
                                        d="M9.757 5.67A1 1 0 0 1 11 6.64v1.75l-2 .5v3.61c0 .495-.301.883-.662 1.123C7.974 13.866 7.499 14 7 14c-.5 0-.974-.134-1.338-.377-.36-.24-.662-.628-.662-1.123s.301-.883.662-1.123C6.026 11.134 6.501 11 7 11c.356 0 .7.068 1 .196V6.89a1 1 0 0 1 .757-.97l1-.25z"
                                    />
                                </svg>
                                <svg
                                    v-if="item.type === 'v'"
                                    width="80px"
                                    height="80px"
                                    viewBox="0 0 16 16"
                                    class="bi bi-file-earmark-play-fill"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2 2a2 2 0 0 1 2-2h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm7.5 1.5v-2l3 3h-2a1 1 0 0 1-1-1zM6 6.883v4.234a.5.5 0 0 0 .757.429l3.528-2.117a.5.5 0 0 0 0-.858L6.757 6.454a.5.5 0 0 0-.757.43z"
                                    />
                                </svg>
                                <svg
                                    v-if="item.type === 'd'"
                                    width="80px"
                                    height="80px"
                                    viewBox="0 0 16 16"
                                    class="bi bi-file-earmark"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M4 0h5.5v1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h1V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"
                                    />
                                    <path d="M9.5 3V0L14 4.5h-3A1.5 1.5 0 0 1 9.5 3z" />
                                </svg>
                                <svg
                                    v-if="item.type === 'f'"
                                    width="80px"
                                    height="80px"
                                    viewBox="0 0 16 16"
                                    class="bi bi-folder"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M9.828 4a3 3 0 0 1-2.12-.879l-.83-.828A1 1 0 0 0 6.173 2H2.5a1 1 0 0 0-1 .981L1.546 4h-1L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3v1z"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        d="M13.81 4H2.19a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zM2.19 3A2 2 0 0 0 .198 5.181l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H2.19z"
                                    />
                                </svg>
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
import vueDropzone from 'vue2-dropzone';

export default {
    components: {
        vueDropzone,
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
            baseUrl: '/api/files',
            locale: TypiCMS.content_locale,
            options: {
                modal: this.modal,
                dropzone: this.dropzone,
                multiple: this.multiple,
                single: this.single,
                open: this.open,
                overlay: this.overlay,
            },
            dropOptions: {
                clickable: ['#upload-files-button'],
                url: '/api/files',
                dictDefaultMessage: this.$i18n.t('Drop to upload.'),
                acceptedFiles: [
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
                ].join(),
                timeout: null,
                maxFilesize: 60,
                paramName: 'name',
                headers: {
                    Authorization: 'Bearer ' + document.head.querySelector('meta[name="api-token"]').content,
                },
            },
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
            $('html, body').addClass('noscroll');
            this.options = options;
        });
        this.$root.$on('openFilepicker', (options) => {
            $('html, body').addClass('noscroll');
            this.options = options;
        });
    },
    computed: {
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
            let url = this.baseUrl;
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
        fetchData() {
            this.startLoading();
            axios
                .get(this.url)
                .then((response) => {
                    this.data = response.data;
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
        dropzoneSending(file, xhr, formData) {
            this.startLoading();
            formData.append('folder_id', this.folder.id);
            for (var i = TypiCMS.locales.length - 1; i >= 0; i--) {
                formData.append('title[' + TypiCMS.locales[i].short + ']', '');
                formData.append('description[' + TypiCMS.locales[i].short + ']', '');
                formData.append('alt_attribute[' + TypiCMS.locales[i].short + ']', '');
            }
        },
        dropzoneSuccess(file, response) {
            window.setTimeout(() => {
                $(file.previewElement).fadeOut('fast', () => {
                    this.$refs.dropzone.removeFile(file);
                    this.data.models.push(response.model);
                    this.data.models.sort((a, b) => a.id - b.id);
                });
            }, 1000);
        },
        dropzoneComplete() {
            if (
                this.$refs.dropzone.getUploadingFiles().length === 0 &&
                this.$refs.dropzone.getQueuedFiles().length === 0
            ) {
                setTimeout(() => {
                    this.stopLoading();
                }, 1000);
            }
        },
        dropzoneError(file, message, xhr) {
            let errorMessage = '';
            if (typeof message.errors !== 'undefined') {
                errorMessage = Object.values(message.errors)[0];
            } else {
                errorMessage = message.message;
            }
            file.previewElement.querySelectorAll('.dz-error-message span')[0].textContent = errorMessage;
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
        dragEnd(event) {
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
        drop(targetItem, event) {
            event.target.classList.remove('filemanager-item-over');
            this.dragging = false;

            let ids = [];
            this.selectedItems.forEach((item) => {
                ids.push(item.id);
            });

            if (targetItem.type !== 'f' || ids.indexOf(targetItem.id) !== -1) {
                return;
            }

            for (var i = this.selectedItems.length - 1; i >= 0; i--) {
                let draggedItem = this.selectedItems[i];
                var index = this.data.models.indexOf(draggedItem);
                this.data.models.splice(index, 1);
            }

            let data = {
                folder_id: targetItem.id,
            };

            axios
                .patch('/api/files/' + ids.join(), data)
                .then((response) => {
                    this.fetchData();
                })
                .catch((error) => {
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });

            this.checkNone();
        },
        newFolder(folderId) {
            let name = window.prompt(this.$i18n.t('Enter a name for the new folder.'));
            if (!name) {
                return;
            }
            let data = {
                folder_id: folderId,
                type: 'f',
                name: name,
                title: {},
                description: {},
                alt_attribute: {},
            };
            for (var i = TypiCMS.locales.length - 1; i >= 0; i--) {
                data['title'][TypiCMS.locales[i].short] = null;
                data['description'][TypiCMS.locales[i].short] = null;
                data['alt_attribute'][TypiCMS.locales[i].short] = null;
            }

            axios
                .post('/api/files', data)
                .then((response) => {
                    this.data.models.push(response.data.model);
                })
                .catch((error) => {
                    alertify.error(error.response.data.message || this.$i18n.t('An error occurred.'));
                });
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
        moveToParentFolder() {
            if (!this.folder.id) {
                return;
            }

            var ids = [],
                models = this.selectedItems,
                number = models.length;

            if (this.selectedItems.length > this.deleteLimit) {
                alertify.error('Too much elements (max ' + this.deleteLimit + ' items.)');
                return false;
            }

            models.forEach((item) => {
                ids.push(item.id);
                var index = this.data.models.indexOf(item);
                this.data.models.splice(index, 1);
            });

            this.checkNone();

            this.startLoading();

            let data = {
                folder_id: this.path[this.path.length - 2].id,
            };

            axios
                .patch('/api/files/' + ids.join(), data)
                .then((response) => {
                    this.stopLoading();
                    if (response.data.number < number) {
                        alertify.error(
                            this.$i18n.tc('# files could not be moved.', number - response.data.number, {
                                count: number - response.data.number,
                            })
                        );
                    }
                    if (response.data.number > 0) {
                        alertify.success(
                            this.$i18n.tc('# files moved.', response.data.number, {
                                count: response.data.number,
                            })
                        );
                    }
                })
                .catch((error) => {
                    this.stopLoading();
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });
        },
        addSingleFile(item) {
            this.$root.$emit('fileAdded', item);
            var CKEditorCleanUpFuncNum = $('#filepicker').data('CKEditorCleanUpFuncNum'),
                CKEditorFuncNum = $('#filepicker').data('CKEditorFuncNum');
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
            $('html, body').removeClass('noscroll');
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
        deleteSelected() {
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

            axios
                .all(
                    this.selectedItems.map((item) =>
                        axios
                            .delete(this.baseUrl + '/' + item.id)
                            .catch((error) =>
                                alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'))
                            )
                    )
                )
                .then((responses) => {
                    let successes = responses.filter((response) => response.statusText === 'OK');
                    if (successes.length > 0) {
                        alertify.success(
                            this.$i18n.tc('# items deleted', successes.length, {
                                count: successes.length,
                            })
                        );
                    }
                    this.stopLoading();
                    this.checkNone();
                    this.fetchData();
                });
        },
    },
};
</script>
