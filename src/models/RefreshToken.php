<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;


/**
 * Class RefreshToken
 * Refresh tokens model
 *
 * @property int                          $id            ID
 * @property string                       $access_token  Access token
 * @property string                       $refresh_token Refresh token
 * @property boolean                      $revoked       Is revoked
 * @property Carbon|\DateTime|string|null $expire_date   Expire refresh date
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class RefreshToken extends Model implements RefreshTokenEntityInterface
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'refresh_tokens';

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['access_token', 'refresh_token', 'expiry_date', 'revoked', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->id = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryDateTime()
    {
        return $this->expire_date;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expire_date = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        /** @var $accessToken AccessToken|AccessTokenEntityInterface */
        $this->setRelation('token', $accessToken);
        $this->attributes['access_token'] = $accessToken->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }
}