<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 10/2/17
 * Time: 1:01 PM
 */

namespace sonrac\lumenRest\middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthMiddleware
 *
 * @package sonrac\lumenRest\middleware
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthMiddleware
{
    protected $_server;

    /**
     * AuthMiddleware constructor.
     *
     * @param \League\OAuth2\Server\ResourceServer $server
     */
    public function __construct(ResourceServer $server)
    {
        $this->_server = $server;
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = app()->make(ResponseInterface::class);
        $request = app()->make(ServerRequestInterface::class);
        try {
            $request = $this->_server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
            // @codeCoverageIgnoreStart
        } catch (\Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
            // @codeCoverageIgnoreEnd
        }

        // Pass the request and response on to the next responder in the chain
        return $next($request, $response);
    }

}