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
$app->configure('oauth2');

$app->register(\sonrac\lumenRest\Oauth2ServiceProvider::class);

$app->make('Illuminate\Contracts\Console\Kernel');

$app->router->post('/oauth/access_token', '\sonrac\lumenRest\controllers\OauthController@accessToken');
$app->router->get('/oauth/access_token', '\sonrac\lumenRest\controllers\OauthController@accessToken');

$app->router->post('/authorize', '\sonrac\lumenRest\controllers\OauthController@userAuthorize');
$app->router->get('/authorize', '\sonrac\lumenRest\controllers\OauthController@userAuthorize');

return $app;