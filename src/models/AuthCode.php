<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use sonrac\lumenRest\contracts\AuthCodeEntityInterface;
use sonrac\lumenRest\contracts\ScopeEntityInterface as SEntityInterface;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class AuthCode
 * Auth codes model.
 *
 * @property int    $id           ID
 * @property string $code         Code
 * @property int    $client_id    Client ID
 * @property int    $user_id      User ID
 * @property bool   $revoked      Is revoked
 * @property int    $expires_at   Expire at
 * @property string $redirect_uri Redirect uri
 * @property User   $user
 * @property Client $clientApp
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthCode extends Model implements AuthCodeEntityInterface
{
    use UnixTimestampsTrait;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;
    /**
     * {@inheritdoc}
     */
    protected $table = 'auth_codes';
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'redirect_uri',
        'client_id',
        'user_id',
        'created_at',
        'updated_at',
        'code',
        'expires_at',
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
    protected $primaryKey = 'code';

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        $serializeScopes = function ($model) {
            /** @var $model \sonrac\lumenRest\models\AccessToken */
            if (!isset($model->attributes['code_scopes'])) {
                $model->attributes['code_scopes'] = '';
            }

            $scopes = '';
            if (\is_object($model->attributes['code_scopes']) || \is_array($model->attributes['code_scopes'])) {
                foreach ($model->attributes['code_scopes'] as $code_scopes) {
                    /** @var $code_scopes \sonrac\lumenRest\models\Scope */
                    if (\is_array($code_scopes)) {
                        $scopes .= ' '.\explode(' ', $code_scopes);
                        continue;
                    }
                    if (\is_object($code_scopes)) {
                        $scopes .= ' '.\trim($code_scopes->name);
                    } else {
                        $scopes .= ' '.\trim($code_scopes);
                    }
                }
            }

            $model->attributes['code_scopes'] = \trim($scopes);
        };

        $deserializeScopes = function ($model) {
            /** @var \sonrac\lumenRest\models\AccessToken $model */
            if (!isset($model->attributes['code_scopes'])) {
                $model->attributes['code_scopes'] = '';
            }
            if ($model->attributes['code_scopes'] && \is_string($model->attributes['code_scopes'])) {
                $scopeClass  = \get_class(app(SEntityInterface::class));
                $scopes      = \explode(' ', $model->attributes['code_scopes']);
                $finalScopes = new Collection();
                foreach ($scopes as $scope) {
                    $scope = \trim($scope);

                    if ($scope) {
                        $finalScopes->add((new $scopeClass(['name' => $scope])));
                    }
                }

                $model->attributes['code_scopes'] = $finalScopes;
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
    public function getRedirectUri()
    {
        return $this->getAttributeFromArray('redirect_uri');
    }

    /**
     * {@inheritdoc}
     */
    public function setRedirectUri($uri)
    {
        $this->attributes['redirect_uri'] = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->code = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client ?? $this->client = $this->clientApp;
    }

    /**
     * Client model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function clientApp()
    {
        return $this->hasOne(Client::class, 'client_id', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client_id = $client->getIdentifier();
        $this->setRelation('clientApp', $client);
    }

    /**
     * User relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(\get_class(app('sonrac\lumenRest\contracts\ClientEntityInterface')), 'user_id', 'id');
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
    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at', 'expires_at'];
    }

    /**
     * {@inheritdoc}
     */
    public function setUserIdentifier($identifier)
    {
        $this->user_id = $identifier;
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
    public function getExpiryDateTime()
    {
        return $this->expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        if (!isset($this->attributes['code_scopes'])) {
            $this->attributes['code_scopes'] = [];
        }
        /* @var $scope \sonrac\lumenRest\models\Scope */
        $this->attributes['code_scopes'][$scope->getIdentifier()] = $scope->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return isset($this->attributes['code_scopes']) ? $this->attributes['code_scopes'] : '';
    }
}
