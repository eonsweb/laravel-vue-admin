<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{

    protected function success($data = [], $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'status'  => $statusCode,
            'data'    => $data
        ], $statusCode);
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

         // if already structured (has "type" or "status"), return directly
        if (is_array($errors) && (isset($errors['type']) || isset($errors['status']))) {
            return response()->json($errors, $statusCode ?? ($errors['status'] ?? 500));
        }

        // fallback: wrap generic array
        return response()->json([
            'errors' => $errors
        ], $statusCode ?? 500);
        }

    protected function ok($message){
        return $this->success($message,200);
    }


}
