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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_LICENCIA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_licencia = $_POST["id_licencia"];

    // Se elimina la licencia
    $operacion_borrado = "
        DELETE
        FROM licencias
        WHERE
            id = '".$bd_red->_($id_licencia)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se eliminan las licencias de los usuarios
        $operacion_borrado_licencias_usuarios = "
            DELETE
            FROM licencias_usuarios
            WHERE
                licencia = '".$bd_red->_($id_licencia)."'";
        $res_borrado_licencias_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_licencias_usuarios);
        if ($res_borrado_licencias_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_licencias_usuarios."'");
        }

        // Se actualiza el menu con la licencia eliminada
        inicializa_modulos();
        $html_menu_modulos = dame_menu_modulos(MODULO_ADMINISTRACION);

        $res = "OK";
        $msg = $idiomas->_("Licencia eliminada correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "html_menu_modulos" => $html_menu_modulos))
    );
?>
