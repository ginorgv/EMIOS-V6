<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_EJECUCION_MANUAL_PROCESADO, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $tipo_ejecucion_procesado = $_POST['tipo_ejecucion_procesado'];
    $clase_sensor = $_POST['clase_sensor'];
    $tipo_sensor = $_POST['tipo_sensor'];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    if ($mqtt->conecta() == true)
    {
        if ($clase_sensor != CLASE_NINGUNA)
        {
            $mqtt->publica("PROCESS/EXECUTION_TYPE/".$tipo_ejecucion_procesado."/CLASS/".$clase_sensor."/EXECUTE", "", 0);
        }
        if ($tipo_sensor != TIPO_NINGUNO)
        {
            $mqtt->publica("PROCESS/EXECUTION_TYPE/".$tipo_ejecucion_procesado."/TYPE/".$tipo_sensor."/EXECUTE", "", 0);
        }
        $mqtt->desconecta();

        $res = "OK";
        $msg = $idiomas->_("Petición de ejecución manual de procesado de datos enviada correctamente");
    }
    else
    {
        $res = "ERROR";
        $msg = $idiomas->_("No se ha podido enviar la petición de ejecución manual de procesado de datos");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
