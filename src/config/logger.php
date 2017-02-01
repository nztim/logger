<?php

return [

    // Capture Laravel log entries
    'laravel' => true,

    // Base subfolder for custom logs, use '' for the storage/logs foler
    'folder' => 'custom',

    // Log file names that are to be handled as daily logs
    'daily' => [],

    // Maximum number of daily log files to keep
    'max_daily' => 7,

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

