<?php namespace Syscover\Core\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Syscover\Core\Exceptions\ParameterNotFoundException;
use Syscover\Core\Exceptions\ParameterValueException;

/**
 * Class CoreController
 * @package Syscover\Pulsar\Core
 */

abstract class CoreController extends BaseController
{
    protected $model;

    public function __construct(Request $request)
    {

    }

    /**
     * Display a listing of the resource.
     * @param   Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // get parameters from url route
        $parameters = $request->route()->parameters();

        $query = call_user_func($this->model . '::builder');

        if(isset($parameters['lang']))
            $query->where($this->model . 'lang_id', $parameters['lang']);

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

        // build query
        $query = call_user_func($this->model . '::builder');


        foreach ($parameters['parameters'] as $param)
        {
            if(! isset($param['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter');

            if(! isset($param['field']))
                throw new ParameterNotFoundException('Parameter field not found in request, please set field parameter');

            if(! isset($param['operator']))
                throw new ParameterNotFoundException('Parameter operator not found in request, please set operator parameter');

            if(! isset($param['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter');


            switch ($param['command']) {
                case 'where':
                    $query->where($param['field'], $param['operator'], $param['value']);
                    break;
                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }

        }

        $objects = $query->get();

        $response['status'] = "success";
        $response['data'] = $objects;

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

        $query = call_user_func($this->model . '::builder');

        if(isset($parameters['lang']))
        {
            $model = new $this->model;
            $table = $model->getTable();

            $object = $query
                ->where($table . '.lang_id', $parameters['lang'])
                ->where($table . '.id', $parameters['id'])
                ->first();
        }
        else
        {
            $object = $query->find($parameters['id']);
        }

        $response['status'] = "success";
        $response['data'] = $object;

        return response()->json($response);
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

        $query = call_user_func($this->model . '::builder');

        if(isset($parameters['lang']))
        {
            $model = new $this->model;
            $table = $model->getTable();

            $object = $query
                ->where($table . '.lang_id', $parameters['lang'])
                ->where($table . '.id', $parameters['id'])
                ->first();

            call_user_func($this->model . '::deleteTranslationRecord', $parameters);
        }
        else
        {
            $object = $query->find($parameters['id']);
            $object->delete();
        }



        $response['status'] = "success";
        $response['data']   = $object;

        return response()->json($response);
    }
}