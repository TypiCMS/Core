<?php

return [
    'linkable_to_page' => true,
    'per_page' => 50,
    'order' => [
        'tag' => 'asc',
    ],
    'sidebar' => [
        'icon' => '<i class="bi bi-tags"></i>',
        'weight' => 130,
    ],
    'permissions' => [
        'read tags' => 'Read',
        'create tags' => 'Create',
        'update tags' => 'Update',
        'delete tags' => 'Delete',
    ],
];
