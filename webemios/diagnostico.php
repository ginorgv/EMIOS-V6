<?php
// Página de diagnóstico para Railway
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><title>EMIOS - Diagnóstico</title>
<style>
body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #eee; }
h1 { color: #e94560; }
.card { background: #16213e; padding: 15px; margin: 10px 0; border-radius: 8px; }
.ok { color: #4ecca3; font-weight: bold; }
.error { color: #e94560; font-weight: bold; }
.warn { color: #f5a623; font-weight: bold; }
pre { background: #0f3460; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
</head>
<body>
<h1>🔍 EMIOS - Diagnóstico</h1>

<?php
function test($name, $result, $detail = '') {
    $cls = $result ? 'ok' : 'error';
    $icon = $result ? '✅' : '❌';
    echo "<div class='card'><span class='$cls'>$icon $name</span>";
    if ($detail) echo "<pre>$detail</pre>";
    echo "</div>";
}

// 1. PHP info básica
test('PHP Versión', true, phpversion() . ' | OS: ' . PHP_OS);

// 2. Extensiones
$exts = ['mysqli', 'json', 'mbstring', 'gd', 'pdo_mysql', 'curl', 'session'];
foreach ($exts as $ext) {
    test("Extensión: $ext", extension_loaded($ext));
}

// 3. Directorio actual
$dir = __DIR__;
test('Directorio raíz', is_dir($dir), $dir);
test('Directorio sessions', is_dir('/tmp/sessions') || @mkdir('/tmp/sessions', 0777, true), 
     'Sessions: ' . (is_writable('/tmp/sessions') ? 'escribible' : 'NO escribible'));

// 4. Config.ini
$ini_path = $dir . '/rsc/config/config.ini';
if (file_exists($ini_path)) {
    $ini = parse_ini_file($ini_path);
    test('config.ini existe', true, 
         "Host: {$ini['ip_base_datos_red']}:{$ini['puerto_base_datos_red']}\nBD: {$ini['nombre_base_datos_red']}\nUser: {$ini['usuario_base_datos_red']}");
} else {
    test('config.ini existe', false, "No encontrado en: $ini_path");
}

// 5. Router
$router_path = $dir . '/router.php';
test('router.php existe', file_exists($router_path));

// 6. Sesión
$session_id = session_id() ?: 'no iniciada';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
test('Sesión PHP', session_status() === PHP_SESSION_ACTIVE, "Session ID: " . session_id());

// 7. Conexión a BD
try {
    $ini = parse_ini_file($ini_path);
    $mysqli = @new mysqli(
        $ini['ip_base_datos_red'],
        $ini['usuario_base_datos_red'],
        $ini['contrasenya_base_datos_red'],
        $ini['nombre_base_datos_red'],
        $ini['puerto_base_datos_red']
    );
    if ($mysqli->connect_error) {
        test('Conexión MySQL', false, $mysqli->connect_error);
    } else {
        $ver = $mysqli->query("SELECT VERSION()")->fetch_row()[0];
        test('Conexión MySQL', true, "Versión: $ver");
        $res = $mysqli->query("SELECT COUNT(*) as c FROM usuarios");
        $users = $res->fetch_assoc()['c'];
        test('Usuarios en BD', true, "$users usuarios registrados");
        $mysqli->close();
    }
} catch (Exception $e) {
    test('Conexión MySQL', false, $e->getMessage());
}

// 8. DNS
$host = $ini['ip_base_datos_red'] ?? 'localhost';
$ip = gethostbyname($host);
test('Resolución DNS', $ip !== $host, "$host → $ip");
?>
</body>
</html>
