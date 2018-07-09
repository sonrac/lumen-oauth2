<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use sonrac\lumenRest\contracts\AccessTokenEntityInterface;
use sonrac\lumenRest\contracts\ClientEntityInterface;
use sonrac\lumenRest\contracts\UserEntityInterface;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class AccessToken.
 *
 * @property int                $id           ID
 * @property int                $user_id      User ID
 * @property string             $name         Name
 * @property string             $secret_key   Secret key
 * @property string             $redirect_uri Redirect URI
 * @property Carbon|string|null $last_login   Last login
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Client extends Model implements ClientEntityInterface
{
    use UnixTimestampsTrait;

    protected $fillable = [
        'user_id',
        'redirect_uri',
        'last_login',
        'name',
        'created_at',
        'updated_at',
        'secret_key',
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
        return $this->attributes['redirect_uri'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function token()
    {
        return $this->belongsTo(\get_class(app(AccessTokenEntityInterface::class)), 'client_id', 'id');
    }

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|User|\Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function user()
    {
        return $this->belongsTo(\get_class(app(UserEntityInterface::class)), 'user_id', 'id');
    }
}
