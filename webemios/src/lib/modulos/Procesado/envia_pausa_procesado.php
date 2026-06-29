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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_PAUSA_PROCESADO, $_POST);

	$idiomas = new Idiomas();

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    if ($mqtt->conecta() == true)
    {
        $mqtt->publica("PROCESS/PAUSE", "", 0);
        $mqtt->desconecta();

        $res = "OK";
        $msg = $idiomas->_("Petición de pausa de procesado de datos enviada correctamente");
    }
    else
    {
        $res = "ERROR";
        $msg = $idiomas->_("No se ha podido enviar la petición de pausa de procesado de datos");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
