<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin_department' => \App\Http\Middleware\AdminDepartmentMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            $segments = $request->segments();
            $country = $segments[0] ?? 'gh';
            $lang = $segments[1] ?? 'en';
            
            return route('login', ['country' => $country, 'lang' => $lang]);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                // Standardize all API errors into the WADEX-Guard format
                $status = 500;
                $message = 'WADEX PRO: System under maintenance or internal resilience delay.';
                $code = 'SYS_ERR';

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                    $message = $e->getMessage();
                    $code = 'HTTP_' . $status;
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $status = 404;
                    $message = 'Resource not found.';
                    $code = 'NOT_FOUND';
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    return null; // Let Laravel handle validation errors normally (422)
                }

                // Mask fatal errors in production-like testing
                if ($status === 500) {
                    $message = 'Internal resilience error. Our engineers are notified.';
                    $code = 'GATEWAY_GUARD_ACTIVE';
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'code' => $code,
                    'request_id' => $request->header('X-Request-ID') ?? null
                ], $status);
            }
        });
    })->create();
