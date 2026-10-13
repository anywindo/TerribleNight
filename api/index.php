<?php

// Prepare writable storage directory structure in /tmp for Vercel Serverless
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $directories = [
        '/tmp/storage',
        '/tmp/storage/app',
        '/tmp/storage/app/public',
        '/tmp/storage/framework',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
    ];

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    // Fix: Vercel serverless functions strip `/api` from REQUEST_URI
    if (isset($_SERVER['REQUEST_URI'])) {
        $uri = $_SERVER['REQUEST_URI'];
        if (str_starts_with($uri, '/v1') || str_starts_with($uri, '/health') || $uri === '' || $uri === '/') {
            if ($uri !== '' && $uri !== '/') {
                $_SERVER['REQUEST_URI'] = '/api' . $uri;
            }
        }
    }
}

// Forward the request to Laravel's standard entry point
require __DIR__ . '/../public/index.php';
