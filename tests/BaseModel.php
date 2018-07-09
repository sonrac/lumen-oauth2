<?php


namespace sonrac\lumenRest\tests;

/**
 * Class BaseModel
 */

use Illuminate\Database\Eloquent\Model;
use sonrac\lumenRest\traits\UnixTimestampsTrait;

/**
 * Class BaseModel
 * Base model timestamps.
 *
 * @property int                         $id
 * @property string                      $name
 * @property string|Carbon|DateTime|null $last_login
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class BaseModel extends Model
{
    use UnixTimestampsTrait;

    public $timestamps     = true;
    public $unixTimestamps = true;
    protected $table       = 'test';
    protected $fillable    = ['last_login', 'created_at', 'updated_at', 'name'];

    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at', 'last_login'];
    }

    public function getTable()
    {
        return 'test';
    }
}
