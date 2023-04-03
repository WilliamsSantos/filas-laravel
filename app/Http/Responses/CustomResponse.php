<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as RESPONSE_HTTP;

class CustomResponse extends Response
{
    private static $defaultRoute;

    public function __construct($defaultRoute = 'index') {
        self::$defaultRoute = $defaultRoute;
    }

    public static function successRoute(
        ?string $router = null, 
        ?string $message = null, 
        ?array $params = []
    ): RedirectResponse
    {
        session()->flash('success-info', $message);

        return redirect()->route($router, $params);
    }

    public static function errorRoute(
        ?string $message, 
        int $errorCode = RESPONSE_HTTP::HTTP_INTERNAL_SERVER_ERROR, 
        int $status = RESPONSE_HTTP::HTTP_BAD_REQUEST
    ): RedirectResponse
    {
        session()->flash('error', $message);

        return redirect()->route(self::$defaultRoute);
    }
}