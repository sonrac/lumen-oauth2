[![Build Status](https://travis-ci.org/sonrac/lumen-oauth2.svg?branch=master)](https://travis-ci.org/sonrac/lumen-oauth2) 
![Scrutinizer Build](https://scrutinizer-ci.com/g/sonrac/lumen-oauth2/badges/build.png?b=master)
![Scrutinizer](https://scrutinizer-ci.com/g/sonrac/lumen-oauth2/badges/quality-score.png?b=master)
![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/sonrac/lumen-oauth2/badges/coverage.png?b=master)
[![codecov](https://codecov.io/gh/sonrac/lumen-oauth2/branch/master/graph/badge.svg)](https://codecov.io/gh/sonrac/lumen-oauth2)
![Packagist](https://poser.pugx.org/sonrac/lumen-league-oauth2/v/stable.svg)
[![Latest Unstable Version](https://poser.pugx.org/sonrac/lumen-league-oauth2/v/unstable)](https://packagist.org/packages/sonrac/lumen-league-oauth2)
![License](https://poser.pugx.org/laravel/framework/license.svg)
![VersionEYE](https://www.versioneye.com/user/projects/59d60bec368b0864f151d18c/badge.svg?style=flat-square)
[![Total Downloads](https://poser.pugx.org/sonrac/lumen-league-oauth2/downloads)](https://packagist.org/packages/sonrac/lumen-league-oauth2)
[![composer.lock](https://poser.pugx.org/sonrac/lumen-league-oauth2/composerlock)](https://packagist.org/packages/sonrac/lumen-league-oauth2)

# Installation

```bash
composer require sonrac/lumen-league-oauth2
```

# Usages

Resister service provider first:

Add to your `bootstrap/app.php`

```php
$app->register(\sonrac\lumenRest\Oauth2ServiceProvider::class);
```

# Description

`league/oauth2 -server` implementation for lumen

# Contracts

Contracts or oauth2 server implemented in `sonrac\lumenRest\Oauth2ServiceProvider`

# Events 

Events usages described in [official documentation](https://oauth2.thephpleague.com/authorization-server/events/)

# Middleware

Use `League\OAuth2\Server\Middleware\ResourceMiddleware` for validate authentication request

Use `League\OAuth2\Server\Middleware\AuthorizationServerMiddleware` for user authenticate
 
# Example routing

## Get access token

```php
$app->router->post('/access_token', function (\Psr\Http\Message\ServerRequestInterface $request,
                                              \Psr\Http\Message\ResponseInterface $response) use ($app) {
                                  
  /* @var \League\OAuth2\Server\AuthorizationServer $server */
  $server = $app->make(\League\OAuth2\Server\AuthorizationServer::class);

  try {

      // Try to respond to the request
      return $server->respondToAccessTokenRequest($request, $response);

  } catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {

      // All instances of OAuthServerException can be formatted into a HTTP response
      return $exception->generateHttpResponse($response);

  } catch (\Exception $exception) {

      // Unknown exception
      $body = new \Zend\Diactoros\Stream('php://temp', 'r+');
      $body->write($exception->getMessage());
      return $response->withStatus(500)->withBody($body);

  }
});
```

## Authorize third-party clients (implicit & auth code grants)

```php
$app->router->get('/authorize', function (\League\OAuth2\Server\AuthorizationServer $server,
                                          \Psr\Http\Message\ServerRequestInterface $request,
                                          \Psr\Http\Message\ResponseInterface $response) {
    try {
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(app()->make(\League\OAuth2\Server\Entities\UserEntityInterface::class)); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    } catch (\Exception $exception) {

        // Unknown exception
        $body = new \Zend\Diactoros\Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);

    }
});

```

# JWT guard 

For using JWT token you need define JWT guard

Example config:

```php
'defaults' => [
        'guard' => 'jwt'
    ],
    'guards' => [
        'jwt' => [
            'driver' => 'jwt',
            'provider' => 'clients',
        ],
        'user' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'clients' => [
            'driver' => 'eloquent',
            'model' => app(\League\OAuth2\Server\Entities\ClientEntityInterface::class),
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => app(\League\OAuth2\Server\Entities\UserEntityInterface::class),
        ],
    ],
```

# Generate keys

For using SSL encryption generate keys first:

```bash
php artisan generate:keys
```