<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:56 PM
 */

namespace sonrac\lumenRest\controllers;

use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OauthController
 * Login controller
 *
 * @package sonrac\lumenRest\controllers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class OauthController extends Controller
{
    /**
     * PSR-7 Authorization server
     *
     * @var ServerRequestInterface
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_sever;

    /**
     * PSR-7 Response
     *
     * @var ResponseInterface
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_response;

    public function __construct(ServerRequestInterface $serverRequest, ResponseInterface $response)
    {
        $this->_response = $response;
        $this->_sever = $serverRequest;
    }

    /**
     * Get access token for client
     *
     * @param ServerRequestInterface $request
     *
     * @throws
     *
     * @return Response
     */
    public function accessToken()
    {
        /** @var AuthorizationServer $server */
        $server = app('oauth2.server');

        try {
            return $server->respondToAccessTokenRequest($this->_sever, $this->_response);
        } catch (OAuthServerException $exp) {
            return response()->json([
                'error_type' => $exp->getErrorType(),
                'error'      => $exp->getHint(),
                'message'    => $exp->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function userAuthorize(ServerRequestInterface $request, ResponseInterface $response,
                                  AuthorizationServer $server, UserEntityInterface $userEntity)
    {
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser($userEntity); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        $resp = $server->completeAuthorizationRequest($authRequest, $response);

        return $resp;
    }
}