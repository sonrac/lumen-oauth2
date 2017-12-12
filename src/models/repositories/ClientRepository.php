<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use sonrac\lumenRest\contracts\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\ClientRepositoryInterface as CRepositoryInterface;

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
    public function __construct(CRepositoryInterface $client = null)
    {
        $this->client = $client ?? app(ClientEntityInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityByIdentifier($identifier)
    {
        return $this->client::find($identifier);
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
