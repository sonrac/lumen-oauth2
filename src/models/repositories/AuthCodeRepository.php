<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;


/**
 * Class AuthCodeRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /** @var \League\OAuth2\Server\Entities\AuthCodeEntityInterface|\sonrac\lumenRest\models\AuthCode */
    protected $_authCode = null;

    public function __construct(AuthCodeEntityInterface $authCode)
    {
        $this->_authCode = $authCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return $this->_authCode;
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
        \DB::table('auth_codes')
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
        if ($token = $this->_authCode->find($codeId)) {
            /** @var $token \sonrac\lumenRest\models\AuthCode */
            return $token->revoked;
        }

        return false;
    }

}