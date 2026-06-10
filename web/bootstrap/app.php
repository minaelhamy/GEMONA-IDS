<?php

use Illuminate\Http\Request;
use App\Http\Middleware\Installed;
use App\Http\Middleware\localization;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Session\Middleware\AuthenticateSession;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([]);
        $middleware->validateCsrfTokens(
            except: [
                '/payment/sslcommerz/*',
                '/payment/paytm/*',
                '/payment/cashfree/*',
                '/payment/phonepe/*',
            ]
        );
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'precognitive' => HandlePrecognitiveRequests::class,
            'permission' => PermissionMiddleware::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
            'apiKey' => ApiKeyMiddleware::class,
            'localization' => localization::class,
            'installed' => Installed::class,

        ]);
    })
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:sanctum']],
    )
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                if ($e instanceof UnauthorizedException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User does not have the right permissions.',
                    ], 403);
                }

                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No query results for model.',
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Method not supported for the route.',
                    ], 405);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The specified URL cannot be found.',
                    ], 404);
                }

                if ($e instanceof HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'HTTP error.',
                    ], $e->getStatusCode());
                }

                if ($e instanceof QueryException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A database error occurred.',
                        'error' => config('app.debug') ? $e->getMessage() : null,
                    ], 422);
                }
            }
        });
    })->create();
