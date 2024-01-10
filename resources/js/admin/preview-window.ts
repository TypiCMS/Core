export default () => {
    document.body.insertAdjacentHTML(
        'beforeend',
        `<div id='preview-window' class='typicms-modal'>
            <div id='preview-window-wrapper' class='typicms-modal-wrapper'>
                <iframe id='preview-content' class='typicms-modal-content'></iframe>
            </div>
            <button id='close-preview' class='typicms-modal-btn-close'>
                <i class='bi bi-x fs-3'></i>
            </button>
        </div>`,
    );

    /**
     * Open preview window
     */
    const previewButtons: NodeListOf<HTMLAnchorElement> = document.querySelectorAll('.btn-preview');
    previewButtons.forEach((button: HTMLAnchorElement): void => {
        button.addEventListener(
            'click',
            (event: Event) => {
                event.preventDefault();
                (document.getElementById('preview-content') as HTMLIFrameElement).src = button.href;
                document.body.classList.add('noscroll'); // add noscroll class to <body>
                document.getElementById('preview-window')!.classList.add('typicms-modal-open');
            },
            false,
        );
    });

    /**
     * Close preview window
     */
    const btnClosePreview = document.getElementById('close-preview') as HTMLButtonElement | null;
    if (btnClosePreview) {
        btnClosePreview.addEventListener(
            'click',
            (event: Event) => {
                event.preventDefault();
                (document.getElementById('preview-content') as HTMLIFrameElement).src = '';
                document.body.classList.remove('noscroll'); // remove noscroll class from <body>
                document.getElementById('preview-window')!.classList.remove('typicms-modal-open');
            },
            false,
        );
    }
};
