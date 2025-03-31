<template>
    <div>
        <input :value="fileIds.join()" name="file_ids" type="hidden" />
        <div class="mb-3">
            <p class="form-label mb-2">{{ t(label) }}</p>
            <p>
                <button class="filemanager-field-btn-add" type="button" @click="openFilePicker">
                    <i class="bi bi-plus-circle-fill text-white-50 me-1"></i>
                    {{ t('Add files') }}
                </button>
            </p>
        </div>

        <draggable v-model="files" class="filemanager-list" group="files" @start="drag = true" @end="drag = false" item-key="id">
            <template #item="{ element }">
                <div class="filemanager-item filemanager-item-with-name filemanager-item-removable">
                    <div class="filemanager-item-wrapper">
                        <button class="filemanager-item-removable-button" type="button" @click="remove(element)">
                            <i class="bi bi-x fs-5"></i>
                        </button>
                        <div v-if="element.type === 'i'" class="filemanager-item-icon">
                            <div class="filemanager-item-image-wrapper">
                                <img :alt="element.alt_attribute[contentLocale]" :src="element.thumb_sm" class="filemanager-item-image" />
                            </div>
                        </div>
                        <div v-else :class="'filemanager-item-icon-' + element.type" class="filemanager-item-icon">
                            <i v-if="element.type === 'a'" class="bi bi-file-earmark-music"></i>
                            <i v-if="element.type === 'v'" class="bi bi-file-earmark-play"></i>
                            <i v-if="element.type === 'd'" class="bi bi-file-earmark"></i>
                            <i v-if="element.type === 'f'" class="bi bi-folder"></i>
                        </div>
                        <div class="filemanager-item-name">{{ element.name }}</div>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import draggable from 'vuedraggable';
const { t } = useI18n();

const props = defineProps({
    label: {
        type: String,
        default: 'Files',
    },
    initFiles: {
        type: Array,
        required: true,
    },
});

const contentLocale = ref(window.TypiCMS.content_locale);
const files = ref(props.initFiles);

emitter.on('filesAdded', (addedFiles) => {
    addedFiles.forEach((addedFile) => {
        if (!files.value.find(({ id }) => id === addedFile.id)) {
            files.value.push(addedFile);
        }
    });
});

const fileIds = computed(() => files.value.map((file) => file.id));

function openFilePicker() {
    const options = {
        modal: true,
        modalIsInFront: true,
        multiple: true,
        open: true,
        overlay: true,
        single: false,
    };
    emitter.emit('openFilePicker', options);
}
function remove(file) {
    files.value = files.value.filter((f) => f !== file);
}
</script>
