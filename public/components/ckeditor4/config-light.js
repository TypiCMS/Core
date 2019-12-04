var editors = document.querySelectorAll('.ckeditor-light');
for (var i = 0; i < editors.length; ++i) {
    CKEDITOR.replace(editors[i].id, {
        toolbar: [
            { items: ['Bold', 'Italic', 'Subscript', 'Superscript'] },
            { items: ['RemoveFormat'] },
            { items: ['Source'] },
        ],
        entities: false,
        height: 240,
        contentsCss: ['/css/public.css', '/components/ckeditor/custom.css'],
        language: $('html').attr('lang'),
        stylesSet: [],
        extraPlugins: 'codemirror',
        codemirror: {
            theme: 'twilight',
        },
    });
}
