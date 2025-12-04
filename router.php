<?php
$request = $_SERVER['REQUEST_URI'];

// Si el archivo existe y no es api.php, servirlo directamente
if ($request !== '/api.php' && file_exists(__DIR__ . $request)) {
    return false;
}

// Si es api.php, incluirlo directamente
if ($request === '/api.php') {
    require_once __DIR__ . '/api.php';
    exit;
}

// Si no existe, redirigir a index.php
require_once __DIR__ . '/index.php';
