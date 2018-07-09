<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 10/2/17
 * Time: 1:16 PM.
 */

return [
    'defaults' => [
        'guard' => 'jwt',
    ],
    'guards' => [
        'jwt' => [
            'driver'   => 'jwt',
            'provider' => 'clients',
        ],
        'user' => [
            'driver'   => 'token',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'clients' => [
            'driver' => 'eloquent',
            'model'  => app(sonrac\lumenRest\contracts\ClientEntityInterface::class),
        ],
        'users' => [
            'driver' => 'eloquent',
            'model'  => app(sonrac\lumenRest\contracts\UserEntityInterface::class),
        ],
    ],
];
