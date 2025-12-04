<?php
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Si el archivo existe y no es api.php, servirlo directamente
if ($path !== '/api.php' && file_exists(__DIR__ . $path)) {
    return false;
}

// Si es api.php, incluirlo directamente
if ($path === '/api.php') {
    require_once __DIR__ . '/api.php';
    exit;
}

// Si no existe, redirigir a index.php
require_once __DIR__ . '/index.php';
