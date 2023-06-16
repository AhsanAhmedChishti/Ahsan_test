<?php

namespace DTApi\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function successResponse($result, $message, $code = 200)
    {
        // Create a success response array
        $response = [
            'success' => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $result
        ];

        // Return the response as a JSON with the specified HTTP status code (default: 200)
        return response()->json($response, 200);
    }

    public function errorResponse($error, $errorMessages = [], $code = 203)
    {
        // Create an error response array
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $error,
            'data'    => []
        ];

        // Return the response as a JSON with the specified HTTP status code
        return response()->json($response, $code);
    }
}
