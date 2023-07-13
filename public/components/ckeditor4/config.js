CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;

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

// dialogDefinition
CKEDITOR.on('dialogDefinition', function (event) {
    const editor = event.editor;
    const dialogDefinition = event.data.definition;
    const dialogName = event.data.name;

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

                new Vue({
                    data: {
                        options: {
                            modal: true,
                            dropzone: false,
                            multiple: false,
                            single: true,
                            overlay: false,
                            open: true,
                        },
                    },
                    created() {
                        window.EventBus.$emit('openFilepickerForCKEditor', this.options);
                    },
                });

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
