<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use sonrac\lumenRest\contracts\AuthCodeEntityInterface as ACodeEntityInterface;
use sonrac\lumenRest\contracts\repositories\AuthCodeRepositoryInterface;

/**
 * Class AuthCodeRepository.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /** @var \sonrac\lumenRest\contracts\AuthCodeEntityInterface|\sonrac\lumenRest\models\AuthCode */
    protected $authCode = null;

    public function __construct(ACodeEntityInterface  $authCode)
    {
        $this->authCode = $authCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return $this->authCode;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCodeEntity->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        DB::table('auth_codes')
            ->where('code', '=', $codeId)
            ->update([
                'revoked' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        if ($token = $this->authCode->find($codeId)) {
            /* @var $token \sonrac\lumenRest\models\AuthCode */
            return $token->revoked;
        }

        return false;
    }
}
