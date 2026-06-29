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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_EVENTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $clase_sensor = $_POST["clase_sensor"];
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $granularidad = $_POST["granularidad"];
    $tipo = $_POST["tipo"];
    $parametros = $_POST["parametros"];
    $alarma = $_POST["alarma"];
    $id_evento_anterior = $_POST["id_evento_anterior"];

    // Se comprueba si existe un evento con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM eventos
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un evento con el mismo nombre");
    }
    else
    {
        // Se añade el evento
        $operacion_insercion = "
            INSERT INTO eventos (
                nombre,
                descripcion,
                red,
                clase,
                origen,
                id_origen,
                granularidad,
                tipo,
                parametros,
                alarma
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$bd_red->_($descripcion)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($clase_sensor)."',
                '".$bd_red->_($origen)."',
                '".$bd_red->_($id_origen)."',
                '".$bd_red->_($granularidad)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($parametros)."',
                '".$bd_red->_($alarma)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del evento añadido
            $id_evento = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_evento = dame_fila_evento($id_evento);

            // Si el identificador de evento existe, es un duplicado de un evento existente:
            // - Se duplican los rangos de días y los periodos (si los hay)
            if ($id_evento_anterior != ID_NINGUNO)
            {
                // Duplica los rangos de días y los periodos del evento anterior
                duplica_rangos_dias_evento_anterior($id_evento_anterior, $id_evento);
                duplica_periodos_evento_anterior($id_evento_anterior, $id_evento);
            }

            // Acciones a realizar al añadir un evento
            realiza_acciones_evento_anyadido($fila_evento);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_evento($fila_evento);

            $res = "OK";
            $msg = $idiomas->_("Evento añadido correctamente").".\n".
                $idiomas->_("Si quiere asociar una acción a este evento, recuerde crear una regla en el módulo 'Actuadores'");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Duplica los rangos de días del evento anterior
    function duplica_rangos_dias_evento_anterior($id_evento_anterior, $id_evento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los rangos de días del evento anterior (origen del actual), se cambia el id de origen y se añaden
        $consulta_rangos_dias = "
            SELECT
                origen,
                dia_anyo_inicio,
                dia_anyo_fin
            FROM rangos_dias
            WHERE
                (origen = '".ORIGEN_RANGOS_DIAS_EVENTO."')
                AND (id_origen = '".$bd_red->_($id_evento_anterior)."')";
        $res_rangos_dias = $bd_red->ejecuta_consulta($consulta_rangos_dias);
        if ($res_rangos_dias == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_rangos_dias."'");
        }

        while ($fila_rango_dias = $res_rangos_dias->dame_siguiente_fila())
        {
            $operacion_insercion_rango_dias = "
                INSERT INTO rangos_dias (
                    red,
                    origen,
                    id_origen,
                    dia_anyo_inicio,
                    dia_anyo_fin
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($fila_rango_dias["origen"])."',
                    '".$bd_red->_($id_evento)."',
                    '".$bd_red->_($fila_rango_dias["dia_anyo_inicio"])."',
                    '".$bd_red->_($fila_rango_dias["dia_anyo_fin"])."'
                )";
            $res_insercion_rango_dias = $bd_red->ejecuta_operacion($operacion_insercion_rango_dias);
            if ($res_insercion_rango_dias == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_rango_dias."'");
            }
        }
    }


    // Duplica los periodos del evento anterior
    function duplica_periodos_evento_anterior($id_evento_anterior, $id_evento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los periodos del evento anterior (origen del actual), se cambia el id de origen y se añaden
        $consulta_periodos = "
            SELECT
                origen,
                dia_inicio,
                dia_fin,
                hora_inicio,
                hora_fin
            FROM periodos
            WHERE
                (origen = '".ORIGEN_PERIODOS_EVENTO."')
                AND (id_origen = '".$bd_red->_($id_evento_anterior)."')";
        $res_periodos = $bd_red->ejecuta_consulta($consulta_periodos);
        if ($res_periodos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodos."'");
        }

        while ($fila_periodo = $res_periodos->dame_siguiente_fila())
        {
            $operacion_insercion_periodo = "
                INSERT INTO periodos (
                    red,
                    origen,
                    id_origen,
                    dia_inicio,
                    dia_fin,
                    hora_inicio,
                    hora_fin
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($fila_periodo["origen"])."',
                    '".$bd_red->_($id_evento)."',
                    '".$bd_red->_($fila_periodo["dia_inicio"])."',
                    '".$bd_red->_($fila_periodo["dia_fin"])."',
                    '".$bd_red->_($fila_periodo["hora_inicio"])."',
                    '".$bd_red->_($fila_periodo["hora_fin"])."'
                )";
            $res_insercion_periodo = $bd_red->ejecuta_operacion($operacion_insercion_periodo);
            if ($res_insercion_periodo == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_periodo."'");
            }
        }
    }


    // Realiza acciones al añadir un evento
    function realiza_acciones_evento_anyadido($fila)
    {
        // Se recargan las configuraciones de los sensores del origen del evento
        recarga_configuraciones_sensores_origen_evento(
            $fila["origen"],
            $fila["id_origen"]);
    }


    // Añade la acción de usuario de adición del evento
    function anyade_accion_usuario_anyadir_evento($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_EVENTO;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombre del origen del evento
        $nombre_origen = Evento::dame_nombre_origen_evento($fila["origen"], $fila["id_origen"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_EVENTO] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_GRANULARIDAD] = $fila["granularidad"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_EVENTO] = $fila["tipo"];
        $descripcion_parametros_evento = Evento::dame_descripcion_parametros_evento(
            $fila["clase"],
            $fila["granularidad"],
            $fila["tipo"],
            $fila["parametros"]);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_EVENTO] = $descripcion_parametros_evento;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALARMA] = $fila["alarma"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
