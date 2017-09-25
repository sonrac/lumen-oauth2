<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

$app = new \Laravel\Lumen\Application(__DIR__);
$app->withFacades();
$app->withEloquent();

$app->singleton(
    'Illuminate\Contracts\Console\Kernel',
    'sonrac\lumenRest\tests\app\Kernel'
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    \Laravel\Lumen\Exceptions\Handler::class
);

$app->register(\sonrac\lumenRest\Oauth2ServiceProvider::class);

$app->make('Illuminate\Contracts\Console\Kernel');

return $app;