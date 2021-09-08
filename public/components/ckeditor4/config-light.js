var editors = document.querySelectorAll('.ckeditor-light');
for (var i = 0; i < editors.length; ++i) {
    CKEDITOR.replace(editors[i].id, {
        toolbar: [
            { items: ['Bold', 'Italic', 'Subscript', 'Superscript'] },
            { items: ['RemoveFormat'] },
            { items: ['Link', 'Unlink'] },
            { items: ['Source'] },
        ],
        entities: false,
        emailProtection: 'encode',
        height: 240,
        contentsCss: ['/css/public.css', '/components/ckeditor4/custom.css'],
        language: $('html').attr('lang'),
        stylesSet: [],
        extraPlugins: 'adv_link,codemirror',
        removePlugins: 'link',
        codemirror: {
            theme: 'twilight',
        },
    });
}
