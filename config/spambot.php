<?php

return [
    'username' => env('SPAMBOT_USERNAME', ''),
    'passcode' => env('SPAMBOT_PASSCODE', ''),
    'ignore' => [
        'google.com',
    ],
    'options' => [
        'delete' => false,
    ],
    'scheduled' => [
        'enabled' => env('SPAMBOT_SCHEDULED', false),
    ],
];
