<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (isset($_GET['debug_index'])) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "index.php reached\n";
    exit;
}

if (is_file(__DIR__ . '/../storage/app/debug-probe.txt')) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo file_get_contents(__DIR__ . '/../storage/app/debug-probe.txt');
    exit;
}

if (isset($_GET['clear_route_cache'])) {
    $cacheFile = __DIR__ . '/../bootstrap/cache/routes-v7.php';
    if (is_file($cacheFile)) {
        @unlink($cacheFile);
    }
}

if (is_file(__DIR__ . '/../bootstrap/cache/routes-v7.php')) {
    $routesFile = __DIR__ . '/../routes/web.php';
    $cacheFile = __DIR__ . '/../bootstrap/cache/routes-v7.php';
    if (is_file($routesFile) && filemtime($routesFile) > filemtime($cacheFile)) {
        @unlink($cacheFile);
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
