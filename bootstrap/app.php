<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Register custom exception handler untuk Laravel 12
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token expired. Please refresh the page and try again.',
                    'error' => 'csrf_expired'
                ], 419);
            }
            
            // Redirect to login with error message
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'unauthenticated'
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        });
    })->create();
