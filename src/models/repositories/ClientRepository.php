<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use sonrac\lumenRest\models\Client;

/**
 * Class ClientRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class ClientRepository extends Client implements ClientRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $query = Client::query()
            ->where('clients.id', '=', $clientIdentifier);

        if ($mustValidateSecret) {
            $query->where('secret_key', '=', $clientSecret);
        }

        return $query->first();
    }

}