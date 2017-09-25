<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class AccessToken
 *
 * @property int                $id
 * @property int                $user_id
 * @property string             $name
 * @property string             $secret_key
 * @property string             $redirect_uri
 * @property Carbon|string|null $last_login
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Client extends Model implements ClientEntityInterface
{
    protected $fillable = [
        'user_id', 'redirect_uri', 'last_login', 'name', 'created_at', 'updated_at',
    ];

    protected $hidden = [
        'secret_key',
    ];

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
    public function getName()
    {
        return $this->attributes['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUri()
    {
        return $this->attributes['redirect_url'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function token()
    {
        return $this->belongsTo(get_class(app(AccessTokenEntityInterface::class)), 'client_id', 'id');
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|User|\Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function user()
    {
        return $this->belongsTo(get_class(app(UserEntityInterface::class)), 'id', 'user_id');
    }

}