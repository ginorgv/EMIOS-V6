<?php
	function decodifica_cadena_peticion_php($cadena_codificada)
    {
        $cadena_decodificada = convert_uudecode($cadena_codificada);
        return ($cadena_decodificada);
    }
?>
