<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_compra_energia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones de envío de mensajes MQTT de sensores
    //


    function notifica_operacion_administracion_sensor(
        $tipo_sensor,
        $operacion_administracion,
        $id_sensor,
        $parametros_extra = array())
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta())
        {
            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_REAL:
                {
                    $prefijo_asunto_mqtt = "REAL_SENS";
                    $datos_mqtt = "";
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $prefijo_asunto_mqtt = "VIRTUAL_SENS";
                    $datos_mqtt = "";
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $prefijo_asunto_mqtt = "PROCESS_SENS";
                    $datos_mqtt = "";
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $prefijo_asunto_mqtt = "EXTERNAL_SENS";

                    // Datos 'extra' a enviar en la operación de sensor externo
                    switch ($operacion_administracion)
                    {
                        case OPERACION_ADICION:
                        {
                            $clase_sensor_externo = $parametros_extra["clase_sensor_externo"];
                            $datos_mqtt = $clase_sensor_externo;
                            break;
                        }
                        case OPERACION_MODIFICACION:
                        {
                            $clase_sensor_externo = $parametros_extra["clase_sensor_externo"];
                            $clase_sensor_externo_anterior = $parametros_extra["clase_sensor_externo_anterior"];
                            $datos_mqtt = implode("#", array(
                                $clase_sensor_externo,
                                $clase_sensor_externo_anterior));
                            break;
                        }
                        case OPERACION_BORRADO:
                        {
                            $clase_sensor_externo = $parametros_extra["clase_sensor_externo"];
                            $datos_mqtt = $clase_sensor_externo;
                            break;
                        }
                    }
                    break;
                }
            }
            switch ($operacion_administracion)
            {
                // Operaciones de administración
                case OPERACION_ADICION:
                {
                    $mqtt->publica($prefijo_asunto_mqtt."/SENS/".$id_sensor."/ADDED", $datos_mqtt, 1);
                    break;
                }
                case OPERACION_MODIFICACION:
                {
                    $mqtt->publica($prefijo_asunto_mqtt."/SENS/".$id_sensor."/MODIFIED", $datos_mqtt, 1);
                    break;
                }
                case OPERACION_BORRADO:
                {
                    $mqtt->publica($prefijo_asunto_mqtt."/SENS/".$id_sensor."/DELETED", $datos_mqtt, 1);
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


    function notifica_valores_recibidos_sensor_externo(
        $id_sensor_externo,
        $timestamp_valores_utc,
        $causas_envio_valores,
        $tipo_valores,
        $segundos_incrementos_valores,
        $tipo_hora_incrementos_valores,
        $valores,
        $ultimos_valores)
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta())
        {
            $asunto_mqtt = "EXTERNAL_SENS/VALUES";
            switch ($tipo_valores)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $opciones_valores = "P";
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $opciones_valores = implode(",", array(
                        "I",
                        $segundos_incrementos_valores,
                        $tipo_hora_incrementos_valores));
                    break;
                }
            }
            $cadena_valores = implode(SUSTITUTO_SEPARADOR, $valores);
            if ($ultimos_valores == true)
            {
                $cadena_ultimos_valores = 1;
            }
            else
            {
                $cadena_ultimos_valores = 0;
            }
            $datos_mqtt = implode("#", array(
                $id_sensor_externo,
                $timestamp_valores_utc,
                $causas_envio_valores,
                $opciones_valores,
                $cadena_valores,
                $cadena_ultimos_valores));

            $mqtt->publica($asunto_mqtt, $datos_mqtt, 0);
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT");
        }
    }


    function notifica_operacion_administracion_importacion_valores_sensor_pendiente(
        $operacion_administracion,
        $id_importacion_pendiente)
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta())
        {
            $datos_mqtt = "";
            switch ($operacion_administracion)
            {
                // Operaciones de administración
                case OPERACION_ADICION:
                {
                    $mqtt->publica("PENDING_IMPORT/".$id_importacion_pendiente."/ADDED", $datos_mqtt, 1);
                    break;
                }
                case OPERACION_BORRADO:
                {
                    $mqtt->publica("PENDING_IMPORT/".$id_importacion_pendiente."/DELETED", $datos_mqtt, 1);
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


    //
    // Funciones de consultas de valores de sensores
    //


    // Devuelve la consulta SQL de los valores de la clase de un sensor
    function dame_consulta_valores_sensor(
        $id_sensor,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $parametros_extra)
	{
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Información del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $clase_sensor = $fila_sensor["clase"];
        $nombre_sensor = $fila_sensor["nombre"];
        $id_red_sensor = $fila_sensor["red"];
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];
        $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];

        // Características de la clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
        $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

        // Intervalos de valores de tiempo real
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                break;
            }
        }

        // Comprobación de intervalo de valores soportado en la clase de sensor
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                if ($clase_procesado_valores == false)
                {
                    throw Exception("Intervalo de valores no soportado en la clase de sensor: '".$clase_sensor."'");
                }
                if ($clase_granularidad_cuartohoraria == false)
                {
                    $intervalo_valores = INTERVALO_VALORES_HORA;
                }
                break;
            }
            case INTERVALO_VALORES_HORA:
            case INTERVALO_VALORES_DIA:
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                if ($clase_procesado_valores == false)
                {
                    throw Exception("Intervalo de valores no soportado en la clase de sensor: '".$clase_sensor."'");
                }
                break;
            }
        }

        // Nota: Si el sensor es de gas y el país de tarifas de gas es España,
        // se restan 6 horas a la hora para la agrupación de valores
        // (para que la agrupación vaya por días de 06:00 a 05:59 del día siguiente)
        $cadena_campo_hora_agrupacion = "hora";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {

                        // EMG_ACCIONA : Si la red es de Acciona, el tiempo de gas tiene que ser desde las 00
						$id_red = $_SESSION["id_red"];
						$bd_red = BaseDatosRed::dame_base_datos();
						$consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
						$res = $bd_red->ejecuta_consulta($consulta);
						$fila = $res->dame_siguiente_fila();
						$nombre_cliente = $fila["nombre"];

						if ($nombre_cliente != 'Acciona')
                        {
                            $cadena_campo_hora_agrupacion = "TIMESTAMPADD(HOUR, -6, hora)";
                            break;
                        }
                    }
                }
                break;
            }
        }

        // Se crea la consulta de los valores
        $tabla_datos_base = dame_nombre_tabla_datos_clase_sensor($clase_sensor);
        $tabla_datos = $tabla_datos_base;
		if (isset($_SESSION["usuario_interno"]) && ($_SESSION["usuario_interno"] == USUARIO_INTERNO_API_HTTP)){
			$id_red = $_SESSION["id_red"];
			$bd_red = BaseDatosRed::dame_base_datos();
			$consulta = "SELECT zona_horaria FROM redes WHERE id = ".$id_red;
			$res = $bd_red->ejecuta_consulta($consulta);
			$fila = $res->dame_siguiente_fila();
			$zona_horaria = $fila["zona_horaria"];
		}else{
        	$zona_horaria = dame_zona_horaria_local();
		}
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                if ($tipo_valores_sensor == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $tabla_datos .= SUFIJO_TABLA_INCREMENTOS;
                    if (($clase_procesado_valores == false) || ($incrementos_tiempo_real_horarios == VALOR_NO))
                    {
                        $tabla_datos .= SUFIJO_TABLA_TIEMPO_REAL;
                    }
                }
                $agrupacion_valores = NULL;
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                $tabla_datos .= SUFIJO_TABLA_CUARTOSHORA;
                $agrupacion_valores = NULL;
                break;
            }
            case INTERVALO_VALORES_HORA:
            {
                $tabla_datos .= SUFIJO_TABLA_HORAS;
                $agrupacion_valores = NULL;
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $tabla_datos .= SUFIJO_TABLA_HORAS;
                $agrupacion_valores = "GROUP BY
                    DATE(CONVERT_TZ(".$cadena_campo_hora_agrupacion.", '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))";
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $tabla_datos .= SUFIJO_TABLA_HORAS;
                $agrupacion_valores = "GROUP BY
                    YEARWEEK(CONVERT_TZ(".$cadena_campo_hora_agrupacion.", '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), 1)";
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $tabla_datos .= SUFIJO_TABLA_HORAS;
                $agrupacion_valores = "GROUP BY
                    YEAR(CONVERT_TZ(".$cadena_campo_hora_agrupacion.", '".ZONA_HORARIA_UTC."', '".$zona_horaria."')),
                    MONTH(CONVERT_TZ(".$cadena_campo_hora_agrupacion.", '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))";
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores desconocido: '".$intervalo_valores."'");
            }
        }
        $campo_ordenacion_hora = NULL;
        if ($agrupacion_valores === NULL)
        {
            $consulta_valores_sensor = "SELECT
                hora AS fecha_hora, ";
            $campo_ordenacion_hora = "fecha_hora";
        }
        else
        {
            $consulta_valores_sensor = "SELECT
                MIN(hora) AS fecha_hora, ";
            $campo_ordenacion_hora = "fecha_hora";
        }

        // Campos de la clase de sensor
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                if (($parametros_extra === NULL) || ($parametros_extra == ""))
                {
                    $temperatura_referencia = 0;
                }
                else
                {
                    $temperatura_referencia = $parametros_extra;
                }
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .=
                            CAMPO_TEMPERATURA.", ".
                            "IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") > 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") * -1) AS ".CAMPO_GRADOS_HORA_CALEFACCION.", ".
                            "IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") < 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.")) AS ".CAMPO_GRADOS_HORA_REFRIGERACION.", ".
                            "IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") > 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") * -1 / 24) AS ".CAMPO_GRADOS_DIA_CALEFACCION.", ".
                            "IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") < 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") / 24) AS ".CAMPO_GRADOS_DIA_REFRIGERACION;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .=
                            "AVG(".CAMPO_TEMPERATURA.") AS ".CAMPO_TEMPERATURA.", ".
                            "SUM(IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") > 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") * -1)) AS ".CAMPO_GRADOS_HORA_CALEFACCION.", ".
                            "SUM(IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") < 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia."))) AS ".CAMPO_GRADOS_HORA_REFRIGERACION.", ".
                            "SUM(IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") > 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") * -1 / 24)) AS ".CAMPO_GRADOS_DIA_CALEFACCION.", ".
                            "SUM(IF ((".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") < 0, 0, (".CAMPO_TEMPERATURA." - ".$temperatura_referencia.") / 24)) AS ".CAMPO_GRADOS_DIA_REFRIGERACION;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= CAMPO_HUMEDAD;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "AVG(".CAMPO_HUMEDAD.") AS ".CAMPO_HUMEDAD;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .=
                            CAMPO_ILUMINACION.", ".
                            CAMPO_LUZ_ARTIFICIAL;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            AVG(".CAMPO_ILUMINACION.") AS ".CAMPO_ILUMINACION.",
                            AVG(".CAMPO_LUZ_ARTIFICIAL.") AS ".CAMPO_LUZ_ARTIFICIAL;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .=
                            CAMPO_VELOCIDAD.", ".
                            CAMPO_DIRECCION;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            AVG(".CAMPO_VELOCIDAD.") AS ".CAMPO_VELOCIDAD.",
                            AVG(".CAMPO_DIRECCION.") AS ".CAMPO_DIRECCION;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        switch ($tipo_valores_sensor)
                        {
                            case TIPO_VALORES_SENSOR_PUNTUALES:
                            {
                                $consulta_valores_sensor .= CAMPO_ABSOLUTO;
                                $campo_tiempo_real = CAMPO_ABSOLUTO;
                                break;
                            }
                            case TIPO_VALORES_SENSOR_INCREMENTALES:
                            {
                                $consulta_valores_sensor .= "
                                    horas, ".
                                    CAMPO_INCREMENTO;
                                $campo_tiempo_real = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                    case INTERVALO_VALORES_CUARTOHORA:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_ABSOLUTO.", ".
                            CAMPO_INCREMENTO.", ".
                            "(".CAMPO_INCREMENTO." / horas) AS ".CAMPO_INCREMENTO_POTENCIA.", ".
                            CAMPO_TRAMO.", ".
                            CAMPO_COSTE.", ".
                            CAMPO_SOBREPOTENCIA;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) as horas,
                            AVG(".CAMPO_ABSOLUTO.") AS ".CAMPO_ABSOLUTO.",
                            SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO.",
                            (SUM(".CAMPO_INCREMENTO.") / SUM(horas)) AS ".CAMPO_INCREMENTO_POTENCIA.",
                            AVG(".CAMPO_TRAMO.") AS ".CAMPO_TRAMO.",
                            SUM(".CAMPO_COSTE.") AS ".CAMPO_COSTE.",
                            MAX(".CAMPO_SOBREPOTENCIA.") AS ".CAMPO_SOBREPOTENCIA;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        switch ($tipo_valores_sensor)
                        {
                            case TIPO_VALORES_SENSOR_PUNTUALES:
                            {
                                $consulta_valores_sensor .= CAMPO_ABSOLUTO;
                                $campo_tiempo_real = CAMPO_ABSOLUTO;
                                break;
                            }
                            case TIPO_VALORES_SENSOR_INCREMENTALES:
                            {
                                $consulta_valores_sensor .= "
                                    horas, ".
                                    CAMPO_INCREMENTO;
                                $campo_tiempo_real = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                    case INTERVALO_VALORES_CUARTOHORA:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_ABSOLUTO.", ".
                            CAMPO_INCREMENTO.", ".
                            "(".CAMPO_INCREMENTO." / horas) AS ".CAMPO_INCREMENTO_POTENCIA.", ".
                            CAMPO_TRAMO.", ".
                            CAMPO_COSENO_PHI.", ".
                            CAMPO_PENALIZABLE;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) AS horas,
                            AVG(".CAMPO_ABSOLUTO.") AS ".CAMPO_ABSOLUTO.",
                            SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO.",
                            (SUM(".CAMPO_INCREMENTO.") / SUM(horas)) AS ".CAMPO_INCREMENTO_POTENCIA.",
                            AVG(".CAMPO_TRAMO.") AS ".CAMPO_TRAMO.",
                            AVG(".CAMPO_COSENO_PHI.") AS ".CAMPO_COSENO_PHI.",
                            AVG(".CAMPO_PENALIZABLE.") AS ".CAMPO_PENALIZABLE;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        $consulta_valores_sensor .= CAMPO_CORTES;
                        break;
                    }
                    default:
                    {
                        throw new Exception("La clase de corte de tensión sólo tiene valores en tiempo real");
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_CONSUMO_ESTIMADO;
                        break;
                    }
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_CONSUMO_ESTIMADO.", ".
                            CAMPO_CONSUMO_REAL.", ".
                            CAMPO_DESVIO_CONSUMO.", ".
                            CAMPO_COSTE_DESVIO.", ".
                            CAMPO_PENALIZABLE;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) as horas,
                            SUM(".CAMPO_CONSUMO_ESTIMADO.") AS ".CAMPO_CONSUMO_ESTIMADO.",
                            SUM(".CAMPO_CONSUMO_REAL.") AS ".CAMPO_CONSUMO_REAL.",
                            SUM(".CAMPO_DESVIO_CONSUMO.") AS ".CAMPO_DESVIO_CONSUMO.",
                            SUM(".CAMPO_COSTE_DESVIO.") AS ".CAMPO_COSTE_DESVIO.",
                            AVG(".CAMPO_PENALIZABLE.") AS ".CAMPO_PENALIZABLE;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        switch ($tipo_valores_sensor)
                        {
                            case TIPO_VALORES_SENSOR_PUNTUALES:
                            {
                                $consulta_valores_sensor .= CAMPO_ABSOLUTO;
                                $campo_tiempo_real = CAMPO_ABSOLUTO;
                                break;
                            }
                            case TIPO_VALORES_SENSOR_INCREMENTALES:
                            {
                                $consulta_valores_sensor .= "
                                    horas, ".
                                    CAMPO_INCREMENTO;
                                $campo_tiempo_real = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                    case INTERVALO_VALORES_CUARTOHORA:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_ABSOLUTO.", ".
                            CAMPO_INCREMENTO.", ".
                            CAMPO_CONSUMO.", ".
                            CAMPO_COSTE;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) as horas,
                            AVG(".CAMPO_ABSOLUTO.") AS ".CAMPO_ABSOLUTO.",
                            SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO.",
                            SUM(".CAMPO_CONSUMO.") AS ".CAMPO_CONSUMO.",
                            SUM(".CAMPO_COSTE.") AS ".CAMPO_COSTE;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        switch ($tipo_valores_sensor)
                        {
                            case TIPO_VALORES_SENSOR_PUNTUALES:
                            {
                                $consulta_valores_sensor .= CAMPO_ABSOLUTO;
                                $campo_tiempo_real = CAMPO_ABSOLUTO;
                                break;
                            }
                            case TIPO_VALORES_SENSOR_INCREMENTALES:
                            {
                                $consulta_valores_sensor .= "
                                    horas, ".
                                    CAMPO_INCREMENTO;
                                $campo_tiempo_real = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                    case INTERVALO_VALORES_CUARTOHORA:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_ABSOLUTO.", ".
                            CAMPO_INCREMENTO;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) as horas,
                            AVG(".CAMPO_ABSOLUTO.") AS ".CAMPO_ABSOLUTO.",
                            SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        switch ($tipo_valores_sensor)
                        {
                            case TIPO_VALORES_SENSOR_PUNTUALES:
                            {
                                $consulta_valores_sensor .= CAMPO_VALOR;
                                $campo_tiempo_real = CAMPO_VALOR;
                                break;
                            }
                            case TIPO_VALORES_SENSOR_INCREMENTALES:
                            {
                                $consulta_valores_sensor .= "
                                    horas, ".
                                    CAMPO_INCREMENTO;
                                $campo_tiempo_real = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                    case INTERVALO_VALORES_CUARTOHORA:
                    case INTERVALO_VALORES_HORA:
                    {
                        $consulta_valores_sensor .= "
                            horas, ".
                            CAMPO_VALOR.", ".
                            CAMPO_INCREMENTO;
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            SUM(horas) as horas,
                            AVG(".CAMPO_VALOR.") AS ".CAMPO_VALOR_MEDIA.",
                            SUM(".CAMPO_VALOR.") AS ".CAMPO_VALOR_SUMA.",
                            SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO_SUMA.",
                            AVG(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO_MEDIA;
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }

        // Se añaden el sensor y las horas a la consulta
        $consulta_valores_sensor .= "
            FROM ".$tabla_datos."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$bd_datos->_($id_red_sensor)."')
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        $consulta_valores_sensor .= "
                            AND (".$campo_tiempo_real." IS NOT NULL)";
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            AND ((".CAMPO_ABSOLUTO." IS NOT NULL) OR (".CAMPO_INCREMENTO." IS NOT NULL))";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    {
                        $consulta_valores_sensor .= "
                            AND (".$campo_tiempo_real." IS NOT NULL)";
                        break;
                    }
                    default:
                    {
                        $consulta_valores_sensor .= "
                            AND ((".CAMPO_VALOR." IS NOT NULL) OR (".CAMPO_INCREMENTO." IS NOT NULL))";
                        break;
                    }
                }
                break;
            }
        }

        // Se crea el filtro de consulta de horario semanal y fechas
        $filtro_consulta_horario_semanal_fechas = dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se crean las consultas de los valores agrupados
        $granularidades_tablas_datos_agrupados = array();
        $sufijos_tablas_datos_agrupados = array();
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_DIA:
            {
                array_push($granularidades_tablas_datos_agrupados, GRANULARIDAD_DIARIA);
                array_push($sufijos_tablas_datos_agrupados, SUFIJO_TABLA_DIAS);
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                array_push($granularidades_tablas_datos_agrupados, GRANULARIDAD_DIARIA);
                array_push($sufijos_tablas_datos_agrupados, SUFIJO_TABLA_DIAS);
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                array_push($granularidades_tablas_datos_agrupados, GRANULARIDAD_DIARIA);
                array_push($granularidades_tablas_datos_agrupados, GRANULARIDAD_MENSUAL);
                array_push($sufijos_tablas_datos_agrupados, SUFIJO_TABLA_DIAS);
                array_push($sufijos_tablas_datos_agrupados, SUFIJO_TABLA_MESES);
                break;
            }
        }
        $consultas_valores_agrupados = array();
        for ($i = 0; $i < count($granularidades_tablas_datos_agrupados); $i++)
        {
            $granularidad_tablas_datos_agrupados = $granularidades_tablas_datos_agrupados[$i];
            $sufijo_tabla_datos_agrupados = $sufijos_tablas_datos_agrupados[$i];
            $consulta_valores_agrupados = str_replace($tabla_datos, $tabla_datos_base.$sufijo_tabla_datos_agrupados, $consulta_valores_sensor);
            switch ($granularidad_tablas_datos_agrupados)
            {
                case GRANULARIDAD_DIARIA:
                {
                    $consulta_valores_agrupados .= $filtro_consulta_horario_semanal_fechas;
                    break;
                }
            }
            if ($agrupacion_valores !== NULL)
            {
                $consulta_valores_agrupados .= " ".$agrupacion_valores;
            }
            array_push($consultas_valores_agrupados, $consulta_valores_agrupados);
        }

        // Se añaden el horario semanal, la exclusión e inclusión de fechas y la agrupación de valores
        $consulta_valores_sensor .= $filtro_consulta_horario_semanal_fechas;
        if ($agrupacion_valores !== NULL)
        {
            $consulta_valores_sensor .= " ".$agrupacion_valores;
        }

        // Si hay consultas de valores agrupados se hace una 'UNION' de las consultas
        if (count($consultas_valores_agrupados) > 0)
        {
            $consulta_valores_sensor = "\n\t\t\t\t(".$consulta_valores_sensor.")";
            foreach ($consultas_valores_agrupados as $consulta_valores_agrupados)
            {
                $consulta_valores_sensor .= "\n\nUNION DISTINCT\n\n";
                $consulta_valores_sensor .= "\t\t\t\t(".$consulta_valores_agrupados.")";
            }
        }

        // Ordenación de valores
        if ($campo_ordenacion_hora !== NULL)
        {
            $consulta_valores_sensor .= " ORDER BY ".$campo_ordenacion_hora." ASC";
        }

        //$log = dame_log();
        //$log->info("Consulta de valores de sensor: '".$consulta_valores_sensor."'");
        return ($consulta_valores_sensor);
	}


    // Devuelve el valor de la consulta de un campo de sensor para la consulta de valores
    function dame_valor_consulta_campo_clase_sensor($clase_sensor, $campo, $parametros_extra_campo)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                if (($parametros_extra_campo === NULL) || ($parametros_extra_campo == ""))
                {
                    $temperatura_referencia = 0;
                }
                else
                {
                    $temperatura_referencia = $parametros_extra_campo;
                }
                switch ($campo)
                {
                    case CAMPO_GRADOS_HORA_CALEFACCION:
                    {
                        $campo_valor = CAMPO_TEMPERATURA;
                        $valor_consulta = "IF ((".$campo_valor." - ".$temperatura_referencia.") > 0, 0, (".$campo_valor." - ".$temperatura_referencia.") * -1)";
                        break;
                    }
                    case CAMPO_GRADOS_HORA_REFRIGERACION:
                    {
                        $campo_valor = CAMPO_TEMPERATURA;
                        $valor_consulta = "IF ((".$campo_valor." - ".$temperatura_referencia.") < 0, 0, (".$campo_valor." - ".$temperatura_referencia."))";
                        break;
                    }
                    case CAMPO_GRADOS_DIA_CALEFACCION:
                    {
                        $campo_valor = CAMPO_TEMPERATURA;
                        $valor_consulta = "IF ((".$campo_valor." - ".$temperatura_referencia.") > 0, 0, (".$campo_valor." - ".$temperatura_referencia.") * -1 / 24)";
                        break;
                    }
                    case CAMPO_GRADOS_DIA_REFRIGERACION:
                    {
                        $campo_valor = CAMPO_TEMPERATURA;
                        $valor_consulta = "IF ((".$campo_valor." - ".$temperatura_referencia.") < 0, 0, (".$campo_valor." - ".$temperatura_referencia.") / 24)";
                        break;
                    }
                    default:
                    {
                        $campo_valor = $campo;
                        $valor_consulta = $campo;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($campo)
                {
                    case CAMPO_INCREMENTO_POTENCIA:
                    {
                        $campo_valor = CAMPO_INCREMENTO;
                        $valor_consulta = "(".CAMPO_INCREMENTO." / horas)";
                        break;
                    }
                    default:
                    {
                        $campo_valor = $campo;
                        $valor_consulta = $campo;
                        break;
                    }
                }
                break;
            }
            default:
            {
                $campo_valor = $campo;
                $valor_consulta = $campo;
                break;
            }
        }
        $res = array(
            "campo_valor" => $campo_valor,
            "valor_consulta" => $valor_consulta);
        return ($res);
    }


    //
    // Funciones de recuperación de filas de valores de sensores
    //


    // Devuelve las filas de valores de un sensor
    function dame_filas_valores_sensor($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se realiza la consulta de los valores
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Se recorren y guardan las filas
        $filas_valores_sensor = array();
        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
			array_push($filas_valores_sensor, $fila_valores_sensor);
        }
        return ($filas_valores_sensor);
    }


    // Devuelve las filas de valores de sensores
    function dame_filas_valores_sensores($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se recorren los sensores
        $numero_filas_valores_sensores_totales = 0;
        $filas_valores_sensores = array();
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Id y nombre del sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Se realiza la consulta de los valores
            $consulta_valores_sensor = dame_consulta_valores_sensor(
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $parametros_extra_campo);
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Se recorren y guardan las filas
            $filas_valores_sensor = array();
            while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
            {
                array_push($filas_valores_sensor, $fila_valores_sensor);
            }
            $filas_valores_sensores[$nombre_sensor] = $filas_valores_sensor;

            // Número de filas totales
            $numero_filas_valores_sensores_totales += count($filas_valores_sensor);
            if ($numero_filas_valores_sensores_totales > NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL)
            {
                throw new Exception(
                    "Número máximo de valores superado (refine la búsqueda)",
                    CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL);
            }
        }
        return ($filas_valores_sensores);
    }


    // Añade los ids y nombres de sensores agrupados por clase (a los parámetros)
    function anyade_ids_nombres_sensores_agrupados_clase(&$parametros)
    {
        $clases_sensor = $parametros["clases_sensor"];
        $ids_sensores = $parametros["ids_sensores"];

        $ids_sensores_clases = array();
        $nombres_sensores_clases = array();
        foreach ($clases_sensor as $clase_sensor)
        {
            $ids_sensores_clases[$clase_sensor] = array();
            $nombres_sensores_clases[$clase_sensor] = array();
        }

        foreach ($ids_sensores as $id_sensor)
        {
            $fila_sensor = dame_fila_sensor($id_sensor);
            $clase_sensor = $fila_sensor["clase"];
            $nombre_sensor = $fila_sensor["nombre"];

            array_push($ids_sensores_clases[$clase_sensor], $id_sensor);
            array_push($nombres_sensores_clases[$clase_sensor], $nombre_sensor);
        }
        $parametros["ids_sensores_clases"] = $ids_sensores_clases;
        $parametros["nombres_sensores_clases"] = $nombres_sensores_clases;
    }


    // Devuelve las filas de valores de sensores (de múltiples clases)
    function dame_filas_valores_sensores_clases($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $clases_sensor = $parametros["clases_sensor"];
        $parametros_extra_campos = $parametros["parametros_extra_campos"];
        $ids_sensores_clases = $parametros["ids_sensores_clases"];
        $nombres_sensores_clases = $parametros["nombres_sensores_clases"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se recorre cada una de las clases
        $numero_filas_valores_sensores_totales = 0;
        $filas_valores_sensores = array();
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            // Clase de sensor y parámetros extra de campo
            $clase_sensor = $clases_sensor[$i];
            $parametros_extra_campo = $parametros_extra_campos[$i];

            // Conversión de fechas
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

            // Se recorren los sensores
            for ($j = 0; $j < count($ids_sensores_clases[$clase_sensor]); $j++)
            {
                // Identificador y nombre de sensor
                $id_sensor = $ids_sensores_clases[$clase_sensor][$j];
                $nombre_sensor = $nombres_sensores_clases[$clase_sensor][$j];

                // Se realiza la consulta de los valores
                $consulta = dame_consulta_valores_sensor(
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas,
                    $parametros_extra_campo);
                $res = $bd_datos->ejecuta_consulta($consulta);
                if ($res == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta."'");
                }

                // Se recorren y guardan las filas
                $filas_valores_sensor = array();
                while ($fila = $res->dame_siguiente_fila())
                {
                    array_push($filas_valores_sensor, $fila);
                }
                $filas_valores_sensores[$nombre_sensor] = $filas_valores_sensor;

                // Número de filas totales
                $numero_filas_valores_sensores_totales += count($filas_valores_sensor);
                if ($numero_filas_valores_sensores_totales > NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL)
                {
                    throw new Exception(
                        "Número máximo de valores superado (refine la búsqueda)",
                        CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL);
                }
            }
        }
        return ($filas_valores_sensores);
    }


    //
    // Funciones de sensor real
    //


    // Devuelve el identificador del dispositivo de un sensor real
    function dame_dispositivo_sensor_real($fila_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera el identificador del axón
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_tipo']);
        $id_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON];

        // Se recupera el identificador del dispositivo del axón
        $consulta_dispositivo = "
            SELECT dispositivo
            FROM axones
            WHERE
                id = '".$bd_red->_($id_axon)."'";
        $res_dispositivo = $bd_red->ejecuta_consulta($consulta_dispositivo);
        if (($res_dispositivo == false) || ($res_dispositivo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_dispositivo."'");
        }
        $fila_dispositivo = $res_dispositivo->dame_siguiente_fila();
        $id_dispositivo = $fila_dispositivo["dispositivo"];

        // Se devuelve el identificador del dispositivo
        return ($id_dispositivo);
    }


    //
    // Funciones de consultas de sensores y grupos
    //


    // Devuelve la condición de consulta del filtro de sensores
    function dame_condicion_consulta_filtro_sensores($filtro)
    {
        $campos = array("sensores.nombre");
        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
        return ($condicion_consulta_filtro_busqueda);
    }


    // Devuelve la condición de consulta del filtro de grupos de sensores
    function dame_condicion_consulta_filtro_grupos_sensores($filtro)
    {
        $campos = array("grupos_sensores.nombre");
        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
        return ($condicion_consulta_filtro_busqueda);
    }


    // Devuelve la condición de consulta del estado de sensores
    function dame_condicion_consulta_estado_sensores($estado)
    {
        $condicion = "";
        if ($estado != ESTADO_SENSOR_TODOS)
        {
            switch ($estado)
            {
                case ESTADO_SENSOR_OK:
                {
                    $condicion .= "
                        AND ((sensores.timeout_envio = '".VALOR_NO."')
                            AND ((sensores.eventos_alarma_activados = '".VALOR_NO."')
                                AND (sensores.eventos_alarma_activados_clase_cuartoshora = '".VALOR_NO."')
                                AND (sensores.eventos_alarma_activados_clase_horas = '".VALOR_NO."'))
                            AND (sensores.ultimo_error_valores_tiempo_real_json = '')
                                AND ((sensores.ultimo_error_valores_horarios_json = '')
                                AND (sensores.ultimo_error_valores_cuartohorarios_json = ''))
                                AND ((sensores.ultimo_error_valores_clase_horarios_json = '')
                                AND (sensores.ultimo_error_valores_clase_cuartohorarios_json = '')))";

                    break;
                }
                case ESTADO_SENSOR_ERROR:
                {
                    $condicion .= "
                        AND ((sensores.timeout_envio = '".VALOR_SI."')
                            OR ((sensores.eventos_alarma_activados = '".VALOR_SI."')
                                OR (sensores.eventos_alarma_activados_clase_cuartoshora = '".VALOR_SI."')
                                OR (sensores.eventos_alarma_activados_clase_horas = '".VALOR_SI."'))
                            OR (sensores.ultimo_error_valores_tiempo_real_json <> '')
                                OR ((sensores.ultimo_error_valores_horarios_json <> '')
                                OR (sensores.ultimo_error_valores_cuartohorarios_json <> ''))
                                OR ((sensores.ultimo_error_valores_clase_horarios_json <> '')
                                OR (sensores.ultimo_error_valores_clase_cuartohorarios_json <> '')))";
                    break;
                }
                case ESTADO_SENSOR_TIMEOUT:
                {
                    $condicion .= "
                        AND (sensores.timeout_envio = '".VALOR_SI."')";
                    break;
                }
                case ESTADO_SENSOR_ALARMA:
                {
                    $condicion .= "
                        AND ((sensores.eventos_alarma_activados = '".VALOR_SI."')
                            OR (sensores.eventos_alarma_activados_clase_cuartoshora = '".VALOR_SI."')
                            OR (sensores.eventos_alarma_activados_clase_horas = '".VALOR_SI."'))";
                    break;
                }
                case ESTADO_SENSOR_ERROR_RECUPERACION_VALORES:
                {
                    $condicion .= "
                        AND (sensores.ultimo_error_valores_tiempo_real_json <> '')";
                    break;
                }
                case ESTADO_SENSOR_ERROR_CALCULO_VALORES:
                {
                    $condicion .= "
                        AND ((sensores.ultimo_error_valores_horarios_json <> '')
                            OR (sensores.ultimo_error_valores_cuartohorarios_json <> ''))";
                    break;
                }
                case ESTADO_SENSOR_ERROR_CALCULO_VALORES_CLASE:
                {
                    $condicion .= "
                        AND ((sensores.ultimo_error_valores_clase_horarios_json <> '')
                            OR (sensores.ultimo_error_valores_clase_cuartohorarios_json <> ''))";
                    break;
                }
                case ESTADO_SENSOR_SIN_VALORES:
                {
                    $condicion .= "
                        AND (sensores.hora_ultimos_valores IS NULL)";
                    break;
                }
                default:
                {
                    break;
                }
            }
        }
        return ($condicion);
    }


    // Devuelve la condición de consulta de sensores del usuario actual
    function dame_condicion_consulta_sensores_usuario_actual($incluir_sensores_grupos)
    {
        if (!isset($GLOBALS['condicion_consulta_sensores_usuario_actual']))
        {
            $condicion_consulta_sensores = "";
            if ($incluir_sensores_grupos == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(false);
                $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores_usuario);
                $condicion_consulta_sensores .= "
                    (sensores.id IN (".$cadena_ids_sensores_consulta."))";
            }
            else
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(false);
                $ids_grupos_sensores_usuario = dame_ids_grupos_sensores_usuario_actual(false);
                $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores_usuario);
                $cadena_ids_grupos_sensores_consulta = dame_cadena_ids_consulta($ids_grupos_sensores_usuario);
                $condicion_consulta_sensores .= "
                    ((sensores.id IN (".$cadena_ids_sensores_consulta.")) OR (sensores.grupo IN (".$cadena_ids_grupos_sensores_consulta.")))";
            }
            $GLOBALS['condicion_consulta_sensores_usuario_actual'] = $condicion_consulta_sensores;
        }
        else
        {
            $condicion_consulta_sensores = $GLOBALS['condicion_consulta_sensores_usuario_actual'];
        }
        return ($condicion_consulta_sensores);
    }


    // Devuelve la condición de consulta de grupos de sensores del usuario actual
    function dame_condicion_consulta_grupos_sensores_usuario_actual($incluir_grupos_sensores)
    {
        if (!isset($GLOBALS['condicion_consulta_grupos_sensores_usuario_actual']))
        {
            $condicion_consulta_grupos_sensores = "";
            $ids_grupos_sensores_usuario = dame_ids_grupos_sensores_usuario_actual($incluir_grupos_sensores);
            $cadena_ids_grupos_sensores_consulta = dame_cadena_ids_consulta($ids_grupos_sensores_usuario);
            $condicion_consulta_grupos_sensores .= "
                (grupos_sensores.id IN (".$cadena_ids_grupos_sensores_consulta."))";
        }
        else
        {
            $condicion_consulta_grupos_sensores = $GLOBALS['condicion_consulta_grupos_sensores_usuario_actual'];
        }
        return ($condicion_consulta_grupos_sensores);
    }


    //
    // Funciones de identificadores de sensores y grupos
    //


    // Devuelve los identificadores de los sensores
    function dame_ids_sensores()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT id
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        $ids_sensores = array();
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            array_push($ids_sensores, $fila_sensor["id"]);
        }
        return ($ids_sensores);
    }


    // Devuelve los identificadores de los grupos de sensores
    function dame_ids_grupos_sensores()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos = "
            SELECT id
            FROM grupos_sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }
        $ids_grupos = array();
        while ($fila_grupo = $res_grupos->dame_siguiente_fila())
        {
            array_push($ids_grupos, $fila_grupo["id"]);
        }
        return ($ids_grupos);
    }


    //
    // Funciones de permisos de sensores y grupos
    //


    // Devuelve si se muestran todos los sensores
    function dame_mostrar_todos_sensores()
    {
        $mostrar_todos_sensores =
            (($_SESSION["id_localizacion"] == ID_DESACTIVADO) &&
            (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI)));
        return ($mostrar_todos_sensores);
    }


    // Devuelve los identificadores de los sensores visibles para el usuario actual
    function dame_ids_sensores_usuario_actual($incluir_sensores_grupos)
    {
        // Se recuperan los identificadores de los sensores correspondientes
        if (!isset($_SESSION["usuario_interno"]) or($_SESSION["usuario_interno"] == USUARIO_INTERNO_API_HTTP ))
        {
            if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
            {
                $ids_sensores = dame_ids_sensores_usuario_actual_sensores($incluir_sensores_grupos);
            }
            else
            {
                $ids_sensores = dame_ids_sensores_usuario_actual_localizaciones($_SESSION["id_localizacion"]);
            }
        }
        else
        {
            // Si es usuario interno, se devuelven todos los sensores visibles por sensores y por localizaciones
            $ids_sensores = dame_todos_ids_sensores_usuario_actual();
        }
        return ($ids_sensores);
    }


    // Devuelve todos los identificadores de sensores del usuario actual
    function dame_todos_ids_sensores_usuario_actual()
    {
        if (!isset($GLOBALS['todos_ids_sensores_usuario_actual']))
        {
            $ids_sensores_sensores = dame_ids_sensores_usuario_actual_sensores(true);
            $ids_sensores_localizaciones = dame_ids_sensores_usuario_actual_localizaciones(ID_TODOS);
            $ids_sensores = array_unique(array_merge($ids_sensores_sensores, $ids_sensores_localizaciones));
            array_push($ids_sensores, ID_NINGUNO);
            $GLOBALS['todos_ids_sensores_usuario_actual'] = $ids_sensores;
        }
        else
        {
            $ids_sensores = $GLOBALS['todos_ids_sensores_usuario_actual'];
        }
        return ($ids_sensores);
    }


    // Devuelve los identificadores de los sensores visibles para el usuario actual (según los permisos del módulo Sensores)
    function dame_ids_sensores_usuario_actual_sensores($incluir_sensores_grupos)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Ids de sensores y grupos de sensores de los parámetros de los módulos
        $ids_sensores = $_SESSION["parametros_modulo_sensores"]["ids_sensores"];
        $ids_grupos_sensores = $_SESSION["parametros_modulo_sensores"]["ids_grupos_sensores"];
        if ($ids_sensores === NULL)
        {
            $ids_sensores = array();
        }
        if ($ids_grupos_sensores === NULL)
        {
            $ids_grupos_sensores = array();
        }

        // Identificadores de sensores
        $mostrar_todos_sensores_usuario_actual_sensores = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI));
        if ($mostrar_todos_sensores_usuario_actual_sensores == true)
        {
            $ids_sensores = dame_ids_sensores();
            return ($ids_sensores);
        }

        // Se actualizan los identificadores de sensores con los sensores de los grupos del usuario
        if ($incluir_sensores_grupos == true)
        {
            $cadena_ids_grupos_sensores_consulta = dame_cadena_ids_consulta($ids_grupos_sensores);
            $consulta_sensores_grupos = "
                SELECT id
                FROM sensores
                WHERE
                    grupo IN (".$cadena_ids_grupos_sensores_consulta.")";
            $res_sensores_grupos = $bd_red->ejecuta_consulta($consulta_sensores_grupos);
            if ($res_sensores_grupos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores_grupos."'");
            }
            while ($fila_sensor_grupo = $res_sensores_grupos->dame_siguiente_fila())
            {
                if (in_array($fila_sensor_grupo["id"], $ids_sensores) == false)
                {
                    array_push($ids_sensores, $fila_sensor_grupo["id"]);
                }
            }
        }

        return ($ids_sensores);
    }


    // Devuelve los identificadores de los sensores visibles para el usuario actual (según los permisos del módulo Localizaciones)
    function dame_ids_sensores_usuario_actual_localizaciones($id_localizacion_actual)
    {
        // Se recuperan los identificadores de las localizaciones visibles por el usuario (según la localización actual)
        $ids_localizaciones = array();
        switch ($id_localizacion_actual)
        {
            case ID_NINGUNO:
            {
                $permiso_todos_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI);
                if ($permiso_todos_sensores == true)
                {
                    array_push($ids_localizaciones, ID_NINGUNO);
                }
                break;
            }
            case ID_TODOS:
            {
                $ids_localizaciones = dame_ids_localizaciones_usuario_actual(true);
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                $ids_sensores = NULL;
                foreach ($ids_localizaciones_seleccionadas as $id_localizacion_seleccionada)
                {
                    $ids_sensores_localizacion_seleccionada = dame_ids_sensores_usuario_actual_localizaciones($id_localizacion_seleccionada);
                    if ($ids_sensores === NULL)
                    {
                        $ids_sensores = $ids_sensores_localizacion_seleccionada;
                    }
                    else
                    {
                        switch ($id_localizacion_actual)
                        {
                            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                            {
                                $ids_sensores = array_intersect($ids_sensores, $ids_sensores_localizacion_seleccionada);
                                break;
                            }
                            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                            {
                                $ids_sensores_con_duplicados = array_merge($ids_sensores, $ids_sensores_localizacion_seleccionada);
                                $ids_sensores = array_unique($ids_sensores_con_duplicados);
                                break;
                            }
                        }
                    }
                }
                return ($ids_sensores);
            }
            default:
            {
                $ids_localizaciones = dame_ids_localizaciones_descendientes(array($id_localizacion_actual));
                array_push($ids_localizaciones, $id_localizacion_actual);
                break;
            }
        }

        // Se recuperan los identificadores de los sensores visibles en las localizaciones seleccionadas
        $ids_sensores = dame_ids_nodos_visibles_localizaciones($ids_localizaciones, TIPO_NODO_SENSOR);
        return ($ids_sensores);
    }


    // Devuelve los identificadores de los grupos de sensores visibles para el usuario actual
    function dame_ids_grupos_sensores_usuario_actual($incluir_grupos_sensores)
    {
        // Se recuperan los identificadores de los grupos de sensores correspondientes
		if (!isset($_SESSION["usuario_interno"]) or($_SESSION["usuario_interno"] == USUARIO_INTERNO_API_HTTP ))
        {
            if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
            {
                $ids_grupos_sensores = dame_ids_grupos_sensores_usuario_actual_sensores($incluir_grupos_sensores);
            }
            else
            {
                $ids_grupos_sensores = dame_ids_grupos_sensores_usuario_actual_localizaciones($_SESSION["id_localizacion"]);
            }
        }
        else
        {
            // Si es usuario interno, se devuelven todos los grupos visibles por sensores y por localizaciones
            $ids_grupos_sensores = dame_todos_ids_grupos_sensores_usuario_actual();
        }
        return ($ids_grupos_sensores);
    }


    // Devuelve todos los identificadores de grupos de sensores del usuario actual
    function dame_todos_ids_grupos_sensores_usuario_actual()
    {
        if (!isset($GLOBALS['todos_ids_grupos_sensores_usuario_actual']))
        {
            $ids_grupos_sensores_sensores = dame_ids_grupos_sensores_usuario_actual_sensores(true);
            $ids_grupos_sensores_localizaciones = dame_ids_grupos_sensores_usuario_actual_localizaciones(ID_TODOS);
            $ids_grupos_sensores = array_unique(array_merge($ids_grupos_sensores_sensores, $ids_grupos_sensores_localizaciones));
            $GLOBALS['todos_ids_grupos_sensores_usuario_actual'] = $ids_grupos_sensores;
        }
        else
        {
            $ids_grupos_sensores = $GLOBALS['todos_ids_grupos_sensores_usuario_actual'];
        }
        return ($ids_grupos_sensores);
    }


    // Devuelve los identificadores de los grupos de sensores visibles para el usuario actual (según los permisos del módulo Sensores)
    function dame_ids_grupos_sensores_usuario_actual_sensores($incluir_grupos_sensores)
    {
        $ids_sensores = $_SESSION["parametros_modulo_sensores"]["ids_sensores"];
        $ids_grupos_sensores = $_SESSION["parametros_modulo_sensores"]["ids_grupos_sensores"];

        // Identificadores de grupos de sensores
        $mostrar_todos_sensores_usuario_actual_sensores = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI));
        if ($mostrar_todos_sensores_usuario_actual_sensores == true)
        {
            $ids_grupos_sensores = dame_ids_grupos_sensores();
            return ($ids_grupos_sensores);
        }

        // Se actualizan los identificadores de grupos de sensores con los grupos a los que pertenecen los sensores del usuario
        if ($incluir_grupos_sensores == true)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores);
            $consulta_grupos_sensores = "
                SELECT
                    grupos_sensores.id AS id
                FROM
                    sensores,
                    grupos_sensores
                WHERE
                    (sensores.id IN (".$cadena_ids_sensores_consulta."))
                    AND (sensores.grupo = grupos_sensores.id)";
            $res_grupos_sensores = $bd_red->ejecuta_consulta($consulta_grupos_sensores);
            if ($res_grupos_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_grupos_sensores."'");
            }
            while ($fila_grupo_sensor = $res_grupos_sensores->dame_siguiente_fila())
            {
                if (in_array($fila_grupo_sensor["id"], $ids_grupos_sensores) == false)
                {
                    array_push($ids_grupos_sensores, $fila_grupo_sensor["id"]);
                }
            }
        }

        return ($ids_grupos_sensores);
    }


    // Devuelve los identificadores de los grupos de sensores visibles para el usuario actual (según los permisos del módulo Localizaciones)
    function dame_ids_grupos_sensores_usuario_actual_localizaciones($id_localizacion_actual)
    {
        // Se recuperan los identificadores de las localizaciones visibles por el usuario (según la localización actual)
        $ids_localizaciones = array();
        switch ($id_localizacion_actual)
        {
            case ID_NINGUNO:
            {
                $permiso_todos_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI);
                if ($permiso_todos_sensores == true)
                {
                    array_push($ids_localizaciones, ID_NINGUNO);
                }
                break;
            }
            case ID_TODOS:
            {
                $ids_localizaciones = dame_ids_localizaciones_usuario_actual(true);
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                $ids_grupos_sensores = NULL;
                foreach ($ids_localizaciones_seleccionadas as $id_localizacion_seleccionada)
                {
                    $ids_grupos_sensores_localizacion_seleccionada = dame_ids_grupos_sensores_usuario_actual_localizaciones($id_localizacion_seleccionada);
                    if ($ids_grupos_sensores === NULL)
                    {
                        $ids_grupos_sensores = $ids_grupos_sensores_localizacion_seleccionada;
                    }
                    else
                    {
                        switch ($id_localizacion_actual)
                        {
                            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                            {
                                $ids_grupos_sensores = array_intersect($ids_grupos_sensores, $ids_grupos_sensores_localizacion_seleccionada);
                                break;
                            }
                            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                            {
                                $ids_grupos_sensores_con_duplicados = array_merge($ids_grupos_sensores, $ids_grupos_sensores_localizacion_seleccionada);
                                $ids_grupos_sensores = array_unique($ids_grupos_sensores_con_duplicados);
                                break;
                            }
                        }
                    }
                }
                return ($ids_grupos_sensores);
            }
            default:
            {
                $ids_localizaciones = dame_ids_localizaciones_descendientes(array($id_localizacion_actual));
                array_push($ids_localizaciones, $id_localizacion_actual);
                break;
            }
        }

        // Se recuperan los identificadores de los grupos de sensores
        $ids_grupos_sensores = dame_ids_grupos_nodos_visibles_localizaciones($ids_localizaciones, TIPO_NODO_SENSOR);
        return ($ids_grupos_sensores);
    }


    // Devuelve todos los nombres de sensores del usuario actual
    function dame_todos_nombres_sensores_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $ids_todos_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        $cadena_ids_todos_sensores_usuario_actual = dame_cadena_ids_consulta($ids_todos_sensores_usuario_actual);
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                id IN (".$bd_red->_($cadena_ids_todos_sensores_usuario_actual).")";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $nombres_sensores = array();
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            if (in_array($fila_sensor["nombre"], $nombres_sensores) == false)
            {
                array_push($nombres_sensores, $fila_sensor["nombre"]);
            }
        }
        return ($nombres_sensores);
    }


    //
    // Funciones de tipos y clases de sensor
    //


    // Devuelve los tipos de sensor visibles para el usuario actual
    function dame_tipos_sensor_usuario_actual()
    {
        // Si es usuario superadministrador se devuelven todos los tipos
        if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            return (NodoSensor::dame_tipos_sensor());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        $tipos_sensor_usuario = array();

        // Tipos de los sensores
        $consulta_tipos_sensores = "
            SELECT
                DISTINCT(tipo)
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $consulta_tipos_sensores .= "
                AND ".dame_condicion_consulta_sensores_usuario_actual(true);
        }
        $res_tipos_sensores = $bd_red->ejecuta_consulta($consulta_tipos_sensores);
        if ($res_tipos_sensores == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_tipos_sensores."'");
		}
		while ($fila_tipo_sensor = $res_tipos_sensores->dame_siguiente_fila())
		{
            $tipo_sensor = $fila_tipo_sensor["tipo"];
            array_push($tipos_sensor_usuario, $tipo_sensor);
        }

        // Se ordenan los tipos de sensor
        $tipos_sensor_usuario_ordenados = array();
        $tipos_sensor = NodoSensor::dame_tipos_sensor();
        foreach ($tipos_sensor as $tipo_sensor)
        {
            if (in_array($tipo_sensor, $tipos_sensor_usuario) == true)
            {
                array_push($tipos_sensor_usuario_ordenados, $tipo_sensor);
            }
        }
        return ($tipos_sensor_usuario_ordenados);
    }


    // Devuelve las clases de sensor visibles para el usuario actual
    function dame_clases_sensor_usuario_actual($incluir_clases_grupos)
    {
        // Si es usuario superadministrador se devuelven todas las clases
        if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            return (NodoSensor::dame_clases_sensor());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        $clases_sensor_usuario = array();

        // Clases de sensor
        $consulta_clases_sensor = "
            SELECT
                DISTINCT(clase)
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $consulta_clases_sensor .= "
                AND ".dame_condicion_consulta_sensores_usuario_actual(true);
        }
        $res_clases_sensor = $bd_red->ejecuta_consulta($consulta_clases_sensor);
        if ($res_clases_sensor == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_clases_sensor."'");
		}
		while ($fila_clase_sensor = $res_clases_sensor->dame_siguiente_fila())
		{
            $clase_sensor = $fila_clase_sensor["clase"];
            array_push($clases_sensor_usuario, $clase_sensor);
        }

        // Clases de los grupos de sensores
        if ($incluir_clases_grupos == true)
        {
            $consulta_clases_grupos = "
                SELECT
                    DISTINCT(clase)
                FROM grupos_sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($mostrar_todos_sensores == false)
            {
                $consulta_clases_grupos .= "
                    AND ".dame_condicion_consulta_grupos_sensores_usuario_actual(true);
            }
            $res_clases_grupos = $bd_red->ejecuta_consulta($consulta_clases_grupos);
            if ($res_clases_grupos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_clases_grupos."'");
            }
            while ($fila_clase_grupo = $res_clases_grupos->dame_siguiente_fila())
            {
                $clase_grupo = $fila_clase_grupo["clase"];
                if (in_array($clase_grupo, $clases_sensor_usuario) == false)
                {
                    array_push($clases_sensor_usuario, $clase_grupo);
                }
            }
        }

        // Se ordenan las clases de sensor
        $clases_sensor_usuario_ordenadas = array();
        $clases_sensor = NodoSensor::dame_clases_sensor();
        foreach ($clases_sensor as $clase_sensor)
        {
            if (in_array($clase_sensor, $clases_sensor_usuario) == true)
            {
                array_push($clases_sensor_usuario_ordenadas, $clase_sensor);
            }
        }
        return ($clases_sensor_usuario_ordenadas);
    }


    //
    // Funciones de comprobaciones de configuraciones de sensores
    //


    // Devuelve un mensaje de aviso de comprobación de ubicación de sensor real
    function dame_aviso_comprobacion_ubicacion_sensor_real($id_sensor, $parametros_tipo)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $id_axon = $parametros_tipo[0];
        $clase_interfaz = $parametros_tipo[1];
        $ubicacion_interfaz = $parametros_tipo[2];

        $aviso = "";
        switch ($clase_interfaz)
        {
            case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
            case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
            {
                $consulta_sensores = "
                    SELECT
                        nombre,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS ubicacion_interfaz
                    FROM sensores
                    WHERE
                        (id <> '".$bd_red->_($id_sensor)."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_interfaz)."')";
                $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                if ($res_sensores == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_sensores."'");
                }

                while ($fila_sensor = $res_sensores->dame_siguiente_fila())
                {
                    $ubicacion_interfaz_bucle = $fila_sensor["ubicacion_interfaz"];
                    if ($ubicacion_interfaz_bucle != $ubicacion_interfaz)
                    {
                        $aviso = $idiomas->_("existe un interfaz de sensor de la misma clase con una ubicación diferente en el mismo axón").": ".$fila_sensor["nombre"];
                        break;
                    }
                }
                break;
            }
        }

        // Si no hay aviso
        if ($aviso == "")
        {
            switch ($clase_interfaz)
            {
                case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
                {
                    $clase_interfaz_actuador = CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE;
                    $consulta_actuadores = "
                        SELECT
                            nombre,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS ubicacion_interfaz
                        FROM actuadores
                        WHERE
                            (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_interfaz_actuador)."')";
                    $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
                    if ($res_actuadores == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
                    }

                    while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
                    {
                        $ubicacion_interfaz_bucle = $fila_actuador["ubicacion_interfaz"];
                        if ($ubicacion_interfaz_bucle != $ubicacion_interfaz)
                        {
                            $aviso = $idiomas->_("existe un interfaz de actuador de la misma clase con una ubicación diferente en el mismo axón").": ".$fila_actuador["nombre"];
                            break;
                        }
                    }
                }
            }
        }

        return ($aviso);
    }


    //
    // Funciones de listas
    //


    // Crea una lista desplegable para la selección de un sensor de una clase
    function dame_control_lista_sensores(
        $id_controles,
        $clase_sensor,
        $mostrar_etiquetas,
        $etiqueta,
        $opciones_extra)
    {
        $control_lista_sensores = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_sensores .= "<div id='etiqueta_sensor_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_sensores .= "
            <select id='id_sensor_".$id_controles."'";
        if ($clase_sensor == CLASE_NINGUNA)
        {
            $control_lista_sensores .= " disabled=true";
        }
        $control_lista_sensores .= "
                class='chosen-select' hidden>";
        $control_lista_sensores .= dame_lista_sensores($clase_sensor, array(), $opciones_extra);
        $control_lista_sensores .= "
            </select>";

        return ($control_lista_sensores);
    }


    // Crea una lista desplegable para la selección de un sensor hijo de un sensor
    function dame_control_lista_sensores_hijos(
        $id_controles,
        $clase_sensor,
        $id_sensor_padre,
        $mostrar_etiquetas,
        $etiqueta,
        $opciones_extra)
    {
        $control_lista_sensores_hijos = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_sensores_hijos .= "<div id='etiqueta_sensor_hijo_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_sensores_hijos .= "
            <select id='id_sensor_hijo_".$id_controles."'";
        if ($id_sensor_padre == ID_NINGUNO)
        {
            $control_lista_sensores_hijos .= " disabled=true";
        }
        $control_lista_sensores_hijos .= "
                class='chosen-select' hidden>";
        $control_lista_sensores_hijos .= dame_lista_sensores_hijos(
            $clase_sensor,
            $id_sensor_padre,
            array(),
            $opciones_extra);
        $control_lista_sensores_hijos .= "
            </select>";

        return ($control_lista_sensores_hijos);
    }


    // Crea una lista doble de sensores para selección múltiple de sensores de una clase
    function dame_control_lista_doble_sensores(
        $id_controles,
        $clase_sensor,
        $max_sensores,
        $etiqueta)
    {
        // Nota: En las listas dobles es necesario el atributo 'name'
        $control_lista_doble_sensores = "<span>".$etiqueta.": "."</span><br/>";
        $control_lista_doble_sensores .= "<div id='select_sensores_no_visible_".$id_controles."' hidden></div>";
        $control_lista_doble_sensores .= "
            <select id='ids_sensores_".$id_controles."'
                name='ids_sensores_".$id_controles."'
                max_selected='".$max_sensores."' multiple='multiple'
                class='select100' hidden>";
        $control_lista_doble_sensores .= dame_lista_sensores($clase_sensor, array(), OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $control_lista_doble_sensores .= "
            </select>";

        return ($control_lista_doble_sensores);
    }


    // Devuelve la lista de sensores
    function dame_lista_sensores($clase_sensor, $ids_sensores_seleccionados, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_sensores .= "
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_sensores = true;
        }
        else
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        }
        if ($mostrar_todos_sensores == false)
        {
            $consulta_sensores .= "
                AND (".dame_condicion_consulta_sensores_usuario_actual(true);

            // Nota: En algunas listas el sensor seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese sensor en la lista)
            // (p.e. un sensor en un widget de una localización diferente a la actual)
            $cadena_ids_sensores_seleccionados_consulta = dame_cadena_ids_consulta($ids_sensores_seleccionados);
            $consulta_sensores .= " OR (id IN (".$cadena_ids_sensores_seleccionados_consulta.")))";
        }
        $consulta_sensores .= "
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $lista_sensores = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_sensor['id']."'";
			if (in_array($fila_sensor['id'], $ids_sensores_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores hijos
    function dame_lista_sensores_hijos(
        $clase_sensor,
        $id_sensor_padre,
        $ids_sensores_hijos_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $consulta_sensores_hijos = dame_consulta_sensores_hijos_sensor_compra_energia($id_sensor_padre);
                break;
            }
            default:
            {
                $consulta_sensores_hijos = "
                    SELECT
                        hijos_sensores.sensor_hijo AS id,
                        sensores.nombre
                    FROM
                        hijos_sensores,
                        sensores
                    WHERE
                        (hijos_sensores.red = '".$_SESSION["id_red"]."')
                        AND (hijos_sensores.sensor_padre = '".$bd_red->_($id_sensor_padre)."')
                        AND (hijos_sensores.sensor_hijo = sensores.id)
                    ORDER BY sensores.nombre ASC";
                break;
            }
        }
        $res_sensores_hijos = $bd_red->ejecuta_consulta($consulta_sensores_hijos);
        if ($res_sensores_hijos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores_hijos."'");
        }

        $lista_sensores_hijos = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores_hijos .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores_hijos .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_sensor_hijo = $res_sensores_hijos->dame_siguiente_fila())
        {
            $lista_sensores_hijos .= "<option value='".$fila_sensor_hijo['id']."'";
			if (in_array($fila_sensor_hijo['id'], $ids_sensores_hijos_seleccionados) == true)
			{
				$lista_sensores_hijos .= " selected";
			}
			$lista_sensores_hijos .= ">".htmlspecialchars($fila_sensor_hijo['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores_hijos);
    }


    // Devuelve la lista de sensores (de múltiples clases)
    function dame_lista_sensores_clases($clases_sensor, $ids_sensores_seleccionados, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $clases_sensor = array_unique($clases_sensor);
        $consulta_sensores .= "
                AND (";
        $numero_clase_sensor = 1;
        foreach ($clases_sensor as $clase_sensor)
        {
            if ($numero_clase_sensor > 1)
            {
                $consulta_sensores .= " OR ";
            }
            $consulta_sensores .= "(clase = '".$bd_red->_($clase_sensor)."')";
            $numero_clase_sensor += 1;
        }
        $consulta_sensores .= ")";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_sensores = true;
        }
        else
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        }
        if ($mostrar_todos_sensores == false)
        {
            $consulta_sensores .= "
                AND (".dame_condicion_consulta_sensores_usuario_actual(true);

            // Nota: En algunas listas el sensor seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese sensor en la lista)
            // (p.e. un sensor en un widget de una localización diferente a la actual)
            $cadena_ids_sensores_seleccionados_consulta = dame_cadena_ids_consulta($ids_sensores_seleccionados);
            $consulta_sensores .= "OR (id IN (".$cadena_ids_sensores_seleccionados_consulta.")))";
        }
        $consulta_sensores .= "
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $lista_sensores = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_sensor['id']."'";
			if (in_array($fila_sensor['id'], $ids_sensores_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores externos con las clases de sensor y sensor externo especificadas
    function dame_lista_sensores_externos($clase_sensor, $clase_sensor_externo, $ids_sensores_seleccionados)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_SENSOR_EXTERNO."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_sensores .= "
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        }
        if ($clase_sensor_externo != CLASE_TODAS)
        {
            $consulta_sensores .= "
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_sensor_externo)."')";
        }
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $consulta_sensores .= "
                AND ".dame_condicion_consulta_sensores_usuario_actual(true);
        }
        $consulta_sensores .= "
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $lista_sensores = "";
        $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_sensor['id']."'";
			if (in_array($fila_sensor['id'], $ids_sensores_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores con los identificadores especificados
    function dame_lista_sensores_ids(
        $clase_sensor,
        $ids_sensores,
        $ids_sensores_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_sensores .= "
                AND (clase = '".$clase_sensor."')";
        }
        $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores);
        $consulta_sensores .= "
                AND (id IN (".$cadena_ids_sensores_consulta."))";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_sensores = true;
        }
        else
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        }
        if ($mostrar_todos_sensores == false)
        {
            $consulta_sensores .= "
                AND (".dame_condicion_consulta_sensores_usuario_actual(true);

            // Nota: En algunas listas el sensor seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese sensor en la lista)
            // (p.e. un sensor en un widget de una localización diferente a la actual)
            $cadena_ids_sensores_seleccionados_consulta = dame_cadena_ids_consulta($ids_sensores_seleccionados);
            $consulta_sensores .= " OR (id IN (".$cadena_ids_sensores_seleccionados_consulta.")))";
        }
        $consulta_sensores .= "
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $lista_sensores = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_sensor['id']."'";
			if (in_array($fila_sensor['id'], $ids_sensores_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de grupos de sensores
    function dame_lista_grupos_sensores($clase_sensor, $ids_grupos_seleccionados, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos = "
            SELECT
                id,
                nombre
            FROM grupos_sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_grupos .= "
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_sensores = true;
        }
        else
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        }
        if ($mostrar_todos_sensores == false)
        {
            $consulta_grupos .= "
                AND (".dame_condicion_consulta_grupos_sensores_usuario_actual(false);

            // Nota: En algunas listas el grupo seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese grupo en la lista)
            // (p.e. un grupo en un widget de una localización diferente a la actual)
            $cadena_ids_grupos_seleccionados_consulta = dame_cadena_ids_consulta($ids_grupos_seleccionados);
            $consulta_grupos .= " OR (id IN (".$cadena_ids_grupos_seleccionados_consulta.")))";
        }
        $consulta_grupos .= "
            ORDER BY nombre ASC";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }

        $lista_grupos = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_grupos .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_grupos .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_grupo = $res_grupos->dame_siguiente_fila())
        {
            $lista_grupos .= "<option value='".$fila_grupo['id']."'";
			if (in_array($fila_grupo['id'], $ids_grupos_seleccionados) == true)
			{
				$lista_grupos .= " selected";
			}
			$lista_grupos .= ">".htmlspecialchars($fila_grupo['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos);
    }


    // Crea una lista desplegable para la selección de un tipo de sensor
    function dame_control_lista_tipos_sensor(
        $id_controles,
        $opciones_extra,
        $mostrar_etiqueta,
        $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_tipos = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_tipos .= "<div id='etiqueta_tipo_sensor_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_tipos .= "<select id='tipo_sensor_".$id_controles."' class='filtro-desplegable'>";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
        {
            $control_lista_tipos .= "<option value=".TIPO_TODOS.">".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
        {
            $control_lista_tipos .= "<option value=".TIPO_NINGUNO.">".$idiomas->_("Ninguno")."</option>";
        }
        $tipos_sensor = dame_tipos_sensor_usuario_actual();
        foreach ($tipos_sensor as $tipo_sensor)
        {
            $nombre_tipo_sensor = NodoSensor::dame_descripcion_tipo_sensor($tipo_sensor);
            $control_lista_tipos .= "<option value='".$tipo_sensor."'>".htmlspecialchars($nombre_tipo_sensor, ENT_QUOTES)."</option>";
        }
        $control_lista_tipos .= "
            </select>";

        return ($control_lista_tipos);
    }


    // Crea una lista desplegable para la selección de una clase de sensor
    function dame_control_lista_clases_sensor(
        $id_controles,
        $opciones_extra,
        $incluir_clases_sin_procesado_valores,
        $mostrar_etiqueta,
        $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_clases = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_clases .= "<div id='etiqueta_clase_sensor_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_clases .= "<select id='clase_sensor_".$id_controles."' class='filtro-desplegable'>";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
        }
        $clases_sensor = dame_clases_sensor_usuario_actual(true);
        foreach ($clases_sensor as $clase_sensor)
        {
            if ($incluir_clases_sin_procesado_valores == false)
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                if ($caracteristicas_clase_sensor["procesado_valores"] == false)
                {
                    continue;
                }
            }
            $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $control_lista_clases .= "<option value='".$clase_sensor."'>".htmlspecialchars($nombre_clase_sensor, ENT_QUOTES)."</option>";
        }
        $control_lista_clases .= "
            </select>";

        return ($control_lista_clases);
    }


    // Crea una lista desplegable para la selección de una clase de sensor con campos incrementos
    function dame_control_lista_clases_sensor_campos_incrementos(
        $id_controles,
        $opciones_extra,
        $incluir_clases_sin_procesado_valores,
        $mostrar_etiqueta,
        $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_clases = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_clases .= "<div id='etiqueta_clase_sensor_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_clases .= "<select id='clase_sensor_".$id_controles."' class='filtro-desplegable'>";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
        }
        $clases_sensor = dame_clases_sensor_usuario_actual(true);
        foreach ($clases_sensor as $clase_sensor)
        {
            $campos_incrementos = dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor);
            if (count($campos_incrementos) > 0)
            {
                if ($incluir_clases_sin_procesado_valores == false)
                {
                    $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                    if ($caracteristicas_clase_sensor["procesado_valores"] == false)
                    {
                        continue;
                    }
                }
                $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
                $control_lista_clases .= "<option value='".$clase_sensor."'>".htmlspecialchars($nombre_clase_sensor, ENT_QUOTES)."</option>";
            }
        }
        $control_lista_clases .= "
            </select>";

        return ($control_lista_clases);
    }


    // Crea una lista desplegable para la selección de un grupo de sensores
    function dame_control_lista_grupos_sensores($id_controles, $etiqueta)
    {
        $control_lista_grupos = "";
        $control_lista_grupos .= "<div id='etiqueta_grupo_sensores_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_grupos .= "
            <select id='id_grupo_sensores_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_grupos .= dame_lista_grupos_sensores(CLASE_TODAS, array(), OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO);
        $control_lista_grupos .= "</select>";

        return ($control_lista_grupos);
    }


    // Crea una lista desplegable para la selección de un estado de sensor
    function dame_control_lista_estados_sensor($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_estados .= "<div id='etiqueta_estado_sensor_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_estados .= "<select id='estado_sensor_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_TODOS.">".$idiomas->_("Todos")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_OK.">".$idiomas->_("Ok")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ERROR.">".$idiomas->_("Error")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_TIMEOUT.">".$idiomas->_("Timeout")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ALARMA.">".$idiomas->_("Alarma")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ERROR_RECUPERACION_VALORES.">".$idiomas->_("Error en recuperación de valores")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ERROR_CALCULO_VALORES.">".$idiomas->_("Error en cálculo de valores")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ERROR_CALCULO_VALORES_CLASE.">".$idiomas->_("Error en cálculo de valores de clase")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_SIN_VALORES.">".$idiomas->_("Sin valores")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_OPERACIONES_DATOS_PENDIENTES.">".$idiomas->_("Operaciones de datos pendientes")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_IMPORTACIONES_VALORES_PENDIENTES.">".$idiomas->_("Importaciones pendientes")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_RECALCULOS_VALORES_CLASE_PENDIENTES.">".$idiomas->_("Recálculos de clase pendientes")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_SENSOR_ULTIMOS_VALORES_ANTIGUOS_PROCESADO.">".$idiomas->_("Sensores de procesado pendientes")."</option>";
        $control_lista_estados .= "</select>";

        return ($control_lista_estados);
    }


    // Crea una lista desplegable para la selección de un campo de una clase (incluyendo los campos con parámetros extra)
    function dame_control_lista_campos_clase_sensor_parametros_extra($id_controles, $clase_sensor, $mostrar_etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_campos_clase_sensor = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_campos_clase_sensor .= "<div id='etiqueta_campo_".$id_controles."'>".$idiomas->_("Campo").": "."</div>";
        }
        $control_lista_campos_clase_sensor .= "
            <select id='campo_".$id_controles."'";
        $numero_campos_clase_sensor = count(dame_todos_campos_clase_sensor_parametros_extra($clase_sensor));
        if ($numero_campos_clase_sensor <= 1)
        {
            $control_lista_campos_clase_sensor .= " disabled=true";
        }
        $control_lista_campos_clase_sensor .= "
                class='filtro-desplegable'>";
        $control_lista_campos_clase_sensor .= dame_lista_campos_clase_sensor_parametros_extra($clase_sensor, "");
        $control_lista_campos_clase_sensor .= "
            </select>";
        return ($control_lista_campos_clase_sensor);
    }


    // Crea una lista desplegable para la selección de un campo de una clase con tipo de agrupación de valores (incluyendo los campos con parámetros extra)
    function dame_control_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($id_controles, $clase_sensor, $mostrar_etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_campos_clase_sensor = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_campos_clase_sensor .= "<div id='etiqueta_campo_".$id_controles."'>".$idiomas->_("Campo").": "."</div>";
        }
        $control_lista_campos_clase_sensor .= "
            <select id='campo_".$id_controles."'";
        $numero_campos_clase_sensor = count(dame_todos_campos_clase_sensor_parametros_extra($clase_sensor));
        if ($numero_campos_clase_sensor == 1)
        {
            $control_lista_campos_clase_sensor .= " disabled=true";
        }
        $control_lista_campos_clase_sensor .= "
                class='filtro-desplegable'>";
        $control_lista_campos_clase_sensor .= dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, INTERVALO_VALORES_TIEMPO_REAL, "");
        $control_lista_campos_clase_sensor .= "
            </select>";

        return ($control_lista_campos_clase_sensor);
    }


    // Crea el control para los parametros extra de un campo de una clase de sensor
    function dame_control_parametros_extra_campo_clase_sensor($id_controles, $mostrar_etiquetas)
    {
        $control_parametros_extra = "<div id='control_parametros_extra_campo_".$id_controles."' hidden>";
        if ($mostrar_etiquetas == true)
        {
            $control_parametros_extra .= "<div id='etiqueta_parametros_extra_campo_".$id_controles."'>ND</div>";
        }
        $control_parametros_extra .= "<input type='text' class='texto-parametros-extra-campo' id='parametros_extra_campo_".$id_controles."'>";
        $control_parametros_extra .= "</div>";
        return ($control_parametros_extra);
    }


    // Devuelve la lista de campos incrementos de una clase de sensor (incluyendo los campos con parámetros extra)
    function dame_lista_campos_incrementos_clase_sensor_parametros_extra($clase_sensor, $campo_seleccionado)
    {
        $campos = dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos incrementos de una clase de sensor con tipo de agrupación de valores (incluyendo los campos con parámetros extra)
    function dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores, $campo_seleccionado)
    {
        // Se cambia el valor seleccionado si es necesario
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO_SUMA;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_INCREMENTO_SUMA:
                            case CAMPO_INCREMENTO_MEDIA:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        $campos = dame_todos_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor
    function dame_lista_campos_clase_sensor($clase_sensor, $campo_seleccionado)
    {
        $campos = dame_todos_campos_clase_sensor($clase_sensor);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor con tipo de agrupación de valores
    function dame_lista_campos_clase_sensor_tipo_agrupacion_valores($clase_sensor, $intervalo_valores, $campo_seleccionado)
    {
        // Se cambia el valor seleccionado si es necesario
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_VALOR:
                            {
                                $campo_seleccionado = CAMPO_VALOR_MEDIA;
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO_SUMA;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_VALOR_MEDIA:
                            case CAMPO_VALOR_SUMA:
                            {
                                $campo_seleccionado = CAMPO_VALOR;
                                break;
                            }
                            case CAMPO_INCREMENTO_SUMA:
                            case CAMPO_INCREMENTO_MEDIA:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        $campos = dame_todos_campos_clase_sensor_tipo_agrupacion_valores($clase_sensor, $intervalo_valores);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor (incluyendo los campos con parámetros extra)
    function dame_lista_campos_clase_sensor_parametros_extra($clase_sensor, $campo_seleccionado)
    {
        $campos = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor con tipo de agrupación de valores (incluyendo los campos con parámetros extra)
    function dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores, $campo_seleccionado)
    {
        // Se cambia el valor seleccionado si es necesario
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_VALOR:
                            {
                                $campo_seleccionado = CAMPO_VALOR_MEDIA;
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO_SUMA;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        switch ($campo_seleccionado)
                        {
                            case CAMPO_VALOR_MEDIA:
                            case CAMPO_VALOR_SUMA:
                            {
                                $campo_seleccionado = CAMPO_VALOR;
                                break;
                            }
                            case CAMPO_INCREMENTO_SUMA:
                            case CAMPO_INCREMENTO_MEDIA:
                            {
                                $campo_seleccionado = CAMPO_INCREMENTO;
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        $campos = dame_todos_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor de hijo de sensor de procesado
    function dame_lista_campos_clase_sensor_hijo_sensor_procesado($clase_sensor, $campo_seleccionado, $numero_campo_sensor_padre)
    {
        $campos = dame_todos_campos_clase_sensor($clase_sensor);
        if (count($campos) == 0)
        {
            array_push($campos, CAMPO_NINGUNO);
        }

        if (in_array($campo_seleccionado, $campos) == false)
        {
            if (count($campos) > $numero_campo_sensor_padre)
            {
                $campo_seleccionado = $campos[$numero_campo_sensor_padre];
            }
        }

        $lista_campos = "";
        foreach ($campos as $campo)
        {
            $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
        }
        return ($lista_campos);
    }


    // Devuelve la lista de intervalos de valores de exportación de datos de una clase de sensor
    function dame_lista_intervalos_valores_exportacion_clase_sensor($clase_sensor, $intervalo_seleccionado)
    {
        $lista_intervalo_valores = "";
        $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO), INTERVALO_VALORES_NINGUNO, $intervalo_seleccionado);
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

                $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL), INTERVALO_VALORES_TIEMPO_REAL, $intervalo_seleccionado);
                if ($clase_procesado_valores == true)
                {
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA), INTERVALO_VALORES_CUARTOHORA, $intervalo_seleccionado);
                    }
                    $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA), INTERVALO_VALORES_HORA, $intervalo_seleccionado);
                    $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA), INTERVALO_VALORES_DIA, $intervalo_seleccionado);
                    $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA), INTERVALO_VALORES_SEMANA, $intervalo_seleccionado);
                    $lista_intervalo_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES), INTERVALO_VALORES_MES, $intervalo_seleccionado);
                }
                break;
            }
        }
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para los informes de información y comparación según la clase del sensor y un campo
    function dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo(
        $clase_sensor,
        $campo,
        $intervalo_seleccionado,
        $opciones_extra)
    {
        $intervalos_valores = array();
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO)
        {
            array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
        }
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                if ($opciones_extra != OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO)
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
                }
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                $clase_valores_clase = $caracteristicas_clase_sensor["valores_clase"];

                if ($clase_valores_clase == true)
                {
                    $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                    if (in_array($campo, $campos_clase_sensor) == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                    }
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                    }
                    array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                }
                else
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                    if ($clase_procesado_valores == true)
                    {
                        if ($clase_granularidad_cuartohoraria == true)
                        {
                            array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                        }
                        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                    }
                }
            }
        }
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de comparación con perfil horario
    function dame_lista_intervalos_valores_informe_comparacion_perfil_horario($intervalo_seleccionado)
    {
        $intervalos_valores = array();
        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de comparación de campos diferentes
    function dame_lista_intervalos_valores_informe_comparacion_campos_diferentes($intervalo_seleccionado)
    {
        $intervalos_valores = array();
        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de comparación de análisis comparativo según la clase del sensor
    function dame_lista_intervalos_valores_informe_analisis_comparativo_clase_sensor($clase_sensor, $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

                if ($clase_granularidad_cuartohoraria == true)
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                }
                array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
            }
        }
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de comparación de valores generales según la clase del sensor y un campo
    function dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo($clase_sensor, $campo, $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                $clase_valores_clase = $caracteristicas_clase_sensor["valores_clase"];

                if ($clase_valores_clase == true)
                {
                    $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                    if (in_array($campo, $campos_clase_sensor) == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                    }
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                    }
                    array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                }
                else
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                    if ($clase_procesado_valores == true)
                    {
                        if ($clase_granularidad_cuartohoraria == true)
                        {
                            array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                        }
                        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                    }
                }
            }
        }
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de comparación de incrementos totales según la clase del sensor y un campo
    function dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo($clase_sensor, $campo, $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                $clase_valores_clase = $caracteristicas_clase_sensor["valores_clase"];

                if ($clase_valores_clase == true)
                {
                    $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                    if (in_array($campo, $campos_clase_sensor) == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                    }
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                    }
                    array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                }
                else
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                    if ($clase_procesado_valores == true)
                    {
                        if ($clase_granularidad_cuartohoraria == true)
                        {
                            array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                        }
                        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                    }
                }
            }
        }
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de histograma según la clase del sensor y un campo
    function dame_lista_intervalos_valores_informe_histograma_clase_sensor_campo($clase_sensor, $campo, $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                $clase_valores_clase = $caracteristicas_clase_sensor["valores_clase"];

                if ($clase_valores_clase == true)
                {
                    $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                    if (in_array($campo, $campos_clase_sensor) == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                    }
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                    }
                    array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                }
                else
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                    if ($clase_procesado_valores == true)
                    {
                        if ($clase_granularidad_cuartohoraria == true)
                        {
                            array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                        }
                        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
                    }
                }
            }
        }
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de intervalos de valores para el informe de correlacion
    function dame_lista_intervalos_valores_informe_correlacion($intervalo_seleccionado)
    {
        $intervalos_valores = array();
        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de agregaciones
    function dame_lista_agregaciones($tipo_valores, $tipos_agregacion, $agregacion_seleccionado)
    {
        $agregaciones = array();
        array_push($agregaciones, array(AGREGACION_NINGUNA, dame_descripcion_agregacion(AGREGACION_NINGUNA)));
        switch ($tipo_valores)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                switch ($tipos_agregacion)
                {
                    case TIPOS_AGREGACION_TODOS:
                    {
                        array_push($agregaciones, array(AGREGACION_MEDIA, dame_descripcion_agregacion(AGREGACION_MEDIA)));
                        array_push($agregaciones, array(AGREGACION_MEDIA_CLASES, dame_descripcion_agregacion(AGREGACION_MEDIA_CLASES)));
                        break;
                    }
                    case TIPOS_AGREGACION_SIN_CLASES:
                    {
                        array_push($agregaciones, array(AGREGACION_MEDIA, dame_descripcion_agregacion(AGREGACION_MEDIA)));
                        break;
                    }
                    case TIPOS_AGREGACION_CON_CLASES:
                    {
                        array_push($agregaciones, array(AGREGACION_MEDIA_CLASES, dame_descripcion_agregacion(AGREGACION_MEDIA_CLASES)));
                        break;
                    }
                }
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                switch ($tipos_agregacion)
                {
                    case TIPOS_AGREGACION_TODOS:
                    {
                        array_push($agregaciones, array(AGREGACION_SUMA, dame_descripcion_agregacion(AGREGACION_SUMA)));
                        array_push($agregaciones, array(AGREGACION_MEDIA, dame_descripcion_agregacion(AGREGACION_MEDIA)));
                        array_push($agregaciones, array(AGREGACION_SUMA_CLASES, dame_descripcion_agregacion(AGREGACION_SUMA_CLASES)));
                        array_push($agregaciones, array(AGREGACION_MEDIA_CLASES, dame_descripcion_agregacion(AGREGACION_MEDIA_CLASES)));
                        break;
                    }
                    case TIPOS_AGREGACION_SIN_CLASES:
                    {
                        array_push($agregaciones, array(AGREGACION_SUMA, dame_descripcion_agregacion(AGREGACION_SUMA)));
                        array_push($agregaciones, array(AGREGACION_MEDIA, dame_descripcion_agregacion(AGREGACION_MEDIA)));
                        break;
                    }
                    case TIPOS_AGREGACION_CON_CLASES:
                    {
                        array_push($agregaciones, array(AGREGACION_SUMA_CLASES, dame_descripcion_agregacion(AGREGACION_SUMA_CLASES)));
                        array_push($agregaciones, array(AGREGACION_MEDIA_CLASES, dame_descripcion_agregacion(AGREGACION_MEDIA_CLASES)));
                        break;
                    }
                }
                break;
            }
        }
        $lista_agregaciones = dame_lista_valores($agregaciones, array($agregacion_seleccionado));
        return ($lista_agregaciones);
    }


    // Devuelve la lista de formatos de ficheros de valores
    function dame_lista_formatos_ficheros_valores($formato_seleccionado)
    {
        $lista_formatos_ficheros_valores = "";
        $lista_formatos_ficheros_valores .= dame_opcion_valor_lista_simple_valores_extra(
            dame_descripcion_formato_fichero_valores(FORMATO_FICHERO_VALORES_PERSONALIZADO),
            FORMATO_FICHERO_VALORES_PERSONALIZADO,
            $formato_seleccionado,
            array());
        $lista_formatos_ficheros_valores .= dame_opcion_valor_lista_simple_valores_extra(
            dame_descripcion_formato_fichero_valores(FORMATO_FICHERO_VALORES_WEB_EMIOS),
            FORMATO_FICHERO_VALORES_WEB_EMIOS,
            $formato_seleccionado,
            array(
                "caracter_separador" => ";",
                "numero_filas_cabecera" => 1,
                "columna_fecha" => 1,
                "formato_fecha" => "d-m-Y, H:M:S",
                "hora_columna_independiente" => VALOR_NO
            ));
        $lista_formatos_ficheros_valores .= dame_opcion_valor_lista_simple_valores_extra(
            dame_descripcion_formato_fichero_valores(FORMATO_FICHERO_VALORES_LECTOR_CONTADORES_EMIOS),
            FORMATO_FICHERO_VALORES_LECTOR_CONTADORES_EMIOS,
            $formato_seleccionado,
            array(
                "caracter_separador" => ",",
                "numero_filas_cabecera" => 1,
                "columna_fecha" => 1,
                "formato_fecha" => "d-m-Y H:M:S",
                "hora_columna_independiente" => VALOR_NO,
                "columna_horario_verano" => 9,
                "columnas_valores_".CLASE_SENSOR_ENERGIA_ACTIVA => "2",
                "columnas_valores_".CLASE_SENSOR_ENERGIA_REACTIVA => "4",
                "columnas_valores_".CLASE_SENSOR_CORTES_TENSION => "8,2",
                "tipo_valores_sensor" => TIPO_VALORES_SENSOR_INCREMENTALES,
                "tipo_incrementos" => TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL
            ));
        $lista_formatos_ficheros_valores .= dame_opcion_valor_lista_simple_valores_extra(
            dame_descripcion_formato_fichero_valores(FORMATO_FICHERO_VALORES_DATALOGGER_SATEL),
            FORMATO_FICHERO_VALORES_DATALOGGER_SATEL,
            $formato_seleccionado,
            array(
                "caracter_separador" => ";",
                "numero_filas_cabecera" => 1,
                "columna_fecha" => 1,
                "formato_fecha" => "d/m/Y",
                "hora_columna_independiente" => VALOR_SI,
                "columna_hora" => 2,
                "formato_hora" => "H:M:S",
                "tipo_valores_sensor" => TIPO_VALORES_SENSOR_PUNTUALES
            ));
        $lista_formatos_ficheros_valores .= dame_opcion_valor_lista_simple_valores_extra(
            dame_descripcion_formato_fichero_valores(FORMATO_FICHERO_VALORES_DATADIS),
            FORMATO_FICHERO_VALORES_DATADIS,
            $formato_seleccionado,
            array(
                "caracter_separador" => ";",
                "numero_filas_cabecera" => 1,
                "columna_fecha" => 2,
                "formato_fecha" => "d-m-Y, H:M:S",
                "hora_columna_independiente" => VALOR_NO,
                "columnas_valores_".CLASE_SENSOR_ENERGIA_ACTIVA => "3",
                "tipo_valores_sensor" => TIPO_VALORES_SENSOR_INCREMENTALES,
                "tipo_incrementos" => TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL,
                "datadis" => VALOR_SI
            ));
        return ($lista_formatos_ficheros_valores);
    }


    // Crea una lista desplegable para la selección de clase de sensor, lista para selección de sensor y lista para selección de campo de sensor
    // (incluyendo los campos con parámetros extra)
    function dame_controles_listas_clases_sensor_sensores_campos_parametros_extra(
        $id_controles,
        $incluir_clases_sin_procesado_valores,
        $mostrar_etiquetas)
    {
        $idiomas = new Idiomas();

        $control_lista_clases_sensor = dame_control_lista_clases_sensor(
            $id_controles,
            OPCIONES_EXTRA_LISTA_CLASES_NINGUNA,
            $incluir_clases_sin_procesado_valores,
            $mostrar_etiquetas,
            $idiomas->_("Clase de sensor"));
        $control_lista_sensores = dame_control_lista_sensores(
            $id_controles,
            CLASE_NINGUNA,
            $mostrar_etiquetas,
            $idiomas->_("Sensor"),
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $control_lista_campos_clase_sensor = dame_control_lista_campos_clase_sensor_parametros_extra($id_controles, CLASE_NINGUNA, $mostrar_etiquetas);
        $control_parametros_extra_campo_clase_sensor = dame_control_parametros_extra_campo_clase_sensor($id_controles, $mostrar_etiquetas);

        $controles_listas = array(
            $control_lista_clases_sensor,
            $control_lista_sensores,
            $control_lista_campos_clase_sensor,
            $control_parametros_extra_campo_clase_sensor
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de sensor y lista para selección de campo de sensor de la clase de sensor especificada
    // (incluyendo los campos con parámetros extra)
    function dame_controles_listas_sensores_campos_parametros_extra(
        $id_controles,
        $clase_sensor,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_sensores = dame_control_lista_sensores(
            $id_controles,
            $clase_sensor,
            $mostrar_etiquetas,
            $idiomas->_("Sensor"),
            $opciones_extra);
        $control_lista_campos_clase_sensor = dame_control_lista_campos_clase_sensor_parametros_extra($id_controles, $clase_sensor, $mostrar_etiquetas);
        $control_parametros_extra_campo_clase_sensor = dame_control_parametros_extra_campo_clase_sensor($id_controles, $mostrar_etiquetas);

        $controles_listas = array(
            $control_lista_sensores,
            $control_lista_campos_clase_sensor,
            $control_parametros_extra_campo_clase_sensor
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de sensor y lista para selección de campo de sensor de la clase de sensor especificada con tipo de agrupación de valores
    // (incluyendo los campos con parámetros extra)
    function dame_controles_listas_sensores_campos_tipo_agrupacion_valores_parametros_extra(
        $id_controles,
        $clase_sensor,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_sensores = dame_control_lista_sensores(
            $id_controles,
            $clase_sensor,
            $mostrar_etiquetas,
            $idiomas->_("Sensor"),
            $opciones_extra);
        $control_lista_campos_clase_sensor = dame_control_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($id_controles, $clase_sensor, $mostrar_etiquetas);

        $controles_listas = array(
            $control_lista_sensores,
            $control_lista_campos_clase_sensor,
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de sensor y lista para la selección de sensor hijo
    function dame_controles_listas_sensores_sensores_hijos(
        $id_controles,
        $clase_sensor,
        $mostrar_etiquetas,
        $etiqueta_sensor,
        $etiqueta_sensor_hijo)
    {
        $control_lista_sensores = dame_control_lista_sensores(
            $id_controles,
            $clase_sensor,
            $mostrar_etiquetas,
            $etiqueta_sensor,
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $control_lista_sensores_hijos = dame_control_lista_sensores_hijos(
            $id_controles,
            $clase_sensor,
            ID_NINGUNO,
            $mostrar_etiquetas,
            $etiqueta_sensor_hijo,
            OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

        $controles_listas = array(
            $control_lista_sensores,
            $control_lista_sensores_hijos
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de clase de sensor y lista para selección de campo de sensor de la clase de sensor especificada
    // (incluyendo los campos con parámetros extra)
    function dame_controles_listas_clases_sensor_campos_parametros_extra(
        $id_controles,
        $incluir_clases_sin_procesado_valores,
        $mostrar_etiquetas)
    {
        $idiomas = new Idiomas();

        $control_lista_clases_sensor = dame_control_lista_clases_sensor(
            $id_controles,
            OPCIONES_EXTRA_LISTA_CLASES_NINGUNA,
            $incluir_clases_sin_procesado_valores,
            $mostrar_etiquetas,
            $idiomas->_("Clase de sensor"));
        $control_lista_campos_clase_sensor = dame_control_lista_campos_clase_sensor_parametros_extra($id_controles, CLASE_NINGUNA, $mostrar_etiquetas);
        $control_parametros_extra_campo_clase_sensor = dame_control_parametros_extra_campo_clase_sensor($id_controles, $mostrar_etiquetas);

        $controles_listas = array(
            $control_lista_clases_sensor,
            $control_lista_campos_clase_sensor,
            $control_parametros_extra_campo_clase_sensor
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de clase de sensor y lista para selección de campo incremento de sensor de la clase de sensor especificada (incluyendo los campos con parámetros extra)
    function dame_controles_listas_clases_sensor_campos_incrementos_parametros_extra(
        $id_controles,
        $incluir_clases_sin_procesado_valores,
        $mostrar_etiquetas)
    {
        $idiomas = new Idiomas();

        $control_lista_clases_sensor = dame_control_lista_clases_sensor_campos_incrementos(
            $id_controles,
            OPCIONES_EXTRA_LISTA_CLASES_NINGUNA,
            $incluir_clases_sin_procesado_valores,
            $mostrar_etiquetas,
            $idiomas->_("Clase de sensor"));
        $control_lista_campos_clase_sensor = dame_control_lista_campos_clase_sensor_parametros_extra($id_controles, CLASE_NINGUNA, $mostrar_etiquetas);
        $control_parametros_extra_campo_clase_sensor = dame_control_parametros_extra_campo_clase_sensor($id_controles, $mostrar_etiquetas);

        $controles_listas = array(
            $control_lista_clases_sensor,
            $control_lista_campos_clase_sensor,
            $control_parametros_extra_campo_clase_sensor
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección de un intervalo de valores de sensores para los informes de información
    function dame_control_lista_intervalos_valores_informacion(
        $id_controles,
        $clase_sensor,
        $etiqueta,
        $opciones_extra)
    {
        // Campo e intervalo de valores por defecto
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                $campo_defecto = CAMPO_NINGUNO;
                $intervalo_valores_defecto = INTERVALO_VALORES_NINGUNO;
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $campo_defecto = CAMPO_INCREMENTO;
                $intervalo_valores_defecto = INTERVALO_VALORES_CUARTOHORA;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA:
            {
                $campo_defecto = CAMPO_INCREMENTO;
                $intervalo_valores_defecto = INTERVALO_VALORES_HORA;
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $intervalo_valores_defecto = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $campo_defecto = CAMPO_CONSUMO_ESTIMADO;
                $intervalo_valores_defecto = INTERVALO_VALORES_HORA;
                break;
            }
            default:
            {
                $campo_defecto = CAMPO_NINGUNO;
                $intervalo_valores_defecto = INTERVALO_VALORES_HORA;
                break;
            }
        }

        $control_lista_intervalos = "";
        $control_lista_intervalos .= "<div id='etiqueta_intervalo_valores_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_intervalos .= "<select id='intervalo_valores_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_intervalos .= dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo(
            $clase_sensor,
            $campo_defecto,
            $intervalo_valores_defecto,
            $opciones_extra);
        $control_lista_intervalos .= "</select>";
        return ($control_lista_intervalos);
    }


    // Crea una lista desplegable para la selección de la agregación
    function dame_control_lista_agregaciones($id_controles, $tipo_valores, $tipos_agregacion)
    {
        $idiomas = new Idiomas();

        $lista_agregaciones = dame_lista_agregaciones($tipo_valores, $tipos_agregacion, AGREGACION_NINGUNA);
        $control_lista_agregaciones = dame_control_lista(
            $id_controles,
            "agregacion",
            $idiomas->_("Agregación"),
            $lista_agregaciones,
            "filtro-desplegable");
        return ($control_lista_agregaciones);
    }


    //
    // Funciones de obtención de información de sensores
    //


    // Devuelve la fila del sensor
    function dame_fila_sensor($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensor = "
            SELECT *
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
        if (($res_sensor == false) || ($res_sensor->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor."'");
        }
        $fila_sensor = $res_sensor->dame_siguiente_fila();
        return ($fila_sensor);
    }


    // Devuelve la fila de un sensor a partir de la red y el nombre del sensor
    function dame_fila_sensor_nombre($id_red, $nombre_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensor = "
            SELECT *
            FROM sensores
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (nombre = '".$bd_red->_($nombre_sensor)."')";
        $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
        if ($res_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensor."'");
        }
        if ($res_sensor->dame_numero_filas() == 0)
        {
            $fila_sensor = NULL;
        }
        else
        {
            $fila_sensor = $res_sensor->dame_siguiente_fila();
        }
        return ($fila_sensor);
    }


    // Devuelve el nombre de un sensor
    function dame_nombre_sensor($id_sensor)
    {
        $ids_sensores = array($id_sensor);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $nombre_sensor = $nombres_sensores[0];
        return ($nombre_sensor);
    }


    // Devuelve los nombres de los sensores
    function dame_nombres_sensores($ids_sensores)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_sensores = array();
        foreach ($ids_sensores AS $id_sensor)
        {
            switch ($id_sensor)
            {
                case ID_NINGUNO:
                {
                    $nombre_sensor = $idiomas->_("Ninguno");
                    break;
                }
                case ID_TODOS:
                {
                    $nombre_sensor = $idiomas->_("Todos");
                    break;
                }
                default:
                {
                    $consulta_sensor = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            id = '".$bd_red->_($id_sensor)."'";
                    $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
                    if ($res_sensor == false)
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor."'");
                    }
                    if($res_sensor->dame_numero_filas() == 0)
                    {
                        $nombre_sensor = "Sensor desconocido";
                    }
                    else
                    {
                        $fila_sensor = $res_sensor->dame_siguiente_fila();
                        $nombre_sensor = $fila_sensor["nombre"];
                    }
                    break;
                }
            }
            array_push($nombres_sensores, $nombre_sensor);
        }
        return ($nombres_sensores);
    }


    // Devuelve la fila del grupo de sensores
    function dame_fila_grupo_sensores($id_grupo_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupo_sensores = "
            SELECT *
            FROM grupos_sensores
            WHERE
                id = '".$bd_red->_($id_grupo_sensores)."'";
        $res_grupo_sensores = $bd_red->ejecuta_consulta($consulta_grupo_sensores);
        if (($res_grupo_sensores == false) || ($res_grupo_sensores->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo_sensores."'");
        }
        $fila_grupo_sensores = $res_grupo_sensores->dame_siguiente_fila();
        return ($fila_grupo_sensores);
    }


    // Devuelve el nombre de un grupo de sensores
    function dame_nombre_grupo_sensores($id_grupo_sensores)
    {
        $ids_grupos_sensores = array($id_grupo_sensores);
        $nombres_grupos_sensores = dame_nombres_grupos_sensores($ids_grupos_sensores);
        $nombre_grupo_sensores = $nombres_grupos_sensores[0];
        return ($nombre_grupo_sensores);
    }


    // Devuelve los nombres de los grupos de sensores
    function dame_nombres_grupos_sensores($ids_grupos)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_grupos = array();
        foreach ($ids_grupos AS $id_grupo)
        {
            switch ($id_grupo)
            {
                case ID_NINGUNO:
                {
                    $nombre_grupo = $idiomas->_("Ninguno");
                    break;
                }
                case ID_TODOS:
                {
                    $nombre_grupo = $idiomas->_("Todos");
                    break;
                }
                default:
                {
                    $consulta_grupo = "
                        SELECT nombre
                        FROM grupos_sensores
                        WHERE
                            id = '".$bd_red->_($id_grupo)."'";
                    $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                    if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                    }
                    $fila_grupo = $res_grupo->dame_siguiente_fila();
                    $nombre_grupo = $fila_grupo["nombre"];
                    break;
                }
            }
            array_push($nombres_grupos, $nombre_grupo);
        }
        return ($nombres_grupos);
    }


    // Devuelve la fila del hijo de sensor
    function dame_fila_hijo_sensor($id_hijo_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_hijo_sensor = "
            SELECT *
            FROM hijos_sensores
            WHERE
                id = '".$bd_red->_($id_hijo_sensor)."'";
        $res_hijo_sensor = $bd_red->ejecuta_consulta($consulta_hijo_sensor);
        if (($res_hijo_sensor == false) || ($res_hijo_sensor->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_hijo_sensor."'");
        }
        $fila_hijo_sensor = $res_hijo_sensor->dame_siguiente_fila();
        return ($fila_hijo_sensor);
    }


    // Devuelve la información de los últimos valores del sensor
    function dame_info_ultimos_valores_sensor($fila_sensor)
    {
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor["hora_ultimos_valores"];
        $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);

        $info_ultimos_valores_sensor = array(
            "fecha_hora_ultimos_valores_utc" => $fecha_hora_ultimos_valores_utc);
        return ($info_ultimos_valores_sensor);
    }


    // Devuelve la información de los últimos incrementos del sensor
    function dame_info_ultimos_incrementos_sensor($fila_sensor)
    {
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor["hora_ultimos_valores"];
        $ultimos_valores = $fila_sensor["ultimos_valores"];
        $fecha_hora_inicio_ultimos_incrementos_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
        $fecha_hora_fin_ultimos_incrementos_utc = clone $fecha_hora_inicio_ultimos_incrementos_utc;
        $ultimos_valores_incrementos = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $ultimos_valores);
        $segundos_ultimos_incrementos = $ultimos_valores_incrementos[2];
        $fecha_hora_fin_ultimos_incrementos_utc->add(new DateInterval('PT'.($segundos_ultimos_incrementos - 1).'S'));

        $info_ultimos_incrementos_sensor = array(
            "fecha_hora_fin_ultimos_incrementos_utc" => $fecha_hora_fin_ultimos_incrementos_utc,
            "segundos_ultimos_incrementos" => $segundos_ultimos_incrementos);
        return ($info_ultimos_incrementos_sensor);
    }


    // Devuelve la fila de la información de los valores pendientes de borrado del sensor
    function dame_fila_informacion_valores_pendientes_borrado_sensor($id_red, $nombre_sensor)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        $consulta_informacion_valores_pendientes_borrado = "
            SELECT *
            FROM informacion_valores_pendientes_borrado
            WHERE
                (red = '".$bd_datos->_($id_red)."')
                AND (sensor = '".$bd_datos->_($nombre_sensor)."')";
        $res_informacion_valores_pendientes_borrado = $bd_datos->ejecuta_consulta($consulta_informacion_valores_pendientes_borrado);
        if ($res_informacion_valores_pendientes_borrado == false)
        {
            throw new Exception("Ha ocurrido un error en la consulta: '".$consulta_informacion_valores_pendientes_borrado."'");
        }
        if ($res_informacion_valores_pendientes_borrado->dame_numero_filas() == 0)
        {
            $fila_informacion_valores_pendientes_borrado = NULL;
        }
        else
        {
            $fila_informacion_valores_pendientes_borrado = $res_informacion_valores_pendientes_borrado->dame_siguiente_fila();
        }
        return ($fila_informacion_valores_pendientes_borrado);
    }


    // Devuelve la fila de la importación de valores de sensor pendiente
    function dame_fila_importacion_valores_sensor_pendiente($id_importacion_pendiente)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_importacion_valores_sensor_pendiente = "
            SELECT *
            FROM importaciones_valores_sensores_pendientes
            WHERE
                id = '".$bd_red->_($id_importacion_pendiente)."'";
        $res_importacion_valores_sensor_pendiente = $bd_red->ejecuta_consulta($consulta_importacion_valores_sensor_pendiente);
        if (($res_importacion_valores_sensor_pendiente == false) || ($res_importacion_valores_sensor_pendiente->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_importacion_valores_sensor_pendiente."'");
        }
        $fila_importacion_valores_sensor_pendiente = $res_importacion_valores_sensor_pendiente->dame_siguiente_fila();
        return ($fila_importacion_valores_sensor_pendiente);
    }


    //
    // Funciones de usuarios internos
    //


    // Devuelve si un sensor es visible para el usuario interno
    function dame_sensor_visible_usuario_interno($id_sensor)
    {
        $_SESSION["id_localizacion"] = ID_DESACTIVADO;
        $ids_sensores_sensores = dame_ids_sensores_usuario_actual_sensores(true);
        if (in_array($id_sensor, $ids_sensores_sensores) == true)
        {
            return (true);
        }

        $_SESSION["id_localizacion"] = ID_TODOS;
        $ids_sensores_localizaciones = dame_ids_sensores_usuario_actual_localizaciones($_SESSION["id_localizacion"]);
        if (in_array($id_sensor, $ids_sensores_localizaciones) == true)
        {
            return (true);
        }

        return (false);
    }


    //
    // Funciones de valores de clase de sensor
    //


    // Devuelve el índice de parámetro de clase de sensor CUPS
    function dame_indice_parametro_clase_sensor_cups($clase)
    {
        $indice_parametro_clase_sensor_cups = NULL;
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_cups = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_cups = INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_cups = INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS;
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor incorrecta: '".$clase."'");
            }
        }
        return ($indice_parametro_clase_sensor_cups);
    }


    // Devuelve el índice de parámetro de clase de sensor de tipo de fichero de validación de facturas
    function dame_indice_parametro_clase_sensor_tipo_fichero_validacion_facturas($clase)
    {
        $indice_parametro_clase_sensor_tipo_fichero_validacion_facturas = NULL;
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_tipo_fichero_validacion_facturas = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS;
                        break;
                    }
                }
                break;
            }
        }
        return ($indice_parametro_clase_sensor_tipo_fichero_validacion_facturas);
    }


    // Devuelve el índice de parámetro de clase de sensor de prefijo de fichero de validación de facturas
    function dame_indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas($clase)
    {
        $indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas = NULL;
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS;
                        break;
                    }
                }
                break;
            }
        }
        return ($indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve si hay que mostrar mapas de calor en la información de un sensor
    function dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA:
            {
                if ($tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)
                {
                    $mostrar_mapa_calor = false;
                }
                else
                {
                    $mostrar_mapa_calor = true;
                }
                break;
            }
            default:
            {
                $mostrar_mapa_calor = false;
            }
        }
        return ($mostrar_mapa_calor);
    }


    // Devuelve los nombres de los eventos
    function dame_nombres_eventos($ids_eventos)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_eventos = array();
        foreach ($ids_eventos AS $id_evento)
        {
            if ($id_evento == ID_NINGUNO)
            {
                $nombre_evento = $idiomas->_("Ninguno");
            }
            else
            {
                $consulta_evento = "
                    SELECT nombre
                    FROM eventos
                    WHERE
                        id = '".$bd_red->_($id_evento)."'";
                $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
                if (($res_evento == false) || ($res_evento->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_evento."'");
                }
                $fila_evento = $res_evento->dame_siguiente_fila();
                $nombre_evento = $fila_evento["nombre"];
            }
            array_push($nombres_eventos, $nombre_evento);
        }
        return ($nombres_eventos);
    }


    // Devuelve la descripción de un intervalo de valores
    function dame_descripcion_intervalo_valores($intervalo_valores)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_NINGUNO:
            {
                $descripcion_intervalo_valores = "Ninguno";
                break;
            }
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $descripcion_intervalo_valores = "Tiempo real (líneas)";
                break;
            }
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $descripcion_intervalo_valores = "Tiempo real (puntos)";
                break;
            }
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                $descripcion_intervalo_valores = "Tiempo real";
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                $descripcion_intervalo_valores = "Cuarto de hora";
                break;
            }
            case INTERVALO_VALORES_HORA:
            {
                $descripcion_intervalo_valores = "Hora";
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $descripcion_intervalo_valores= "Día";
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $descripcion_intervalo_valores = "Semana";
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $descripcion_intervalo_valores = "Mes";
                break;
            }
            default:
            {
                $descripcion_intervalo_valores = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_intervalo_valores));
    }


    // Devuelve la descripción del tipo de mapa de calor
    function dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)
    {
        switch ($tipo_mapa_calor)
        {
            case TIPO_MAPA_CALOR_NINGUNO:
            {
                $descripcion_tipo_mapa_calor = "Ninguno";
                break;
            }
            case TIPO_MAPA_CALOR_DIARIO:
            {
                $descripcion_tipo_mapa_calor = "Diario";
                break;
            }
            case TIPO_MAPA_CALOR_SEMANAL:
            {
                $descripcion_tipo_mapa_calor = "Semanal";
                break;
            }
            default:
            {
                $descripcion_tipo_mapa_calor = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_tipo_mapa_calor));
    }


    // Devuelve la descripción de la granularidad
    function dame_descripcion_granularidad($granularidad)
    {
        switch ($granularidad)
        {
            case GRANULARIDAD_TIEMPO_REAL:
            {
                $descripcion_granularidad = "Tiempo real";
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $descripcion_granularidad = "Cuartohoraria";
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $descripcion_granularidad = "Horaria";
                break;
            }
            default:
            {
                $descripcion_granularidad = "Desconocida";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_granularidad));
    }


    // Devuelve la descripción de una de agregación
    function dame_descripcion_agregacion($agregacion)
    {
        switch ($agregacion)
        {
            case AGREGACION_NINGUNA:
            {
                $descripcion_agregacion = "Ninguna";
                break;
            }
            case AGREGACION_MEDIA:
            {
                $descripcion_agregacion = "Media";
                break;
            }
            case AGREGACION_SUMA:
            {
                $descripcion_agregacion = "Suma";
                break;
            }
            case AGREGACION_MEDIA_CLASES:
            {
                $descripcion_agregacion = "Media (por clase)";
                break;
            }
            case AGREGACION_SUMA_CLASES:
            {
                $descripcion_agregacion = "Suma (por clase)";
                break;
            }
            default:
            {
                $descripcion_agregacion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_agregacion));
    }


    // Devuelve la descripción del formato de fichero de valores
    function dame_descripcion_formato_fichero_valores($formato_fichero_valores)
    {
        switch ($formato_fichero_valores)
        {
            case FORMATO_FICHERO_VALORES_PERSONALIZADO:
            {
                $descripcion_formato = "Personalizado";
                break;
            }
            case FORMATO_FICHERO_VALORES_WEB_EMIOS:
            {
                $descripcion_formato = "Web EMIOS";
                break;
            }
            case FORMATO_FICHERO_VALORES_LECTOR_CONTADORES_EMIOS:
            {
                $descripcion_formato = "Lector de contadores EMIOS";
                break;
            }
            case FORMATO_FICHERO_VALORES_DATALOGGER_SATEL:
            {
                $descripcion_formato = "Datalogger Satel";
                break;
            }
            case FORMATO_FICHERO_VALORES_DATADIS:
            {
                $descripcion_formato = "Datadis";
                break;
            }
            default:
            {
                $descripcion_formato = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_formato));
    }


    // Devuelve la descripción de la función de correlación
    function dame_descripcion_funcion_correlacion($funcion_correlacion)
    {
        switch ($funcion_correlacion)
        {
            case FUNCION_CORRELACION_AUTOMATICA:
            {
                $descripcion_funcion = "Automática";
                break;
            }
            case FUNCION_CORRELACION_LINEAL:
            case FUNCION_CORRELACION_MULTIVARIABLE_LINEAL:
            {
                $descripcion_funcion = "Lineal";
                break;
            }
            case FUNCION_CORRELACION_POLINOMIO_GRADO_2:
            {
                $descripcion_funcion = "Polinómica";
                break;
            }
            case FUNCION_CORRELACION_LOGARITMICA:
            {
                $descripcion_funcion = "Logarítmica";
                break;
            }
            case FUNCION_CORRELACION_RAIZ_CUADRADA:
            {
                $descripcion_funcion = "Raíz cuadrada";
                break;
            }
            default:
            {
                $descripcion_funcion = "Desconocida";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_funcion));
    }


    // Devuelve si un sensor está en la lista de sensores o pertenece a algun grupo de los especificados
    function dame_sensor_sensores_grupos($id_sensor, $ids_sensores, $ids_grupos_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se comprueba:
        // 1. Si el sensor está en la lista de sensores
        // 2. Si el grupo del sensor es alguno de los grupos de sensores
        if (in_array($id_sensor, $ids_sensores) == true)
        {
            return (true);
        }
        if (count($ids_grupos_sensores) == 0)
        {
            return (false);
        }

        $consulta_grupo_sensor = "
            SELECT grupo
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res_grupo_sensor = $bd_red->ejecuta_consulta($consulta_grupo_sensor);
        if (($res_grupo_sensor == false) || ($res_grupo_sensor->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos");
        }
        $fila_grupo_sensor = $res_grupo_sensor->dame_siguiente_fila();
        $id_grupo = $fila_grupo_sensor["grupo"];
        if (in_array($id_grupo, $ids_grupos_sensores) == true)
        {
            return (true);
        }
        else
        {
            return (false);
        }
    }


    //
    // Funciones de acciones de usuario
    //


    // Convierte los ids de parámetros de clase de sensor a los nombres correspondientes
    function sustituye_ids_nombres_parametros_clase_sensor_accion_usuario($clase, &$parametros_clase)
    {
        // Parámetros específicos de clase de sensor
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa_electrica = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas_electricas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];
                        $error_maximo_validacion_facturas_energia = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA];
                        $error_maximo_validacion_facturas_potencia = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA];
                        $error_maximo_validacion_facturas_otros_conceptos_coste_total = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL];
                        $tipo_fichero_validacion_facturas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS];
                        $prefijo_fichero_validacion_facturas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS];

                        $tabla_tarifas_electricas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
                        $nombre_tarifa_electrica = dame_nombre_tarifa($tabla_tarifas_electricas, $id_tarifa_electrica);
                        $tabla_grupos_tarifas_electricas = dame_nombre_tabla_grupos_tarifas(MEDICION_ELECTRICIDAD);
                        $nombre_grupo_tarifas_electricas = dame_nombre_grupo_tarifas($tabla_grupos_tarifas_electricas, $id_grupo_tarifas_electricas);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_electrica);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_electricas);

                        $parametros_clase = array(
                            $nombre_tarifa_electrica,
                            $nombre_grupo_tarifas_electricas,
                            $cups,
                            $error_maximo_validacion_facturas_energia,
                            $error_maximo_validacion_facturas_potencia,
                            $error_maximo_validacion_facturas_otros_conceptos_coste_total,
                            $tipo_fichero_validacion_facturas,
                            $prefijo_fichero_validacion_facturas);
                        break;
                    }
										case PAIS_PORTUGAL:
                    {
                        $id_tarifa_electrica = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas_electricas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

                        $tabla_tarifas_electricas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
                        $nombre_tarifa_electrica = dame_nombre_tarifa($tabla_tarifas_electricas, $id_tarifa_electrica);
                        $tabla_grupos_tarifas_electricas = dame_nombre_tabla_grupos_tarifas(MEDICION_ELECTRICIDAD);
                        $nombre_grupo_tarifas_electricas = dame_nombre_grupo_tarifas($tabla_grupos_tarifas_electricas, $id_grupo_tarifas_electricas);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_electrica);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_electricas);

                        $parametros_clase = array(
                            $nombre_tarifa_electrica,
                            $nombre_grupo_tarifas_electricas,
                            $cups,
                            $error_maximo_validacion_facturas_energia,
                            $error_maximo_validacion_facturas_potencia,
                            $error_maximo_validacion_facturas_otros_conceptos_coste_total,
                            $tipo_fichero_validacion_facturas,
                            $prefijo_fichero_validacion_facturas);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas desconocido: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $id_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
                $nombre_sensor_energia_activa = dame_nombre_sensor($id_sensor_energia_activa);
                sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_energia_activa);

                $parametros_clase = array(
                    $nombre_sensor_energia_activa);
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $id_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
                $nombre_sensor_energia_activa = dame_nombre_sensor($id_sensor_energia_activa);
                sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_energia_activa);

                $parametros_clase = array(
                    $nombre_sensor_energia_activa);
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
                $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
                $nombres_sensores_hijos = dame_nombres_sensores($ids_sensores_hijos);
                $nombre_sensor_asociado = dame_nombre_sensor($id_sensor_asociado);
                sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_sensor_asociado);

                $parametros_clase = array(
                    $nombres_sensores_hijos,
                    $nombre_sensor_asociado);
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa_gas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
                        $id_grupo_tarifas_gas = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];

                        $tabla_tarifas_gas = dame_nombre_tabla_tarifas(MEDICION_GAS);
                        $nombre_tarifa_gas = dame_nombre_tarifa($tabla_tarifas_gas, $id_tarifa_gas);
                        $tabla_grupos_tarifas_gas = dame_nombre_tabla_grupos_tarifas(MEDICION_GAS);
                        $nombre_grupo_tarifas_gas = dame_nombre_grupo_tarifas($tabla_grupos_tarifas_gas, $id_grupo_tarifas_gas);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_gas);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_gas);

                        $parametros_clase = array(
                            $nombre_tarifa_gas,
                            $nombre_grupo_tarifas_gas,
                            $cups);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas desconocido: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa_agua = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
                        $id_grupo_tarifas_agua = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS];

                        $tabla_tarifas_agua = dame_nombre_tabla_tarifas(MEDICION_AGUA);
                        $nombre_tarifa_agua = dame_nombre_tarifa($tabla_tarifas_agua, $id_tarifa_agua);
                        $tabla_grupos_tarifas_agua = dame_nombre_tabla_grupos_tarifas(MEDICION_AGUA);
                        $nombre_grupo_tarifas_agua = dame_nombre_grupo_tarifas($tabla_grupos_tarifas_agua, $id_grupo_tarifas_agua);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_tarifa_agua);
                        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_grupo_tarifas_agua);

                        $parametros_clase = array(
                            $nombre_tarifa_agua,
                            $nombre_grupo_tarifas_agua,
                            $cups);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua desconocido: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
        }
    }


    // Convierte los ids de parámetros de tipo de sensor a los nombres correspondientes
    function sustituye_ids_nombres_parametros_tipo_sensor_accion_usuario($tipo, &$parametros_tipo)
    {
        // Parámetros específicos de tipo de sensor
        switch ($tipo)
        {
            case TIPO_SENSOR_REAL:
            {
                $id_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON];
                $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_REAL_OPCIONES_INTERFAZ];

                $nombre_axon = dame_nombre_axon($id_axon);
                sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_axon);

                $parametros_tipo = array(
                    $nombre_axon,
                    $clase_interfaz,
                    $ubicacion_interfaz,
                    $opciones_interfaz);
                break;
            }
        }
    }


    //
    // Funciones de descripciones de errores (en obtención de valores)
    //


    // Devuelve la descripción del error de valores de fichero CSV
    function dame_descripcion_error_valores_fichero_csv($error)
    {
        switch ($error)
        {
            case ERROR_LECTURA_VALORES_FICHERO_CSV_DELIMITADOR_COLUMNAS_INCORRECTO:
            {
                $descripcion_error = "Carácter de separación de columnas incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_PUNTO_DECIMAL_INCORRECTO:
            {
                $descripcion_error = "Punto decimal incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_FECHA_INCORRECTO:
            {
                $descripcion_error = "Columna de fecha incorrecta";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORA_INCORRECTO:
            {
                $descripcion_error = "Columna de hora incorrecta";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_FORMATO_FECHA_HORA_INCORRECTO:
            {
                $descripcion_error = "Formato de fecha y hora incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_ANYO_FECHA_FUERA_LIMITES:
            {
                $descripcion_error = "Año de fecha fuera de límites";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO_INCORRECTO:
            {
                $descripcion_error = "Columna de horario de verano incorrecta";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_HORARIO_VERANO_INCORRECTO:
            {
                $descripcion_error = "Horario de verano incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_VALORES_INCORRECTO:
            {
                $descripcion_error = "Número de valores incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_VALOR_INCORRECTO:
            {
                $descripcion_error = "Columna de valor incorrecta";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_VALOR_INCORRECTO:
            {
                $descripcion_error = "Valor incorrecto";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_CODIFICACION_INCORRECTA:
            {
                $descripcion_error = "Codificación incorrecta";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_VALORES_INCOMPLETOS:
            {
                $descripcion_error = "Valores incompletos";
                break;
            }
            case ERROR_PROCESADO_VALORES_FICHERO_CSV_INTERVALO_TIEMPO_INCREMENTOS_VALORES_INCORRECTO:
            {
                $descripcion_error = "Intervalo de tiempo entre incrementos de valores incorrecto";
                break;
            }
            case ERROR_PROCESADO_VALORES_FICHERO_CSV_SIN_FILAS_VALORES:
            {
                $descripcion_error = "No hay filas de valores";
                break;
            }
            case ERROR_PROCESADO_VALORES_FICHERO_CSV_HAY_FILAS_VALORES_ERRONEOS:
            {
                $descripcion_error = "Hay filas con valores incorrectos";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Modifica la cadena de parámetros de error de valores de fichero CSV (si es necesario)
    function modifica_cadena_parametros_error_valores_fichero_csv($error, $cadena_parametros_error)
    {
        $idiomas = new Idiomas();

        switch ($error)
        {
            case ERROR_LECTURA_VALORES_FICHERO_CSV_DELIMITADOR_COLUMNAS_INCORRECTO:
            {
                if ($cadena_parametros_error == "*")
                {
                    $cadena_parametros_error = ",";
                }
                $cadena_parametros_error = $idiomas->_("separador").": ".$cadena_parametros_error;
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_FECHA_INCORRECTO:
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORA_INCORRECTO:
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO_INCORRECTO:
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_COLUMNA_VALOR_INCORRECTO:
            {
                $cadena_parametros_error = (int) $cadena_parametros_error + 1;
                $cadena_parametros_error = $idiomas->_("número de columna").": ".$cadena_parametros_error;
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_FORMATO_FECHA_HORA_INCORRECTO:
            case ERROR_LECTURA_VALORES_FICHERO_CSV_ANYO_FECHA_FUERA_LIMITES:
            {
                $cadena_parametros_error = $idiomas->_("fecha y hora de fichero").": '".$cadena_parametros_error."'";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_HORARIO_VERANO_INCORRECTO:
            {
                $cadena_parametros_error = $idiomas->_("valor").": '".$cadena_parametros_error."'";
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_NUMERO_VALORES_INCORRECTO:
            {
                $cadena_parametros_error = $idiomas->_("número de valores").": ".$cadena_parametros_error;
                break;
            }
            case ERROR_LECTURA_VALORES_FICHERO_CSV_VALOR_INCORRECTO:
            {
                $parametros_ultimo_error = explode(SEPARADOR_PARAMETROS_CADENA_ULTIMO_ERROR, $cadena_parametros_error);
                $numero_columna = (int) $parametros_ultimo_error[0] + 1;
                $cadena_parametros_error =
                    $idiomas->_("número de columna").": ".$numero_columna.", ".
                    $idiomas->_("valor").": '".$parametros_ultimo_error[1]."'";
                break;
            }
        }
        return ($cadena_parametros_error);
    }


    // Devuelve la descripción del error de recuperación de valores de HTTP Emios
    function dame_descripcion_error_recuperacion_valores_http_emios($error)
    {
        switch ($error)
        {
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_CODIGO_RESPUESTA_INCORRECTO:
            {
                $descripcion_error = "Código de respuesta incorrecto";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_APERTURA_SOCKET:
            {
                $descripcion_error = "Error en la apertura del socket";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PETICION_HTTP:
            {
                $descripcion_error = "Error en la petición HTTP";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PETICION_HTTP_DEMASIADAS_PETICIONES_API_AEMET:
            {
                $descripcion_error = "Demasiadas peticiones al API de Aemet";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_ERROR_PROCESADO_RESPUESTA:
            {
                $descripcion_error = "Error en el procesado de la respuesta";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_EMIOS_TIPO_INFORMACION_NO_DISPONIBLE:
            {
                $descripcion_error = "Tipo de información no disponible";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Devuelve la descripción del error de recuperación de valores de 'HTTP XML Powerstudio'
    function dame_descripcion_error_recuperacion_valores_http_xml_powerstudio($error)
    {
        switch ($error)
        {
            case ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_APERTURA_SOCKET:
            {
                $descripcion_error = "Error en la apertura del socket";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_PETICION_HTTP:
            {
                $descripcion_error = "Error en la petición HTTP";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_CONEXION_CREDENCIALES_INCORRECTAS:
            {
                $descripcion_error = "Error en el procesado de la respuesta";
                break;
            }
            case ERROR_RECUPERACION_VALORES_HTTP_XML_POWERSTUDIO_ERROR_PROCESADO_RESPUESTA:
            {
                $descripcion_error = "Error de conexión o credenciales incorrectas";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }

    // Devuelve la descripción del error de recuperación de valores de 'APIs'
    function dame_descripcion_error_recuperacion_valores_API($error)
    {
        switch ($error)
        {
            case ERROR_RECUPERACION_VALORES_API_ERROR_APERTURA_SOCKET:
            {
                $descripcion_error = "Error en la apertura del socket";
                break;
            }
            case ERROR_RECUPERACION_VALORES_API_ERROR_PETICION_HTTP:
            {
                $descripcion_error = "Error en la petición HTTP";
                break;
            }
            case ERROR_RECUPERACION_VALORES_API_ERROR_LECTURA_VALORES:
            {
                $descripcion_error = "Error en la lectura de valores";
                break;
            }
            case ERROR_RECUPERACION_VALORES_API_ERROR_CONEXION_CREDENCIALES_INCORRECTAS:
            {
                $descripcion_error = "Error de conexión o credenciales incorrectas";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }



    // Devuelve la descripción del error de valores de Modbus IP
    function dame_descripcion_error_recuperacion_valores_modbus_ip($error)
    {
        switch ($error)
        {
            case ERROR_RECUPERACION_VALORES_MODBUS_IP_ERROR_APERTURA_SOCKET:
            {
                $descripcion_error = "Error en la apertura del socket";
                break;
            }
            case ERROR_RECUPERACION_VALORES_MODBUS_IP_SIN_VALORES_LEIDOS:
            {
                $descripcion_error = "Sin valores leídos";
                break;
            }
            case ERROR_RECUPERACION_VALORES_MODBUS_IP_ERROR_LECTURA_VALORES:
            {
                $descripcion_error = "Error en la lectura de valores";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Devuelve la descripción del error de cálculo de valores de sensor de procesado
    function dame_descripcion_error_calculo_valores_sensor_procesado($error)
    {
        switch ($error)
        {
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_PROCESADO_VALORES:
            {
                $descripcion_error = "Sensor hijo sin procesado de valores";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_GRANULARIDAD_CUARTOHORARIA:
            {
                $descripcion_error = "Sensor hijo sin granularidad cuartohoraria";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_RECALCULOS_PENDIENTES:
            {
                $descripcion_error = "Sensor hijo con recálculos pendientes";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_POSTERIORES_HORA_VALOR_SENSOR_PROCESADO:
            {
                $descripcion_error = "Sensor hijo sin valores posteriores a la hora especificada";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_HORA_CALCULO_VALORES:
            {
                $descripcion_error = "Sensor hijo sin valores en la hora especificada";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_VALORES_NO_CALCULADOS:
            {
                $descripcion_error = "Sensor hijo sin valores calculados";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_CALCULO_FUNCION_PROCESADO_NO_POSIBLE:
            {
                $descripcion_error = "No se puede calcular la función de procesado del sensor hijo";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_ERROR_CALCULO_FUNCION_PROCESADO:
            {
                $descripcion_error = "Error al calcular la función de procesado del sensor hijo";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $descripcion_error = "No hay hora mínima de cálculo de consumo de energía bruto";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $descripcion_error = "La hora mínima de cálculo de consumo de energía bruto es posterior a la primera hora de consumo neto";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $descripcion_error = "No hay hora máxima de cálculo de consumo de energía bruto";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $descripcion_error = "La hora máxima de cálculo de consumo de energía bruto es anterior a la última hora de consumo neto";
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_ERROR_FUNCION_VALORES:
            {
                $descripcion_error = "Error al evaluar la función de valores";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Modifica la cadena de parámetros de error de cálculo de valores de sensor de procesado (si es necesario)
    function modifica_cadena_parametros_error_calculo_valores_sensor_procesado($error, $cadena_parametros_error)
    {
        $idiomas = new Idiomas();

        $zona_horaria = dame_zona_horaria_local();
        switch ($error)
        {
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_POSTERIORES_HORA_VALOR_SENSOR_PROCESADO:
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_VALORES_OBLIGATORIOS_HORA_CALCULO_VALORES:
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_VALORES_NO_CALCULADOS:
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_CALCULO_FUNCION_PROCESADO_NO_POSIBLE:
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_ERROR_CALCULO_FUNCION_PROCESADO:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $nombre_sensor_hijo = $parametros_error_procesado[0];
                $cadena_hora_calculo_valores_base_datos_utc = $parametros_error_procesado[1];
                if ($cadena_hora_calculo_valores_base_datos_utc != "")
                {
                    $cadena_hora_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_hora_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                }
                else
                {
                    $cadena_hora_calculo_valores_local_local = $idiomas->_("Ninguna");
                }
                $cadena_parametros_error = "";
                if ($nombre_sensor_hijo != "")
                {
                    $cadena_parametros_error .= $nombre_sensor_hijo.", ";
                }
                $cadena_parametros_error .= $idiomas->_("hora").": ".$cadena_hora_calculo_valores_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $cadena_hora_minima_calculo_consumo_energia_bruto_base_datos_utc = $parametros_error_procesado[0];
                $cadena_primera_hora_valores_sensores_hijos_base_datos_utc = $parametros_error_procesado[1];
                $cadena_primera_hora_valores_sensores_hijos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_primera_hora_valores_sensores_hijos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_primera_hora_valores_sensores_hijos_local_local = convierte_formato_fecha($cadena_primera_hora_valores_sensores_hijos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("primera hora de consumo neto").": ".$cadena_primera_hora_valores_sensores_hijos_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MINIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $cadena_hora_minima_calculo_consumo_energia_bruto_base_datos_utc = $parametros_error_procesado[0];
                $cadena_primera_hora_valores_sensores_hijos_base_datos_utc = $parametros_error_procesado[1];
                $cadena_hora_minima_calculo_consumo_energia_bruto_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_minima_calculo_consumo_energia_bruto_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_minima_calculo_consumo_energia_bruto_local_local = convierte_formato_fecha($cadena_hora_minima_calculo_consumo_energia_bruto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_primera_hora_valores_sensores_hijos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_primera_hora_valores_sensores_hijos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_primera_hora_valores_sensores_hijos_local_local = convierte_formato_fecha($cadena_primera_hora_valores_sensores_hijos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("hora mínima de cálculo").": ".$cadena_hora_minima_calculo_consumo_energia_bruto_local_local.", ".
                    $idiomas->_("primera hora de consumo neto").": ".$cadena_primera_hora_valores_sensores_hijos_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_SIN_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $cadena_hora_maxima_calculo_consumo_energia_bruto_base_datos_utc = $parametros_error_procesado[0];
                $cadena_ultima_hora_valores_sensores_hijos_base_datos_utc = $parametros_error_procesado[1];
                $cadena_ultima_hora_valores_sensores_hijos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_ultima_hora_valores_sensores_hijos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_ultima_hora_valores_sensores_hijos_local_local = convierte_formato_fecha($cadena_ultima_hora_valores_sensores_hijos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("última hora de consumo neto").": ".$cadena_ultima_hora_valores_sensores_hijos_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_SENSOR_HIJO_HORA_MAXIMA_CALCULO_CONSUMO_ENERGIA_BRUTO:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $cadena_hora_maxima_calculo_consumo_energia_bruto_base_datos_utc = $parametros_error_procesado[0];
                $cadena_ultima_hora_valores_sensores_hijos_base_datos_utc = $parametros_error_procesado[1];
                $cadena_hora_maxima_calculo_consumo_energia_bruto_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_maxima_calculo_consumo_energia_bruto_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_maxima_calculo_consumo_energia_bruto_local_local = convierte_formato_fecha($cadena_hora_maxima_calculo_consumo_energia_bruto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_ultima_hora_valores_sensores_hijos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_ultima_hora_valores_sensores_hijos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_ultima_hora_valores_sensores_hijos_local_local = convierte_formato_fecha($cadena_ultima_hora_valores_sensores_hijos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("hora máxima de cálculo").": ".$cadena_hora_maxima_calculo_consumo_energia_bruto_local_local.", ".
                    $idiomas->_("última hora de consumo neto").": ".$cadena_ultima_hora_valores_sensores_hijos_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_PROCESADO_ERROR_FUNCION_VALORES:
            {
                $parametros_error_procesado = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_PROCESADO, $cadena_parametros_error);
                $error_funcion_variables = $parametros_error_procesado[0];
                $cadena_hora_calculo_valores_base_datos_utc = $parametros_error_procesado[1];
                $descripcion_error_funcion_variables = dame_descripcion_error_funcion_variables($error_funcion_variables);
                $cadena_hora_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $descripcion_error_funcion_variables.", ".$idiomas->_("hora").": ".$cadena_hora_calculo_valores_local_local;
                break;
            }
        }
        return ($cadena_parametros_error);
    }


    // Devuelve la descripción del error de cálculo de valores de clase de sensor
    function dame_descripcion_error_calculo_valores_clase_sensor($error)
    {
        switch ($error)
        {
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_DESCONOCIDO:
            {
                $descripcion_error = "Desconocido";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_VALORES_TIEMPO_REAL:
            {
                $descripcion_error = "Hay huecos entre los valores recibidos del sensor";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_INCREMENTOS_VALORES_TIEMPO_REAL:
            {
                $descripcion_error = "Hay huecos entre los incrementos de valores recibidos del sensor";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_FIJO:
            {
                $descripcion_error = "Error desconocido en cálculo de coste (fijo)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PRECIO_MEDIO_PERIODO_CALCULO_COSTES_PASS_POOL:
            {
                $descripcion_error = "Tramo sin precio medio en el periodo de cálculo de costes (pass-pool)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_FECHA_NO_ENCONTRADA_PERIODOS_CALCULO_COSTES_PASS_POOL:
            {
                $descripcion_error = "Fecha no encontrada en los periodos de cálculo de costes (pass-pool)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PERIODOS_CALCULO_COSTES_PASS_POOL:
            {
                $descripcion_error = "No hay periodos de cálculo de costes (pass-pool)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_POOL:
            {
                $descripcion_error = "Error desconocido en cálculo de coste (pass-pool)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH:
            {
                $descripcion_error = "No hay valores de parámetros de energía eléctrica (pass-through)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_THROUGH:
            {
                $descripcion_error = "Error desconocido en cálculo de coste (pass-through)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_CIERRE:
            {
                $descripcion_error = "No hay valores de parámetros de energía eléctrica (cierre)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_CIERRE:
            {
                $descripcion_error = "Error desconocido en cálculo de coste (cierre)";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ENERGIA_ACTIVA_ASOCIADO:
            {
                $descripcion_error = "No hay valores del sensor de energía activa asociado";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ASOCIADO:
            {
                $descripcion_error = "No hay valores del sensor asociado";
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALOR_DESVIOS_ENERGIA_ELECTRICA:
            {
                $descripcion_error = "No hay valor de desvíos de energía eléctrica";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Modifica la cadena de parámetros de error de cálculo de valores de clase de sensor (si es necesario)
    function modifica_cadena_parametros_error_calculo_valores_clase_sensor($error, $cadena_parametros_error)
    {
        $idiomas = new Idiomas();

        $zona_horaria = dame_zona_horaria_local();
        switch ($error)
        {
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_DESCONOCIDO:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_FIJO:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_FECHA_NO_ENCONTRADA_PERIODOS_CALCULO_COSTES_PASS_POOL:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_FIJO:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_POOL:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_ERROR_DESCONOCIDO_CALCULO_COSTE_CONSUMO_CONTRATO_PASS_THROUGH:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ENERGIA_ACTIVA_ASOCIADO:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_FILA_SENSOR_ASOCIADO:
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_VALOR_DESVIOS_ENERGIA_ELECTRICA:
            {
                $parametros_error_valores_clase = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE, $cadena_parametros_error);
                $cadena_hora_calculo_valores_base_datos_utc = $parametros_error_valores_clase[0];
                $cadena_hora_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("hora").": ".$cadena_hora_calculo_valores_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_VALORES_TIEMPO_REAL:
            {
                $parametros_error_valores_clase = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE, $cadena_parametros_error);
                $granularidad_valores = $parametros_error_valores_clase[0];
                $cadena_hora_ultimos_valores_periodos_base_datos_utc = $parametros_error_valores_clase[1];
                $cadena_hora_primer_valor_sin_calcular_base_datos_utc = $parametros_error_valores_clase[2];
                $horas_retraso_recepcion_valores_huecos = $parametros_error_valores_clase[3];
                $cadena_hora_ultimos_valores_periodos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimos_valores_periodos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_ultimos_valores_periodos_local_local = convierte_formato_fecha($cadena_hora_ultimos_valores_periodos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_primer_valor_sin_calcular_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_primer_valor_sin_calcular_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_primer_valor_sin_calcular_local_local = convierte_formato_fecha($cadena_hora_primer_valor_sin_calcular_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                switch ($granularidad_valores)
                {
                    case GRANULARIDAD_HORARIA:
                    {
                        $cadena_parametros_error = $idiomas->_("hora de últimos valores horarios");
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    {
                        $cadena_parametros_error = $idiomas->_("hora de últimos valores cuartohorarios");
                        break;
                    }
                    default:
                    {
                        throw new Exception("Granularidad de valores incorrecta: '".$granularidad_valores."'");
                    }
                }
                $cadena_parametros_error .= (": ".$cadena_hora_ultimos_valores_periodos_local_local.", ".
                    $idiomas->_("hora de primer valor después del hueco").": ".$cadena_hora_primer_valor_sin_calcular_local_local.", ".
                    $idiomas->_("horas de retraso").": ".$horas_retraso_recepcion_valores_huecos);
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_HUECOS_INCREMENTOS_VALORES_TIEMPO_REAL:
            {
                $parametros_error_valores_clase = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE, $cadena_parametros_error);
                $granularidad_valores = $parametros_error_valores_clase[0];
                $cadena_hora_ultimos_incrementos_valores_periodos_base_datos_utc = $parametros_error_valores_clase[1];
                $cadena_hora_primer_incremento_sin_agrupar_base_datos_utc = $parametros_error_valores_clase[2];
                $horas_retraso_recepcion_incrementos_valores_huecos = $parametros_error_valores_clase[3];
                $cadena_hora_ultimos_incrementos_valores_periodos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimos_incrementos_valores_periodos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_ultimos_incrementos_valores_periodos_local_local = convierte_formato_fecha($cadena_hora_ultimos_incrementos_valores_periodos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_primer_incremento_sin_agrupar_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_primer_incremento_sin_agrupar_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_primer_incremento_sin_agrupar_local_local = convierte_formato_fecha($cadena_hora_primer_incremento_sin_agrupar_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                switch ($granularidad_valores)
                {
                    case GRANULARIDAD_HORARIA:
                    {
                        $cadena_parametros_error = $idiomas->_("hora de últimos incrementos horarios");
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    {
                        $cadena_parametros_error = $idiomas->_("hora de últimos incrementos cuartohorarios");
                        break;
                    }
                    default:
                    {
                        throw new Exception("Granularidad de valores incorrecta: '".$granularidad_valores."'");
                    }
                }
                $cadena_parametros_error .= (": ".$cadena_hora_ultimos_incrementos_valores_periodos_local_local.", ".
                    $idiomas->_("hora de primer incremento después del hueco").": ".$cadena_hora_primer_incremento_sin_agrupar_local_local.", ".
                    $idiomas->_("horas de retraso").": ".$horas_retraso_recepcion_incrementos_valores_huecos);
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PRECIO_MEDIO_PERIODO_CALCULO_COSTES_PASS_POOL:
            {
                $parametros_error_valores_clase = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE, $cadena_parametros_error);
                $tramo = $parametros_error_valores_clase[0];
                $cadena_hora_calculo_valores_base_datos_utc = $parametros_error_valores_clase[1];
                $cadena_hora_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("tramo").": ".$tramo.", ".
                    $idiomas->_("hora").": ".$cadena_hora_calculo_valores_local_local;
                break;
            }
            case ERROR_CALCULO_VALORES_CLASE_SENSOR_SIN_PERIODOS_CALCULO_COSTES_PASS_POOL:
            {
                $parametros_error_valores_clase = explode(SEPARADOR_PARAMETROS_CADENA_ERROR_VALORES_CLASE, $cadena_parametros_error);
                $cadena_hora_inicio_calculo_valores_base_datos_utc = $parametros_error_valores_clase[0];
                $cadena_hora_fin_calculo_valores_base_datos_utc = $parametros_error_valores_clase[1];
                $cadena_hora_inicio_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_inicio_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_inicio_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_fin_calculo_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_calculo_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_fin_calculo_valores_local_local = convierte_formato_fecha($cadena_hora_fin_calculo_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_parametros_error = $idiomas->_("hora de inicio").": ".$cadena_hora_inicio_calculo_valores_local_local.", ".
                    $idiomas->_("hora de fin").": ".$cadena_hora_fin_calculo_valores_local_local;
                break;
            }
        }
        return ($cadena_parametros_error);
    }
?>
