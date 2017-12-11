<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 9/1/17
 * Time: 5:48 PM
 */

namespace sonrac\lumenRest\traits;

use Carbon\Carbon;
use DateTime;

/**
 * Trait UnixTimestampsTrait
 *
 * @property string|null|Carbon $created_at
 * @property string|null|Carbon $updated_at
 *
 * @package sonrac\lumenRest\traits
 */
trait UnixTimestampsTrait
{
    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     * Boot timestamp model
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public static function bootUnixTimestampsTrait()
    {
        $updates = function ($model, $unixFormat = false) {
            /** @var \Illuminate\Database\Eloquent\Model|UnixTimestampsTrait $model */
            foreach ($model->getTimestampAttributes() as $attribute) {
                $model->setModelTimeAttribute($attribute, $model->{$attribute}, $unixFormat);
            }
        };

        $callbackSaved = function ($model) use ($updates) {
            /** @var $model \Illuminate\Database\Eloquent\Model|UnixTimestampsTrait */
            if (!$model->unixTimeStampsEnable()) {
                return;
            }
            $updates($model);
        };

        $callbackSave = function ($model) use ($updates) {
            /** @var $model \Illuminate\Database\Eloquent\Model|UnixTimestampsTrait */
            if (!$model->unixTimeStampsEnable()) {
                return;
            }
            $updates($model, true);
        };

        self::saving($callbackSave);
        self::retrieved($callbackSaved);
        self::saved($callbackSaved);
    }

    /***
     * @param \Illuminate\Database\Eloquent\Model|UnixTimestampsTrait $model
     * @param string                                                  $attribute
     * @param \DateTime|\Carbon\Carbon|int|string                     $value
     * @param bool                                                    $unixFormat
     *
     * @return bool
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function setModelTimeAttribute($attribute, $value, $unixFormat = false)
    {
        if (is_numeric($value)) {
            $value = Carbon::createFromTimestampUTC($value);
        }

        if (!is_object($value) && !($value = strtotime($value))) {
            if (!in_array($attribute, $this->getTimestampAttributes())) {
                return false;
            }
            $value = Carbon::now();
        }

        if ($unixFormat) {
            $this->attributes[$attribute] = $value->getTimestamp();
        } else {
            $this->setAttribute($attribute, $value);
        }

        return true;
    }

    /**
     * Unix timestamp check enable
     *
     * @return bool
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function unixTimeStampsEnable()
    {
        return property_exists($this, 'unixTimestamps') && $this->unixTimestamps;
    }

    /**
     * @return array
     */
    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at'];
    }

    /**
     * Get created_at column
     *
     * @return Carbon|null|string
     */
    public function getCreatedAt()
    {
        return $this->getTimeFromAttribute('created_at');
    }

    /**
     * Get carbon object from timestamp attribute
     *
     * @param string $attribute Attribute name
     *
     * @return Carbon|null|string
     */
    private function getTimeFromAttribute($attribute)
    {
        if (!$this->{$attribute}) {
            return $this->attributes[$attribute] = Carbon::now()->timestamp;
        }

        if (is_string($this->attributes[$attribute]) && $timestamp = strtotime($this->attributes[$attribute])) {
            return $this->attributes[$attribute] = (new Carbon())->setTimestamp($timestamp);
        }

        return $this->attributes[$attribute];
    }

    /**
     * Get created_at column
     *
     * @return Carbon|null|string
     */
    public function getUpdatedAtColumn()
    {
        return $this->getTimeFromAttribute('updated_at');
    }

    /**
     * Get created_at column
     *
     * @return Carbon|null|string
     */
    public function getLastLoginColumn()
    {
        return $this->getTimeFromAttribute('last_login');
    }

    /**
     * Set updated_at attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setUpdatedAtAttribute($time)
    {
        $this->setDate('updated_at', $time);
    }

    /**
     * Set date for column in object style
     *
     * @param string                     $column
     * @param string|int|Carbon|DateTime $value
     *
     * @return Carbon
     *
     * @throws \Exception
     */
    private function setDate($column, $value)
    {
        if (is_string($value) || is_numeric($value)) {
            if (static::isValidTimeStamp((string) $value)) {
                return $this->attributes[$column] = (new Carbon())->setTimestamp($value);
            }
            $time = strtotime($value);
            if (!$time) {
                throw new \Exception('Invalid date');
            }

            return $this->attributes[$column] = (new Carbon())->setTimestamp((int) $value);
        }

        if ($value instanceof DateTime) {
            return $this->attributes[$column] = (new Carbon())->setTimestamp($value->getTimestamp());
        }

        throw new \Exception('Invalid date');
    }

    public static function isValidTimeStamp($timestamp)
    {
        return ((string) (int) $timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    /**
     * Set updated_at attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setUpdatedAt($time)
    {
        $this->setDate('updated_at', $time);
    }

    /**
     * Set created_at attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setCreatedAtAttribute($time)
    {
        $this->setDate('created_at', $time);
    }

    /**
     * Set created_at attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setCreatedAt($time)
    {
        $this->setDate('created_at', $time);
    }

    /**
     * Set last_login attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setLastLogin($time)
    {
        $this->setDate('last_login', $time);
    }

    /**
     * Set last_login attribute
     *
     * @param int|string|Carbon|DateTime $time
     */
    public function setLastLoginAttribute($time)
    {
        $this->setDate('last_login', $time);
    }
}
