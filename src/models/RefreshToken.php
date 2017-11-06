<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class RefreshToken
 * Refresh tokens model
 *
 * @property string                       $access_token   Access token
 * @property string                       $refresh_token  Refresh token
 * @property bool                      $revoked        Is revoked
 * @property Carbon|\DateTime|string|null $expires_at     Expire refresh date
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class RefreshToken extends Model implements RefreshTokenEntityInterface
{
    use UnixTimestampsTrait;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;
    /**
     * {@inheritdoc}
     */
    protected $table = 'refresh_tokens';
    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'access_token';
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_at',
        'revoked',
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'revoked' => false,
    ];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->access_token = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryDateTime()
    {
        return $this->expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expires_at = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        /* @var $accessToken AccessToken|AccessTokenEntityInterface */
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

    /**
     * {@inheritdoc}
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at', 'expires_at'];
    }
}
