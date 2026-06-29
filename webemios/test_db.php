<?php
$dir = __DIR__;
session_start();
$_SESSION["directorio"] = $dir;

require_once "$dir/comun/src/lib/herramientas/util_cadenas.php";
require_once "$dir/src/lib/constantes/constantes.php";
require_once "$dir/src/lib/BasesDatos/BaseDatosRed.php";

try {
    $bd = BaseDatosRed::dame_base_datos();
    $res = $bd->ejecuta_consulta("SELECT id, contrasenya FROM usuarios WHERE id='admin'");
    $row = $res->dame_siguiente_fila();
    
    echo "User found: " . $row['id'] . "\n";
    echo "Hash len: " . strlen($row['contrasenya']) . "\n";
    $match = crypt('admin', $row['contrasenya']) === $row['contrasenya'];
    echo "Password match: " . ($match ? "YES" : "NO") . "\n";
    
    // Test form POST simulation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "POST received!\n";
        echo "usuario: " . ($_POST['usuario'] ?? 'NOT SET') . "\n";
        echo "contrasenya: " . ($_POST['contrasenya'] ?? 'NOT SET') . "\n";
    }
    
    $bd->desconecta();
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
