<?php
// Router para el servidor PHP integrado
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Inicializar sesión y directorio para cualquier petición PHP
if (preg_match('/\.php$/', $path)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["directorio"])) {
        $_SESSION["directorio"] = __DIR__;
    }
}

// Si el archivo existe, servirlo directamente
$filePath = __DIR__ . $path;
if ($path != '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// Rutas de la aplicación
if ($path == '/' || $path == '/index.php') {
    require __DIR__ . '/comun/index.php';
} elseif (strpos($path, '/comun/') === 0) {
    // Si está en /comun/ ruta directa
    $comunPath = __DIR__ . $path;
    if (file_exists($comunPath)) {
        return false;
    }
    // Si no existe, redirigir al login
    require __DIR__ . '/comun/login.php';
} elseif ($path == '/login.php') {
    require __DIR__ . '/comun/login.php';
} elseif ($path == '/logout.php') {
    require __DIR__ . '/comun/src/login/logout.php';
} elseif (strpos($path, '/src/') === 0 || strpos($path, '/rsc/') === 0 || 
          strpos($path, '/js/') === 0 || strpos($path, '/css/') === 0 ||
          strpos($path, '/includes/') === 0) {
    return false;
} elseif (strpos($path, '/api/') === 0) {
    return false;
} else {
    // Para cualquier otra ruta, intentar servir el archivo
    if (file_exists($filePath)) {
        return false;
    }
    // Si no existe, redirigir al login
    require __DIR__ . '/comun/login.php';
}
