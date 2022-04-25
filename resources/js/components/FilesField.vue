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

        <draggable v-model="files" group="files" @start="drag = true" @end="drag = false">
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
                        <svg
                            v-if="file.type === 'a'"
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
                            v-if="file.type === 'v'"
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
                            v-if="file.type === 'd'"
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
                            v-if="file.type === 'f'"
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
