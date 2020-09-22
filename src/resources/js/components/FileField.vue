<template>
    <div class="mb-4">
        <div class="form-group">
            <label :for="field">
                <span v-if="label"> {{ label }} </span>
                <span v-else>
                    {{ type === 'document' ? $t('Document') : $t('Image') }}
                </span>
            </label>
            <input type="hidden" :name="field" :id="field" :rel="field" v-model="fileId" />
            <div>
                <div
                    v-if="file !== null"
                    class="filemanager-item filemanager-item-with-name filemanager-item-removable"
                >
                    <div class="filemanager-item-wrapper">
                        <button class="filemanager-item-removable-button" type="button" @click="remove">
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
                                <img class="filemanager-item-image" :src="file.thumb_sm" :alt="file.alt" />
                            </div>
                        </div>
                        <div class="filemanager-item-icon" :class="'filemanager-item-icon-' + file.type" v-else></div>
                        <div class="filemanager-item-name">{{ file.name }}</div>
                    </div>
                </div>
            </div>
            <div>
                <button v-if="file === null" @click="openFilepicker" class="btn btn-sm btn-secondary" type="button">
                    <span class="fa fa-plus fa-fw text-white-50"></span> {{ $t('Add') }}
                </button>
            </div>
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
