<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\contracts\repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface as CRepositoryInterface;

/**
 * Class ClientEntityInterface
 *
 * @package sonrac\lumenRest\contracts
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
interface ClientRepositoryInterface extends CRepositoryInterface
{
    /**
     * Find entity by identifier
     *
     * @param mixed $identifier
     *
     * @return mixed
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function getEntityByIdentifier($identifier);
}