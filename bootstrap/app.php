<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'is.owner' => \App\Http\Middleware\IsOwner::class,
            'is.admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            if (isset($_ENV['IS_VERCEL'])) {
                echo "<h1>🔥 ORIGINAL ERROR: " . $e->getMessage() . "</h1>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                exit;
            }
        });
    })->create();

if (isset($_ENV['IS_VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
    
    // Create required directories since /tmp is empty on boot
    $directories = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/storage/app/public',
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

return $app;
