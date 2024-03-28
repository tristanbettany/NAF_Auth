<?php

use Database\Seeds\UserSeed;

return [
    'login_url' => env('SSO_LOGIN_URL'),
    'cert_file_name' => env('SSO_CERT_FILE_NAME'),
    'attribute_names' => [
        'id' => env('SSO_ID_ATTRIBUTE_NAME'),
        'email' => env('SSO_EMAIL_ATTRIBUTE_NAME'),
        'firstName' => env('SSO_FIRST_NAME_ATTRIBUTE_NAME'),
        'lastName' => env('SSO_LAST_NAME_ATTRIBUTE_NAME'),
    ],
];