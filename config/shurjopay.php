<?php

$apiDomain = env('SPAY_TESTMODE') ? "https://sandbox.shurjopayment.com" : "https://engine.shurjopayment.com";
return [
    'prefix'        => env("SPAY_PREFIX"),
    'testmode'      => env("SPAY_TESTMODE", true),
    'redirect_url'  => env("SPAY_REDIRECT_URL"),
    'credentials'   => [
        'username'  => env("SPAY_MERCHANT_USERNAME"),
        'password'  => env("SPAY_MERCHANT_PASSWORD"),
    ],
    'api_url'       => [
        'token'     => "{$apiDomain}/api/get_token",
        'payment'   => "{$apiDomain}/api/secret-pay",
        'validate'  => "{$apiDomain}/api/verification",
    ],
    'success_url'   => '/success-from-spay',
    'cancel_url'    => '/cancel-from-spay',
    'ipn_url'       => '/ipn-from-spay',
];
