<?php namespace Syscover\Core\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

// Scope to be used in tables with table lang related
class TableLangScope implements Scope
{
    /**
     * Apply the scope to a given table lang.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();
        $builder->join($table . '_lang', $table . '.id', '=', $table . '_lang.id');
    }
}