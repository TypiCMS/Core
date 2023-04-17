<template>
    <div>
        <input type="hidden" name="file_ids" :value="fileIds.join()" />
        <div class="mb-3">
            <label class="form-label">{{ $t(label) }}</label>
            <p>
                <button class="filemanager-field-btn-add" @click="openFilepicker" type="button">
                    <svg
                        class="filemanager-field-btn-add-icon"
                        width="1em"
                        height="1em"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"
                        />
                    </svg>
                    {{ $t('Add files') }}
                </button>
            </p>
        </div>

        <draggable class="filemanager-list" v-model="files" group="files" @start="drag = true" @end="drag = false">
            <div
                class="filemanager-item filemanager-item-with-name filemanager-item-removable"
                v-for="file in files"
                :id="'item_' + file.id"
                :key="file.id"
            >
                <div class="filemanager-item-wrapper">
                    <button class="filemanager-item-removable-button" @click="remove(file)" type="button">
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
                    <div class="filemanager-item-icon" v-if="file.type === 'i'">
                        <div class="filemanager-item-image-wrapper">
                            <img
                                class="filemanager-item-image"
                                :src="file.thumb_sm"
                                :alt="file.alt_attribute[contentLocale]"
                            />
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
