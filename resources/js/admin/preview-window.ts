import Modal from 'bootstrap/js/dist/modal';

export default () => {
    document.body.insertAdjacentHTML(
        'beforeend',
        `<div id="preview-modal" class="modal fade">
            <div class="preview-modal-dialog modal-dialog modal-xl modal-dialog-centered">
                <div class="preview-modal-content modal-content">
                    <iframe class="preview-modal-iframe" id="preview-content"></iframe>
                    <button class="preview-modal-btn-close btn-close" type="button" id="close-preview" data-bs-dismiss="modal" aria-label="Close window"></button>
                </div>
            </div>
        </div>`,
    );

    const previewModal = new Modal('#preview-modal');
    const previewIframe = document.getElementById('preview-content') as HTMLIFrameElement;
    const closeButton = document.getElementById('close-preview');

    const openPreview = (event: Event) => {
        const target = event.target as HTMLAnchorElement;
        previewIframe.src = target.href;
        previewModal.show();
        event.preventDefault();
    };

    const closePreview = () => {
        previewModal.hide();
        previewIframe.src = '';
    };

    document.querySelectorAll('.btn-preview').forEach((button) => {
        button.addEventListener('click', openPreview);
    });

    closeButton?.addEventListener('click', closePreview);

    document.addEventListener('keydown', (event: KeyboardEvent) => {
        if (event.code === 'Escape') {
            closePreview();
        }
    });
};
