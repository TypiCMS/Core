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
    const btnPreview = document.querySelector('.btn-preview') as HTMLAnchorElement | null;
    if (btnPreview) {
        btnPreview.addEventListener(
            'click',
            (event: Event) => {
                event.preventDefault();
                (document.getElementById('preview-content') as HTMLIFrameElement).src = btnPreview.href;
                document.documentElement.classList.add('noscroll'); // add noscroll class to <html>
                document.body.classList.add('noscroll'); // add noscroll class to <body>
                document.getElementById('preview-window')!.classList.add('typicms-modal-open');
            },
            false,
        );
    }

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
                document.documentElement.classList.remove('noscroll'); // remove noscroll class from <html>
                document.body.classList.remove('noscroll'); // remove noscroll class from <body>
                document.getElementById('preview-window')!.classList.remove('typicms-modal-open');
            },
            false,
        );
    }
};
