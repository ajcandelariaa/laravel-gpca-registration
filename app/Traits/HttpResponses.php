<?php

namespace App\Traits;

trait HttpResponses{
    public function success($data, $message, $code){
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function error($data, $message, $code){
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorValidation($errors){
        return response()->json([
            'status' => 422,
            'message' => "Validation failed",
            'errors' => $errors,
        ], 422);
    }
}