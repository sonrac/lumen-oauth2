<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use sonrac\lumenRest\traits\UnixTimestampsTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class AccessToken
 *
 * @property string                       $access_token
 * @property int                          $client_id
 * @property string                       $grant_type
 * @property int                          $user_id
 * @property boolean                      $revoke
 * @property int                          $expire_date_time
 *
 * @property \sonrac\lumenRest\models\Client           $client
 * @property \sonrac\lumenRest\models\Scope|Collection $scopes
 *
 * @property User                         $user
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AccessToken extends Model implements AccessTokenEntityInterface
{
    use AccessTokenTrait,
        UnixTimestampsTrait,
        EntityTrait;

    const TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const TYPE_AUTHORIZATION_CODE = 'code';
    const TYPE_PASSWORD = 'password';
    const TYPE_IMPLICIT = 'token';
    const TYPE_REFRESH_TOKEN = 'refresh_token';

    protected $fillable = [
        'id', 'access_token', 'client_id', 'user_id', 'grant_type', 'created_at', 'updated_at', 'revoked'
    ];

    protected $hidden = [
        'expire_date_time',
    ];

    public $unixTimestamps = true;

    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            /** @var $model \sonrac\lumenRest\models\AccessToken */
        });

        static::updated(function ($model) {
            /** @var $model \sonrac\lumenRest\models\AccessToken */
        });

        static::deleted(function ($model) {
            /** @var $model \sonrac\lumenRest\models\AccessToken */
        });
    }

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
     * Associate a scope with the token.
     *
     * @param ScopeEntityInterface[] $scopes
     */
    public function addScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        if (!$this->scopes) {
            $this->setRelation('scopes', new Collection([]));
        }

        /** @var $scope \sonrac\lumenRest\models\Scope */
        $this->scopes[$scope->name] = $scope;
    }

    /**
     * Set client
     *
     * @param ClientEntityInterface $client
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client_id = $client->getIdentifier();
        $this->client = $client;
        $this->setRelation('client', $client);
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(app(UserEntityInterface::class)->class, 'id', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryDateTime()
    {
        return $this->attributes['expire_date_time'];
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->setDate('expire_date_time', $dateTime);;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserIdentifier($identifier)
    {
        $this->attributes['user_id'] = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Client relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|\sonrac\lumenRest\models\Client|\League\OAuth2\Server\Entities\ClientEntityInterface
     */
    public function client()
    {
        return $this->hasOne(app(ClientEntityInterface::class)->class, 'id', 'client_id');
    }

    /**
     * Scopes relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function scopes()
    {
        return $this->belongsToMany(get_class(app(ScopeEntityInterface::class)),
            'access_token_scopes', 'access_token', 'scope',
            'access_token', 'name');
    }
}