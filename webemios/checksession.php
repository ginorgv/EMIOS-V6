<?php
session_start();
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "id_usuario: " . ($_SESSION['id_usuario'] ?? 'NOT SET') . "\n";
echo "id_red: " . ($_SESSION['id_red'] ?? 'NOT SET') . "\n";
echo "perfil: " . ($_SESSION['perfil'] ?? 'NOT SET') . "\n";
echo "directorio: " . ($_SESSION['directorio'] ?? 'NOT SET') . "\n";
echo "</pre>";
