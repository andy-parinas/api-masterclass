<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponses {


    protected function ok($message){
        return $this->success($message, Response::HTTP_OK);
    }


    protected function success($message, $status = Response::HTTP_OK) : JsonResponse
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $status
        ], $status);
    }
}