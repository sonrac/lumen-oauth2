<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:24 PM
 */

namespace sonrac\lumenRest\events;

use sonrac\lumenRest\models\AccessToken;
use sonrac\lumenRest\models\RefreshToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * Class TokenRefreshedEvent
 * Token refreshed event
 *
 * @package sonrac\lumenRest\events
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class TokenRefreshedEvent extends Event
{
    /**
     * Handle refresh token event
     *
     * @param AccessTokenEntityInterface|AccessToken   $accessToken  Access token
     * @param RefreshTokenEntityInterface|RefreshToken $refreshToken Refresh token
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function handle(AccessTokenEntityInterface $accessToken, RefreshTokenEntityInterface $refreshToken)
    {

    }
}