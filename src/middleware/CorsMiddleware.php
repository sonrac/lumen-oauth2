<?php
/**
 * Created by PhpStorm.
 * User: conci
 * Date: 9/29/17
 * Time: 2:32 PM.
 */

namespace sonrac\lumenRest\middleware;

/**
 * Class CorsMiddleware
 * Add Allow cross origin requests header middleware.
 */
class CorsMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        /** |\Illuminate\Http\Response */
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);
        $response->headers->add([
            'Access-Control-Allow-Methods'  => 'HEAD, GET, POST, PUT, PATCH, DELETE',
            'Access-Control-Allow-Headers'  => $request->header('Access-Control-Request-Headers'),
            'Access-Control-Expose-Headers' => 'location, bearer, cache-control, content-type, x-application-token, '.
                'authorization',
            'Access-Control-Allow-Origin'   => '*',
        ]);

        return $response;
    }
}
