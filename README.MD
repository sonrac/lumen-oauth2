[![Build Status](https://travis-ci.org/sonrac/lumen-oauth2.svg?branch=master)](https://travis-ci.org/sonrac/lumen-oauth2) 
![StyleCI](https://styleci.io/repos/105322873/shield?style=flat)

# Installation

```bash
composer require 
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

```