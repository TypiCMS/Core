<template>
    <div class="image-cropper" v-if="imageUrl">
        <div class="image-cropper-container">
            <img ref="imageElement" :src="imageUrl" alt="Image to crop" />
        </div>
        <div class="image-cropper-actions">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-light" @click="rotate(-90)">
                    <rotate-ccw-icon :size="18" stroke-width="2" />
                </button>
                <button type="button" class="btn btn-sm btn-light" @click="rotate(90)">
                    <rotate-cw-icon :size="18" stroke-width="2" />
                </button>
            </div>
            <div class="btn-group me-auto">
                <button type="button" class="btn btn-sm btn-light" @click="flipHorizontal">
                    <flip-horizontal-icon :size="18" stroke-width="2" />
                </button>
                <button type="button" class="btn btn-sm btn-light" @click="flipVertical">
                    <flip-vertical-icon :size="18" stroke-width="2" />
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-light" @click="reset">
                {{ t('Reset') }}
            </button>
            <button type="button" class="btn btn-sm btn-primary" @click="saveCroppedImage">
                {{ t('Save cropped image') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import { CheckIcon, FlipHorizontalIcon, FlipVerticalIcon, RotateCcwIcon, RotateCwIcon } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    imageUrl: {
        type: String,
        required: true,
    },
    fileId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['cropped']);

const imageElement = ref(null);
const cropper = ref(null);

onMounted(() => {
    initCropper();
});

onUnmounted(() => {
    destroyCropper();
});

watch(
    () => props.imageUrl,
    () => {
        destroyCropper();
        initCropper();
    },
);

function initCropper() {
    if (imageElement.value && props.imageUrl) {
        cropper.value = new Cropper(imageElement.value, {
            aspectRatio: NaN,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            restore: true,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    }
}

function destroyCropper() {
    if (cropper.value) {
        cropper.value.destroy();
        cropper.value = null;
    }
}

function reset() {
    if (cropper.value) {
        cropper.value.reset();
    }
}

function rotate(degrees) {
    if (cropper.value) {
        cropper.value.rotate(degrees);
    }
}

function flipHorizontal() {
    if (cropper.value) {
        const scaleX = cropper.value.getData().scaleX || 1;
        cropper.value.scaleX(-scaleX);
    }
}

function flipVertical() {
    if (cropper.value) {
        const scaleY = cropper.value.getData().scaleY || 1;
        cropper.value.scaleY(-scaleY);
    }
}

async function saveCroppedImage() {
    if (!cropper.value) {
        return;
    }

    const canvas = cropper.value.getCroppedCanvas();

    canvas.toBlob(async (blob) => {
        const formData = new FormData();
        formData.append('cropped_image', blob, 'cropped.jpg');
        formData.append('_method', 'POST');

        try {
            const response = await fetch(`/admin/files/${props.fileId}/crop`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    Accept: 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                alertify.success(t('Image cropped successfully'));
                emit('cropped', data);
                // Reload the page to show the updated image
                window.location.reload();
            } else {
                alertify.error(t('Error cropping image'));
            }
        } catch (error) {
            console.error('Error:', error);
            alertify.error(t('Error cropping image'));
        }
    }, 'image/jpeg');
}
</script>
