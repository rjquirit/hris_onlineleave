<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Encryption Passcode
    |--------------------------------------------------------------------------
    |
    | This passcode is used to encrypt sensitive data in the database.
    | It should be a strong, random string stored securely in your .env file.
    | NEVER commit this value to version control.
    |
    */
    'passcode' => env('ENCRYPTION_PASSCODE'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Algorithm
    |--------------------------------------------------------------------------
    |
    | The encryption algorithm used for encrypting sensitive data.
    | AES-256-CBC provides strong encryption with good performance.
    |
    */
    'algorithm' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Blind Index Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for blind indexes used to enable searching on encrypted data.
    | The hash algorithm is used to create searchable indexes without exposing
    | the actual data.
    |
    */
    'blind_index' => [
        'algorithm' => 'sha256',
        'key' => env('BLIND_INDEX_KEY', env('APP_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Searchable Fields Configuration
    |--------------------------------------------------------------------------
    |
    | Define which model fields should have searchable encryption enabled.
    | These fields will have blind indexes created for LIKE query support.
    |
    */
    'searchable_fields' => [
        'users' => ['name', 'email'],
        'office_personnel' => ['first_name', 'last_name', 'email', 'position', 'department'],
    ],
];
