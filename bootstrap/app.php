<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/*',
        ]);
        $middleware->alias([
            'auth.jwt' => \App\Http\Middleware\JWTMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function(TokenInvalidException $e, $request){
            return Response::json([
                'error' => true,
                'data' => [
                    'message' => $e->getMessage()
                ]
            ], 401);
        });

        $exceptions->renderable(function(TokenExpiredException $e, $request){
            return Response::json([
                'error' => true,
                'data' => [
                    'message' => $e->getMessage()
                ]
            ], 401);
        });

        $exceptions->renderable(function(JsonException $e, $request){
            return Response::json([
                'error' => true,
                'data' => [
                    'message' => $e->getMessage()
                ]
            ], 401);
        });
    })->create();
