<?php namespace Syscover\Core\Traits;

trait HasAttribute
{
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }
}