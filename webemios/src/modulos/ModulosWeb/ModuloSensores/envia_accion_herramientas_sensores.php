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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_ACCION_HERRAMIENTAS_SENSORES, $_POST);

    $idiomas = new Idiomas();

    // Parámetros
    $boton = $_POST["boton"];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    switch ($boton)
    {
        case "boton_recargar_configuraciones_sensores":
        {
            if ($mqtt->conecta() == true)
            {
                $mqtt->publica("REAL_SENS/NET/".$_SESSION["id_red"]."/RELOAD", "", 0);
                $mqtt->publica("VIRTUAL_SENS/NET/".$_SESSION["id_red"]."/RELOAD", "", 1);
                $mqtt->publica("PROCESS_SENS/NET/".$_SESSION["id_red"]."/RELOAD", "", 1);
                $mqtt->publica("EXTERNAL_SENS/NET/".$_SESSION["id_red"]."/RELOAD", "", 1);
                $mqtt->desconecta();

                $res = "OK";
                $msg = $idiomas->_("Petición de recarga de configuración de sensores enviada correctamente");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se ha podido enviar la petición de recarga de configuración de sensores");
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
