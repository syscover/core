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
        $query = call_user_func($this->model . '::builder');

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
}