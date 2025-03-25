CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.dtd.a.div = 1;
CKEDITOR.dtd.a.p = 1;
CKEDITOR.dtd.a.h1 = 1;
CKEDITOR.dtd.a.h2 = 1;
CKEDITOR.dtd.a.h3 = 1;
CKEDITOR.dtd.a.h4 = 1;
CKEDITOR.dtd.a.h5 = 1;
CKEDITOR.dtd.a.h6 = 1;
CKEDITOR.dtd.a.img = 1;

// Get the local pages for making links to CMS pages.
let localPages = null;
const apiTokenElement = document.head.querySelector('meta[name="api-token"]');

if (apiTokenElement) {
    fetch('/api/pages/links-for-editor', {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            Authorization: `Bearer ${apiTokenElement.content}`,
            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            localPages = data;
        })
        .catch(() => {
            alertify.error('An error occurred while loading the local pages for the editor.');
        });
}
document.addEventListener('keydown', (event) => {
    if (this.modal && this.options.modalIsInFront && event.code === 'Escape') {
        this.closeModal();
    }
});
// dialogDefinition
CKEDITOR.on('dialogDefinition', function (event) {
    const editor = event.editor;
    const dialogDefinition = event.data.definition;
    const dialogName = event.data.name;

    dialogDefinition.onCancel = function (event) {
        if (window.ckEditorDialogBlured) {
            return false;
        } else {
            return true;
        }
    };

    // table config
    if (dialogName === 'table') {
        const info = dialogDefinition.getContents('info');
        info.get('txtWidth')['default'] = '100%';
        info.get('txtBorder')['default'] = '0';
        info.get('txtCellPad')['default'] = '0';
        info.get('txtCellSpace')['default'] = '0';
    }

    // Filepicker
    const cleanUpFuncRef = CKEDITOR.tools.addFunction(function () {
        const filepicker = document.getElementById('filepicker');
        filepicker.dataset.CKEditorCleanUpFuncNum = '0';
        filepicker.dataset.CKEditorFuncNum = '0';
        document.body.classList.remove('noscroll');
    });

    const tabCount = dialogDefinition.contents.length;
    for (let i = 0; i < tabCount; i++) {
        const browseButton = dialogDefinition.contents[i].get('browse');

        if (browseButton !== null) {
            browseButton.hidden = false;
            browseButton.onClick = function (dialog, i) {
                editor._.filebrowserSe = this;

                const options = {
                    modal: true,
                    modalIsInFront: true,
                    dropzone: false,
                    multiple: false,
                    single: true,
                    overlay: false,
                    open: true,
                };
                emitter.emit('openFilepickerForCKEditor', options);

                const filepicker = document.getElementById('filepicker');
                filepicker.dataset.CKEditorCleanUpFuncNum = cleanUpFuncRef;
                filepicker.dataset.CKEditorFuncNum = CKEDITOR.instances[event.editor.name]._.filebrowserFn;
            };
        }
    }
});

CKEDITOR.on('instanceReady', function (event) {
    event.editor.on('beforeCommandExec', function (event) {
        // Show the paste dialog for the paste buttons and right-click paste
        if (event.data.name == 'paste') {
            event.editor._.forcePasteDialog = true;
        }
        // Don't show the paste dialog for Ctrl+Shift+V
        if (event.data.name == 'pastetext' && event.data.commandData.from == 'keystrokeHandler') {
            event.cancel();
        }
    });
});
