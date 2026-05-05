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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Custom Environment Loader: Reads ENVIRONMENT from base .env
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), 'ENVIRONMENT=')) {
            $environment = trim(substr(trim($line), 12));
            $customEnvFile = '.env.' . $environment;
            if (file_exists(dirname(__DIR__) . '/' . $customEnvFile)) {
                $app->loadEnvironmentFrom($customEnvFile);
            }
            break;
        }
    }
}

return $app;
