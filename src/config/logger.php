<?php

return [

    // Capture Laravel log entries
    'laravel' => true,

    'email' => [

        // Sending of error emails
        'send'      => false,

        // Log level for which emails are sent
        'level'     => 'ERROR',

        //  Email recipient address
        'recipient' => env('LOGGER_EMAIL_RECIPIENT', 'recipient@example.com'),

        // App name
        'name'      => 'MyApp',
    ],
];
