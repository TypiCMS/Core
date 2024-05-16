const editors = document.querySelectorAll('.ckeditor-full');
const publicCssFile = window.TypiCMS.public_css_file;

for (let i = 0; i < editors.length; ++i) {
    CKEDITOR.replace(editors[i].id, {
        toolbar: [
            { items: ['Format', 'Styles', 'Font', 'FontSize'] },
            { items: ['Bold', 'Italic', 'Strike', 'Subscript', 'Superscript'] },
            { items: ['RemoveFormat'] },
            { items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent'] },
            { items: ['Blockquote', 'CreateDiv', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
            { items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
            { items: ['Link', 'Unlink', 'Anchor'] },
            { items: ['Image', 'oembed', 'Table', 'HorizontalRule', 'SpecialChar'] },
            { items: ['Maximize', 'ShowBlocks', 'Source'] },
        ],
        versionCheck: false,
        entities: false,
        emailProtection: 'encode',
        format_tags: 'p;h1;h2;h3;h4;h5;h6',
        stylesSet: [
            { name: 'Button 1', element: 'a', attributes: { class: 'btn btn-primary' } },
            { name: 'Button 2', element: 'a', attributes: { class: 'btn btn-secondary' } },
            { name: 'Button 1 outline', element: 'a', attributes: { class: 'btn btn-outline-primary' } },
            { name: 'Button 2 outline', element: 'a', attributes: { class: 'btn btn-outline-secondary' } },

            { name: 'Responsive table', element: 'div', attributes: { class: 'table-responsive' } },

            { name: 'Alert Success', element: 'div', attributes: { class: 'alert alert-success' } },
            { name: 'Alert Info', element: 'div', attributes: { class: 'alert alert-info' } },
            { name: 'Alert Warning', element: 'div', attributes: { class: 'alert alert-warning' } },
            { name: 'Alert Danger', element: 'div', attributes: { class: 'alert alert-danger' } },

            { name: 'Small', element: 'small' },

            { name: 'Language: RTL', element: 'span', attributes: { dir: 'rtl' } },
            { name: 'Language: LTR', element: 'span', attributes: { dir: 'ltr' } },
        ],
        extraPlugins: 'adv_link,image2,codemirror,panelbutton,oembed,showblocks,div,dialogadvtab',
        removePlugins: 'image,link',
        oembed_WrapperClass: 'ratio ratio-16x9',
        extraAllowedContent: 'dl;dt;dd;small;img[!src,alt,width,height,loading]',
        bodyClass: 'rich-content',
        height: 500,
        contentsCss: [publicCssFile, '/components/ckeditor4/custom.css'],
        codemirror: {
            theme: 'twilight',
        },
        language: document.documentElement.lang,
        filebrowserBrowseUrl: '/admin/files?view=filepicker',
        filebrowserImageBrowseUrl: '/admin/files?type=i&view=filepicker',
    });
}
