<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{

    protected function successResponse($data = null, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'data'    => $data,
            'message' => $message,
            'success' => true,
        ], $code);
    }

    protected function errorResponse(?\Throwable $th = null,$message = null, $code = 400): JsonResponse
    {
       $errors = [];

       if($th && app()->isLocal()){
           $errors = app()->isLocal() ? [
               'message' => $th->getMessage(),
               'file'    => $th->getFile(),
               'line'    => $th->getLine(),
           ] : [];
       }

        return response()->json([
            'errors'  => $errors,
            'message' => $message,
            'success' => false,
        ], $code);
    }

    protected function success($message,$statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ],$statusCode);
    }
    protected function error($errors = [],$statusCode = null)
    {
        if(is_string($errors))
        {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode
            ],$statusCode);
        }

        return response()->json([
            'errors' => $errors
        ]);
    }

    protected function ok($message){
        return $this->success($message,200);
    }


}
