<?php
return [
    'database_type'                    => 'Mongo',
    'storage_type'                     => 'Mongo',
    'storage_dir'                      => APP_PATH . '/public/image/',
    'storage_path'                     => '/image/',
    'site_url'                         => 'http://gol',
    'debug'            => false,
    // Clab2 beállítások
    'mongo' => [
        'host' => 'localhost',
        'port' => 27017,
        'database' => 'gol'
    ],
    'frontend'  =>  array(
        'module_path'   =>  "workbench"
    )
];
