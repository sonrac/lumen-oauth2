<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 10/2/17
 * Time: 1:32 PM
 */

namespace sonrac\lumenRest\guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Traits\Macroable;
use sonrac\lumenRest\contracts\ClientEntityInterface;
use sonrac\lumenRest\contracts\UserEntityInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use sonrac\lumenRest\contracts\repositories\ClientRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\UserRepositoryInterface;

class JWT implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * Guard name
     *
     * @var Guard
     */
    protected $name;

    /**
     * Request
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * Client application
     *
     * @var ClientEntityInterface
     */
    protected $client;

    /**
     * JWT constructor.
     *
     * @param                        $name     Guard name
     * @param UserProvider           $provider Provider
     * @param ResourceServer         $server   Resource server
     * @param ServerRequestInterface $request  Request
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function __construct($name, UserProvider $provider, ResourceServer $server, ServerRequestInterface $request)
    {
        $this->name = $name;
        $this->provider = $provider;

        $this->request = $server->validateAuthenticatedRequest($request);

        $this->client = app(ClientRepositoryInterface::class)->getEntityByIdentifier($this->request->getAttribute('oauth_client_id'));
        if ($user = $this->request->getAttribute('oauth_user_id')) {
            $this->user = app(UserRepositoryInterface::class)->getEntityByIdentifier($user);
        } else {
            if ($this->client && $this->client->user_id) {
                $this->user = $this->client->user;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return !is_null($this->client) && !is_null($this->user);
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        return $this->user ? $this->user->id : ($this->client ? $this->client->id : null);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $credentials = [])
    {
        return $this->client() && $this->user();
    }

    /**
     * Get authorization client
     *
     * @return ClientEntityInterface
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Get authenticate user
     *
     * @return UserEntityInterface|Authenticatable
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Set client
     *
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }
}
