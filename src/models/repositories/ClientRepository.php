<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use sonrac\lumenRest\models\Client;

/**
 * Class ClientRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Client model
     *
     * @var ClientEntityInterface|null
     */
    protected $client = null;

    /**
     * ClientRepository constructor.
     *
     * @param ClientEntityInterface $client
     */
    public function __construct(ClientEntityInterface $client = null)
    {
        $this->client = $client ?? app(ClientEntityInterface::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $query = $this->client->query()
            ->where('clients.id', '=', $clientIdentifier);

        if ($mustValidateSecret) {
            $query->where('secret_key', '=', $clientSecret);
        }

        return $query->first();
    }

}