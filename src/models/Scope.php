<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\models;

use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * Class Scope
 * Scopes model
 *
 * @property string $name        Scope name
 * @property string $description Scope description
 *
 * @package sonrac\lumenRest\models
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Scope extends Model implements ScopeEntityInterface
{

    protected $fillable = ['name', 'description'];

    protected $table = 'scopes';

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->name;
    }

}