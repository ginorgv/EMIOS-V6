<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_AXON, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_axon = $_POST['id_axon'];

    // Se comprueba si existen actuadores o sensores en el axón
    $consulta_actuadores = "
        SELECT nombre
        FROM actuadores
        WHERE
            (tipo = '".TIPO_ACTUADOR_HARDWARE."')
            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
        ORDER BY nombre ASC";
    $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
    if ($res_actuadores == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
    }
    if ($res_actuadores->dame_numero_filas() > 0)
    {
        $fila_actuador = $res_actuadores->dame_siguiente_fila();
        $nombre_actuador = $fila_actuador["nombre"];

        $res = "ERROR";
        $msg = $idiomas->_("No se puede eliminar el axón porque tiene actuadores asignados")."\n(".
            $nombre_actuador.")";
    }
    else
    {
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                (tipo = '".TIPO_SENSOR_REAL."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        if ($res_sensores->dame_numero_filas() > 0)
        {
            $fila_sensor = $res_sensores->dame_siguiente_fila();
            $nombre_sensor = $fila_sensor["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el axón porque tiene sensores asignados")."\n(".
                $nombre_actuador.")";
        }
        else
        {
            // Se borra el axón
            $operacion_borrado = "
                DELETE
                FROM axones
                WHERE
                    id = '".$bd_red->_($id_axon)."'";
            $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
            if ($res_borrado == true)
            {
                $res = "OK";
                $msg = $idiomas->_("Axón eliminado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_borrado."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
