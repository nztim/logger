<?php

return [

    'laravel' => true, // Capture Laravel log entries

    'email' => [
        'send'      => false,       // Sending of error emails
        'level'     => 'ERROR',     // Minimum log level for which emails are sent
        'recipient' => env('LOGGER_EMAIL_RECIPIENT', 'recipient@example.com'),
        'name'      => 'MyApp',     // App name
    ],

    'database' => [
        'channels' => [],           // Channels to store in the db, use ['*'] for all channels
    ],
];
