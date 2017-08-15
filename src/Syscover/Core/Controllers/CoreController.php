<?php namespace Syscover\Core\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Syscover\Core\Exceptions\ParameterNotFoundException;
use Syscover\Core\Exceptions\ParameterValueException;
use Syscover\Core\Services\SQLService;

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

        // if has lang in url parameter, filter by lang_id
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

        // search records
        if($request->has('sql'))
        {
            $query = SQLService::getQueryFiltered($query, $request->input('sql'));
            $query = SQLService::getQueryOrderedAndLimited($query, $request->input('sql'));
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
    // TODO delete this function
    public function search_TO_DELETE(Request $request)
    {
        // get parameters from request
        $args = $request->all();

        // check query and throw exceptions
        if(! isset($args['sql']))
            throw new ParameterNotFoundException('Parameter not found in request, please set parameters array parameter');

        // get table name, replace to $query = call_user_func($this->model . '::builder')
        $model = new $this->model;

        // build query
        $query = SQLService::getQueryFiltered($model->builder(), $args['sql']);
        // count records filtered
        $filtered = $query->count();

        // get query ordered and limited
        $query = SQLService::getQueryOrderedAndLimited($query, $args['sql']);

        $objects = $query->get();

        // N total records
        $total = SQLService::countPaginateTotalRecords($model->builder(), isset($args['lang'])? $args['lang'] : null);

        $response['status']         = "success";
        $response['total']          = $total;
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
            (
                isset($parameters['lang'])
                /**
                 * check if table has lang_id, maybe to have translations in one column,
                 * in this case the table has not lang_id for example table field
                 */
                && Schema::hasColumn($table ,'lang_id')
            )
            ||
            (
                isset($parameters['lang'])
                && ! Schema::hasColumn($table ,'lang_id')
                && isset($this->modelLang)
            )
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
        $args = $request->route()->parameters();

        $this->destroyCustom($args);

        $object = SQLService::destroyRecord($args['id'], $this->model, isset($args['lang'])? $args['lang'] : null, $this->modelLang);

        $response['status'] = "success";
        $response['data']   = $object;

        return response()->json($response);
    }

    /**
     * function to be overridden
     *
     * @access	public
     * @param   array       $parameters
     */
    public function destroyCustom($parameters) { }

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