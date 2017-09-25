<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * Class AuthCode
 * Auth codes model
 *
 * @property int    $id
 * @property string $code
 * @property int    $client_id
 * @property int    $user_id
 * @property string $redirect_uri
 *
 * @property User   $user
 * @property Client $clientApp
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class AuthCode extends Model implements AuthCodeEntityInterface
{
    use TokenEntityTrait;

    protected $table = 'auth_codes';
    protected $fillable = ['redirect_uri', 'client_id', 'user_id'];

    protected static function boot()
    {
        static::registerModelEvent('booted', function ($model) {
            /** @var $model AuthCode */
            $model->getCodeAttribute();
        });

        parent::boot();
    }

    public function getCodeAttribute()
    {
        if (!$this->code) {
            $this->code = Str::random(52);
        }
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
    public function getClient()
    {
        return $this->client ?? $this->client = $this->clientApp;
    }

    /**
     * Client model relation
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
        $this->client = $client;
        $this->setRelation('clientApp', $client);
    }

    /**
     * User relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(Client::class, 'user_id', 'id');
    }
}