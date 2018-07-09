<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface as RTokenEntityInterface;
use sonrac\lumenRest\contracts\RefreshTokenEntityInterface;
use sonrac\lumenRest\contracts\repositories\RefreshTokenRepositoryInterface;
use sonrac\lumenRest\models\RefreshToken;

/**
 * Class RefreshTokenRepository.
 *
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
    protected $refreshToken = null;

    public function __construct(RefreshTokenEntityInterface $refreshToken)
    {
        $this->refreshToken                = $refreshToken;
        $this->refreshToken->refresh_token = Str::random(64);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RTokenEntityInterface $refreshTokenEntity)
    {
        $refreshTokenEntity->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        DB::table('refresh_tokens')
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
        if ($token = $this->refreshToken->find($tokenId)) {
            return $token->revoked;
        }

        return false;
    }
}
