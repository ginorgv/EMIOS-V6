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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_ACCION_HERRAMIENTAS_SENSOR, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $boton = $_POST["boton"];
    $id_sensor = $_POST["id_sensor"];
    $tipo_sensor = $_POST["tipo_sensor"];

    $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
    $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
    switch ($tipo_sensor)
    {
        // Botones de sensor real
        case TIPO_SENSOR_REAL:
        {
            // Se envía la petición de lectura de valores del sensor
            switch ($boton)
            {
                // Lectura de valores
                case "boton_leer_sensor":
                {
                    $id_axon = dame_axon_sensor_real($id_sensor);
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("AX/".$id_axon."/SENS/".$id_sensor."/READ", "", 0);
                        $mqtt->desconecta();

                        $res = "OK";
                        $msg = $idiomas->_("Petición de lectura del sensor enviada correctamente");
                    }
                    else
                    {
                        $res = "ERROR";
                        $msg = $idiomas->_("No se ha podido enviar la petición de lectura del sensor");
                    }
                    break;
                }
                // Recarga de configuración
                case "boton_recargar_configuracion":
                {
                    $fila_sensor = dame_fila_sensor($id_sensor);
                    $id_dispositivo = dame_dispositivo_sensor_real($fila_sensor);
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("MNG/DEV/".$id_dispositivo."/RELOAD", "", 0);
                        $mqtt->publica("REAL_SENS/SENS/".$_POST['id_sensor']."/RELOAD", "", 0);
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
                default:
                {
                    throw new Exception("Botón desconocido: '".$boton."'");
                }
            }
            break;
        }
        // Botones de sensor virtual
        case TIPO_SENSOR_VIRTUAL:
        {
            switch ($boton)
            {
                // Lectura de valores
                case "boton_leer_sensor":
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("VIRTUAL_SENS/SENS/".$id_sensor."/READ", "", 0);
                        $mqtt->desconecta();

                        $res = "OK";
                        $msg = $idiomas->_("Petición de lectura del sensor enviada correctamente");
                    }
                    else
                    {
                        $res = "ERROR";
                        $msg = $idiomas->_("No se ha podido enviar la petición de lectura del sensor");
                    }
                    break;
                }
                // Recarga de configuración
                case "boton_recargar_configuracion":
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("VIRTUAL_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
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
                default:
                {
                    throw new Exception("Botón desconocido: '".$boton."'");
                }
            }
            break;
        }
        // Botones de sensor de procesado
        case TIPO_SENSOR_PROCESADO:
        {
            switch ($boton)
            {
                // Recarga de configuración
                case "boton_recargar_configuracion":
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("PROCESS_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
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
                default:
                {
                    throw new Exception("Botón desconocido: '".$boton."'");
                }
            }
            break;
        }
        // Botones de sensor externo
        case TIPO_SENSOR_EXTERNO:
        {
            switch ($boton)
            {
                // Recarga de configuración
                case "boton_recargar_configuracion":
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("EXTERNAL_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
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
                default:
                {
                    throw new Exception("Botón desconocido: '".$boton."'");
                }
            }
            break;
        }
        default:
        {
            throw new Exception("Tipo de sensor incorrecto: '".$tipo_sensor."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
