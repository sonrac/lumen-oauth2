<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use sonrac\lumenRest\models\User;
use Illuminate\Database\Query\Builder;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    )
    {
        /** @var User|UserEntityInterface $class */
        $class = get_class(app(UserEntityInterface::class));

        $user = $class::query()
            ->where(function ($q) use ($username) {
                /** @var $q Builder */
                return $q
                    ->where('username', '=', $username)
                    ->orWhere('email', '=', $username);
            })
            ->first();

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

}