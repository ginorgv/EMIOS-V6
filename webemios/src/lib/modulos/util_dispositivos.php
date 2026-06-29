<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    //
    // Funciones de envío de mensajes MQTT de administración de dispositivos
    //


    function notifica_operacion_administracion_dispositivo($operacion_administracion, $id_dispositivo)
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta())
        {
            switch ($operacion_administracion)
            {
                // Operaciones de administración
                case OPERACION_ADICION:
                {
                    $mqtt->publica("MNG/".$id_dispositivo."/ADDED", "", 0);
                    break;
                }
                case OPERACION_MODIFICACION:
                {
                    $mqtt->publica("MNG/".$id_dispositivo."/MODIFIED", "", 0);
                    break;
                }
                case OPERACION_BORRADO:
                {
                    $mqtt->publica("MNG/".$id_dispositivo."/DELETED", "", 0);
                    break;
                }
            }
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT");
        }
    }

    function notifica_servidor_remoto_subscripcion_dispositivo($operacion_administracion, $imei_dispositivo, $array_ids_externos = NULL)
    {
        $ip_mqtt = "3.251.47.212";
        $user = "emios_pub";
        $pass = "energyminus20*";
        $externalContent = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
        $ip_publica_servidor_emios = $m[1];
        $ip_publica_servidor_emios =file_get_contents("http://ipecho.net/plain");

        $cadena_id_externo = implode('*',$array_ids_externos);
        $array_elementos_mensaje = array($imei_dispositivo,$cadena_id_externo,$ip_publica_servidor_emios);

        $cuerpo_mensaje = implode('#',$array_elementos_mensaje);
        #$log->error("CBLANCO funciona log cuerpo: ".$cuerpo_mensaje." array antes implode: ".$array_elementos_mensaje." ip publica: ".$ip_publica_servidor_emios);
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta_auth($user, $pass))
        {
            switch ($operacion_administracion) 
            {
                case OPERACION_ADICION:
                    {
                        $mqtt->publica("DISPOSITIVO/CONTROL/ADD", "$cuerpo_mensaje", 0);
                        break;
                    }
                case OPERACION_MODIFICACION:
                    {
                        
                        $mqtt->publica("DISPOSITIVO/CONTROL/MOD", "$cuerpo_mensaje", 0);
                        break;
                    }
                case OPERACION_BORRADO:
                    {
                        $array_mensaje = array($imei_dispositivo,$ip_publica_servidor_emios);
                        $cuerpo_mensaje = implode('#',$array_mensaje);
                        $mqtt->publica("DISPOSITIVO/CONTROL/DEL", "$cuerpo_mensaje", 0);
                        break;
                    }
            }
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT externo");
        }

    }


    // Mensaje de ejemplo
    // 516,867035049382185,quest_v0992.bin,0.992,ftp.drivehq.com
    function notifica_servidor_remoto_actualizar_dispositivo($imei_dispositivo, $parametros_mensaje)
    {
        $ip_mqtt = "3.251.47.212";
        $user = "emios_pub";
        $pass = "energyminus20*";
        $codigo_mensaje = '516';
        // Se crea el mensaje como un array y luego como un texto separado por ','
        array_unshift($parametros_mensaje, $imei_dispositivo);
        array_unshift($parametros_mensaje, $codigo_mensaje);
        $cuerpo_mensaje = implode(',',$parametros_mensaje);
        $topic = "DISPOSITIVO/".$imei_dispositivo."/CONTROL";
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta_auth($user, $pass))
        {
            $mqtt->publica($topic, "$cuerpo_mensaje", 0);
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT externo");
        }

    }
?>
