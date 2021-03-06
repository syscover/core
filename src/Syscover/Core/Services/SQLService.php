<?php namespace Syscover\Core\Services;

use Illuminate\Support\Facades\Schema;
use Syscover\Core\Exceptions\ParameterNotFoundException;
use Syscover\Core\Exceptions\ParameterValueException;

/**
 * Class SQLService
 * @package Syscover\Core\Services
 */
class SQLService
{
    /**
     * Get query apply sql or filters
     *
     * @param $query
     * @param array $sql
     * @param null $filters
     * @return mixed
     */
    public static function getGroupQueryFiltered($query, $sql = null, $filters = null)
    {
        if(! $sql) $sql = [];

        // filter all data by lang
        if(isset($filters) && is_array($filters))
        {
            // filter query
            $query = SQLService::setGroupQueryFilter($query, $filters);

            // apply query parameters over filter
            $query->where(function ($query) use ($sql) {
                SQLService::setGroupQueryFilter($query, $sql);
            });
        }
        else
        {
            $query = SQLService::setGroupQueryFilter($query, $sql);
        }

        return $query;
    }

    /**
     * @param $query
     * @param null $filters sql to filter total count
     * @return mixed
     */
    public static function countGroupPaginateTotalRecords($query, $filters = null)
    {
        if(isset($filters))
            $query = SQLService::setGroupQueryFilter($query, $filters);

        return $query->count();
    }

    /**
     * @param $query
     * @param $filter
     * @return mixed
     *
     * Example of filter parameter
     *  [
     *      'filter' => [
     *          'type'  => 'and',  // operator between groups
     *          'sql'   => [
     *              [
     *                  'type'  => 'and', // operator between sql
     *                  'sql'   => [
     *                      [
     *                          'column' => 'prefix',
     *                          'operator' => '>',
     *                          'value' => 40
     *                      ],
     *                      [
     *                          'column' => 'prefix',
     *                          'operator' => '<',
     *                          'value' => 50
     *                      ]
     *                  ],
     *              ],
     *              [
     *                  'type'  => 'or', // operator between sql
     *                  'sql'   => [
     *                      [
     *                          'column' => 'lang_id',
     *                          'operator' => '=',
     *                          'value' => 'es'
     *                      ],
     *                      [
     *                          // raw property
     *                          'raw' => 'CASE `market_product`.`product_class_tax_id` WHEN 1 THEN (`market_product`.`subtotal` * 1.21) > 8 END'
     *                      ]
     *                  ]
     *              ]
     *          ]
     *      ]
     *  ];
     */
    public static function setGroupQueryFilter($query, $filter)
    {
        if(isset($filter['type']))
        {
            $filter['type'] = strtoupper($filter['type']);

            foreach ($filter['sql'] as $sql)
            {
                if(isset($sql['column']) || isset($sql['raw']))
                {
                    if($filter['type'] === 'AND')
                    {
                        if(isset($sql['raw']))
                        {
                            $query->whereRaw($sql['raw']);
                        }
                        elseif (isset($sql['relation']))
                        {
                            $query->whereHas($sql['relation'], function ($query) use ($sql) {
                                $query->where($sql['column'], $sql['operator'], $sql['value']);
                            });
                        }
                        else
                        {
                            $query->where($sql['column'], $sql['operator'], $sql['value']);
                        }

                    }
                    elseif ($filter['type'] === 'OR')
                    {
                        if(isset($sql['raw']))
                        {
                            $query->orWhereRaw($sql['raw']);
                        }
                        elseif (isset($sql['relation']))
                        {
                            $query->orWhereHas($sql['relation'], function ($query) use ($sql) {
                                $query->where($sql['column'], $sql['operator'], $sql['value']);
                            });
                        }
                        else
                        {
                            $query->orWhere($sql['column'], $sql['operator'], $sql['value']);
                        }
                    }
                }
                else // is a grouped query
                {
                    if($filter['type'] === 'AND')
                    {
                        $query->where(function ($query) use ($sql) {
                            self::setGroupQueryFilter($query, $sql);
                        });
                    }
                    elseif ($filter['type'] === 'OR')
                    {
                        $query->orWhere(function ($query) use ($sql) {
                            self::setGroupQueryFilter($query, $sql);
                        });
                    }
                }
            }
        }

        return $query;
    }

