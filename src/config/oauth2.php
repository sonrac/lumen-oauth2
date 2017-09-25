<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/1/17
 * Time: 3:16 PM
 */

return [
    'loadDefRoutsIfNeedle' => env('LOAD_DEFAULT_ROUTES', true),
    'accessTokenParamName' => env('ACCESS_TOKEN_PARAM_NAME', 'access_token'),
    'clientAccessTokenParamName' => env('CLIENT_ACCESS_TOKEN_PARAM_NAME', env('ACCESS_TOKEN_PARAM_NAME', 'access_token')),
    'keyPath' => storage_path('framework/keys'),
    'publicKeyName' => 'public.key',
    'privateKeyName' => 'private.key',
    'passPhrase' => '', // May be overwrite by .env
    'params' => [
        'scope_param' => env('SCOPE_PARAM', 'scopes'),
        'default_scope' => env('DEFAULT_SCOPE', 'default'),
        'state_param' => env('STATE_PARAM', 'state'),
        'scope_delimiter' => env('SCOPE_DELIMITER', ','),
    ],
    'access_token_ttl' => env('ACCESS_TOKEN_TTL', 'PT1H'),
    'refresh_token_ttl' => env('REFRESH_ACCESS_TOKEN_TTL', 'PT1H'),
    'key_path' => __DIR__ . '/../storage/framework/keys',
    'encryption_key' => env('APP_KEY'),
    'token_type' => [
        'client' => [
            'class' => \League\OAuth2\Server\Grant\ClientCredentialsGrant::class,
        ],
        'password' => [
            'class' => \League\OAuth2\Server\Grant\PasswordGrant::class,
        ],
        'refresh_token' => [
            'class' => \League\OAuth2\Server\Grant\RefreshTokenGrant::class,
        ],
    ],
];