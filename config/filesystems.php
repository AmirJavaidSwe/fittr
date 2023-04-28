<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public-remote' => [
            'driver' => 'scoped',
            'disk' => 's3',
            'prefix' => 'public',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'private-remote' => [
            'driver' => 'scoped',
            'disk' => 's3',
            'prefix' => 'private',
            'visibility' => 'private',
        ],

        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'visibility' => 'private',
        ],

        'seeders' => [
            'driver' => 'local',
            'root' => database_path('seeders'),
            'visibility' => 'private',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_S3_ACCESS_KEY_ID'),
            'secret' => env('AWS_S3_SECRET_ACCESS_KEY'),
            'region' => env('AWS_S3_DEFAULT_REGION'),
            'bucket' => env('AWS_S3_BUCKET'),
            'url' => env('AWS_URL'),//not used
            'endpoint' => env('AWS_ENDPOINT'),//not used
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
