<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 10/2/17
 * Time: 1:01 PM.
 */

namespace sonrac\lumenRest\middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

/**
 * Class AuthMiddleware
 * OAuth2 authenticate middleware.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthMiddleware
{
    protected $server;

    /**
     * AuthMiddleware constructor.
     *
     * @param \League\OAuth2\Server\ResourceServer $server
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = app()->make(ResponseInterface::class);
        $request  = (new DiactorosFactory())->createRequest($request);
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
        }

        return $next($request, $response);
    }
}
