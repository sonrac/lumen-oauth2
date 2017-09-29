<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/1/17
 * Time: 3:16 PM
 */

return [
    'loadDefRoutsIfNeedle'       => env('LOAD_DEFAULT_ROUTES', true),
    'accessTokenParamName'       => env('ACCESS_TOKEN_PARAM_NAME', 'access_token'),
    'clientAccessTokenParamName' => env('CLIENT_ACCESS_TOKEN_PARAM_NAME', env('ACCESS_TOKEN_PARAM_NAME', 'access_token')),
    'keyPath'                    => env('OAUTH_KEY_PATH', storage_path('framework/keys')),
    'publicKeyName'              => env('OAUTH_PUBLIC_KEY_NAME', 'public.key'),
    'privateKeyName'             => env('OAUTH_PRIVATE_KEY_NAME', 'private.key'),
    'passPhrase'                 => env('OAUTH_PASS_PHRASE', ''), // May be overwrite by .env
    'params'                     => [
        'scope_param'     => env('SCOPE_PARAM', 'scopes'),
        'default_scope'   => env('DEFAULT_SCOPE', 'default'),
        'state_param'     => env('STATE_PARAM', 'state'),
        'scope_delimiter' => env('SCOPE_DELIMITER', ','),
    ],
    'access_token_ttl'           => env('OAUTH_ACCESS_TOKEN_TTL', 'PT1H'),
    'refresh_token_ttl'          => env('OAUTH_REFRESH_TOKEN_TTL', 'P5M'),
    'encryption_key'             => env('OAUTH_ENC_KEY', env('APP_KEY')),
    'token_type'                 => [
        'client'        => [
            'class' => \League\OAuth2\Server\Grant\ClientCredentialsGrant::class,
        ],
        'password'      => [
            'class' => \League\OAuth2\Server\Grant\PasswordGrant::class,
        ],
        'refresh_token' => [
            'class' => \League\OAuth2\Server\Grant\RefreshTokenGrant::class,
        ],
        'implicit'      => [
            'class' => \League\OAuth2\Server\Grant\ImplicitGrant::class,
        ],
        'code'          => [
            'class'    => \League\OAuth2\Server\Grant\ImplicitGrant::class,
            'code_ttl' => env('OAUTH_CODE_TTL', 'PT10M'),
        ],
    ],
    'available_scopes'           => [
        'default' => 'Default scope',
        'email'   => 'User email scope',
        'basic'   => 'Base user info scope',
    ],
    'enable_scope_exception'     => false,
];