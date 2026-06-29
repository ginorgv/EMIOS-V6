<?php
	// Se recupera el directorio raíz del proyecto y se guarda en la variable _SESSION
	$directorio_actual = getcwd();
	$directorio_actual = str_replace('\\', '/', $directorio_actual);
	$directorio_raiz = str_replace('/src/lib/test', '', $directorio_actual);

	session_start();
	$_SESSION["directorio"] = $directorio_raiz;
?>