<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace sonrac\lumenRest\contracts\repositories;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface as ACodeRepositoryInterface;
/**
 * Auth code storage interface.
 */
interface AuthCodeRepositoryInterface extends ACodeRepositoryInterface
{
}
