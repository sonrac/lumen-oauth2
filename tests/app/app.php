<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

$app = new \sonrac\lumenRest\tests\app\Application(__DIR__);
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
//$app->register(\Illuminate\Auth\AuthServiceProvider::class);

$app->make('Illuminate\Contracts\Console\Kernel');

$app->routeMiddleware([
    'resource' => \sonrac\lumenRest\middleware\AuthMiddleware::class,
]);

$app->router->post('/oauth/access_token', function (\Psr\Http\Message\ServerRequestInterface $request,
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
$app->router->get('/authorize', function (\League\OAuth2\Server\AuthorizationServer $server,
                                          \Psr\Http\Message\ServerRequestInterface $request,
                                          \Psr\Http\Message\ResponseInterface $response) {
    try {
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(app()->make(\sonrac\lumenRest\contracts\UserEntityInterface::class)); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    } catch (\Exception $exception) {

        throw $exception;
        // Unknown exception
        $body = new \Zend\Diactoros\Stream('php://temp', 'r+');
        $body->write($exception->getMessage());

        return $response->withStatus(500)->withBody($body);
    }
});

$app->router->group([
    'middleware' => 'resource',
], function () use ($app) {
    $app->router->post('/user-info', function () use ($app) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $client = \Illuminate\Support\Facades\Auth::client();
        return response()->json([
            'user'   => $user ? $user->getAttributes() : null,
            'client' => $client ? $client->getAttributes() : null,
        ]);
    });
});

return $app;
