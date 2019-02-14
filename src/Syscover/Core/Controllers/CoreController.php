<?php namespace Syscover\Core\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Syscover\Core\Exceptions\ModelNotChangeException;
use Syscover\Core\Traits\ApiResponse;

/**
 * Class CoreController
 * @package Syscover\Pulsar\Core
 */

abstract class CoreController extends BaseController
{
    use ApiResponse;

    protected $model;
    protected $service;

    /**
     * List all items
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse($this->model->all());
    }

    /**
     * Get item
     *
     * @param   $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $object = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $model = class_basename($e->getModel());
            return $this->errorResponse('Does not exist any instance of ' . $model . ' with the given id', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($object);
    }

    /**
     * Store item
     *
     * @param   Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $object = $this->service->store($request->all());
        }
        catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse($object, Response::HTTP_CREATED);
    }

    /**
     * Update item
     *
     * @param   Request $request
     * @param   $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $object = $this->service->update($request->all(), $id);
        }
        catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        catch (ModelNotFoundException $e) {
            $model = class_basename($e->getModel());
            return $this->errorResponse('Does not exist any instance of ' . $model . ' with the given id', Response::HTTP_NOT_FOUND);
        }
        catch (ModelNotChangeException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse($object);
    }

    public function destroy($id)
    {

    }









    /******
     * OLD
     */

    //use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Schema;
//use Syscover\Core\Exceptions\ParameterNotFoundException;
//use Syscover\Core\Exceptions\ParameterValueException;
//use Syscover\Core\Services\SQLService;

//    /**
//     * Display a listing of the resource.
//     *
//     * @param Request $request
//     * @return \Illuminate\Http\JsonResponse
//     * @throws ParameterNotFoundException
//     * @throws ParameterValueException
//     */
//    public function index(Request $request)
//    {
//        // get parameters from url route
//        $parameters = $request->route()->parameters();
//
//        // get table name, replace to $query = call_user_func($this->model . '::builder')
//        $model  = new $this->model;
//        $table  = $model->getTable();
//        $query  = $model->builder();
//
//        // if has lang in url parameter, filter by lang_id
//        if(isset($parameters['lang']))
//        {
//            /**
//             * Check if controller has defined modelLang property,
//             * if has modelLang, this means that the translations are in another table.
//             * Get table name to do the query
//             */
//            if(isset($this->modelLang))
//            {
//                $modelLang = new $this->modelLang;
//                $tableLang = $modelLang->getTable();
//            }
//            else
//            {
//                $tableLang = $table;
//            }
//
//            // add query lang
//            $query->where($tableLang . '.lang_id', $parameters['lang']);
//        }
//
//        // search records
//        if($request->has('sql'))
//        {
//            $query = SQLService::getQueryFiltered($query, $request->input('sql'));
//            $query = SQLService::getQueryOrderedAndLimited($query, $request->input('sql'));
//        }
//
//        $objects = $query->get();
//
//        $response['status'] = "success";
//        $response['data'] = $objects;
//
//        return response()->json($response);
//    }
//
//    public function search()
//    {
//        $model = new $this->model;
//        $query = $model->calculateFoundRows()->builder();
//
//        // save eager loads to load after execute FOUND_ROWS() MySql Function
//        // FOUND_ROWS function get total number rows of last query, if model has eagerLoads, after execute the query model,
//        // will execute eagerLoads losing the reference os last query to execute FOUND_ROWS() MySql Function
//        $eagerLoads = $query->getEagerLoads();
//        $query      = $query->setEagerLoads([]);
//
//        // get query filtered by sql statement and filterd by filters statement
//        $query = SQLService::getQueryFiltered($query, request('sql') ?? null, request('filter') ?? null);
//
//        // get query ordered and limited
//        $query = SQLService::getQueryOrderedAndLimited($query, request('sql') ?? null);
//
//        // get objects filtered
//        $objects = $query->get();
//
//        // execute FOUND_ROWS() MySql Function and save filtered value, to be returned in resolveFilteredField() function
//        // this function is executed after resolveObjectsField according to the position of fields marked in the GraphQL query
//        $filtered = DB::select(DB::raw("SELECT FOUND_ROWS() AS 'filtered'"))[0]->filtered;
//
//        // load eager loads
//        $objects->load($eagerLoads);
//
//        $response['status']     = 200;
//        $response['statusText'] = "OK";
//        $response['filtered']   = $filtered;
//        $response['data']       = $objects;
//
//        return response()->json($response);
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param   Request $request
//     * @return  \Illuminate\Http\JsonResponse
//     */
//    public function show(Request $request)
//    {
//        // get parameters from url route
//        $parameters = $request->route()->parameters();
//
//        // get table name, replace to $query = call_user_func($this->model . '::builder')
//        $model      = new $this->model;
//        $table      = $model->getTable();
//        $primaryKey = $model->getKeyName();
//        $query      = $model->builder();
//
//        if(
//            (
//                isset($parameters['lang'])
//                /**
//                 * check if table has lang_id, maybe to have translations in one column,
//                 * in this case the table has not lang_id for example table field
//                 */
//                && Schema::hasColumn($table ,'lang_id')
//            )
//            ||
//            (
//                isset($parameters['lang'])
//                && ! Schema::hasColumn($table ,'lang_id')
//                && isset($this->modelLang)
//            )
//        )
//        {
//            /**
//             * Check if controller has defined modelLang property,
//             * if has modelLang, this means that the translations are in another table.
//             * Get table name to do the query
//             */
//            if(isset($this->modelLang))
//            {
//                $modelLang = new $this->modelLang;
//                $tableLang = $modelLang->getTable();
//            }
//            else
//            {
//                $tableLang = $table;
//            }
//
//            // add query lang
//            $query->where($tableLang . '.lang_id', $parameters['lang']);
//        }
//
//        $query->where($table . '.' . $primaryKey, $parameters['id']);
//
//        $object = $query->first();
//
//        $object = $this->addLazyRelations($object, $model);
//
//        // do custom operations
//        $object = $this->showCustom($parameters, $object);
//
//        $response['status'] = "success";
//        $response['data'] = $object;
//
//        return response()->json($response);
//    }
//
//    /**
//     * function to be overridden
//     *
//     * @param $parameters
//     * @param $object
//     * @return mixed
//     */
//    public function showCustom($parameters, $object)
//    {
//        return $object;
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param Request $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy(Request $request)
//    {
//        // get parameters from url route
//        $args = $request->route()->parameters();
//
//        $this->destroyCustom($args);
//
//        $object = SQLService::deleteRecord($args['id'], $this->model, isset($args['lang'])? $args['lang'] : null, $this->modelLang);
//
//        $response['status'] = "success";
//        $response['data']   = $object;
//
//        return response()->json($response);
//    }
//
//    /**
//     * function to be overridden
//     *
//     * @access	public
//     * @param   array       $parameters
//     */
//    public function destroyCustom($parameters) { }
//
//    /**
//     * Set relations in query
//     */
//    private function addLazyRelations($object, $model)
//    {
//        if(is_array($model->lazyRelations) && count($model->lazyRelations) > 0)
//            $object->load($model->lazyRelations);
//
//        return $object;
//    }
}
