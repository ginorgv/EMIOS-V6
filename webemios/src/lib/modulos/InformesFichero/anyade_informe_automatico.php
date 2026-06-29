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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_INFORME_AUTOMATICO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $hora_envio = $_POST['hora_envio'];
    $periodicidad = $_POST['periodicidad'];
    $parametros_periodicidad = $_POST['parametros_periodicidad'];
    $parametros_periodo_tiempo = $_POST['parametros_periodo_tiempo'];
    $numero_horas_desplazamiento = $_POST['numero_horas_desplazamiento'];
    $tipo = $_POST['tipo'];
    $parametros_tipo = $_POST['parametros_tipo'];
    $parametros_tipo_json = $_POST['parametros_tipo_json'];
    $direcciones_email_destino = $_POST['direcciones_email_destino'];
    $parametros_periodo_personalizado = $_POST['parametros_periodo_personalizado'];

    // Se comprueba si existen el número máximo de informes automáticos
    $consulta_numero_informes_automaticos = "
        SELECT
            COUNT(*) AS numero_informes_automaticos
        FROM informes_automaticos
        WHERE
            (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_numero_informes_automaticos = $bd_red->ejecuta_consulta($consulta_numero_informes_automaticos);
    if (($res_numero_informes_automaticos == false) || ($res_numero_informes_automaticos->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_informes_automaticos."'");
    }

    $fila_numero_informes_automaticos = $res_numero_informes_automaticos->dame_siguiente_fila();
    $numero_maximo_informes_automaticos = dame_numero_maximo_informes_automaticos();
    if (($numero_maximo_informes_automaticos > 0) &&
        ($fila_numero_informes_automaticos['numero_informes_automaticos'] >= $numero_maximo_informes_automaticos))
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen el número máximo de informes automáticos");
    }
    else
    {
        // Se comprueba si existe un informe automático del usuario con el mismo nombre
        $consulta_existe = "
            SELECT nombre
            FROM informes_automaticos
            WHERE
                (nombre = '".$bd_red->_($nombre)."')
                AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
        if ($res_existe == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_existe."'");
        }
        if ($res_existe->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un informe automático con el mismo nombre");
        }
        else
        {
            $operacion_insercion = "
                INSERT INTO informes_automaticos (
                    nombre,
                    red,
                    usuario,
                    periodicidad,
                    parametros_periodicidad,
                    parametros_periodo_tiempo,
                    numero_horas_desplazamiento,
                    tipo,
                    parametros_tipo,
                    parametros_tipo_json,
                    direcciones_email_destino,
                    hora_ultimo_envio,
                    ultimo_envio_correcto,
                    hora_envio,
                    parametros_periodo_personalizado
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($_SESSION["id_usuario"])."',
                    '".$bd_red->_($periodicidad)."',
                    '".$bd_red->_($parametros_periodicidad)."',
                    '".$bd_red->_($parametros_periodo_tiempo)."',
                    '".$bd_red->_($numero_horas_desplazamiento)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($parametros_tipo)."',
                    '".$bd_red->_($parametros_tipo_json)."',
                    '".$bd_red->_($direcciones_email_destino)."',
                    NULL,
                    NULL,
                    '".$bd_red->_($hora_envio)."',
                    '".$bd_red->_($parametros_periodo_personalizado)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recupera el id del informe automático añadido
                $id_informe_automatico = $bd_red->dame_id_autoincremental_ultima_insercion();

                $res = "OK";
                $msg = $idiomas->_("Informe automático añadido correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_informe_automatico" => $id_informe_automatico))
    );
?>
