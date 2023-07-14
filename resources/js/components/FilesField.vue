<template>
    <div>
        <input type="hidden" name="file_ids" :value="fileIds.join()" />
        <div class="mb-3">
            <p class="form-label mb-2">{{ $t(label) }}</p>
            <p>
                <button class="filemanager-field-btn-add" @click="openFilepicker" type="button">
                    <i class="bi bi-plus-circle-fill text-white-50 me-1"></i>
                    {{ $t('Add files') }}
                </button>
            </p>
        </div>

        <draggable class="filemanager-list" v-model="files" group="files" @start="drag = true" @end="drag = false">
            <div class="filemanager-item filemanager-item-with-name filemanager-item-removable" v-for="file in files" :id="'item_' + file.id" :key="file.id">
                <div class="filemanager-item-wrapper">
                    <button class="filemanager-item-removable-button" @click="remove(file)" type="button">
                        <i class="bi bi-x fs-5"></i>
                    </button>
                    <div class="filemanager-item-icon" v-if="file.type === 'i'">
                        <div class="filemanager-item-image-wrapper">
                            <img class="filemanager-item-image" :src="file.thumb_sm" :alt="file.alt_attribute[contentLocale]" />
                        </div>
                    </div>
                    <div class="filemanager-item-icon" :class="'filemanager-item-icon-' + file.type" v-else>
                        <i class="bi bi-file-earmark-music" v-if="file.type === 'a'"></i>
                        <i class="bi bi-file-earmark-play" v-if="file.type === 'v'"></i>
                        <i class="bi bi-file-earmark" v-if="file.type === 'd'"></i>
                        <i class="bi bi-folder" v-if="file.type === 'f'"></i>
                    </div>
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
            default: 'Files',
        },
        initFiles: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            contentLocale: window.TypiCMS.content_locale,
            files: this.initFiles,
        };
    },
    mounted() {
        this.$root.$on('filesAdded', (files) => {
            for (let i = files.length - 1; i >= 0; i--) {
                if (this.files.find(({ id }) => id === files[i].id) === undefined) {
                    this.files.push(files[i]);
                }
            }
        });
    },
    computed: {
        fileIds() {
            let fileIds = [];
            for (let i = 0; i < this.files.length; i++) {
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
