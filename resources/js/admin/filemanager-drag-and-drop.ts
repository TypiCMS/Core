export default (): void => {
    const dropzone = document.querySelector('#dropzone') as HTMLElement;
    const filepicker = document.querySelector('#filepicker') as HTMLElement;
    const fileManagerModalOpen = document.querySelector('.filemanager-modal-open') as HTMLElement;

    const containsFiles = (event: DragEvent): boolean => {
        if (event.dataTransfer?.types) {
            return Array.from(event.dataTransfer.types).includes('Files');
        }
        return false;
    };

    const onDragEnterFilePicker = (event: DragEvent): void => {
        if (fileManagerModalOpen && containsFiles(event)) {
            dropzone.classList.add('fullscreen');
        }
    };

    const onDragEnterBody = (event: DragEvent): void => {
        if (!fileManagerModalOpen && containsFiles(event)) {
            dropzone.classList.add('fullscreen');
        }
    };

    const onDropOrDragLeaveDropzone = (): void => {
        dropzone.classList.remove('fullscreen');
    };

    document.addEventListener('DOMContentLoaded', (): void => {
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
