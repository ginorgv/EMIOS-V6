<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/RangoDias.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/util_rangos_dias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_RANGO_DIAS, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_rango_dias = $_POST['id_rango_dias'];
    $origen = $_POST['origen'];
    $id_origen = $_POST['id_origen'];
    $cadena_dia_anyo_inicio_local = $_POST['dia_anyo_inicio'];
    $cadena_dia_anyo_fin_local = $_POST['dia_anyo_fin'];

    // Conversión de días anuales
    $cadena_dia_anyo_inicio_base_datos = convierte_formato_fecha($cadena_dia_anyo_inicio_local, $_SESSION["formato_dia_anyo_local"], FORMATO_DIA_ANYO_BASE_DATOS);
    $cadena_dia_anyo_fin_base_datos = convierte_formato_fecha($cadena_dia_anyo_fin_local, $_SESSION["formato_dia_anyo_local"], FORMATO_DIA_ANYO_BASE_DATOS);

    // Se comprueba si existe un rango de días con el mismo origen y días
    $consulta_existe = "
        SELECT *
        FROM rangos_dias
        WHERE
            (origen = '".$bd_red->_($origen)."')
            AND (id_origen = '".$bd_red->_($id_origen)."')
            AND (dia_anyo_inicio = '".$bd_red->_($cadena_dia_anyo_inicio_base_datos)."')
            AND (dia_anyo_fin = '".$bd_red->_($cadena_dia_anyo_fin_base_datos)."')
            AND (id <> '".$bd_red->_($id_rango_dias)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un rango de días igual");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_rango_dias_anterior = dame_fila_rango_dias($id_rango_dias);

        // Se modifica el rango de días
        $operacion_modificacion = "
            UPDATE rangos_dias
            SET
                origen = '".$bd_red->_($origen)."',
                id_origen = '".$bd_red->_($id_origen)."',
                dia_anyo_inicio = '".$bd_red->_($cadena_dia_anyo_inicio_base_datos)."',
                dia_anyo_fin = '".$bd_red->_($cadena_dia_anyo_fin_base_datos)."'
            WHERE
                id = '".$bd_red->_($id_rango_dias)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se recupera la fila actual
            $fila_rango_dias_actual = dame_fila_rango_dias($id_rango_dias);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_rango_dias(
                $fila_rango_dias_actual,
                $fila_rango_dias_anterior);

            $res = "OK";
            $msg = $idiomas->_("Rango de días modificado correctamente");
            $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    // Añade la acción de usuario de modificación del rango de días
    function anyade_accion_usuario_modificar_rango_dias($fila_actual, $fila_anterior)
    {
        // Nombre del origen del rango de días
        $nombre_origen = RangoDias::dame_nombre_id_origen_rango_dias($fila_actual["origen"], $fila_actual["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_RANGO_DIAS;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_RANGO_DIAS] = $fila_actual["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila_actual["dia_anyo_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila_actual["dia_anyo_fin"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila_anterior["dia_anyo_inicio"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila_anterior["dia_anyo_fin"];

        // Si no se ha modificado nada, no se añade la acción
        if (($fila_actual["dia_anyo_inicio"] == $fila_anterior["dia_anyo_inicio"]) &&
            ($fila_actual["dia_anyo_fin"] == $fila_anterior["dia_anyo_fin"]))
        {
            return;
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
