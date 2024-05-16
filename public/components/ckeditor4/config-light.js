const editors = document.querySelectorAll('.ckeditor-light');
const publicCssFile = window.TypiCMS.public_css_file;

for (let i = 0; i < editors.length; ++i) {
    CKEDITOR.replace(editors[i].id, {
        toolbar: [{ items: ['Bold', 'Italic', 'Subscript', 'Superscript'] }, { items: ['RemoveFormat'] }, { items: ['Link', 'Unlink'] }, { items: ['Source'] }],
        versionCheck: false,
        entities: false,
        emailProtection: 'encode',
        height: 240,
        contentsCss: [publicCssFile, '/components/ckeditor4/custom.css'],
        language: document.documentElement.lang,
        stylesSet: [],
        extraPlugins: 'adv_link,codemirror',
        removePlugins: 'link',
        codemirror: {
            theme: 'twilight',
        },
    });
}
