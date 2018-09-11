<template>

    <div class="wrapper">

        <button class="filepicker-btn-close" id="close-filepicker"><span class="fa fa-close"></span></button>

        <a class="btn btn-primary" id="uploaderAddButton" href="#" :title="$t('Add files')">
            <i class="fa fa-plus-circle"></i> {{ $t('Add files') }}
        </a>

        <h1>
            <span v-for="(folder, index) in path">
                <a v-if="path.length !== index+1" href="#" @click="handle(folder)">{{ folder.name }}</a>
                <span v-if="path.length === index+1">{{ folder.name }}</span>
                <span v-if="path.length !== index+1">/</span>
            </span>
        </h1>

        <div class="btn-toolbar">
            <button class="btn btn-light mr-2" @click="newFolder(folder.id)" type="button">
                <span class="fa fa-folder-o fa-fw"></span> {{ $t('New folder') }}
            </button>
            <div class="btn-group dropdown mr-2">
                <button class="btn btn-light dropdown-toggle"
                    :class="{disabled: !checkedItems.length}"
                    type="button"
                    id="dropdownMenu1"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="true">
                    {{ $t('Action') }}
                    <span class="caret"></span>
                    <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <a class="dropdown-item" @click="deleteChecked()" href="#">{{ $t('Delete') }}</a></li>
                    <a class="dropdown-item" :class="{disabled: !folder.id}" @click="moveToParentFolder()" href="#">
                        {{ $t('Move to parent folder') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item disabled" href="#">
                        {{ $tc('# items selected', checkedItems.length, { count: checkedItems.length }) }}
                    </a>
                </div>
            </div>
            <div class="btn-group">
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

<!--
        <div class="dropzone" dropzone id="dropzone" :folder-id="folder.id">
            <div class="dz-message">{{ $t('Click or drop files to upload') }}</div>
        </div>
 -->
        <div class="filemanager" @click="checkNone()" :class="{'filemanager-list': view === 'list'}">
            <div class="filemanager-item filemanager-item-with-name filemanager-item-editable"
                v-for="model in filteredItems"
                @click="check(model, $event)"
                :id="'item_'+model.id"
                :class="{
                    'filemanager-item-selected': checkedItems.indexOf(model) !== -1,
                    'filemanager-item-folder': model.type === 'f',
                    'filemanager-item-file': model.type !== 'f',
                    'filemanager-item-target': dragging && checkedItems.indexOf(model) !== -1,
                }"
                draggable="true"
                @drag="drag(model)"
                @drop="drop(model, $event)"
                @dragstart="dragStart(model, $event)"
                @dragover="dragOver($event)"
                @dragenter="dragEnter($event)"
                @dragleave="dragLeave($event)"
                @dragend="dragEnd($event)"
                @dblclick="handle(model)"
                >
                <div class="filemanager-item-wrapper">
                    <div class="filemanager-item-icon" v-if="model.type === 'i'">
                        <div class="filemanager-item-image-wrapper">
                            <img class="filemanager-item-image"
                                :src="model.thumb_sm"
                                :alt="model.alt_attribute_translated">
                        </div>
                    </div>
                    <div class="filemanager-item-icon" :class="'filemanager-item-icon-'+model.type" v-else></div>
                    <div class="filemanager-item-name">{{ model.name }}</div>
                    <a class="filemanager-item-editable-button" :href="'/admin/files/'+model.id+'/edit'">
                        <span class="fa fa-pencil"></span>
                    </a>
                </div>
            </div>
        </div>

        <button class="btn btn-success filepicker-btn-add btn-add-multiple"
            type="button"
            @click="addSelectedFiles()"
            id="btn-add-selected-files">
            {{ $t('Add selected files') }}
        </button>
        <button class="btn btn-success filepicker-btn-add btn-add-single"
            :disabled="checkedItems.length !== 1"
            type="button"
            @click="handle(checkedItems[0])"
            id="btn-add-selected-file">{{ $t('Add selected file') }}</button>

    </div>

</template>

<script>
import ItemListActions from './ItemListActions';

export default {
    components: {
        ItemListActions,
    },
    props: {
        urlBase: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            dragging: false,
            loading: false,
            total: 0,
            view: 'grid',
            checkedItems: [],
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
        // this.$on('drag-start', el => {
        //     console.log(el);
        //     console.log('drag start');
        // });
    },
    computed: {
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
            return this.filteredItems.length > 0 && this.filteredItems.length === this.checkedItems.length;
        },
        numberOfcheckedItems() {
            return this.checkedItems.length;
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
        dragStart(model, event) {
            event.dataTransfer.setData('text', '');
            this.dragging = true;
            if (this.checkedItems.indexOf(model) === -1) {
                this.checkedItems = [];
                this.checkedItems.push(model);
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
            if (event.target.classList.contains('filemanager-item-folder')) {
                event.target.classList.remove('filemanager-item-over');
            }
        },
        drag(model) {
            // console.log('drag');
        },
        drop(targetModel, event) {
            event.target.classList.remove('filemanager-item-over');
            this.dragging = false;

            let ids = [];
            this.checkedItems.forEach(model => {
                ids.push(model.id);
            });

            if (targetModel.type !== 'f' || ids.indexOf(targetModel.id) !== -1) {
                return;
            }

            for (var i = this.checkedItems.length - 1; i >= 0; i--) {
                let draggedModel = this.checkedItems[i];
                var index = this.data.models.indexOf(draggedModel);
                this.data.models.splice(index, 1);
            }

            let data = {
                folder_id: targetModel.id,
            };

            axios
                .patch('/admin/files/' + ids.join(), data)
                .then(response => {})
                .catch(error => {
                    alertify.error('Error ' + error.status + ' ' + error.statusText);
                });

            this.checkedItems = [];
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
        check(model, $event) {
            $event.stopPropagation();
            let indexOfLastCheckedItem = this.data.models.indexOf(this.checkedItems[this.checkedItems.length - 1]);
            let index = this.checkedItems.indexOf(model);
            if (!($event.ctrlKey || $event.metaKey || $event.shiftKey)) {
                this.checkedItems = [];
            }
            if (index !== -1 && ($event.metaKey || $event.ctrlKey)) {
                this.checkedItems.splice(index, 1);
            } else if (this.checkedItems.indexOf(model) === -1) {
                this.checkedItems.push(model);
            }
            if (index === -1) {
                if ($event.shiftKey) {
                    let currentItemIndex = this.data.models.indexOf(model);
                    this.data.models.forEach((model, index) => {
                        if (currentItemIndex > indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem === -1) {
                                if (index <= currentItemIndex) {
                                    this.checkedItems.push(model);
                                }
                            }
                            if (indexOfLastCheckedItem !== -1) {
                                if (index > indexOfLastCheckedItem && index < currentItemIndex) {
                                    if (this.checkedItems.indexOf(model) === -1) {
                                        this.checkedItems.push(model);
                                    }
                                }
                            }
                        }
                        if (currentItemIndex < indexOfLastCheckedItem) {
                            if (indexOfLastCheckedItem !== -1) {
                                if (index < indexOfLastCheckedItem && index > currentItemIndex) {
                                    if (this.checkedItems.indexOf(model) === -1) {
                                        this.checkedItems.push(model);
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
                models = this.checkedItems,
                number = models.length;

            if (this.checkedItems.length > this.deleteLimit) {
                alertify.error('Too much elements (max ' + this.deleteLimit + ' items.)');
                return false;
            }

            models.forEach(model => {
                ids.push(model.id);
                var index = this.data.models.indexOf(model);
                this.data.models.splice(index, 1);
            });

            this.checkedItems = [];

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
        remove(model) {
            // var segments = $location.absUrl().split('?')[0].split('/').reverse(),
            //     modelId = segments[1],
            //     module = segments[2],
            //     index = this.model.models.indexOf(model);
            // this.model.models.splice(index, 1);
            // this.loading = true;
            // axios.patch('/admin/' + module + '/' + modelId, {remove: model.id}).then(response => {
            //     this.loading = false;
            // }, function (reason) {
            //     this.loading = false;
            //     alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            // });
        },
        deleteChecked() {
            // var ids = [],
            //     models = this.checkedItems,
            //     number = models.length;
            // if (this.checkedItems.length > this.deleteLimit) {
            //     alertify.error('Impossible to delete more than ' + this.deleteLimit + ' items in one go.');
            //     return false;
            // }
            // if (!window.confirm('Are you sure you want to delete ' + number + ' items?')) {
            //     return false;
            // }
            // models.forEach(model => {
            //     ids.push(model.id);
            // });
            // this.loading = true;
            // axios.delete('/admin/files/'+ids.join()).then(response => {
            //     this.loading = false;
            //     if (response.data.number === 0) {
            //         alertify.error(response.data.message);
            //     } else if (response.data.number < number) {
            //         alertify.error((number - response.data.number) + ' items could not be deleted.');
            //     }
            //     if (response.data.number === number) {
            //         alertify.success(response.data.number + ' items deleted.');
            //         models.forEach(model => {
            //             var index = this.data.models.indexOf(model);
            //             this.data.models.splice(index, 1);
            //         });
            //         this.checkedItems = [];
            //     }
            // }, function (reason) {
            //     this.loading = false;
            //     alertify.error('Error ' + reason.status + ' ' + reason.statusText);
            // });
        },
        addSelectedFiles() {
            var ids = [],
                models = this.checkedItems,
                data = {},
                segments = $location
                    .absUrl()
                    .split('?')[0]
                    .split('/')
                    .reverse(),
                modelId = segments[1],
                module = segments[2];

            if (models.length === 0) {
                $('html, body').removeClass('noscroll');
                $('#filepicker').removeClass('filepicker-modal-open');
                return;
            }

            models.forEach(model => {
                ids.push(model.id);
            });
            data.files = ids;

            axios
                .patch('/admin/' + module + '/' + modelId, data)
                .then(response => {
                    this.checkedItems = [];

                    // $rootScope.$broadcast('filesAdded', response.data.models);
                    $('html, body').removeClass('noscroll');
                    $('#filepicker').removeClass('filepicker-modal-open');

                    if (response.data.number === 0) {
                        alertify.error(response.data.message);
                    } else {
                        alertify.success(response.data.message);
                    }
                })
                .catch(reason => {
                    console.log(reason);
                    alertify.error('Error ' + reason.status + ' ' + reason.statusText);
                });
        },
        switchView(view) {
            this.view = view;
            sessionStorage.setItem('view', JSON.stringify(view));
        },
        handle(model) {
            if (model.type === 'f') {
                this.folder = model;
                sessionStorage.setItem('folder', JSON.stringify(model));
                this.fetchData();
                this.checkedItems = [];
            } else {
                var CKEditorCleanUpFuncNum = $('#filepicker').data('CKEditorCleanUpFuncNum'),
                    CKEditorFuncNum = $('#filepicker').data('CKEditorFuncNum');
                if (!!CKEditorFuncNum || !!CKEditorCleanUpFuncNum) {
                    parent.CKEDITOR.tools.callFunction(CKEditorFuncNum, '/storage/' + model.path);
                    parent.CKEDITOR.tools.callFunction(CKEditorCleanUpFuncNum);
                } else {
                    // $rootScope.$broadcast('fileAdded', model);
                    $('html, body').removeClass('noscroll');
                    $('#filepicker').removeClass('filepicker-modal-open');
                }
            }
        },
        addSelectedFile() {
            // $rootScope.$broadcast('fileAdded', this.checkedItems[0]);
            $('html, body').removeClass('noscroll');
            $('#filepicker').removeClass('filepicker-modal-open');
        },
        checkAll() {
            this.checkedItems = this.filteredItems;
        },
        checkNone() {
            this.checkedItems = [];
        },
        destroy() {
            this.data.current_page = 1;
            const deleteLimit = 100;

            if (this.checkedItems.length > deleteLimit) {
                alertify.error(this.$i18n.t('Impossible to delete more than # items in one go.', { deleteLimit }));
                return false;
            }
            if (
                !window.confirm(
                    this.$i18n.tc('Are you sure you want to delete # items?', this.numberOfcheckedItems, {
                        count: this.numberOfcheckedItems,
                    })
                )
            ) {
                return false;
            }

            this.loading = true;

            axios
                .all(this.checkedItems.map(model => axios.delete(this.urlBase + '/' + model.id)))
                .then(responses => {
                    let successes = responses.filter(response => response.data.error === false);
                    this.loading = false;
                    alertify.success(
                        this.$i18n.tc('# items deleted', successes.length, {
                            count: successes.length,
                        })
                    );
                    this.fetchData();
                    this.checkedItems = [];
                })
                .catch(error => {
                    alertify.error(error.response.data.message || this.$i18n.t('Sorry, an error occurred.'));
                });
        },
    },
};
</script>
