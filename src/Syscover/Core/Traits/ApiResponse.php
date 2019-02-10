<?php namespace Syscover\Core\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * @param   $data
     * @param   int $code
     * @return  \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json([
            'status'        => $code,
            'statusText'    => 'success',
            'data'          => $data
        ], $code);
    }

    /**
     * @param   $message
     * @param   $code
     * @return  \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code)
    {
        return response()->json([
            'status'        => $code,
            'statusText'    => $message,
        ], $code);
    }
}
