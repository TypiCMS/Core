@props(['name', 'id', 'defaultValue', 'locale'])
<div {{ $attributes->merge(['class' => '']) }} data-language="{{ $locale }}">
    <div class="mb-10" id="{{ $id }}"></div>
    <input type="hidden" name="{{ $name }}" id="quill-editor-area-{{ $name }}" value="{!! $defaultValue !!}" />

    @push('js')
        <script defer>

            document.addEventListener('DOMContentLoaded', () => {
                if (document.getElementById('{{ $id }}')) {
                    const editor = new Quill('#{{ $id }}', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{'header': 1}, {'header': 2}, {'header': [1, 2, 3, 4, 5, false]}],
                                ['bold', 'italic', 'strike'],
                                ['blockquote', 'code-block'],
                                ['link', 'image', 'video', 'formula'],
                                [{'list': 'ordered'}, {'list': 'bullet'}],
                                [{'script': 'sub'}, {'script': 'super'}],
                                [{'indent': '-1'}, {'indent': '+1'}],
                                ['clean']
                            ],
                        },
                    });
                    const quillEditor = document.getElementById('quill-editor-area-{{ $name }}');

                    // Set default value if it's not empty
                    const defaultValue = quillEditor.value.trim();
                    if (defaultValue) {
                        editor.clipboard.dangerouslyPasteHTML(defaultValue);
                    }

                    // Sync Quill with the hidden input
                    editor.on('text-change', function () {
                        quillEditor.value = editor.root.innerHTML;
                    });

                    quillEditor.addEventListener('input', function () {
                        editor.root.innerHTML = quillEditor.value;
                    });

                    function showFilePicker() {
                        console.log('Image button clicked');
                        const options = {
                            modal: true,
                            modalIsInFront: true,
                            dropzone: false,
                            multiple: false,
                            single: true,
                            overlay: true,
                            open: true,
                        };
                        emitter.emit('openFilePickerForCKEditor', options);
                        emitter.on('fileAdded', (file) => {
                            if (file) {
                                const range = editor.getSelection();
                                const text = editor.getText(range.index, range.length);
                                if (file.type === 'i') {
                                    editor.insertEmbed(range.index, 'image', file.url);
                                } else {
                                    editor.deleteText(range.index, range.length);
                                    editor.insertText(range.index, text, 'link', file.url);
                                }
                            }
                        });
                    }

                    const toolbar = editor.getModule('toolbar');
                    toolbar.addHandler('image', showFilePicker);

                }
            });
        </script>
    @endpush
</div>
