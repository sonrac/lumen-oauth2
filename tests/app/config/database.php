<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/28/17
 * Time: 7:03 PM.
 */

return [
    'default'     => env('DB_CONNECTION', 'sqlite'),
    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', __DIR__.'/../../out/database.sqlite'),
            'prefix'   => env('DB_PREFIX', ''),
        ],
    ],
    'migrations'  => 'm',
];
