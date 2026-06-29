<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    //
    // Funciones de peticiones de API (interno)
    //


    // Devuelve la versión de fuentes de la red del dispositivo con la mac especificada
    function dame_version_fuentes_dispositivo_mac($parametros)
    {
        try
        {
            $mac_dispositivo = $parametros["mac"];
            $ips = $parametros["ipds"];
            $version_fuentes = Nodo::dame_version_fuentes_dispositivo_mac($mac_dispositivo, $ips);

            $res_version_fuentes_dispositivo_mac = array(
                "res" => "OK",
                "version_fuentes" => $version_fuentes);
            return ($res_version_fuentes_dispositivo_mac);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_version_fuentes_dispositivo_mac = array("res"=> "ERROR");
            return ($res_version_fuentes_dispositivo_mac);
        }
	}


    // Devuelve la versión de fuentes web de la red de la web del dispositivo con la mac especificada
    function dame_version_fuentes_web_dispositivo_mac($parametros)
    {
        try
        {
            $mac_dispositivo = $parametros["mac"];
            $version_fuentes = Nodo::dame_version_fuentes_web_dispositivo_mac($mac_dispositivo);

            $res_version_fuentes_web_dispositivo_mac = array(
                "res" => "OK",
                "version_fuentes" => $version_fuentes);
            return ($res_version_fuentes_web_dispositivo_mac);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_version_fuentes_web_dispositivo_mac = array("res"=> "ERROR");
            return ($res_version_fuentes_web_dispositivo_mac);
        }
	}


    // Devuelve los 'hashes' de las configuraciones del dispositivo y del axón (si existe)
    function dame_hash_configuraciones_dispositivo($parametros)
    {
        try
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Parámetros
            $mac_dispositivo = $parametros["mac"];

            // 'Hash' de configuración del dispositivo
            $conf = array();
            $conf[TIPO_NODO_DISPOSITIVO] = md5(urldecode(Nodo::dame_configuracion_dispositivo_mac($mac_dispositivo)));

            // 'Hash' de configuración del axón (si existe)
            $consulta_axones = "
                SELECT axones.*
                FROM
                    dispositivos,
                    axones
                WHERE
                    (dispositivos.direccion_mac = '".$bd_red->_($_GET["mac"])."')
                    AND (dispositivos.id = axones.dispositivo)";
            $res_axones = $bd_red->ejecuta_consulta($consulta_axones);
            if ($res_axones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_axones."'");
            }
            $numero_axones = $res_axones->dame_numero_filas();
            if ($numero_axones > 0)
            {
                $fila_axon = $res_axones->dame_siguiente_fila();
                $nodo = Nodo::crea_nodo($fila_axon["id"], TIPO_NODO_AXON, $fila_axon);
                $conf[TIPO_NODO_AXON] = array(
                    "ID" => $nodo->id,
                    "HASH" => md5(urldecode(json_encode($nodo->dame_conf())))
                );
            }

            // Se actualiza la dirección IP pública del dispositivo
            $operacion_modificacion_ip = "
                UPDATE dispositivos
                SET
                    ip_publica = '".$_SERVER['REMOTE_ADDR']."'
                WHERE
                    direccion_mac = '".$bd_red->_($_GET["mac"])."'";
            $res_modificacion_ip = $bd_red->ejecuta_operacion($operacion_modificacion_ip);
            if ($res_modificacion_ip == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_ip."'");
            }

            $res_hash_configuraciones_dispositivo = array(
                "res" => "OK",
                "conf" => $conf);
            return ($res_hash_configuraciones_dispositivo);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_hash_configuraciones_dispositivo = array("res"=> "ERROR");
            return ($res_hash_configuraciones_dispositivo);
        }
	}


    // Devuelve la configuración del dispositivo
    function dame_configuracion_dispositivo($parametros)
    {
        try
        {
            $mac_dispositivo = $parametros["mac"];
            $conf = urldecode(Nodo::dame_configuracion_dispositivo_mac($mac_dispositivo));

            $res_configuracion_dispositivo = array(
                "res" => "OK",
                "conf" => $conf);
            return ($res_configuracion_dispositivo);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_configuracion_dispositivo = array("res"=> "ERROR");
            return ($res_configuracion_dispositivo);
        }
	}


    // Devuelve la configuración del axón
    function dame_configuracion_axon($parametros)
    {
        try
        {
            $id_axon = $parametros["id"];
            $conf = urldecode(Nodo::dame_configuracion_axon($id_axon));

            $res_configuracion_axon = array(
                "res" => "OK",
                "conf" => $conf);
            return ($res_configuracion_axon);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_configuracion_axon = array("res"=> "ERROR");
            return ($res_configuracion_axon);
        }
	}


    // Devuelve la configuración del fichero '.ini'
    function dame_configuracion_ini()
    {
        try
        {
            $entradas_ini = dame_entradas_ini();

            $res_configuracion_ini = $entradas_ini;
            return ($res_configuracion_ini);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_configuracion_ini = array();
            return ($res_configuracion_ini);
        }
	}


    // Actualiza los órdenes de los sensores (virtuales y externos)
    function actualiza_ordenes_sensores()
    {
        try
        {
            actualiza_ordenes_sensores_tipo(TIPO_SENSOR_VIRTUAL);
            actualiza_ordenes_sensores_tipo(TIPO_SENSOR_PROCESADO);

            $res_ordenes_sensores = array("res"=> "OK");
            return ($res_ordenes_sensores);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $res_ordenes_sensores = array("res"=> "ERROR");
            return ($res_ordenes_sensores);
        }
	}
?>