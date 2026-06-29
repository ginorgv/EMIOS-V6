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
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_plantilla_informe = $_POST['id_plantilla_informe'];

    // Se elimina la plantilla de informe
    $operacion_borrado = "
        DELETE
        FROM plantillas_informes
        WHERE
            id = '".$bd_red->_($id_plantilla_informe)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Acciones a realizar al eliminar una plantilla de informe
        realiza_acciones_plantilla_informe_eliminada($id_plantilla_informe);

        $res = "OK";
        $msg = $idiomas->_("Plantilla de informe eliminada correctamente");
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


    // Realiza acciones al eliminar una plantilla de informe
    function realiza_acciones_plantilla_informe_eliminada($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se eliminan las imágenes de la plantilla de informe
        elimina_imagen_base_datos(ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, $id_plantilla_informe);

        // Se eliminan los parámetros y los elementos de la plantilla
        $operacion_borrado_parametros = "
            DELETE
            FROM parametros_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'";
        $res_borrado_parametros = $bd_red->ejecuta_operacion($operacion_borrado_parametros);
        if ($res_borrado_parametros == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros."'");
        }

        $operacion_borrado_elementos = "
            DELETE
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'";
        $res_borrado_elementos = $bd_red->ejecuta_operacion($operacion_borrado_elementos);
        if ($res_borrado_elementos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_elementos."'");
        }

        // Se eliminan las imágenes de los elementos de la plantilla con imágenes
        $operacion_borrado_imagenes_elementos = "
            DELETE
            FROM imagenes
            WHERE
                (origen = '".ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(0 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = '".$bd_red->_($id_plantilla_informe)."')";
        $res_borrado_imagenes_elementos = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_elementos);
        if ($res_borrado_imagenes_elementos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_elementos."'");
        }

        // Se eliminan los informes automáticos de esta plantilla de informe
        elimina_informes_automaticos_plantilla_informe_eliminada($id_plantilla_informe);
    }
?>
