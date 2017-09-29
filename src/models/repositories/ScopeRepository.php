<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models\repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * Class ScopeRepository
 *
 * @package sonrac\lumenRest\models\repositories
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * Scope entity
     *
     * @var \League\OAuth2\Server\Entities\ScopeEntityInterface|\sonrac\lumenRest\models\Scope
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $scope;

    public function __construct(ScopeEntityInterface $scope)
    {
        $this->scope = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $this->scope->name = $identifier;

        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    )
    {
        $finalScopes = [];

        foreach ($scopes as $scope) {
            if (!is_object($scope)) {
                $class = get_class($this->scope);
                if (is_array($scope)) {
                    $finalScopes[] = new $class($scope);
                } else {
                    $finalScopes[] = new $class(['name' => $scope]);
                }

                continue;
            }

            $finalScopes[] = $scope;
        }

        return $finalScopes;
    }

}