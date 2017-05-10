<?php namespace Syscover\Core\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Syscover\Admin\Models\Lang;
use Syscover\Core\Exceptions\ParameterNotFoundException;
use Syscover\Core\Exceptions\ParameterValueException;

/**
 * Class CoreController
 * @package Syscover\Pulsar\Core
 */

class CoreController extends BaseController
{
    protected $model;
    protected $modelLang;
    public $relations;

    /**
     * Display a listing of the resource.
     * @param   Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // get parameters from url route
        $parameters = $request->route()->parameters();

        // get table name, replace to $query = call_user_func($this->model . '::builder')
        $model  = new $this->model;
        $table  = $model->getTable();
        $query  = $model->builder();

        if(isset($parameters['lang']))
        {
            /**
             * Check if controller has defined modelLang property,
             * if has modelLang, this means that the translations are in another table.
             * Get table name to do the query
             */
            if(isset($this->modelLang))
            {
                $modelLang = new $this->modelLang;
                $tableLang = $modelLang->getTable();
            }
            else
            {
                $tableLang = $table;
            }

            // add query lang
            $query->where($tableLang . '.lang_id', $parameters['lang']);
        }

        $objects = $query->get();

        $response['status'] = "success";
        $response['data'] = $objects;

        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public function search(Request $request)
    {
        // get parameters from request
        $parameters = $request->all();

        // check query and throw exceptions
        if($parameters['type'] !== 'query')
            throw new ParameterValueException('type parameter has a incorrect value, must to be query');

        if(! isset($parameters['parameters']))
            throw new ParameterNotFoundException('Parameter not found in request, please set parameters array parameter');

        // get table name, replace to $query = call_user_func($this->model . '::builder')
        $model = new $this->model;

        // build query
        $query = $model->builder();

        // filter all data by lang
        if(isset($parameters['lang']))
        {
            $query
                ->where('lang_id', $parameters['lang'])
                ->where(function ($query) use ($parameters){
                    $this->setQueries($query, $parameters);
                });
        }
        else
        {
            $query = $this->setQueries($query, $parameters);
        }


        $filtered = $query->count();


        // commands for pagination
        foreach ($parameters['parameters'] as $param)
        {
            if(! isset($param['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($param));

            if($param['command'] !== "orderBy" && ! isset($param['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter in: ' . json_encode($param));

            switch ($param['command']) {
                case 'where':
                case 'orWhere';
                    // commands not accepted
                    break;
                case 'orderBy':
                    $query->orderBy($param['column'], $param['operator']);
                    break;
                case 'offset':
                    $query->skip($param['value']);
                    break;
                case 'limit':
                    $query->take($param['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be offset or take');
            }
        }

        $objects = $query->get();

        // additional information
        $query = $model->builder();

        // filter all data by lang
        if(isset($parameters['lang']))
            $query->where('lang_id', $parameters['lang']);

        $response['status']         = "success";
        $response['total']          = $query->count();
        $response['filtered']       = $filtered;
        $response['data']           = $objects;

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param   Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // get parameters from url route
        $parameters = $request->route()->parameters();

        // get table name, replace to $query = call_user_func($this->model . '::builder')
        $model      = new $this->model;
        $table      = $model->getTable();
        $primaryKey = $model->getKeyName();
        $query      = $model->builder();


        if(
            isset($parameters['lang'])
            // check if table has lang_id, maybe to have translations in one column,
            // in this case the table has not lang_id
            // for example table field
            && Schema::hasColumn($table ,'lang_id')
        )
        {
            /**
             * Check if controller has defined modelLang property,
             * if has modelLang, this means that the translations are in another table.
             * Get table name to do the query
             */
            if(isset($this->modelLang))
            {
                $modelLang = new $this->modelLang;
                $tableLang = $modelLang->getTable();
            }
            else
            {
                $tableLang = $table;
            }

            // add query lang
            $query->where($tableLang . '.lang_id', $parameters['lang']);
        }

        $query->where($table . '.' . $primaryKey, $parameters['id']);

        $object = $query->first();
        $object = $this->addLazyRelations($object, $model);

        // do custom operations
        $object = $this->showCustom($parameters, $object);

        $response['status'] = "success";
        $response['data'] = $object;

        return response()->json($response);
    }

    /**
     * function to be overridden
     *
     * @access	public
     * @param   array       $parameters
     * @param   object      $object
     * @return	array       $object
     */
    public function showCustom($parameters, $object)
    {
        return $object;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        // get parameters from url route
        $parameters = $request->route()->parameters();

        // get data to do model queries
        $model      = new $this->model;
        $table      = $model->getTable();
        $primaryKey = $model->getKeyName();

        /**
         *  Delete object with lang.
         *  If destroy baseLang object, delete all objects with this id
         */
        if(
            isset($parameters['lang']) &&
            Lang::getBaseLang()->id !== $parameters['lang'])
        {
            /**
             * Check if controller has defined modelLang property,
             * if has modelLang, this means that the translations are in another table.
             * Get table name to do the query
             */
            if(isset($this->modelLang))
            {
                // get data to do model queries
                $modelLang      = new $this->modelLang;
                $tableLang      = $modelLang->getTable();
                $primaryKeyLang = $modelLang->getKeyName();

                // get object from main table and lang table
                $object = $model->builder()
                    ->where($tableLang . '.lang_id', $parameters['lang'])
                    ->where($table . '.' . $primaryKey, $parameters['id'])
                    ->first();

                /**
                 * This option is for tables that dependent of other tables to set your languages
                 * set parameter $deleteLangDataRecord to false, because lang model haven't data_lag column
                 */
                $modelLang->deleteTranslationRecord($parameters, false);

                /**
                 * This kind of tables has field data_lang in main table, not in lang table
                 * delete data_lang parameter
                 */
                $model->deleteLangDataRecord($parameters);


                /**
                 * Count records, to know if has more lang
                 */
                $nRecords = $modelLang->builder()
                    ->where($tableLang . '.' . $primaryKeyLang, $parameters['id'])
                    ->count();

                /**
                 * if haven't any lang record, delete record from main table
                 */
                if($nRecords === 0)
                {
                    $model->where($table . '.' . $primaryKey, $parameters['id'])
                        ->delete();
                }
            }
            else
            {
                //???????
                $object = $model->builder()
                    ->where($table . '.lang_id', $parameters['lang'])
                    ->where($table . '.' . $primaryKey, $parameters['id'])
                    ->first();

                /**
                 * Delete record from table without dependency from other table lang
                 */
                $model->deleteTranslationRecord($parameters);
            }
        }
        else
        {
            // Delete single record
            $object = $model->builder()
                ->where($table . '.' . $primaryKey, $parameters['id'])
                ->first();

            $object->delete();
        }

        $response['status'] = "success";
        $response['data']   = $object;

        return response()->json($response);
    }

    /**
     * Set query parameters
     */
    private function setQueries($query, $parameters)
    {
        // commands without pagination
        foreach ($parameters['parameters'] as $param)
        {
            if(! isset($param['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($param));

            if(($param['command'] === "where" || $param['command'] === "orderBy") && ! isset($param['column']))
                throw new ParameterNotFoundException('Parameter column not found in request, please set column parameter in ' . json_encode($param));

            if(($param['command'] === "where" || $param['command'] === "orderBy") && ! isset($param['operator']))
                throw new ParameterNotFoundException('Parameter operator not found in request, please set operator parameter in ' . json_encode($param));


            switch ($param['command']) {
                case 'offset':
                case 'limit':
                case 'orderBy':
                    // commands not accepted
                    break;
                case 'where':
                    $query->where($param['column'], $param['operator'], $param['value']);
                    break;
                case 'orWhere':
                    $query->orWhere($param['column'], $param['operator'], $param['value']);
                    break;


                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }
        }

        return $query;
    }

    /**
     * Set relations in query
     */
    private function addLazyRelations($object, $model)
    {
        if(is_array($model->lazyRelations) && count($model->lazyRelations) > 0)
            $object->load($model->lazyRelations);

        return $object;
    }
}