<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_RATIO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_ratio = $_POST['id_ratio'];

    // Se recupera la fila del ratio
    $fila_ratio = dame_fila_ratio($id_ratio);

    // Se elimina el ratio
    $operacion_borrado = "
        DELETE
        FROM ratios
        WHERE
            id = '".$bd_red->_($id_ratio)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se borra los valores de este ratio de las localizaciones
        $operacion_borrado_ratios_localizaciones = "
            DELETE
            FROM ratios_localizaciones
            WHERE
                ratio = '".$bd_red->_($id_ratio)."'";
        $res_borrado_ratios_localizaciones = $bd_red->ejecuta_operacion($operacion_borrado_ratios_localizaciones);
        if ($res_borrado_ratios_localizaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_ratios_localizaciones."'");
        }

        // Acciones a realizar al eliminar un ratio
        realiza_acciones_ratio_eliminado($id_ratio);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_ratio($fila_ratio);

        $res = "OK";
        $msg = $idiomas->_("Ratio eliminado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un ratio
    function realiza_acciones_ratio_eliminado($id_ratio)
    {
        // Se eliminan los widgets correspondientes que contengan este ratio
        elimina_widgets_ratio_eliminado($id_ratio);

        // Se modifican los widgets correspondientes que contengan este ratio (se establece a ninguno)
        modifica_widgets_ratio_eliminado($id_ratio);

        // Se modifican los elementos de plantillas de informes que contengan este ratio (se establece a ninguno)
        modifica_elementos_plantillas_informes_ratio_eliminado($id_ratio);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_ratio_eliminado($id_ratio);
    }


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del ratio
    function anyade_accion_usuario_eliminar_ratio($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_RATIO;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
