<?php

namespace App\Http\Controllers;

use App\Enums\ReturnType;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    /**
     * errorResponse
     * Formaterd json response.
     *
     * @param  \Exception  $ex
     * @param  int|integer $errorCode error code number
     * @return \Illuminate\Http\Response
     */
    protected function errorResponse(\Exception $ex, int $errorCode = 500)
    {
        \Log::error('Error Message : '.  $ex->getMessage());
        \Log::error('Error Line: '. $ex->getLine());
        \Log::error('Error File: ' .$ex->getFile());

        $code = (array_key_exists($ex->getCode(), Response::$statusTexts)) ? $ex->getCode() : $errorCode;
        return response()->json([
            'type' => ReturnType::ERROR,
            'message' => $ex->getMessage(),
            'status' => $ex->getCode(),
        ], $code);
    }
}
