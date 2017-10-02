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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;

class JWT implements Guard
{
    use GuardHelpers, Macroable;

    protected $name;
    protected $provider;
    protected $request;
    protected $user;
    protected $client;

    public function __construct($name, UserProvider $provider, ResourceServer $server, ServerRequestInterface $request)
    {
        $this->name = $name;
        $this->provider = $provider;

        $this->request = $server->validateAuthenticatedRequest($request);

        $this->client = app(ClientEntityInterface::class)->find($this->request->getAttribute('oauth_client_id'));
        if ($user = $this->request->getAttribute('oauth_user_id')) {
            $this->user = app(UserEntityInterface::class)->find($user);
        } else if ($this->client && $this->client->user_id) {
            $this->user = $this->client->user;
        }
    }

    public function check()
    {
        return !is_null($this->client) || !is_null($this->user);
    }

    public function user()
    {
        return $this->user;
    }

    public function client()
    {
        return $this->client;
    }

    public function id()
    {
        return $this->user ? $this->user->id : ($this->client ? $this->client->id : null);
    }

    public function validate(array $credentials = [])
    {
        // TODO Add validation for guard client by scopes

        return true;
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }

}