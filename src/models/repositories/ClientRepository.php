<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use sonrac\lumenRest\contracts\ClientEntityInterface;
use sonrac\lumenRest\contracts\repositories\ClientRepositoryInterface as CRepositoryInterface;

/**
 * Class ClientRepository.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Client model.
     *
     * @var ClientEntityInterface|null
     */
    protected $client = null;

    /**
     * ClientRepository constructor.
     *
     * @param \sonrac\lumenRest\contracts\ClientEntityInterface $client
     */
    public function __construct(CRepositoryInterface $client = null)
    {
        $this->client = $client ?? app(ClientEntityInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityByIdentifier($identifier)
    {
        return $this->client->find($identifier);
    }

    /**
     * {@inheritdoc}
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
