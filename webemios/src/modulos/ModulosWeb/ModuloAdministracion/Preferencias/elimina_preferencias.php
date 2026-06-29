<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PREFERENCIAS, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_preferencias = $_POST["id_preferencias"];

    // Se eliminan las preferencias
    $operacion_borrado = "
        DELETE
        FROM preferencias
        WHERE
            id = '".$bd_red->_($id_preferencias)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se eliminan las imágenes de las preferencias
        elimina_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, $id_preferencias);
        elimina_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, $id_preferencias);

        $res = "OK";
        $msg = $idiomas->_("Preferencias borradas correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
