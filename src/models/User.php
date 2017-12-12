<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use sonrac\lumenRest\contracts\UserEntityInterface;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class User
 * User class
 *
 * @property int               $id         ID
 * @property string            $username   Username
 * @property string            $first_name First name
 * @property string            $last_name  Last name
 * @property string            $email      Email
 * @property string            $password   Password
 * @property int|string|Carbon $last_login Last login date
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
        'name',
        'email',
        'created_at',
        'updated_at',
        'last_login',
        'first_name',
        'last_name',
        'last_login',
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
