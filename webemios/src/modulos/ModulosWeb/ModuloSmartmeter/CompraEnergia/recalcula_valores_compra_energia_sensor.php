<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_herramientas_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_RECALCULAR_VALORES_COMPRA_ENERGIA_SENSOR, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre_sensor = $_POST["nombre_sensor"];
    $id_sensor = $_POST["id_sensor"];
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];

    // Se recalculan los valores de compra de energía del sensor:
    // - 1: Se borran los valores del sensor asociado a partir de la fecha de recálculo de valores
    // - 2: Se guarda la fecha de recálculo de valores de clase del sensor de compra de energía

    // Se recuperan las filas del sensor y del sensor asociado
    $fila_sensor = dame_fila_sensor($id_sensor);
    $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);
    $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
    $fila_sensor_asociado = dame_fila_sensor($id_sensor_asociado);
    $nombre_sensor_asociado = $fila_sensor_asociado["nombre"];

    // Se borran los valores del sensor asociado a partir de la fecha de recálculo de valores
    $parametros_borrado_valores = array();
    $parametros_borrado_valores["clase_sensor"] = CLASE_SENSOR_ENERGIA_ACTIVA;
    $parametros_borrado_valores["nombre_sensor"] = $nombre_sensor_asociado;
    $parametros_borrado_valores["id_sensor"] = $id_sensor_asociado;
    $parametros_borrado_valores["fecha_hora_inicio"] = $cadena_fecha_hora_local_local;
    $parametros_borrado_valores["fecha_hora_fin"] = "";
    $parametros_borrado_valores["borrar_valores_tiempo_real"] = VALOR_SI;
    $resultado_borrado_valores = borra_valores_sensor($parametros_borrado_valores);
    if ($resultado_borrado_valores["res"] != "OK")
    {
        $res = $resultado_borrado_valores["res"];
        $msg = $resultado_borrado_valores["msg"];
    }

    // Si se han podido borrar los valores del sensor asociado,
    // se guarda la fecha de recálculo de valores de clase del sensor de compra de energía
    if ($resultado_borrado_valores["res"] == "OK")
    {
        $parametros_guardado_fecha_recalculo = array();
        $parametros_guardado_fecha_recalculo["clase_sensor"] = CLASE_SENSOR_COMPRA_ENERGIA;
        $parametros_guardado_fecha_recalculo["nombre_sensor"] = $nombre_sensor;
        $parametros_guardado_fecha_recalculo["id_sensor"] = $id_sensor;
        $parametros_guardado_fecha_recalculo["fecha_hora"] = $cadena_fecha_hora_local_local;
        $resultado_guardado_fecha_recalculo = guarda_fecha_recalculo_valores_clase_sensor($parametros_guardado_fecha_recalculo);
        if ($resultado_guardado_fecha_recalculo["res"] != "OK")
        {
            $res = $resultado_guardado_fecha_recalculo["res"];
            $msg = $resultado_guardado_fecha_recalculo["msg"];
        }
        else
        {
            $res = "OK";

            $msg = $idiomas->_("Valores del sensor asociado al sensor de compra de energía borrados correctamente")."\n(".
                strtolower($resultado_borrado_valores["msg"]).").\n".
                $idiomas->_("Fecha de inicio de recálculo de valores de compra de energía del sensor guardada correctamente").".\n".
                $idiomas->_("Los datos se recalcularán en el siguiente procesado de datos. Esto puede tardar unos minutos");
        }
    }

    // Se devuelve el resultado
    print(json_encode(array(
        "res" => "OK",
        "msg" => $msg))
    );
?>
