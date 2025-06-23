<?php

$apiDomain = env('SSLCZ_TESTMODE') ? "https://sandbox.sslcommerz.com" : "https://securepay.sslcommerz.com";
return [
    'testmode'      => env("SSLCZ_TESTMODE", true),
    'redirect_url'  => env("SSLCZ_REDIRECT_URL"),
    'credentials'   => [
        'username'  => env("SSLCZ_STORE_ID"),
        'password'  => env("SSLCZ_STORE_PASSWORD"),
    ],
    'api_url'       => [
        'payment'   => "{$apiDomain}/gwprocess/v4/api.php",
        'validate'  => "{$apiDomain}/validator/api/validationserverAPI.php",
    ],
    'success_url'   => '/success-from-ssl',
    'failed_url'    => '/failed-from-ssl',
    'cancel_url'    => '/cancel-from-ssl',
    'ipn_url'       => '/ipn',
];
