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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_INFORME_AUTOMATICO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_informe_automatico = $_POST['id_informe_automatico'];
    $hora_envio = $_POST['hora_envio'];
    $nombre = $_POST['nombre'];
    $periodicidad = $_POST['periodicidad'];
    $parametros_periodicidad = $_POST['parametros_periodicidad'];
    $parametros_periodo_tiempo = $_POST['parametros_periodo_tiempo'];
    $numero_horas_desplazamiento = $_POST['numero_horas_desplazamiento'];
    $direcciones_email_destino = $_POST['direcciones_email_destino'];
    $parametros_periodo_personalizado = $_POST['parametros_periodo_personalizado'];

    // Se comprueba si existe otro informe automático del usuario con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM informes_automaticos
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_informe_automatico)."')";
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
        $operacion_modificacion = "
            UPDATE informes_automaticos
            SET
                nombre = '".$bd_red->_($nombre)."',
                hora_envio = '".$bd_red->_($hora_envio)."',
                periodicidad = '".$bd_red->_($periodicidad)."',
                parametros_periodicidad = '".$bd_red->_($parametros_periodicidad)."',
                parametros_periodo_tiempo = '".$bd_red->_($parametros_periodo_tiempo)."',
                numero_horas_desplazamiento = '".$bd_red->_($numero_horas_desplazamiento)."',
                direcciones_email_destino = '".$bd_red->_($direcciones_email_destino)."',
                parametros_periodo_personalizado = '".$bd_red->_($parametros_periodo_personalizado)."'
            WHERE
                id = '".$bd_red->_($id_informe_automatico)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Informe automático modificado correctamente");
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
?>