    /**
     * DEPRECATED by getGroupQueryFiltered
     * Get query apply sql or filters
     *
     * @param $query
     * @param array $sql
     * @param null $filters
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function getQueryFiltered($query, $sql = null, $filters = null)
    {
        if(! $sql) $sql = [];

        // filter all data by lang
        if(isset($filters) && is_array($filters))
        {
            // filter query
            $query = SQLService::setQueryFilter($query, $filters);

            // apply query parameters over filter
            $query->where(function ($query) use ($sql) {
                SQLService::setQueryFilter($query, $sql);
            });
        }
        else
        {
            $query = SQLService::setQueryFilter($query, $sql);
        }

        return $query;
    }

    /**
     * DEPRECATED by countGroupPaginateTotalRecords
     * @param $query
     * @param null $filters sql to filter total count
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function countPaginateTotalRecords($query, $filters = null)
    {
        if(isset($filters))
            $query = SQLService::setQueryFilter($query, $filters);

        return $query->count();
    }

    /**
     * DEPRECATED by setGroupQueryFilter
     * @param $query
     * @param $filters
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function setQueryFilter($query, $filters)
    {
        // commands without pagination and limit
        foreach ($filters as $sql)
        {
            if(! isset($sql['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($sql));

            if(($sql['command'] === "where" || $sql['command'] === "orderBy") && ! isset($sql['column']))
                throw new ParameterNotFoundException('Parameter column not found in request, please set column parameter in ' . json_encode($sql));

            if(($sql['command'] === "where" || $sql['command'] === "orderBy") && ! isset($sql['operator']))
                throw new ParameterNotFoundException('Parameter operator not found in request, please set operator parameter in ' . json_encode($sql));

            switch ($sql['command'])
            {
                case 'offset':
                case 'limit':
                case 'orderBy':
                    // commands not accepted
                    break;
                case 'where':
                    $query->where($sql['column'], $sql['operator'], $sql['value']);
                    break;
                case 'orWhere':
                    $query->orWhere($sql['column'], $sql['operator'], $sql['value']);
                    break;
                case 'whereIn':
                    $query->whereIn($sql['column'], $sql['value']);
                    break;
                case 'whereJsonContains':
                    $query->whereJsonContains($sql['column'], $sql['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }
        }

        return $query;
    }

    /**
     * @param   $query
     * @param   array $filters
     * @return  mixed
     * @throws  ParameterNotFoundException
     * @throws  ParameterValueException
     */
    public static function getQueryOrderedAndLimited($query, $filters = null)
    {
        if(! $filters) return $query;

        // sentences for order query and limited
        foreach ($filters as $sql)
        {
            if(! isset($sql['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($sql));

            if(($sql['command'] === "offset" || $sql['command'] === "limit") && ! isset($sql['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter in: ' . json_encode($sql));

            switch ($sql['command']) {
                case 'where':
                case 'orWhere';
                case 'whereIn';
                case 'whereJsonContains';
                    // commands not accepted, already
                    // implemented in Syscover\Core\Services\SQLService::setQueryFilter method
                    break;
                case 'orderBy':
                    $query->orderBy($sql['column'], $sql['operator']);
                    break;
                case 'offset':
                    $query->offset($sql['value']);
                    break;
                case 'limit':
                    $query->limit($sql['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be offset or take');
            }
        }

        return $query;
    }

    /**
     * @param int           $id
     * @param string        $modelClassName
     * @param string|null   $langId
     * @param string|null   $modelLangClassName
     * @param array         $filters            filters to select and delete records
     * @return mixed
     */
    public static function deleteRecord(
        $id,
        string $modelClassName,
        string $langId = null,
        string $modelLangClassName = null,
        array $filters = []
    )
    {
        // get data to do model queries
        $model      = new $modelClassName;
        $table      = $model->getTable();
        $primaryKey = $model->getKeyName();

        /**
         *  Delete object with lang.
         *  If destroy baseLang object, delete all objects with this id
         */
        if(isset($langId))
        {
            /**
             * Check if controller has defined $modelLangClassName property,
             * if has $modelLangClassName, this means that the translations are in another table.
             * Get table name to do the query
             */
            if($modelLangClassName !== null)
            {
                // get data to do model queries
                $modelLang      = new $modelLangClassName;
                $tableLang      = $modelLang->getTable();

                // get object from main table and lang table
                // in builder method do the join between table and table lang
                $object = $model->builder()
                    ->where($tableLang . '.lang_id', $langId)
                    ->where($table . '.id', $id)
                    ->first();

                // check if must delete base_lang object
                if(base_lang() === $langId)
                {
                    // Delete record from main table and delete records in table lang by relations
                    $model::where($table . '.id', $id)
                        ->delete();

                    return $object;
                }

                /**
                 * This option is for tables that dependent of other tables to set your languages
                 * set parameter $deleteLangDataRecord to false, because lang model haven't data_lag column
                 */
                $modelLang->deleteTranslationRecord($id, $langId, false);

                /**
                 * This kind of tables has field data_lang in main table, not in lang table
                 * delete data_lang parameter
                 */
                $model->deleteDataLang($langId, $id, 'id');

                /**
                 * Count records, to know if has more lang
                 */
                $nRecords = $modelLang->builder()
                    ->where($tableLang . '.id', $id)
                    ->count();

                /**
                 * if haven't any lang record, delete record from main table
                 */
                if($nRecords === 0)
                {
                    $model->where($table . '.' . $primaryKey, $id)
                        ->delete();
                }

                return $object;
            }
            else
            {
                $query = $model->builder()
                    ->where($table . '.id', $id);

                /**
                 * The table may have lang parameter but not have the field lang_id.
                 * Whe is false, the model overwrite method deleteTranslationRecord
                 * to delete json language field, for example in field table with labels column
                 */
                if(Schema::hasColumn($table, 'lang_id')) $query->where($table . '.lang_id', $langId);

                $object = $query->filterQuery($filters)->first();

                // check if must delete base_lang object
                if(base_lang() === $langId)
                {
                    // Delete record from main table and delete records in table lang by relations
                    $model::where($table . '.id', $id)
                        ->delete();

                    return $object;
                }

                // delete record from table without dependency from other table lang
                $model->deleteTranslationRecord($id, $langId, true, $filters);

                return $object;
            }
        }
        else
        {
            $object = $model->builder()
                    ->where($table . '.id', $id)
                    ->filterQuery($filters)
                    ->first();

            // Delete single record
            $object->delete();

            return $object;
        }
    }
}
