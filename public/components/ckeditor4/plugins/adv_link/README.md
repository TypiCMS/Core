# ckeditor-adv_link

A CKEditor 4 link plugin adding the ability to link to local (CMS) pages. Rely on jQuery for ajax calls.

Tested with CKEditor 4.14.

## Disclaimer

I developed this script for my needs, it is inspired by this [repository](https://github.com/simogeo/ckeditor-adv_link) and this [blog post](http://blog.xoundboy.com/?p=393).

## How to install it ?

1. Download and extract `adv_link` folder into CKEditor plugins folder

2. Disable default link plugin and enable the new one. To do so, in your config.js file :

```javascript
CKEDITOR.editorConfig = function(config) {
    // Define changes to default configuration here.

    config.removePlugins = 'link';
    config.extraPlugins = 'adv_link';

    // whatever
};
```

3. In `dialogs/links.js` file, find `/pages.php` and set the URL of the page which generates inputs to populate the plugin. A sample PHP script is given in `sample` folder and can be a good start.

4. Test your installation by using the plugin, if it does not work, use javascript debugging tool.

## Internationalization

2 translations are defined in the language files:

```javascript
localPage:'Local page',
selectPageLabel:'Select a page.',
```

Feel free to send pull requests!
