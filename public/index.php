<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Jika request di-rewrite dari root .htaccess (doc root = project root), pulihkan REQUEST_URI asli
$originalUri = $_SERVER['REDIRECT_ORIGINAL_URI'] ?? $_SERVER['ORIGINAL_URI'] ?? $_SERVER['REDIRECT_HTTP_X_ORIGINAL_URI'] ?? null;
if ($originalUri === null || $originalUri === '') {
    $urlParam = $_GET['_url'] ?? null;
    if ($urlParam !== null && $urlParam !== '') {
        $originalUri = '/' . ltrim($urlParam, '/');
        unset($_GET['_url']);
        $_SERVER['QUERY_STRING'] = http_build_query($_GET);
    }
}
if ($originalUri !== null && $originalUri !== '') {
    $_SERVER['REQUEST_URI'] = $originalUri;
    if (!isset($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] === '') {
        $_SERVER['QUERY_STRING'] = '';
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
