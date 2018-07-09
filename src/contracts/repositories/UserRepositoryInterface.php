<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\contracts\repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface as URepositoryInterface;

/**
 * Class UserEntityInterface.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
interface UserRepositoryInterface extends URepositoryInterface
{
    /**
     * @author Donii Sergii <doniysa@gmail.com>
     *
     * @param mixed $identifier
     *
     * @return mixed
     */
    public function getEntityByIdentifier($identifier);
}
