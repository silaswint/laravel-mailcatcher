<?php
return [
    'layer' => env('MAIL_MAILCATCHER_LAYER', 'smtp'),
    'address' => env('MAILCATCHER_ADDRESS', 'mailcatcher{+name}@example.de'),
];
