<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/util_consumos_costes_informes_fichero.php');


    //
    // Funciones de informes de consumos y costes (gas - España)
    //


    // Devuelve información de sobrecaudales (excesos de caudal) de un sensor
    function dame_sobrecaudales_sensor_gas_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $cadena_fecha_hora_inicio_local_local = $parametros['fecha_hora_inicio'];
        $cadena_fecha_hora_fin_local_local = $parametros['fecha_hora_fin'];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables
        $tipo_calculo_coste_termino_fijo_sensor = NULL;
        $datos_caudales_sensor = new VectorDatos();
        $max_caudal_sensor = 0;
        $datos_sobrecaudales_sensor = new VectorDatos();
        $caudal_diario_contratado = 0;
        $info_sobrecaudales_sensor = array();

        // Se recupera el identificador de tarifa de gas del sensor en la fecha inicial
        $id_tarifa_sensor_inicial = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
        if ($id_tarifa_sensor_inicial == ID_NINGUNO)
        {
            $msg = $idiomas->_("El sensor no tiene tarifa de gas asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }

        // Se recupera el identificador de tarifa de gas del sensor en la fecha final y si no coincide con la tarifa en la fecha inicial,
        // se devuelve un error (los datos de sobrecaudales pueden no ser correctos)
        $id_tarifa_sensor_final = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_fin_local_local);
        // lo quitamos a peticion del cliente Jose Ramon Ixatu
        /*if ($id_tarifa_sensor_final != $id_tarifa_sensor_inicial)
        {
            $msg = $idiomas->_("El sensor tiene diferentes tarifas de gas asignadas entre las fechas seleccionadas");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }*/

        // Se recuperan los datos de sobrecaudales del sensor
        dame_datos_sobrecaudales_sensor_fecha_gas_Espanya(
            $nombre_sensor,
            $id_tarifa_sensor_inicial,
            $cadena_fecha_hora_inicio_local_utc,
            $cadena_fecha_hora_fin_local_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $milisegundos_desfase_zonas_horarias_cliente_local,
            $tipo_calculo_coste_termino_fijo_sensor,
            $datos_caudales_sensor,
            $max_caudal_sensor,
            $datos_sobrecaudales_sensor,
            $caudal_diario_contratado,
            $info_sobrecaudales_sensor);

        // Si el tipo de cálculo de coste de término fijo es sin excesos, se devuelve un error
        if ($tipo_calculo_coste_termino_fijo_sensor == TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS)
        {
            $msg = $idiomas->_("La tarifa de gas asignada al sensor no tiene excesos de caudal");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }

        // Si no hay datos no se hace nada
        if (count($info_sobrecaudales_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Se crea la tabla de información de excesos de caudal (si hay datos)
        if (count($info_sobrecaudales_sensor) > 0)
        {
            $porcentaje_dias_sobrecaudal = ($info_sobrecaudales_sensor["dias_sobrecaudal"] * 100) / $info_sobrecaudales_sensor["dias_caudal"];
            $cadena_dias_sobrecaudal_tramo = formatea_numero($info_sobrecaudales_sensor["dias_sobrecaudal"], 2)." ".$idiomas->_("días")." (".formatea_numero($porcentaje_dias_sobrecaudal, 2)." "."%".")";

            $cadena_fecha_hora_maximo_sobrecaudal_absoluto_local_utc = convierte_formato_fecha($info_sobrecaudales_sensor["cadena_fecha_hora_maximo_sobrecaudal_absoluto_base_datos_utc"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_maximo_sobrecaudal_absoluto_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_maximo_sobrecaudal_absoluto_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_maximo_sobrecaudal_absoluto_local_local = convierte_formato_fecha($cadena_fecha_hora_maximo_sobrecaudal_absoluto_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_maximo_sobrecaudal_absoluto = formatea_numero($info_sobrecaudales_sensor["max_caudal"], 2)." ".$idiomas->_("kWh")." [".formatea_numero($info_sobrecaudales_sensor["max_sobrecaudal_absoluto"], 2)." ".$idiomas->_("kWh")."]".
                " (".$cadena_fecha_maximo_sobrecaudal_absoluto_local_local.")";
            $cadena_coste_sobrecaudal = formatea_numero($info_sobrecaudales_sensor["coste_sobrecaudal"], 2, false)." ".$unidad_medida_coste;

            // Se añade la fila de la tabla de sobrecaudales
            $fila_tabla_sobrecaudales = array(
                $cadena_dias_sobrecaudal_tramo,
                $cadena_maximo_sobrecaudal_absoluto,
                $cadena_coste_sobrecaudal);

            // Tabla de sobrecaudales
            $params_tabla = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBRECAUDAL_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBRECAUDAL_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_sobrecaudales = new TablaDatos(
                "tabla-sobrecaudal-sensor-excesos-caudal",
                $idiomas->_("Excesos de caudal diario"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera_tabla = array(
                $idiomas->_("Días excedidos"),
                $idiomas->_("Caudal diario máximo"),
                $idiomas->_("Coste")
            );
            $tabla_sobrecaudales->anyade_cabecera("", $cabecera_tabla);

            // Se añade la fila a la tabla
            $tabla_sobrecaudales->anyade_fila("", $fila_tabla_sobrecaudales);
        }

        // Gráficas de caudales y sobrecaudales
        $grafica_caudales_sobrecaudales = new VectorDatos();
        $grafica_caudales_sobrecaudales->anyade_dato($datos_caudales_sensor->dame_datos());
        $grafica_caudales_sobrecaudales->anyade_dato($datos_sobrecaudales_sensor->dame_datos());

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "grafica_caudales_sobrecaudales" => $grafica_caudales_sobrecaudales->dame_datos(),
            "max_caudal" => $max_caudal_sensor,
            "caudal_diario_contratado" => $caudal_diario_contratado,
            "tabla_sobrecaudales" => $tabla_sobrecaudales->dame_tabla());
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Calculo de sobrecaudales (excesos de caudal) de un sensor
    function dame_datos_sobrecaudales_sensor_fecha_gas_Espanya(
        $nombre_sensor,
        $id_tarifa,
        $cadena_fecha_hora_inicio_local_utc,
        $cadena_fecha_hora_fin_local_utc,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $milisegundos_desfase_zonas_horarias_cliente_local,
        &$tipo_calculo_coste_termino_fijo_sensor,
        &$datos_caudales_sensor,
        &$max_caudal_sensor,
        &$datos_sobrecaudales_sensor,
        &$caudal_diario_contratado,
        &$info_sobrecaudales_sensor)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Se recupera el tipo de cálculo de coste de término fijo de la tarifa de gas del sensor
        $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_gas = $fila_tarifa_gas["tipo"];
        $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($tipo_tarifa_gas);
        $tipo_calculo_coste_termino_fijo_sensor = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];

        // Si el tipo de cálculo de coste es sin excesos no se hace nada
        if ($tipo_calculo_coste_termino_fijo_sensor == TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS)
        {
            return;
        }

        // Precio de caudal diario y caudal diario contratado
        $precio_caudal_diario = $fila_tarifa_gas["precio_caudal_diario"];
        $caudal_diario_contratado = $fila_tarifa_gas["caudal_diario"];

        // - Los 'sobrecaudales' son los caudales en los cuales ha habido 'sobrecaudal positiva' (exceso de caudal)
        // - En 'info_sobrecaudal_sensor' se encuentra la siguiente información:
        //   - 1. Días totales de caudal y 'sobrecaudal positivo'
        //   - 2. Hora, caudal y sobrecaudal del sobrecaudal absoluto máximo (positivo o negativo)
        //   - 3. Coste del sobrecaudal

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Consulta de valores del sensor
        $consulta_valores_sensor = "
            SELECT
                MIN(hora) AS fecha_hora,
                SUM(consumo) AS caudal
            FROM ".TABLA_DATOS_GAS.SUFIJO_TABLA_HORAS."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (consumo IS NOT NULL)
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_valores_sensor .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se añaden la agrupación y la ordenación y se ejecuta la consulta
        // (se agrupan por días de 06:00 a 05:59 del día siguiente)
        $consulta_valores_sensor .= "
            GROUP BY
                DATE(CONVERT_TZ(TIMESTAMPADD(HOUR, -6, hora), '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))
            ORDER BY hora ASC";
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
		}

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_DIA, NULL);

        // Variables de información de sobrecaudal
        $datos_caudales_sensor = new VectorDatos();
        $max_caudal_sensor = -INF;
        $datos_sobrecaudales_sensor = new VectorDatos();
        $info_sobrecaudales_sensor = array();
        $dias_caudal = 0;
        $dias_sobrecaudal = 0;

        // Si no hay datos no se hace nada
        $numero_filas_sensor = $res_valores_sensor->dame_numero_filas();
        if ($numero_filas_sensor == 0)
        {
            return;
        }

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_sensor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
		$exceso_caudal = 0;
        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y caudal del sensor
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $caudal_sensor = (float) $fila_valores_sensor['caudal'];

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_sensor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_sensor_utc - $timestamp_fecha_hora_sensor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_caudales_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_sensor_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_sensor_anterior_utc = $timestamp_fecha_hora_sensor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el caudal
            $datos_caudales_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_sensor_utc, $caudal_sensor);
            $dias_caudal += 1;
            if ($caudal_sensor > $caudal_diario_contratado)
            {
                $datos_sobrecaudales_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_sensor_utc, $caudal_sensor);
                $dias_sobrecaudal += 1;
				// Mes del exceso
				$fecha_hora_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, dame_zona_horaria_local());
				$mes = split("-",$fecha_hora_local)[1];
				$multiplicador_mes = unserialize(CONSTANTE_TARIFA_GAS_MULTIPLICADOR_DIARIO_2021)[intval($mes)-1];
				$exceso_caudal += ($caudal_sensor - $caudal_diario_contratado) * $multiplicador_mes;
            }
            if ($caudal_sensor > $max_caudal_sensor)
            {
                $max_caudal_sensor = $caudal_sensor;
                $cadena_fecha_hora_maximo_sobrecaudal_absoluto_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
        }

		if($tipo_calculo_coste_termino_fijo_sensor == TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES)
		{
			// Se calcula el coste del sobrecaudal (puede ser negativo) y se guarda la información de sobrecaudales
        	if ($max_caudal_sensor > (1.05 * $caudal_diario_contratado))
        	{
            	$caudal_diario_facturado = $max_caudal_sensor + 2 * ($max_caudal_sensor - 1.05 * $caudal_diario_contratado);
        	}
        	else
        	{
            	if (($max_caudal_sensor >= (0.85 * $caudal_diario_contratado)) && ($max_caudal_sensor <= (1.05 * $caudal_diario_contratado)))
            	{
                	$caudal_diario_facturado = $max_caudal_sensor;
            	}
            	else
            	{
                	$caudal_diario_facturado = 0.85 * $caudal_diario_contratado;
            	}
        	}

        	$coste_caudal = (($caudal_diario_facturado * $precio_caudal_diario)) * $dias_caudal;
        	$coste_caudal_sin_sobrecaudal = (($caudal_diario_contratado * $precio_caudal_diario)) * $dias_caudal;
		}elseif ($tipo_calculo_coste_termino_fijo_sensor == TIPO_CALCULO_COSTE_TARIFAS_2021) {
			if (($exceso_caudal > 0))
			{
				// Buscamos los meses del informe.



				$coste_caudal = 3 * $exceso_caudal * ($precio_caudal_diario/365) ;
			}
		}

        // Información de sobrecaudal
        $info_sobrecaudales_sensor["dias_caudal"] = $dias_caudal;
        $info_sobrecaudales_sensor["dias_sobrecaudal"] = $dias_sobrecaudal;
        $info_sobrecaudales_sensor["max_caudal"] = $max_caudal_sensor;
        $info_sobrecaudales_sensor["max_sobrecaudal_absoluto"] = $max_caudal_sensor - $caudal_diario_contratado;
        $info_sobrecaudales_sensor["cadena_fecha_hora_maximo_sobrecaudal_absoluto_base_datos_utc"] = $cadena_fecha_hora_maximo_sobrecaudal_absoluto_base_datos_utc;
        $info_sobrecaudales_sensor["coste_sobrecaudal"] = $coste_caudal - $coste_caudal_sin_sobrecaudal;
	}


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_excesos_caudal_gas_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_GRAFICA_CAUDALES_SOBRECAUDALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_TABLA_SOBRECAUDALES);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_excesos_caudal_gas_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_GRAFICA_CAUDALES_SOBRECAUDALES:
            {
                $descripcion = "Gráfica de caudales diarios y excesos de caudal diario";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ESPANYA_TABLA_SOBRECAUDALES:
            {
                $descripcion = "Tabla de excesos de caudal diario";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_excesos_caudal_gas_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-excesos-caudal'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-excesos-caudal' hidden>
                        <div class='grafica100' id='grafica-caudales-sobrecaudales-excesos-caudal'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-sobrecaudales-excesos-caudal'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de excesos de caudal
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-excesos-caudal-excesos-caudal'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-excesos-caudal-excesos-caudal'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-caudales-sobrecaudales-excesos-caudal'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-sobrecaudales-excesos-caudal'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas_Espanya(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-caudales-sobrecaudales-excesos-caudal'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-sobrecaudales-excesos-caudal'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-caudales-sobrecaudales-excesos-caudal'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-sobrecaudales-excesos-caudal'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_sobrecaudales_sensor_gas_Espanya($parametros_informe);
        return ($datos_elemento);
    }
?>
