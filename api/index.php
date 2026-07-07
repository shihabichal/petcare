<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */
// Enable strict error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fix Vercel read-only filesystem issues for bootstrap/cache
$tmpCacheDir = '/tmp/storage/bootstrap/cache';
if (!is_dir($tmpCacheDir)) {
    mkdir($tmpCacheDir, 0755, true);
}

$_ENV['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] = $tmpCacheDir . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] = $tmpCacheDir . '/packages.php';
$_ENV['APP_CONFIG_CACHE']   = $_SERVER['APP_CONFIG_CACHE']   = $tmpCacheDir . '/config.php';
$_ENV['APP_ROUTES_CACHE']   = $_SERVER['APP_ROUTES_CACHE']   = $tmpCacheDir . '/routes.php';
$_ENV['APP_EVENTS_CACHE']   = $_SERVER['APP_EVENTS_CACHE']   = $tmpCacheDir . '/events.php';

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>🔥 Fatal Error in Vercel:</h1>";
    echo "<p style='color:red;'><b>" . $e->getMessage() . "</b></p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#f4f4f4;padding:15px;overflow:auto;'>" . $e->getTraceAsString() . "</pre>";
}
