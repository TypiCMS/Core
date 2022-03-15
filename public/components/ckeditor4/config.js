CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;

// Get the local pages for making links to CMS pages.
var localPages = null;
axios
    .get('/api/pages/links-for-editor')
    .then((response) => {
        localPages = response.data;
    })
    .catch((error) => {
        alertify.error('An error occurred while loading the local pages for the editor.');
    });

// dialogDefinition
CKEDITOR.on('dialogDefinition', function (event) {
    var editor = event.editor;
    var dialogDefinition = event.data.definition;
    var dialogName = event.data.name;

    // table config
    if (dialogName === 'table') {
        var info = dialogDefinition.getContents('info');
        info.get('txtWidth')['default'] = '100%';
        info.get('txtBorder')['default'] = '0';
        info.get('txtCellPad')['default'] = '0';
        info.get('txtCellSpace')['default'] = '0';
    }

    // filepicker
    var cleanUpFuncRef = CKEDITOR.tools.addFunction(function () {
        $('#filepicker').data('CKEditorCleanUpFuncNum', 0).data('CKEditorFuncNum', 0);
        $('html, body').removeClass('noscroll');
    });

    var tabCount = dialogDefinition.contents.length;
    for (var i = 0; i < tabCount; i++) {
        var browseButton = dialogDefinition.contents[i].get('browse');

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

                $('#filepicker')
                    .data('CKEditorCleanUpFuncNum', cleanUpFuncRef)
                    .data('CKEditorFuncNum', CKEDITOR.instances[event.editor.name]._.filebrowserFn);
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

