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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PERIODO, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $origen = $_POST['origen'];
    $id_origen = $_POST['id_origen'];
    $dia_inicio = $_POST['dia_inicio'];
    $dia_fin = $_POST['dia_fin'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Se comprueba si existe un periodo con el mismo origen, días y horas
    $consulta_existe = "
        SELECT *
        FROM periodos
        WHERE
            (origen = '".$bd_red->_($origen)."')
            AND (id_origen = '".$bd_red->_($id_origen)."')
            AND (dia_inicio = '".$bd_red->_($dia_inicio)."')
            AND (dia_fin = '".$bd_red->_($dia_fin)."')
            AND (hora_inicio = '".$bd_red->_($hora_inicio)."')
            AND (hora_fin = '".$bd_red->_($hora_fin)."')";
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
            // Se añade el periodo
            $operacion_insercion = "
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
                    '".$bd_red->_($origen)."',
                    '".$bd_red->_($id_origen)."',
                    '".$bd_red->_($dia_inicio)."',
                    '".$bd_red->_($dia_fin)."',
                    '".$bd_red->_($hora_inicio)."',
                    '".$bd_red->_($hora_fin)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila del periodo añadido
                $id_periodo = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_periodo = dame_fila_periodo($id_periodo);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_periodo($fila_periodo);

                $res = "OK";
                $msg = $idiomas->_("Periodo añadido correctamente");
                $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
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


    // Añade la acción de usuario de adición del periodo
    function anyade_accion_usuario_anyadir_periodo($fila)
    {
        // Nombre del origen del periodo
        $nombre_origen = Periodo::dame_nombre_id_origen_periodo($fila["origen"], $fila["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_PERIODO;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_PERIODO] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_INICIO] = $fila["dia_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_FIN] = $fila["dia_fin"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_INICIO] = $fila["hora_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_FIN] = $fila["hora_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
