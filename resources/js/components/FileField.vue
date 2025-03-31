<template>
    <div>
        <p class="form-label mb-2">
            <span v-if="label">{{ t(label) }}</span>
            <span v-else>
                {{ type === 'document' ? t('Document') : t('Image') }}
            </span>
        </p>
        <input :id="field" v-model="fileId" :name="field" :rel="field" type="hidden" />
        <div>
            <div v-if="file" class="filemanager-item filemanager-item-with-name filemanager-item-removable">
                <div class="filemanager-item-wrapper">
                    <button class="filemanager-item-removable-button" type="button" @click="remove">
                        <i class="bi bi-x fs-5"></i>
                    </button>
                    <div v-if="file.type === 'i'" class="filemanager-item-icon">
                        <div class="filemanager-item-image-wrapper">
                            <img :alt="file.alt" :src="file.thumb_sm" class="filemanager-item-image" />
                        </div>
                    </div>
                    <div v-else :class="'filemanager-item-icon-' + file.type" class="filemanager-item-icon">
                        <i v-if="file.type === 'a'" class="bi bi-file-earmark-music"></i>
                        <i v-if="file.type === 'v'" class="bi bi-file-earmark-play"></i>
                        <i v-if="file.type === 'd'" class="bi bi-file-earmark"></i>
                        <i v-if="file.type === 'f'" class="bi bi-folder"></i>
                    </div>
                    <div class="filemanager-item-name">{{ file.name }}</div>
                </div>
            </div>
        </div>
        <div v-if="file === null" class="mb-3">
            <button class="filemanager-field-btn-add" type="button" @click="openFilePicker">
                <i class="bi bi-plus-circle-fill text-white-50 me-1"></i>
                {{ t('Add') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
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
        validator(value) {
            // The value must match one of these strings
            return ['image', 'document'].indexOf(value) !== -1;
        },
    },
    initFile: {
        type: Object,
        default: null,
    },
});

const file = ref(props.initFile);
const choosingFile = ref(false);

const fileId = computed(() => {
    if (file.value) {
        return file.value.id;
    }
    return null;
});

emitter.on('fileAdded', (addedFile) => {
    if (choosingFile.value === true) {
        file.value = addedFile;
    }
    choosingFile.value = false;
});

watch(
    file,
    (newValue) => {
        file.value = newValue;
    },
    { immediate: true },
);

function remove() {
    file.value = null;
}

function openFilePicker() {
    choosingFile.value = true;
    const options = {
        modal: true,
        modalIsInFront: true,
        multiple: false,
        open: true,
        overlay: true,
        single: true,
    };
    emitter.emit('openFilePicker', options);
}
</script>
