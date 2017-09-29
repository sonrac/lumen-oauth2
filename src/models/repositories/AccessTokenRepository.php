<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use Illuminate\Events\Dispatcher;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use sonrac\lumenRest\events\TokenCreatedEvent;

/**
 * Class AccessTokenRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * Access token repository interface
     *
     * @var \League\OAuth2\Server\Entities\AccessTokenEntityInterface|\sonrac\lumenRest\models\AccessToken
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_token;

    /**
     * Events
     *
     * @var \Illuminate\Events\Dispatcher
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_events;

    public function __construct(AccessTokenEntityInterface $token, Dispatcher $events)
    {
        $this->_token = $token;

        $this->_events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        /** @var \sonrac\lumenRest\models\AccessToken $token */
        $token = get_class($this->_token);
        $token = new $token;

        $token->setClient($clientEntity);
        $token->addScopes($scopes);
        $expiryDate = new \DateTime();
        $expiryDate->modify('+ ' . (new \DateInterval(config('oauth2.access_token_ttl', 'PT1H')))->s . ' seconds');
        $token->setExpiryDateTime($expiryDate);
        if ($userIdentifier) {
            $token->setUserIdentifier($userIdentifier);
        }

        $this->_events->dispatch(new TokenCreatedEvent($token));

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->_token->create([
            'access_token' => $accessTokenEntity->getIdentifier(),
            'user_id'      => $accessTokenEntity->getUserIdentifier(),
            'client_id'    => $accessTokenEntity->getClient()->getIdentifier(),
            'revoked'      => false,
            'grant_type'   => 1,
            'created_at'   => new \DateTime,
            'updated_at'   => new \DateTime,
            'expires_at'   => $accessTokenEntity->getExpiryDateTime(),
        ]);

        return $this->_token;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        \DB::table('access_tokens')
            ->where('access_token', '=', $tokenId)
            ->update([
                'revoked' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        if ($token = $this->_token->find($tokenId)) {
            return $token->revoked;
        }

        return false;
    }
}