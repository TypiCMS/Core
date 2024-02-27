export default (): void => {
    document.body.insertAdjacentHTML(
        'beforeend',
        `<div id='preview-window' class='typicms-modal'>
            <div id='preview-window-wrapper' class='typicms-modal-wrapper'>
                <iframe id='preview-content' class='typicms-modal-content'></iframe>
            </div>
            <button id='close-preview' class='typicms-modal-btn-close' aria-label='Close window'>
                <span aria-hidden="true">×</span>
            </button>
        </div>`,
    );

    // Open preview window
    document.querySelectorAll('.btn-preview').forEach((button: Element): void => button.addEventListener('click', openPreview));

    // Close preview window
    document.getElementById('close-preview')?.addEventListener('click', closePreview);
    document.addEventListener('keydown', (event: KeyboardEvent) => event.code === 'Escape' && closePreview());

    function openPreview(event: Event): void {
        event.preventDefault();
        (document.getElementById('preview-content') as HTMLIFrameElement).src = (event.target as HTMLAnchorElement).href;
        document.body.classList.add('noscroll'); // add noscroll class to <body>
        document.getElementById('preview-window')!.classList.add('typicms-modal-open');
    }

    function closePreview(): void {
        (document.getElementById('preview-content') as HTMLIFrameElement).src = '';
        document.body.classList.remove('noscroll'); // remove noscroll class from <body>
        document.getElementById('preview-window')!.classList.remove('typicms-modal-open');
    }
};
