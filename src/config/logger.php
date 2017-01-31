<?php

return [

    // Capture Laravel log entries
    'laravel' => true,

    // Base subfolder for custom logs, use '' for the storage/logs foler
    'folder' => 'custom',

    // Email notifications
    'email' => [
        'send'      => false,       // Sending of error emails
        'level'     => 'ERROR',     // Minimum log level for which emails are sent
        'recipient' => env('LOGGER_EMAIL_RECIPIENT', 'recipient@example.com'),
        'name'      => 'MyApp',     // App name
    ],

    'database' => [
        'channels' => [],           // Which channels should be stored in the db, use ['*'] for all channels
    ],
];

