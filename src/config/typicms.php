<?php

use TypiCMS\Modules\Core\Models\Page;

return [
    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | For website header, auth views, and main json-ld file.
    |
    */
    'logo' => env('APP_LOGO'),

    /*
    |--------------------------------------------------------------------------
    | Default Open Graph Image
    |--------------------------------------------------------------------------
    */
    'og_image' => env('APP_OG_IMAGE'),

    /*
    |--------------------------------------------------------------------------
    | Webmaster email
    |--------------------------------------------------------------------------
    |
    | For mail notifications
    |
    */
    'webmaster_email' => env('WEBMASTER_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | List of available locales for your website, the first key of this array
    | have to be the value of APP_LOCALE in your .env file, this key is also
    | the value returned by the mainLocale() helper.
    |
    */
    'locales' => [
        'en' => 'en_US',
        'fr' => 'fr_FR',
        'nl' => 'nl_NL',
    ],

    /*
    |--------------------------------------------------------------------------
    | You can choose not to have main locale in URLs
    |--------------------------------------------------------------------------
    |
    | If set to false, the main locale, the first of the locales array
    | will not appear in URLs.
    |
    */
    'main_locale_in_url' => true,

    /*
    |--------------------------------------------------------------------------
    | You can choose to have a lang chooser in the root url
    |--------------------------------------------------------------------------
    |
    | If set to false, the root URL will redirect to the browser locale
    | or the main locale (the first in the above array). If set to true,
    | main_locale_in_url must be true.
    |
    */
    'lang_chooser' => false,

    /*
    |--------------------------------------------------------------------------
    | Max file upload size allowed
    |--------------------------------------------------------------------------
    */
    'max_file_upload_size' => env('MAX_FILE_UPLOAD_SIZE', 60000),

    /*
    |--------------------------------------------------------------------------
    | Welcome message url present in Dashboard
    |--------------------------------------------------------------------------
    */
    'welcome_message_url' => env('WELCOME_MESSAGE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Folder to find template files for pages.
    | This folder is a subfolder of /resources/views/vendor/pages
    |--------------------------------------------------------------------------
    */
    'template_dir' => 'public',

    /*
    |--------------------------------------------------------------------------
    | If you use MariaDB, set this value to true
    |--------------------------------------------------------------------------
    */
    'mariadb' => false,

    /*
    |--------------------------------------------------------------------------
    | The following IP’s can visit the website without login when
    | the website is protected by a login and a password.
    |--------------------------------------------------------------------------
    */
    'authorized_ips' => [
        // '127.0.0.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Search engine configuration.
    |--------------------------------------------------------------------------
    */
    'search' => [
        'linkable_to_page' => true,
        'pages' => [
            'model' => Page::class,
            'columns' => [
                'title',
                'body',
            ],
        ],
        // 'news' => [
        //     'model' => News::class,
        //     'columns' => [
        //         'title',
        //         'body',
        //     ],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules configuration.
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'news' => [
            // 'per_page' => 30
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Send the x-powered-by: TypiCMS Header.
    |--------------------------------------------------------------------------
    */
    'send_powered_by_header' => true,
];
