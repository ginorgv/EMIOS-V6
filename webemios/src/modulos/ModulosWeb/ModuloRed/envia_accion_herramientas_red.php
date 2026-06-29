<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_ACCION_HERRAMIENTAS_RED, $_POST);

    $idiomas = new Idiomas();

    // Parámetros
    $boton = $_POST["boton"];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    switch ($boton)
    {
        case "boton_ping":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("NET/".$_SESSION["id_red"]."/PING", "", 0);
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
        case "boton_recargar_configuraciones_dispositivos":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("MNG/NET/".$_SESSION["id_red"]."/RELOAD", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de recarga de configuración de dispositivos enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de recarga de configuración de dispositivos");
            }
            break;
        }
        case "boton_leer_estado_dispositivos":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("MNG/NET/".$_SESSION["id_red"]."/READ_STATE", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de lectura de estado de dispositivos enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de lectura de estado de dispositivos");
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
