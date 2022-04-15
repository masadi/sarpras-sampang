<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => true,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => false,

    'roles_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'referensi' => 'c,r,u,d',
            'news' => 'c,r,u,d'
        ],
        'smp' => [
            'news' => 'c,r,u,d'
        ],
        'sd' => [
            'news' => 'c,r,u,d',
        ]
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
