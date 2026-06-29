<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Se cambia el flag para mostrar u ocultar las etiquetas de los iconos en el mapa
    if ($_SESSION["etiquetas_mapa"] == VALOR_SI)
    {
        $_SESSION["etiquetas_mapa"] = VALOR_NO;
    }
    else
    {
        $_SESSION["etiquetas_mapa"] = VALOR_SI;
    }

	print(json_encode(array(
        "res" => "OK"))
    );
?>
