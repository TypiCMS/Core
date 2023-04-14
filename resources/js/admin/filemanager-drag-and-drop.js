export default () => {
    const dropzone = document.querySelector('#dropzone');
    const filepicker = document.querySelector('#filepicker');
    const fileManagerModalOpen = document.querySelector('.filemanager-modal-open');

    const containsFiles = (event) => {
        if (event.dataTransfer.types) {
            return Array.from(event.dataTransfer.types).includes('Files');
        }
        return false;
    };

    const onDragEnterFilePicker = (event) => {
        if (fileManagerModalOpen && containsFiles(event)) {
            dropzone.classList.add('fullscreen');
        }
    };

    const onDragEnterBody = (event) => {
        if (!fileManagerModalOpen && containsFiles(event)) {
            dropzone.classList.add('fullscreen');
        }
    };

    const onDropOrDragLeaveDropzone = () => {
        dropzone.classList.remove('fullscreen');
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (filepicker) {
            filepicker.addEventListener('dragenter', onDragEnterFilePicker);
            document.body.addEventListener('dragenter', onDragEnterBody);
        }

        if (dropzone) {
            dropzone.addEventListener('drop', onDropOrDragLeaveDropzone);
            dropzone.addEventListener('dragleave', onDropOrDragLeaveDropzone);
        }
    });
};
