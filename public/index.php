<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (isset($_GET['debug_index'])) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "index.php reached\n";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
