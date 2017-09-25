<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * @var $router \Laravel\Lumen\Routing\Router
 */

$router->get('/oauth/access_token', 'LoginController@accessToken');
$router->post('/oauth/access_token', 'LoginController@accessToken');
$router->get('/', 'HomeController@info');