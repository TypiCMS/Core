<template>
    <div>
        <label class="form-label" :for="field">
            <span v-if="label">{{ $t(label) }}</span>
            <span v-else>
                {{ type === 'document' ? $t('Document') : $t('Image') }}
            </span>
        </label>
        <input type="hidden" :name="field" :id="field" :rel="field" v-model="fileId" />
        <div>
            <div class="filemanager-item filemanager-item-with-name filemanager-item-removable" v-if="file !== null">
                <div class="filemanager-item-wrapper">
                    <button class="filemanager-item-removable-button" type="button" @click="remove">
                        <i class="bi bi-x fs-3"></i>
                    </button>
                    <div class="filemanager-item-icon" v-if="file.type === 'i'">
                        <div class="filemanager-item-image-wrapper">
                            <img class="filemanager-item-image" :src="file.thumb_sm" :alt="file.alt" />
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
        </div>
        <div class="mb-3" v-if="file === null">
            <button class="filemanager-field-btn-add" @click="openFilepicker" type="button">
                <i class="bi bi-plus-circle-fill text-white-50 me-1"></i>
                {{ $t('Add') }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        field: {
            type: String,
            required: true,
        },
        label: {
            type: String,
        },
        type: {
            type: String,
            required: true,
            validator: function (value) {
                // The value must match one of these strings
                return ['image', 'document'].indexOf(value) !== -1;
            },
        },
        initFile: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            file: this.initFile,
            choosingFile: false,
        };
    },
    computed: {
        fileId() {
            if (this.file !== null) {
                return this.file.id;
            }
            return null;
        },
    },
    mounted() {
        this.$root.$on('fileAdded', (file) => {
            if (this.choosingFile === true) {
                this.file = file;
            }
            this.choosingFile = false;
        });
    },
    methods: {
        remove() {
            this.file = null;
        },
        openFilepicker() {
            this.choosingFile = true;
            let options = {
                open: true,
                multiple: false,
                overlay: true,
                single: true,
                modal: true,
            };
            this.$root.$emit('openFilepicker', options);
        },
    },
};
</script>
