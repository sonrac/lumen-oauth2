<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:26 PM
 */

namespace sonrac\lumenRest\events;

use sonrac\lumenRest\models\User;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class UserAuthenticate
 * User authenticate event
 *
 * @package sonrac\lumenRest\events
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class UserAuthenticate extends Event
{
    /**
     * Handle user authenticated event
     *
     * @param UserEntityInterface|User $user User
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function handle(UserEntityInterface $user)
    {

    }
}