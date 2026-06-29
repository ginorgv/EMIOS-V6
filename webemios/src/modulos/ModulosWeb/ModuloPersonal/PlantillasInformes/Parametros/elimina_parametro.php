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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_administracion_parametros.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PARAMETRO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_parametro = $_POST['id_parametro'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];

    // Comprobaciones antes de eliminar el parámetro de la plantilla de informe:
    // - Si se está utilizando el parámetro en algún elemento, no se puede eliminar
    // - Si está asociado a otro parámetro, no se puede eliminar
    $eliminar_parametro = true;

    // Si se está utilizando el parámetro en algún elemento, no se puede eliminar
    if ($eliminar_parametro == true)
    {
        $nombre_elemento = NULL;
        $parametro_utilizado = dame_parametro_utilizado_elementos_plantilla_informe($id_plantilla_informe, $id_parametro, $nombre_elemento);
        if ($parametro_utilizado == true)
        {
            $eliminar_parametro = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el parámetro porque se esta utilizando en algún elemento")."\n(".
                $nombre_elemento.")";
        }
    }

    // Si está asociado a otro parámetro, no se puede eliminar
    if ($eliminar_parametro == true)
    {
        $consulta_parametro_asociado = "
            SELECT nombre
            FROM parametros_plantillas_informes
            WHERE
                (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_ID_PARAMETRO_SENSOR_ASOCIADO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_parametro)."')
            ORDER BY nombre ASC";
        $res_parametro_asociado = $bd_red->ejecuta_consulta($consulta_parametro_asociado);
        if ($res_parametro_asociado == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametro_asociado."'");
        }

        if ($res_parametro_asociado->dame_numero_filas() > 0)
        {
            $eliminar_parametro = false;

            $fila_parametro_asociado = $res_parametro_asociado->dame_siguiente_fila();
            $nombre_parametro_asociado = $fila_parametro_asociado["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el parámetro porque está asociado a otro parámetro")."\n(".
                $nombre_parametro_asociado.")";
        }
    }

    // Se elimina el parámetro
    if ($eliminar_parametro == true)
    {
        // Se elimina el parámetro
        $operacion_borrado = "
            DELETE
            FROM parametros_plantillas_informes
            WHERE
                id = '".$bd_red->_($id_parametro)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Parámetro eliminado correctamente");

            // Acciones a realizar al eliminar un parámetro
            realiza_acciones_parametro_eliminado($id_plantilla_informe, $id_parametro);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un parámetro
    function realiza_acciones_parametro_eliminado($id_plantilla_informe, $id_parametro)
    {
        // Se actualiza el usuario de la plantilla de informe (si es necesario)
        actualiza_usuario_plantilla_informe($id_plantilla_informe);

        // Se elimina el parámetro de plantilla de informe configurable de los informes automáticos correspondientes
        elimina_parametro_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_parametro);
    }
?>
