<?php namespace Syscover\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @package Syscover\Pulsar\Core
 */

abstract class CoreModel extends BaseModel
{
    /**
     * Overwrite construct to set params in model
     *
     * Model constructor.
     * @param array $attributes
     */
    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param   $query
     * @return  mixed
     */
    public function scopeBuilder($query)
    {
        return $query;
    }
}