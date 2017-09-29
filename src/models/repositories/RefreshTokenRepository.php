<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use Illuminate\Support\Str;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use sonrac\lumenRest\models\RefreshToken;


/**
 * Class RefreshTokenRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var RefreshTokenEntityInterface|RefreshToken
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_refreshToken = null;

    public function __construct(RefreshTokenEntityInterface $refreshToken)
    {
        $this->_refreshToken = $refreshToken;
        $this->_refreshToken->refresh_token = Str::random(64);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return $this->_refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshTokenEntity->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        \DB::table('refresh_tokens')
            ->where('refresh_token', '=', $tokenId)
            ->update([
                'revoked' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        /** @var RefreshToken $token */
        if ($token = $this->_refreshToken->find($tokenId)) {
            return $token->revoked;
        }

        return false;
    }

}