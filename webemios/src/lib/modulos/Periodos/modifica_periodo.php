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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/Periodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/util_periodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PERIODO, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_periodo = $_POST['id_periodo'];
    $origen = $_POST['origen'];
    $id_origen = $_POST['id_origen'];
    $dia_inicio = $_POST['dia_inicio'];
    $dia_fin = $_POST['dia_fin'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Se comprueba si existe otro periodo con el mismo origen, días y horas
    $consulta_existe = "
        SELECT *
        FROM periodos
        WHERE
            (origen = '".$bd_red->_($origen)."')
            AND (id_origen = '".$bd_red->_($id_origen)."')
            AND (dia_inicio = '".$bd_red->_($dia_inicio)."')
            AND (dia_fin = '".$bd_red->_($dia_fin)."')
            AND (hora_inicio = '".$bd_red->_($hora_inicio)."')
            AND (hora_fin = '".$bd_red->_($hora_fin)."')
            AND (id <> '".$bd_red->_($id_periodo)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un periodo igual");
    }
    else
    {
        // Comprobación de periodo válido
        if (($dia_inicio > $dia_fin) || ($hora_inicio > $hora_fin))
        {
            $res = "ERROR";
            $msg = $idiomas->_("Los datos del periodo son incorrectos");
        }
        else
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_periodo_anterior = dame_fila_periodo($id_periodo);

            // Se modifica el periodo
            $operacion_modificacion = "
                UPDATE periodos
                SET
                    origen = '".$bd_red->_($origen)."',
                    id_origen = '".$bd_red->_($id_origen)."',
                    dia_inicio = '".$bd_red->_($dia_inicio)."',
                    dia_fin = '".$bd_red->_($dia_fin)."',
                    hora_inicio = '".$bd_red->_($hora_inicio)."',
                    hora_fin = '".$bd_red->_($hora_fin)."'
                WHERE
                    id = '".$bd_red->_($id_periodo)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_periodo_actual = dame_fila_periodo($id_periodo);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_periodo(
                    $fila_periodo_actual,
                    $fila_periodo_anterior);

                $res = "OK";
                $msg = $idiomas->_("Periodo modificado correctamente");
                $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del periodo
    function anyade_accion_usuario_modificar_periodo($fila_actual, $fila_anterior)
    {
        // Nombre del origen del periodo
        $nombre_origen = Periodo::dame_nombre_id_origen_periodo($fila_actual["origen"], $fila_actual["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_PERIODO;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_PERIODO] = $fila_actual["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_INICIO] = $fila_actual["dia_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_FIN] = $fila_actual["dia_fin"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_INICIO] = $fila_actual["hora_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_FIN] = $fila_actual["hora_fin"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_INICIO] = $fila_anterior["dia_inicio"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_FIN] = $fila_anterior["dia_fin"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_HORA_INICIO] = $fila_anterior["hora_inicio"];
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_HORA_FIN] = $fila_anterior["hora_fin"];

        // Si no se ha modificado nada, no se añade la acción
        if (($fila_actual["hora_inicio"] == $fila_anterior["hora_inicio"]) &&
            ($fila_actual["hora_fin"] == $fila_anterior["hora_fin"]) &&
            ($fila_actual["dia_inicio"] == $fila_anterior["dia_inicio"]) &&
            ($fila_actual["dia_fin"] == $fila_anterior["dia_fin"]))
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
