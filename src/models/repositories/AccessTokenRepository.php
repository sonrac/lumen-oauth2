<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use sonrac\lumenRest\contracts\AccessTokenEntityInterface as ATokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use sonrac\lumenRest\contracts\repositories\AccessTokenRepositoryInterface;

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
     * @var \sonrac\lumenRest\contracts\AccessTokenEntityInterface|\sonrac\lumenRest\models\AccessToken
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_token;

    public function __construct(ATokenEntityInterface $token)
    {
        $this->_token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        /* @var \sonrac\lumenRest\models\AccessToken $token */
        $this->_token->setClient($clientEntity);
        $this->_token->addScopes($scopes);
        $expiryDate = new \DateTime();
        $expiryDate->modify('+ ' . (new \DateInterval(config('oauth2.access_token_ttl', 'PT1H')))->s . ' seconds');
        $this->_token->setExpiryDateTime($expiryDate);
        if ($userIdentifier) {
            $this->_token->setUserIdentifier($userIdentifier);
        }

        return $this->_token;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $attributes = [
            'access_token' => $accessTokenEntity->getIdentifier(),
            'user_id'      => $accessTokenEntity->getUserIdentifier(),
            'client_id'    => $accessTokenEntity->getClient()->getIdentifier(),
            'revoked'      => false,
            'grant_type'   => 1,
            'created_at'   => new \DateTime,
            'updated_at'   => new \DateTime,
            'expires_at'   => $accessTokenEntity->getExpiryDateTime(),
        ];
        $this->_token->create($attributes);

        return $this->_token;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        DB::table('access_tokens')
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
