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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_ACCION_HERRAMIENTAS_AXON, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $boton = $_POST["boton"];
    $id_axon = $_POST['id_axon'];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    switch ($boton)
    {
        case "boton_ping":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("AX/".$id_axon."/PING", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Ping enviado correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar el ping");
            }
            break;
        }
        case "boton_leer_sensores":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("AX/".$id_axon."/ALL_SENS/READ", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de lectura de sensores enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de lectura de sensores");
            }
            break;
        }
        default:
        {
            throw new Exception("Botón desconocido: '".$boton."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
