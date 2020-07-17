<template>
    <div class="mb-4">
        <input type="hidden" name="file_ids" :value="fileIds.join()" />
        <div class="form-group">
            <label>
                <span v-if="label">{{ label }}</span>
                <span v-else>{{ $t('Files') }}</span>
            </label>
            <p>
                <button class="btn btn-sm btn-secondary mr-2" @click="openFilepicker" type="button">
                    <i class="fa fa-plus text-white-50"></i> {{ $t('Add files') }}
                </button>
            </p>
        </div>

        <draggable class="filemanager" v-model="files" group="files" @start="drag = true" @end="drag = false">
            <div
                class="filemanager-item filemanager-item-with-name filemanager-item-file filemanager-item-removable"
                v-for="file in files"
                :id="'item_' + file.id"
                :key="file.id"
            >
                <div class="filemanager-item-wrapper">
                    <button class="filemanager-item-removable-button" @click="remove(file)" type="button">
                        <span class="fa fa-times"></span>
                    </button>
                    <div class="filemanager-item-icon" v-if="file.type === 'i'">
                        <div class="filemanager-item-image-wrapper">
                            <img
                                class="filemanager-item-image"
                                :src="file.thumb_sm"
                                :alt="file.alt_attribute_translated"
                            />
                        </div>
                    </div>
                    <div class="filemanager-item-icon" :class="'filemanager-item-icon-' + file.type" v-else></div>
                    <div class="filemanager-item-name">{{ file.name }}</div>
                </div>
            </div>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    components: {
        draggable,
    },
    props: {
        label: {
            type: String,
        },
        initFiles: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            files: this.initFiles,
            loading: false,
        };
    },
    mounted() {
        this.$root.$on('filesAdded', (files) => {
            for (var i = files.length - 1; i >= 0; i--) {
                if (this.files.find(({ id }) => id === files[i].id) === undefined) {
                    this.files.push(files[i]);
                }
            }
        });
    },
    computed: {
        fileIds() {
            let fileIds = [];
            for (var i = 0; i < this.files.length; i++) {
                fileIds.push(this.files[i].id);
            }
            return fileIds;
        },
    },
    methods: {
        openFilepicker() {
            let options = {
                open: true,
                multiple: true,
                overlay: true,
                single: false,
                modal: true,
            };
            this.$root.$emit('openFilepicker', options);
        },
        remove(file) {
            let index = this.files.indexOf(file);
            this.files.splice(index, 1);
        },
    },
};
</script>
