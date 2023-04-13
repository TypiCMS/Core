export default () => {
    document.body.insertAdjacentHTML(
        'beforeend',
        `<div id="preview-window" class="typicms-modal">
            <div id="preview-window-wrapper" class="typicms-modal-wrapper">
                <iframe id="preview-content" class="typicms-modal-content"></iframe>
            </div>
            <button id="close-preview" class="typicms-modal-btn-close">
                <svg width="20" height="20" viewBox="0 0 1792 1792" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/>
                </svg>
            </button>
        </div>`
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
            false
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
            false
        );
    }
};