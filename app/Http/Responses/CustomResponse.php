<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class CustomResponse extends Response
{
    private static $defaultRoute = 'index';
 
    public static function successRoute($router = null, $message = null, $params)
    {
        session()->flash('success-info', $message);

        return redirect()->route($router);
    }

    public static function errorRoute($message, $errorCode = 500, $status = 400)
    {
        session()->flash('error', $message);

        return redirect()->route(self::$defaultRoute);
    }
}