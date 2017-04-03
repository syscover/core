<?php namespace Syscover\Core\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * Class CoreController
 * @package Syscover\Pulsar\Core
 */

abstract class CoreController extends BaseController
{
    public function __construct(Request $request)
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $objects = call_user_func($this->model . '::builder')->get();

        $response['data'] = $objects;

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param   string  $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $object = call_user_func($this->model . '::builder')->find($id);

        $response['data'] = $object;

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param   string  $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $object = call_user_func($this->model . '::builder')->find($id);

        $object->delete();

        $response['status'] = "success";
        $response['data']   = $object;

        return response()->json($response);
    }
}