<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\LogAdminActions;
use App\Http\Middleware\SecurityHeaders;
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
        $middleware->append(SecurityHeaders::class);
        
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'admin.audit' => LogAdminActions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $throwable): void {
            if (! app()->bound('request')) {
                return;
            }

            $request = request();

            if ($request === null) {
                return;
            }

            $isAdminContext = (bool) ($request->user()?->is_admin)
                || str_starts_with((string) $request->path(), 'dashboard')
                || str_starts_with((string) $request->path(), 'profile');

            if (! $isAdminContext) {
                return;
            }

            logger()->channel('admin_daily')->error('Admin area exception', [
                'message' => $throwable->getMessage(),
                'exception' => $throwable::class,
                'path' => $request->path(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'admin_id' => $request->user()?->id,
            ]);
        });
    })->create();
