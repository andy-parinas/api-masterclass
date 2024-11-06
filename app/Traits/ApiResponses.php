<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponses {


    protected function ok($message, $data = []){
        return $this->success($message, $data, Response::HTTP_OK);
    }


    protected function success($message, $data = [], $status = Response::HTTP_OK) : JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'statusCode' => $status
        ], $status);
    }

    protected function error($message, $status) : JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $status
        ], $status);
    }
}