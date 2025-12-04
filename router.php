<?php
// Si el archivo existe, servirlo directamente
if (file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return false;
}

// Si no, redirigir a index.php
require_once __DIR__ . '/index.php';
