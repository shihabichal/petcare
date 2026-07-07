<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>🔥 Fatal Error in Vercel:</h1>";
    echo "<p style='color:red;'><b>" . $e->getMessage() . "</b></p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#f4f4f4;padding:15px;overflow:auto;'>" . $e->getTraceAsString() . "</pre>";
}
