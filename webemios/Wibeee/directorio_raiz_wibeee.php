<?php
    // Se recupera el directorio raíz del proyecto y se guarda en la variable _SESSION
    $directorio_actual = getcwd();
    $directorio_actual = str_replace('\\', '/', $directorio_actual);
    $directorio_raiz = str_replace('/Wibeee_', '', $directorio_actual);
    if ($directorio_raiz == $directorio_actual)
    {
        $directorio_raiz = str_replace('/Wibeee', '', $directorio_actual);
    }

    session_start();
    $_SESSION["directorio"] = $directorio_raiz;
?>