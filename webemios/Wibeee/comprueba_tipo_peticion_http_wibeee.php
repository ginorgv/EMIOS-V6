<?php
    // Si no es un 'GET' se ignora (se pueden enviar antes las cabeceras)
    // (http://stackoverflow.com/questions/11616603/script-running-twice)
    if ($_SERVER['REQUEST_METHOD'] != "GET")
    {
        exit;
    }
?>