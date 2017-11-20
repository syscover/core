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
     * @param   $query
     * @param   $sql
     * @param   null $filters
     * @return  mixed
     *
     * Get query apply sql or filters
     */
    public static function getQueryFiltered($query, $sql, $filters = null)
    {
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
     * @param   $query
     * @param   null $filters sql to filter total count
     * @return  mixed
     */
    public static function countPaginateTotalRecords($query, $filters = null)
    {
        if(isset($filters))
            $query = SQLService::setQueryFilter($query, $filters);

        return $query->count();
    }

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


            switch ($sql['command']) {
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


                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }
        }

        return $query;
    }

    public static function getQueryOrderedAndLimited($query, $filters)
    {
        // sentences for order query and limited
        foreach ($filters as $sql)
        {
            if(! isset($sql['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($sql));

            if($sql['command'] !== "orderBy" && ! isset($sql['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter in: ' . json_encode($sql));

            switch ($sql['command']) {
                case 'where':
                case 'orWhere';
                case 'whereIn';
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
     * @param $id
     * @param $modelClassName
     * @param null $langId
     * @param null $modelLangClassName
     * @return mixed
     */
    public static function destroyRecord(
        $id,
        $modelClassName,
        $langId = null,
        $modelLangClassName = null
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
                    ->where($tableLang . '.object_id', $id)
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
                /**
                 * The table may have lang parameter but not have the field lang_id.
                 * Whe is false, the model overwrite method deleteTranslationRecord
                 * to delete json language field, for example in field table with labels column
                 */
                if(Schema::hasColumn($table, 'lang_id'))
                {
                    $object = $model->builder()
                        ->where($table . '.lang_id', $langId)
                        ->where($table . '.object_id', $id)
                        ->first();

                    // check if must delete base_lang object
                    if(base_lang() === $langId)
                    {
                        // Delete record from main table and delete records in table lang by relations
                        $model::where($table . '.object_id', $id)
                            ->delete();

                        return $object;
                    }
                }
                else
                {
                    $object = $model->builder()
                        ->where($table . '.' . $primaryKey, $id)
                        ->first();

                    // check if must delete base_lang object
                    if(base_lang() === $langId)
                    {
                        // Delete record from main table and delete records in table lang by relations
                        $model::where($table . '.' . $primaryKey, $id)
                            ->delete();

                        return $object;
                    }
                }

                // delete record from table without dependency from other table lang
                $model->deleteTranslationRecord($id, $langId);

                return $object;
            }
        }
        else
        {
            // delete objects without languages
            if(Schema::hasColumn($table, 'object_id'))
            {
                // Delete single record
                $object = $model->builder()
                    ->where($table . '.object_id', $id)
                    ->first();
            }
            else
            {
                // Delete single record
                $object = $model->builder()
                    ->where($table . '.' . $primaryKey, $id)
                    ->first();
            }

            $object->delete();

            return $object;
        }
    }
}