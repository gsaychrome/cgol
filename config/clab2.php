<?php

return [
    'language' => [
        'data' => [
            'default' => 'hu',
            'available' => ['hu']
        ],
        'portal' => [
            'default' => 'hu',
            'available' => ['hu']
        ]
    ],
    'business' => [
        // Az alapértelmezett adapter típus
        'adapterType' => 'Odm\\Doctrine'
    ],
    'golapi' => [
        'business' => [
            'GameController' => [
                'adapterType' => 'Rule\\Conway'
            ]
        ],
        'rest' => [
            'register' => ['Game']
        ]
    ]
];
