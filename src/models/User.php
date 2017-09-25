<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use sonrac\lumenRest\traits\UnixTimestampsTrait;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class User
 * User class
 *
 * @property int               $id
 * @property string            $username
 * @property string            $first_name
 * @property string            $last_name
 * @property string            $email
 * @property string            $password
 * @property int|string|Carbon $last_login
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class User extends Model implements UserEntityInterface, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable,
        Authorizable,
        UnixTimestampsTrait;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'created_at', 'updated_at', 'last_login', 'first_name', 'last_name', 'last_login'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }

}