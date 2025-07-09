import Modal from 'bootstrap/js/dist/modal';

export default (): void => {
    document.body.insertAdjacentHTML(
        'beforeend',
        `<div id='preview-window' class='modal fade'>
            <div class='modal-dialog modal-xl'>
                <div class='modal-content'>
                    <iframe id='preview-content' class='typicms-modal-content'></iframe>
                    <button id='close-preview' class='modal-btn-close' data-bs-dismiss='modal' aria-label='Close window'>
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        </div>`,
    );

    const previewModal = new Modal('#preview-window');

    // Open preview window
    document.querySelectorAll('.btn-preview').forEach((button: Element): void => button.addEventListener('click', openPreview));

    // Close preview window
    document.getElementById('close-preview')?.addEventListener('click', closePreview);
    document.addEventListener('keydown', (event: KeyboardEvent) => event.code === 'Escape' && closePreview());

    function openPreview(event: Event): void {
        (document.getElementById('preview-content') as HTMLIFrameElement).src = (event.target as HTMLAnchorElement).href;
        previewModal.show();
        event.preventDefault();
    }

    function closePreview(): void {
        previewModal.hide();
        (document.getElementById('preview-content') as HTMLIFrameElement).src = '';
    }
};
