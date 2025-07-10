<template>
    <div id="filepicker">
        <div v-if="modal" id="filepicker-modal" class="modal fade" tabindex="-1" aria-labelledby="filepickerLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <file-manager-content :single="options.single" :multiple="options.multiple"></file-manager-content>
                </div>
            </div>
        </div>
        <div v-else>
            <file-manager-content :single="options.single" :multiple="options.multiple"></file-manager-content>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import Modal from 'bootstrap/js/dist/modal';
import FileManagerContent from './FileManagerContent.vue';

const { t } = useI18n();

const filePickerModal = ref(null);

const props = defineProps({
    modal: {
        type: Boolean,
        default: true,
    },
    modalIsInFront: {
        type: Boolean,
        default: false,
    },
    dropzone: {
        type: Boolean,
        default: true,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    single: {
        type: Boolean,
        default: false,
    },
    open: {
        type: Boolean,
        default: false,
    },
    overlay: {
        type: Boolean,
        default: true,
    },
});

const options = ref({
    dropzone: props.dropzone,
    modal: props.modal,
    modalIsInFront: props.modalIsInFront,
    multiple: props.multiple,
    open: props.open,
    overlay: props.overlay,
    single: props.single,
});

emitter.on('openFilePicker', (opts) => {
    options.value = opts;
    filePickerModal.value.show();
});

emitter.on('modalIsBehind', () => {
    options.value.modalIsInFront = false;
});

emitter.on('modalIsInFront', () => {
    options.value.modalIsInFront = true;
});

emitter.on('modalShouldClose', () => {
    closeModal();
});

document.addEventListener('keydown', (event) => {
    if (event.code === 'Escape') {
        if (options.value.modalIsInFront) {
            closeModal();
        }
    }
});

function closeModal() {
    filePickerModal.value.hide();
    options.value.open = false;
    options.value.modalIsInFront = false;
}

const classes = computed(() => {
    return {
        'filemanager-multiple': options.value.multiple,
        'filemanager-single': options.value.single,
    };
});

onMounted(() => {
    filePickerModal.value = new Modal('#filepicker-modal');

    const myModal = document.querySelector('#filepicker-modal');
    myModal.addEventListener('hide.bs.modal', (event) => {
        if (!options.value.modalIsInFront) {
            return event.preventDefault();
        }
    });
});
</script>
