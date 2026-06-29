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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_ACCION_HERRAMIENTAS_DISPOSITIVO, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $boton = $_POST["boton"];
    $id_dispositivo = $_POST['id_dispositivo'];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    switch ($_POST["boton"])
    {
        case "boton_reiniciar":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("MNG/DEV/".$id_dispositivo."/RESET", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de reinicio enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de reinicio");
            }
            break;
        }
        case "boton_recargar_configuracion":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("MNG/DEV/".$id_dispositivo."/RELOAD", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de recarga de configuración enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de recarga de configuración");
            }
            break;
        }
        case "boton_leer_estado":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("MNG/DEV/".$id_dispositivo."/READ_STATE", "", 0);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de lectura de estado enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de lectura de estado");
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
