<template>

    <div class="filepicker" :class="classes" id="filepicker">

        <div class="wrapper">

            <div class="filepicker-header">
                <h2 class="filepicker-title">
                    <span v-for="(folder, index) in path">
                        <a v-if="path.length !== index+1" href="#" @click="openFolder(folder)">{{ folder.name }}</a>
                        <span v-if="path.length === index+1">{{ folder.name }}</span>
                        <span v-if="path.length !== index+1">/</span>
                    </span>
                </h2>
                <button type="button" class="btn btn-sm btn-primary mr-2" id="btnAddFiles">
                    <i class="fa fa-upload text-white-50"></i> {{ $t('Upload files') }}
                </button>
            </div>

            <button class="filepicker-btn-close" id="close-filepicker"><span class="fa fa-close"></span></button>

            <div class="btn-toolbar">
                <button class="btn btn-sm btn-light mr-2" @click="newFolder(folder.id)" type="button">
                    <span class="fa fa-folder-o fa-fw"></span> {{ $t('New folder') }}
                </button>
                <div class="btn-group btn-group-sm dropdown mr-2">
                    <button class="btn btn-light dropdown-toggle"
                        :class="{disabled: !selectedItems.length}"
                        type="button"
                        id="dropdownMenu1"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="true">
                        {{ $t('Action') }}
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <a class="dropdown-item" @click="deleteSelected" href="#">{{ $t('Delete') }}</a></li>
                        <a class="dropdown-item" :class="{disabled: !folder.id}" @click="moveToParentFolder()" href="#">
                            {{ $t('Move to parent folder') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item disabled" href="#">
                            {{ $tc('# items selected', selectedItems.length, { count: selectedItems.length }) }}
                        </a>
                    </div>
                </div>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-light"
                        :class="{active: view === 'grid'}"
                        @click="switchView('grid')">
                        <span class="fa fa-fw fa-th"></span> Grid
                    </button>
                    <button type="button" class="btn btn-light"
                        :class="{active: view === 'list'}"
                        @click="switchView('list')">
                        <span class="fa fa-fw fa-bars"></span> List
                    </button>
                </div>
                <div class="d-flex align-items-center ml-2">
                    <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
                </div>
            </div>

            <vue-dropzone
                id="dropzone"
                ref="dropzone"
                :options="dropOptions"
                @vdropzone-success="dropzoneSuccess"
                @vdropzone-sending="dropzoneSending"
                >
            </vue-dropzone>

            <div @click="checkNone()" class="filemanager" :class="{'filemanager-list': view === 'list'}">
                <div class="filemanager-item filemanager-item-with-name filemanager-item-editable"
                    v-for="item in filteredItems"
                    @click="check(item, $event)"
                    :id="'item_'+item.id"
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
                    @dblclick="item.type === 'f' ? openFolder(item) : onDoubleClick(item)"
                    >
                    <div class="filemanager-item-wrapper">
                        <div class="filemanager-item-icon" v-if="item.type === 'i'">
                            <div class="filemanager-item-image-wrapper">
                                <img class="filemanager-item-image"
                                    :src="item.thumb_sm"
                                    :alt="item.alt_attribute_translated">
                            </div>
                        </div>
                        <div class="filemanager-item-icon" :class="'filemanager-item-icon-'+item.type" v-else></div>
                        <div class="filemanager-item-name">{{ item.name }}</div>
                        <a class="filemanager-item-editable-button" :href="'/admin/files/'+item.id+'/edit'">
                            <span class="fa fa-pencil"></span>
                        </a>
                    </div>
                </div>
            </div>

            <button class="btn btn-success filepicker-btn-add btn-add-multiple"
                type="button"
                :disabled="selectedFiles.length < 1"
                @click="addSelectedFiles()"
                id="btn-add-selected-files"
            >
                {{ $t('Add selected files') }}
            </button>

            <button class="btn btn-success filepicker-btn-add btn-add-single"
                :disabled="selectedFiles.length !== 1"
                type="button"
                @click="addSingleFile(selectedFiles[0])"
                id="btn-add-selected-file"
            >
                {{ $t('Add selected file') }}
            </button>

        </div>

    </div>

</template>

<script>
import ItemListActions from './ItemListActions';
import vueDropzone from 'vue2-dropzone';

export default {
    components: {
        ItemListActions,
        vueDropzone,
    },
    props: {
        options: {
            type: Object,
            required: false,
            default: {
                modal: false,
                dropzone: true,
                multiple: false,
            },
        },
        relatedTable: {
            type: String,
            required: true,
        },
        relatedId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            dragging: false,
            loading: false,
            total: 0,
            view: 'grid',
            selectedItems: [],
            baseUrl: '/api/files',
            dropOptions: {
                clickable: ['#btnAddFiles', '#dropzone'],
                url: '/admin/files',
                dictDefaultMessage: this.$i18n.t('Click or drop files to upload'),
                acceptedFiles: [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                    'application/vnd.openxmlformats-officedocument.presentationml.slide',
                    'application/msword', // doc
                    'application/vnd.ms-powerpoint', // ppt
                    'application/vnd.ms-excel', // xls
                    'application/pdf',
                    'application/postscript',
                    'application/zip',
                    'image/tiff',
                    'image/jpeg',
                    'image/gif',
                    'image/png',
                    'image/bmp',
                    'image/gif',
                ].join(),
                timeout: null,
                maxFilesize: 60,
                paramName: 'name',
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
        this.$root.$on('openFilepicker', () => {
            $('html, body').addClass('noscroll');
            $('#filepicker')
                .addClass('filepicker-modal-open')
                .addClass('filepicker-multiple');
        });
    },
    computed: {
        classes() {
            return {
                'filepicker-modal': this.options.modal,
                'filepicker-no-dropzone': !this.options.dropzone,
                'filepicker-multiple': this.options.multiple,
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
            return this.selectedItems.filter(item => item.type !== 'f');
        },
    },
    methods: {
        fetchData() {
            this.loading = true;
            axios
                .get(this.url)
                .then(response => {
                    this.data = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    alertify.error(
                        error.response.data.message || this.$i18n.t('An error occurred with the data fetch.')
                    );
                });
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
        dropzoneSending(file, xhr, formData) {
            formData.append('_token', document.head.querySelector('meta[name="csrf-token"]').content);
            for (var i = TypiCMS.locales.length - 1; i >= 0; i--) {
                formData.append('folder_id', this.folder.id);
                formData.append('description[' + TypiCMS.locales[i] + ']', '');
                formData.append('alt_attribute[' + TypiCMS.locales[i] + ']', '');
            }
        },
        dragStart(item, event) {
            event.dataTransfer.setData('text', '');
            this.dragging = true;
            if (this.selectedItems.indexOf(item) === -1) {
                this.selectedItems = [];
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
            this.selectedItems.forEach(item => {
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
                .patch('/admin/files/' + ids.join(), data)
                .then(response => {})
                .catch(error => {
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });

            this.selectedItems = [];
        },
        newFolder(folderId) {
            let name = window.prompt('What is the name of the new folder?');
            if (!name) {
                return;
            }
            let data = {
                folder_id: folderId,
                type: 'f',
                name: name,
                description: {},
                alt_attribute: {},
            };

            axios
                .post('/admin/files', data)
                .then(response => {
                    this.data.models.push(response.data.model);
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('An error occurred.'));
                });
        },
        check(item, $event) {
            $event.stopPropagation();
            let indexOfLastCheckedItem = this.data.models.indexOf(this.selectedItems[this.selectedItems.length - 1]);
            let index = this.selectedItems.indexOf(item);
            if (!($event.ctrlKey || $event.metaKey || $event.shiftKey)) {
                this.selectedItems = [];
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

            models.forEach(item => {
                ids.push(item.id);
                var index = this.data.models.indexOf(item);
                this.data.models.splice(index, 1);
            });

            this.selectedItems = [];

            this.loading = true;

            let data = {
                folder_id: this.path[this.path.length - 2].id,
            };

            axios
                .patch('/admin/files/' + ids.join(), data)
                .then(response => {
                    this.loading = false;
                    if (response.data.number < number) {
                        alertify.error(number - response.data.number + ' items could not be moved.');
                    }
                    if (response.data.number > 0) {
                        alertify.success(response.data.number + ' items moved.');
                    }
                })
                .catch(error => {
                    this.loading = false;
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });
        },
        addSelectedFiles() {
            let ids = [],
                data = {};

            if (this.selectedFiles.length === 0) {
                $('html, body').removeClass('noscroll');
                $('#filepicker').removeClass('filepicker-modal-open');
                return;
            }

            this.selectedFiles.forEach(file => {
                ids.push(file.id);
            });
            data.files = ids;

            axios
                .patch('/admin/' + this.relatedTable + '/' + this.relatedId, data)
                .then(response => {
                    this.selectedItems = [];
                    this.$root.$emit('filesAdded', response.data.models);
                    $('html, body').removeClass('noscroll');
                    $('#filepicker').removeClass('filepicker-modal-open');

                    if (response.data.number === 0) {
                        alertify.error(response.data.message);
                    } else {
                        alertify.success(response.data.message);
                    }
                })
                .catch(error => {
                    console.log(error);
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });
        },
        switchView(view) {
            this.view = view;
            sessionStorage.setItem('view', JSON.stringify(view));
        },
        openFolder(folder) {
            this.folder = folder;
            sessionStorage.setItem('folder', JSON.stringify(folder));
            this.fetchData();
            this.selectedItems = [];
        },
        onDoubleClick(item) {
            if ($('#filepicker').hasClass('filepicker-multiple')) {
                this.addSelectedFiles();
            } else {
                this.addSingleFile(item);
            }
        },
        addSingleFile(item) {
            var CKEditorCleanUpFuncNum = $('#filepicker').data('CKEditorCleanUpFuncNum'),
                CKEditorFuncNum = $('#filepicker').data('CKEditorFuncNum');
            if (!!CKEditorFuncNum || !!CKEditorCleanUpFuncNum) {
                parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, '/storage/' + item.path);
                parent.CKEDITOR.tools.callFunction(CKEditorCleanUpFuncNum);
            } else {
                // this.$root.$emit('fileAdded', item);
                $('html, body').removeClass('noscroll');
                $('#filepicker').removeClass('filepicker-modal-open');
            }
        },
        checkNone() {
            this.selectedItems = [];
        },
        deleteSelected() {
            this.data.current_page = 1;
            const deleteLimit = 100;

            for (let item of this.selectedItems) {
                if (item.children.length > 0) {
                    alertify.error(this.$i18n.t('A non-empty folder cannot be deleted.'));
                    return false;
                }
            }

            if (this.selectedItems.length > deleteLimit) {
                alertify.error(this.$i18n.t('Impossible to delete more than # items in one go.', { deleteLimit }));
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

            this.loading = true;

            axios
                .all(this.selectedItems.map(item => axios.delete(this.baseUrl + '/' + item.id)))
                .then(responses => {
                    let successes = responses.filter(response => response.data.error === false);
                    this.loading = false;
                    alertify.success(
                        this.$i18n.tc('# items deleted', successes.length, {
                            count: successes.length,
                        })
                    );
                    this.fetchData();
                    this.selectedItems = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
    },
};
</script>
