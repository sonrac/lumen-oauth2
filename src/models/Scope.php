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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->name;
    }

    /**
     * Set name attribute
     *
     * @param string $name
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function setNameColumn($name)
    {
        $this->setNameAttribute($name);
    }

    /**
     * Set name attribute
     *
     * @param string $name
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = is_object($name) ? $name->name : $name;
    }
}
