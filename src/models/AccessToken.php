<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class AccessToken
 * Access token model
 *
 * @property string                                      $access_token Access token
 * @property int                                         $client_id    Client ID
 * @property string                                      $grant_type   Token grant type
 * @property int                                         $user_id      User ID
 * @property boolean                                     $revoked      Is access token revoked
 * @property \sonrac\lumenRest\models\Scope[]|Collection $token_scopes Access token scopes
 * @property int                                         $expires_at   Expire at
 *
 * Relations:
 * @property \sonrac\lumenRest\models\Client             $client       Client
 * @property User                                        $user         User
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AccessToken extends Model implements AccessTokenEntityInterface
{
    use UnixTimestampsTrait,
        EntityTrait;

    /**
     * Client credentials type
     *
     * @cont
     * @type int
     */
    const TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    /**
     * Authorization code type
     *
     * @cont
     * @type int
     */
    const TYPE_AUTHORIZATION_CODE = 'authorization_code';

    /**
     * Authorization response authorization code
     *
     * @cont
     * @type int
     */
    const RESPONSE_AUTHORIZATION_CODE = 'code';

    /**
     * Password type
     *
     * @cont
     * @type int
     */
    const TYPE_PASSWORD = 'password';

    /**
     * Implicit response
     *
     * @cont
     * @type int
     */
    const RESPONSE_IMPLICIT = 'token';

    /**
     * Refresh token type
     *
     * @cont
     * @type int
     */
    const TYPE_REFRESH_TOKEN = 'refresh_token';

    /**
     * {@inheritdoc}
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $unixTimestamps = true;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;
    /**
     * {@inheritdoc}
     */
    public $incrementing = false;
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'access_token',
        'client_id',
        'user_id',
        'grant_type',
        'created_at',
        'token_scopes',
        'updated_at',
        'revoked',
        'expires_at',
    ];
    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'expires_at',
    ];
    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'access_token';
    /**
     * {@inheritdoc}
     */
    protected $table = 'access_tokens';
    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'revoked' => false,
    ];

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        $serializeScopes = function ($model) {
            /** @var $model \sonrac\lumenRest\models\AccessToken */

            if (!isset($model->attributes['token_scopes'])) {
                $model->attributes['token_scopes'] = '';
            }

            $scopes = '';
            if (is_object($model->attributes['token_scopes']) || is_array($model->attributes['token_scopes'])) {
                foreach ($model->attributes['token_scopes'] as $token_scope) {
                    /** @var $token_scope \sonrac\lumenRest\models\Scope */
                    $scopes .= ' ' . trim($token_scope->name);
                }
            }

            $model->attributes['token_scopes'] = trim($scopes);
        };

        $deserializeScopes = function ($model) {
            /** @var \sonrac\lumenRest\models\AccessToken $model */
            if (!isset($model->attributes['token_scopes'])) {
                $model->attributes['token_scopes'] = '';
            }
            if ($model->attributes['token_scopes'] && is_string($model->attributes['token_scopes'])) {
                $scopeClass = get_class(app(ScopeEntityInterface::class));
                $scopes = explode(' ', $model->attributes['token_scopes']);
                $finalScopes = new Collection();
                foreach ($scopes as $scope) {
                    $scope = trim($scope);

                    if ($scope) {
                        $finalScopes->add((new $scopeClass(['name' => $scope])));
                    }
                }

                $model->attributes['token_scopes'] = $finalScopes;
            }
        };

        static::creating($serializeScopes);
        static::updating($serializeScopes);

        static::created($deserializeScopes);
        static::retrieved($deserializeScopes);
        static::updated($deserializeScopes);

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
        if (!isset($this->attributes['token_scopes'])) {
            $this->attributes['token_scopes'] = [];
        }
        /** @var $scope \sonrac\lumenRest\models\Scope */
        $this->attributes['token_scopes'][$scope->getIdentifier()] = $scope->name;
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

        if ($this->client->user_id) {
            $this->setUserIdentifier($this->client->user_id);
        }

        $this->setRelation('client', $client);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserIdentifier($identifier)
    {
        $this->attributes['user_id'] = $identifier;
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
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->setDate('expires_at', $dateTime);;
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
     * {@inheritdoc}
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at', 'expires_at'];
    }

    public function convertToJWT(CryptKey $privateKey)
    {
        $scopes = $this->getScopes();

        if (!empty($scopes) && is_array($scopes)) {

            reset($scopes);
            if (is_object(current($scopes))) {
                $mergedScopes = '';
                foreach ($scopes as $scope) {
                    $mergedScopes .= ' ' . $scope->getIdentifier();
                }
                $scopes = trim($mergedScopes);
            }
        }

        if (is_array($scopes)) {
            $scopes = implode(' ', $scopes);
        }

        return (new Builder())
            ->setAudience($this->getClient()->getIdentifier())
            ->setId($this->getIdentifier(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpiryDateTime()->getTimestamp())
            ->setSubject($this->getUserIdentifier())
            ->set('scopes', $scopes)
            ->sign(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()))
            ->getToken();
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
    public function getIdentifier()
    {
        return $this->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryDateTime()
    {
        if (!$this->attributes['expires_at']) {
            $this->attributes['expires_at'] = Carbon::now()->modify('+3600 seconds');
        }

        return $this->attributes['expires_at'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier()
    {
        return $this->user_id ?? ($this->client ? $this->client->user_id : null);
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return isset($this->attributes['token_scopes']) ? $this->attributes['token_scopes'] : '';
    }
}