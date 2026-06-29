<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug de URLs generadas</h1>";

$directorio_base = 'C:/raul/webemios';
$script_actual = $_SERVER['SCRIPT_NAME']; // /webemios/comun/debug_urls.php
$directorio_script = dirname($script_actual); // /webemios/comun

echo "<p><strong>SCRIPT_NAME:</strong> $script_actual</p>";
echo "<p><strong>Directorio del script:</strong> $directorio_script</p>";
echo "<p><strong>Directorio físico:</strong> $directorio_base</p>";

echo "<h2>URLs que el navegador intenta cargar (según errores):</h2>";
echo "<ul>";
echo "<li>http://localhost:8080/comun/comun/rsc/lib/font-awesome/css/font-awesome.css</li>";
echo "<li>http://localhost:8080/comun/css/comun_rsc_v5.2.0.0.css</li>";
echo "<li>http://localhost:8080/comun/comun/rsc/estilos/estilos.php</li>";
echo "<li>http://localhost:8080/comun/js/comun_rsc_v5.2.0.0.js</li>";
echo "<li>http://localhost:8080/comun/js/comun_src_v5.4.0.0_R2.js</li>";
echo "</ul>";

echo "<h2>URLs correctas deberían ser:</h2>";
echo "<ul>";
echo "<li>http://localhost:8080/webemios/comun/rsc/lib/font-awesome/css/font-awesome.css</li>";
echo "<li>http://localhost:8080/webemios/css/comun_rsc_v5.2.0.0.css</li>";
echo "<li>http://localhost:8080/webemios/comun/rsc/estilos/estilos.php</li>";
echo "<li>http://localhost:8080/webemios/js/comun_rsc_v5.2.0.0.js</li>";
echo "<li>http://localhost:8080/webemios/js/comun_src_v5.4.0.0_R2.js</li>";
echo "</ul>";

echo "<h2>Verificando archivos en las URLs correctas:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>URL correcta</th><th>Ruta física</th><th>Existe</th></tr>";

$urls_correctas = [
    '/webemios/comun/rsc/lib/font-awesome/css/font-awesome.css' => '/comun/rsc/lib/font-awesome/css/font-awesome.css',
    '/webemios/css/comun_rsc_v5.2.0.0.css' => '/css/comun_rsc_v5.2.0.0.css',
    '/webemios/comun/rsc/estilos/estilos.php' => '/comun/rsc/estilos/estilos.php',
    '/webemios/js/comun_rsc_v5.2.0.0.js' => '/js/comun_rsc_v5.2.0.0.js',
    '/webemios/js/comun_src_v5.4.0.0_R2.js' => '/js/comun_src_v5.4.0.0_R2.js',
];

foreach ($urls_correctas as $url => $ruta) {
    $ruta_fisica = $directorio_base . $ruta;
    $existe = file_exists($ruta_fisica) ? '✅' : '❌';
    echo "<tr>";
    echo "<td>$url</td>";
    echo "<td><small>$ruta_fisica</small></td>";
    echo "<td>$existe</td>";
    echo "</tr>";
}
echo "</table>";

// Revisar el contenido de los archivos include que generan estas rutas
echo "<h2>Contenido de los archivos include que generan las rutas:</h2>";

$archivos_include = [
    'comun/includes/estilos_librerias.php',
    'comun/includes/estilos_web.php',
    'includes/estilos_librerias.php',
    'includes/estilos_web.php',
];

foreach ($archivos_include as $archivo) {
    $ruta = $directorio_base . '/' . $archivo;
    echo "<h3>$archivo</h3>";
    if (file_exists($ruta)) {
        echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto;'>";
        echo htmlspecialchars(file_get_contents($ruta));
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>No encontrado en: $ruta</p>";
    }
}
?>