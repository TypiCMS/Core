<?php

declare(strict_types=1);

return [
    'per_page' => 50,
    'order' => [
        'position' => 'asc',
    ],
    'sidebar' => [
        'icon' => '<i class="icon-image"></i>',
        'weight' => 50,
    ],
    'permissions' => [
        'read files' => 'Read',
        'create files' => 'Create',
        'update files' => 'Update',
        'delete files' => 'Delete',
    ],
];
