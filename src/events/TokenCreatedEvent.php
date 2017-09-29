<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:25 PM
 */

namespace sonrac\lumenRest\events;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * Class TokenCreatedEvent
 * Token created event
 *
 * @package sonrac\lumenRest\events
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class TokenCreatedEvent extends Event
{
    /**
     * Token
     *
     * @var \sonrac\lumenRest\models\AccessToken
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $_token;

    /**
     * TokenCreatedEvent constructor.
     *
     * @param \League\OAuth2\Server\Entities\AccessTokenEntityInterface $accessToken
     */
    public function __construct(AccessTokenEntityInterface $accessToken)
    {
        $this->_token = $accessToken;
    }

    /**
     * Handle created token event
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function handle()
    {

    }
}