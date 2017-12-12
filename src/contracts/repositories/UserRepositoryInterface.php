<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\contracts\repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface as URepositoryInterface;

/**
 * Class UserEntityInterface
 *
 * @package sonrac\lumenRest\contracts
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
interface UserRepositoryInterface extends URepositoryInterface
{
    /**
     * @return mixed
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getEntityByIdentifier($identifier);
}