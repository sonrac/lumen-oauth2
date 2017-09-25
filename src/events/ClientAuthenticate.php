<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:26 PM
 */

namespace sonrac\lumenRest\events;

use sonrac\lumenRest\models\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * Class ClientAuthenticate
 * Client authenticate event
 *
 * @package sonrac\lumenRest\events
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class ClientAuthenticate extends Event
{
    protected $_client;

    /**
     * ClientAuthenticate constructor.
     *
     * @param ClientEntityInterface|Client $client Client
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function __construct(ClientEntityInterface $client)
    {
        $this->_client = $client;
    }

    /**
     * Handle client authenticate event
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function handle()
    {

    }
}