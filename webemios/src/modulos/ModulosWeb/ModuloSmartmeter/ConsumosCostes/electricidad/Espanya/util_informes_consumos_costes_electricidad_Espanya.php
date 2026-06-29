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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/util_electricidad.php');


    //
    // Funciones de información de consumos y costes (electricidad - España)
    //


    // Devuelve información de sobrepotencias (excesos de potencia) de un sensor
    function dame_sobrepotencias_sensor_electricidad_Espanya($parametros)
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
        $granularidad = $parametros['granularidad'];
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
        $tipo_calculo_coste_potencias_sensor = NULL;
        $datos_sobrepotencias_absolutas_sensor = new VectorDatos();
        $min_sobrepotencia_absoluta_sensor = 0;
        $max_sobrepotencia_absoluta_sensor = 0;
        $datos_potencias_sensor = new VectorDatos();
        $datos_potencias_contratadas_sensor = new VectorDatos();
        $total_potencia_sensor = 0;
        $max_potencia_sensor = 0;
        $total_sobrepotencia_sensor = 0;
        $info_sobrepotencias_tramos_sensor = array();
        $numero_dias = 0;

        // Se recupera el identificador de tarifa eléctrica del sensor en la fecha inicial
        $id_tarifa_sensor_inicial = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
        if ($id_tarifa_sensor_inicial == ID_NINGUNO)
        {
            $msg = $idiomas->_("El sensor no tiene tarifa eléctrica asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }

        // Se recupera el identificador de tarifa eléctrica del sensor en la fecha final y si no coincide con la tarifa en la fecha inicial,
        // se devuelve un error (los datos de sobrepotencias pueden no ser correctos)
        /*$id_tarifa_sensor_final = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_fin_local_local);
        if ($id_tarifa_sensor_final != $id_tarifa_sensor_inicial)
        {
            $msg = $idiomas->_("El sensor tiene diferentes tarifas eléctricas asignadas entre las fechas seleccionadas");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }
        */
        // ELI: Lo quito a petición de Jose Ramón de Ixatu ya que no acepta la solución de que no aparezca. Buscar otra alternativa.

        // Se recuperan los datos de sobrepotencias del sensor
        dame_datos_sobrepotencias_sensor_fecha_electricidad_Espanya(
            $nombre_sensor,
            $id_tarifa_sensor_inicial,
            $cadena_fecha_hora_inicio_local_utc,
            $cadena_fecha_hora_fin_local_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $milisegundos_desfase_zonas_horarias_cliente_local,
            $granularidad,
            $tipo_calculo_coste_potencias_sensor,
            $datos_sobrepotencias_absolutas_sensor,
            $min_sobrepotencia_absoluta_sensor,
            $max_sobrepotencia_absoluta_sensor,
            $datos_potencias_sensor,
            $datos_potencias_contratadas_sensor,
            $total_potencia_sensor,
            $max_potencia_sensor,
            $total_sobrepotencia_sensor,
            $info_sobrepotencias_tramos_sensor,
            $numero_dias,
			$prorrateo_potencias,
			$tipo_tarifa_electrica);

        // Si el tipo de cálculo de coste de potencias es sin excesos, se devuelve un error
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS)
        {
            $msg = $idiomas->_("La tarifa eléctrica asignada al sensor no tiene excesos de potencia");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }

        // Si no hay datos no se hace nada
        if (count($info_sobrepotencias_tramos_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Se crean las filas para la tabla de excesos de potencia (ordenadas por tramo)
        ksort($info_sobrepotencias_tramos_sensor, SORT_NUMERIC);
        $filas_tabla_sobrepotencias_tramos = array();
        $total_horas_potencia = 0;
        $total_horas_sobrepotencia = 0;
        $total_potencia = 0.0;
        $total_sobrepotencia = 0.0;
        $mayor_maxima_sobrepotencia_absoluta = -INF;
        $total_coste_potencia = 0;
        $total_coste_sobrepotencia = 0;
        $total_aei_sensor = 0;
        foreach ($info_sobrepotencias_tramos_sensor as $tramo => $info_sobrepotencia_tramo)
        {
            $potencia_tramo = $info_sobrepotencia_tramo["potencia_tramo"];
            $precio_potencia_tramo = $info_sobrepotencia_tramo["precio_potencia_tramo"];
            $coste_potencia_tramo = (($potencia_tramo * $precio_potencia_tramo)) * $numero_dias;
            $cadena_potencia_coste_tramo = formatea_numero($potencia_tramo, 2)." "."kW"." (".formatea_numero($coste_potencia_tramo, 2, false)." ".$unidad_medida_coste.")";
            $total_coste_potencia += $coste_potencia_tramo;

            // Se comprueba si hay consumo en el tramo
            $hay_consumo_tramo = $info_sobrepotencia_tramo["hay_consumo"];
            if ($hay_consumo_tramo == true)
            {
                $porcentaje_horas_sobrepotencia_tramo = ($info_sobrepotencia_tramo["horas_sobrepotencia"] * 100) / $info_sobrepotencia_tramo["horas_potencia"];
                $cadena_horas_sobrepotencia_tramo = formatea_numero($info_sobrepotencia_tramo["horas_sobrepotencia"], 2)." ".$idiomas->_("horas")." (".formatea_numero($porcentaje_horas_sobrepotencia_tramo, 2)." "."%".")";

                $total_horas_potencia += $info_sobrepotencia_tramo["horas_potencia"];
                $total_horas_sobrepotencia += $info_sobrepotencia_tramo["horas_sobrepotencia"];

                $porcentaje_sobrepotencia_tramo = ($info_sobrepotencia_tramo["sobrepotencia"] * 100) / $info_sobrepotencia_tramo["potencia"];
                $cadena_porcentaje_sobrepotencia_tramo = formatea_numero($porcentaje_sobrepotencia_tramo, 2)." "."%";

                $total_potencia += $info_sobrepotencia_tramo["potencia"];
                $total_sobrepotencia += $info_sobrepotencia_tramo["sobrepotencia"];
                $total_coste_sobrepotencia += $info_sobrepotencia_tramo["coste_sobrepotencia"];

                $cadena_fecha_hora_maxima_sobrepotencia_absoluta_local_utc = convierte_formato_fecha($info_sobrepotencia_tramo["cadena_fecha_hora_maxima_sobrepotencia_absoluta_base_datos_utc"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_maxima_sobrepotencia_absoluta_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_maxima_sobrepotencia_absoluta_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_maxima_sobrepotencia_absoluta = formatea_numero($info_sobrepotencia_tramo["maxima_potencia"], 2)." ".$idiomas->_("kW")." [".formatea_numero($info_sobrepotencia_tramo["maxima_sobrepotencia_absoluta"], 2)." ".$idiomas->_("kW")."]".
                    " (".$cadena_fecha_hora_maxima_sobrepotencia_absoluta_local_local.")";
                if ($info_sobrepotencia_tramo["maxima_sobrepotencia_absoluta"] > $mayor_maxima_sobrepotencia_absoluta)
                {
                    $mayor_maxima_sobrepotencia_absoluta = $info_sobrepotencia_tramo["maxima_sobrepotencia_absoluta"];
                    $cadena_mayor_maxima_sobrepotencia_absoluta = $cadena_maxima_sobrepotencia_absoluta;
                }
                $cadena_coste_sobrepotencia = formatea_numero($info_sobrepotencia_tramo["coste_sobrepotencia"], 2, false)." ".$unidad_medida_coste;
            }
            else
            {
                $cadena_horas_sobrepotencia_tramo = $idiomas->_("ND");
                $cadena_porcentaje_sobrepotencia_tramo = $idiomas->_("ND");
                $cadena_maxima_sobrepotencia_absoluta = $idiomas->_("ND");
                $cadena_coste_sobrepotencia = $idiomas->_("ND");
            }

            // Se añade la fila de sobrepotencia del tramo
            $fila_sobrepotencia_tramo = array(
                "P".$tramo,
                $cadena_horas_sobrepotencia_tramo,
                $cadena_porcentaje_sobrepotencia_tramo,
                $cadena_maxima_sobrepotencia_absoluta,
                $cadena_potencia_coste_tramo,
                $cadena_coste_sobrepotencia);

			// Si la tarifa tiene prorrateo de potencias, calculamos el coste del exceso de potencia prorrateado.
			if($prorrateo_potencias){
				$fecha_inicio = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
				$mes_inicio = $fecha_inicio -> format("m");
				$anyo_inicio =  $fecha_inicio -> format("Y");
				$fecha_fin_valores = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
				$mes_fin = $fecha_fin_valores -> format("m");
				$numero_dias_tramo = 0;

				if ($mes_inicio == $mes_fin)
				{
					$numero_dias_tramo = $numero_dias;
				}
				else{
					$mes_periodo = $mes_inicio;
					$fecha_mes_intermedio = new DateTime();
					$fecha_mes_intermedio->setDate($anyo_inicio, $mes_inicio, 1);
					$tramos = [];

					while ($fecha_mes_intermedio <= $fecha_fin_valores){
						$tramos = TarifaElectrica_Espanya::dame_tramos_mes_tipo_tarifa($tipo_tarifa_electrica, $mes_periodo);
						if (in_array($tramo,$tramos))
						{
							if (intval($mes_periodo) == intval($mes_fin)){
								$diferencia = date_diff($fecha_mes_intermedio,$fecha_fin_valores)->format("%a");
								$numero_dias_tramo += $diferencia +1 ;
								date_add($fecha_mes_intermedio,date_interval_create_from_date_string('1 month'));
							}
							# Número de días del primer mes del informe
							else if (intval($mes_periodo) == intval($mes_inicio)){
								date_add($fecha_mes_intermedio,date_interval_create_from_date_string('1 month'));
								$diferencia = date_diff($fecha_mes_intermedio,$fecha_inicio)->format("%a");
								$numero_dias_tramo += intval($diferencia);
							}
							else{
								//$diferencia = date_diff($fecha_mes_intermedio,date_add($fecha_mes_intermedio,date_interval_create_from_date_string('1 month')))->format("%d");
								$diferencia = cal_days_in_month(CAL_GREGORIAN,$mes_periodo, $fecha_mes_intermedio -> format("Y"));
								$numero_dias_tramo += intval($diferencia);
								date_add($fecha_mes_intermedio,date_interval_create_from_date_string('1 month'));
							}
						}else{
							date_add($fecha_mes_intermedio,date_interval_create_from_date_string('1 month'));
						}
						$mes_periodo =  $fecha_mes_intermedio -> format("m");
					}
				}

				$coste_sobrepotencia_prorrateo = $info_sobrepotencia_tramo["coste_sobrepotencia"] * $numero_dias_tramo/30;
				$cadena_coste_sobrepotencia_prorrateo = formatea_numero($coste_sobrepotencia_prorrateo, 2, false)." ".$unidad_medida_coste;
				array_push($fila_sobrepotencia_tramo, $cadena_coste_sobrepotencia_prorrateo);
			}

			if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS)
            {
                if ($hay_consumo_tramo == true)
                {
                    $aei_tramo = $info_sobrepotencia_tramo["aei"];
                    $cadena_aei_tramo = formatea_numero($aei_tramo, 2)." "."kW";
                    $total_aei_sensor += $aei_tramo;
                }
                else
                {
                    $cadena_aei_tramo = $idiomas->_("ND");
                }
                array_push($fila_sobrepotencia_tramo, $cadena_aei_tramo);
            }
            if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO)
            {
                if ($hay_consumo_tramo == true)
                {
                    $exceso_potencia = $info_sobrepotencia_tramo["exceso_potencia"];
                    $cadena_exceso_potencia_tramo = formatea_numero($exceso_potencia, 2)." "."kW";
                    $total_exceso_potencia_sensor += $exceso_potencia;
                }
                else
                {
                    $cadena_exceso_potencia_tramo = $idiomas->_("ND");
                }
                array_push($fila_sobrepotencia_tramo, $cadena_exceso_potencia_tramo);
            }
            array_push($filas_tabla_sobrepotencias_tramos, $fila_sobrepotencia_tramo);
        }



        // Se crea la fila sobrepotencias totales (al final) y la tabla de información de sobrepotencia
        $cadena_total_horas_sobrepotencia = formatea_numero($total_horas_sobrepotencia, 2);
        $porcentaje_horas_sobrepotencia = ($total_horas_sobrepotencia * 100) / $total_horas_potencia;
        $cadena_total_horas_sobrepotencia = $cadena_total_horas_sobrepotencia." ".$idiomas->_("horas")." (".formatea_numero($porcentaje_horas_sobrepotencia, 2)." "."%".")";
        $porcentaje_sobrepotencia = ($total_sobrepotencia * 100) / $total_potencia;
        $cadena_porcentaje_sobrepotencia = formatea_numero($porcentaje_sobrepotencia, 2)." "."%";
        $cadena_total_coste_potencia = formatea_numero($total_coste_potencia, 2, false)." ".$unidad_medida_coste;
        $cadena_total_coste_sobrepotencia = formatea_numero($total_coste_sobrepotencia, 2, false)." ".$unidad_medida_coste;

        $fila_sobrepotencia_total = array(
            $idiomas->_("Total"),
            $cadena_total_horas_sobrepotencia,
            $cadena_porcentaje_sobrepotencia,
            $cadena_mayor_maxima_sobrepotencia_absoluta,
            $cadena_total_coste_potencia,
            $cadena_total_coste_sobrepotencia);
				if($prorrateo_potencias){
						$coste_sobrepotencia_prorrateo = $total_coste_sobrepotencia * $numero_dias/30;
						$cadena_coste_sobrepotencia_prorrateo = formatea_numero($coste_sobrepotencia_prorrateo, 2, false)." ".$unidad_medida_coste;
						array_push($fila_sobrepotencia_total, $cadena_coste_sobrepotencia_prorrateo);
				}
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS)
        {
            $cadena_aei_total = formatea_numero($total_aei_sensor, 2)." "."kW";
            array_push($fila_sobrepotencia_total, $cadena_aei_total);
        }
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO)
        {
            $cadena_exceso_potencia_total = formatea_numero($total_exceso_potencia_sensor, 2)." "."kW";
            array_push($fila_sobrepotencia_total, $cadena_exceso_potencia_total);
        }
        array_push($filas_tabla_sobrepotencias_tramos, $fila_sobrepotencia_total);

        // Tabla de sobrepotencias por tramo
        switch ($tipo_calculo_coste_potencias_sensor)
        {
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
            {
				if ($prorrateo_potencias){
					$params_tabla = array(
						"numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA_PRORRATEO,
						"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA_PRORRATEO),
						"generar_valores_xml" => true
						);
				}else{
					$params_tabla = array(
                    	"numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA,
                    	"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA),
                    	"generar_valores_xml" => true
                		);
				}
                break;
            }
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
            {
							if ($prorrateo_potencias){
								$params_tabla = array(
										"numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA_PRORRATEO,
										"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA_PRORRATEO),
										"generar_valores_xml" => true
								);
							}else{
								$params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_CUARTOHORARIOS_ESPANYA),
                    "generar_valores_xml" => true
                );
							}
              break;
            }
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
            {
							if ($prorrateo_potencias){
								$params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA_PRORRATEO,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA_PRORRATEO),
                    "generar_valores_xml" => true
                );
							}else{
								$params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SOBREPOTENCIAS_TRAMOS_EXCESOS_MAXIMETRO_ESPANYA),
                    "generar_valores_xml" => true
                );
							}
                break;
            }
        }


        $tabla_sobrepotencias_tramos = new TablaDatos(
            "tabla-sobrepotencias-tramos-excesos-potencia",
            $idiomas->_("Excesos de potencia por tramo"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $cabecera_tabla = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Horas excedidas"),
            $idiomas->_("Porcentaje de excesos"),
            $idiomas->_("Potencia máxima"),
            $idiomas->_("Potencia contratada"),
            $idiomas->_("Coste de excesos")
        );
				if ($prorrateo_potencias)
				{
					array_push($cabecera_tabla, $idiomas->_("Coste excesos prorrateado"));
				}
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS)
        {
            array_push($cabecera_tabla, $idiomas->_("Aei"));
        }
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO)
        {
            array_push($cabecera_tabla, $idiomas->_("Exceso potencia"));
        }
			$tabla_sobrepotencias_tramos->anyade_cabecera("", $cabecera_tabla);

				// Se añaden las filas a la tabla
        foreach ($filas_tabla_sobrepotencias_tramos as $tramo => $fila_sobrepotencia_tramo_sensor)
        {
			$tabla_sobrepotencias_tramos->anyade_fila("", $fila_sobrepotencia_tramo_sensor);
        }

        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_sensor_inicial);
        $tipo_medida_tarifa_electrica = $fila_tarifa_electrica["tipo_medida"];

        // Gráficas de potencias y sobrepotencias
        $grafica_potencias_potencias_contratadas = new VectorDatos();
        $grafica_sobrepotencias_absolutas = new VectorDatos();
        if ($tipo_medida_tarifa_electrica == "ALTA_TENSION"){
            $grafica_potencias_potencias_contratadas->anyade_dato($datos_potencias_sensor->dame_datos());
        }
        else {
            $grafica_potencias_potencias_contratadas->anyade_dato($datos_potencias_sensor->dame_datos());
        }        
        $grafica_potencias_potencias_contratadas->anyade_dato($datos_potencias_contratadas_sensor->dame_datos());
        $grafica_sobrepotencias_absolutas->anyade_dato($datos_sobrepotencias_absolutas_sensor->dame_datos());

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "grafica_potencias_potencias_contratadas" => $grafica_potencias_potencias_contratadas->dame_datos(),
            "max_potencia" => $max_potencia_sensor,
            "grafica_sobrepotencias_absolutas" => $grafica_sobrepotencias_absolutas->dame_datos(),
            "min_sobrepotencia_absoluta" => $min_sobrepotencia_absoluta_sensor,
            "max_sobrepotencia_absoluta" => $max_sobrepotencia_absoluta_sensor,
            "tabla_sobrepotencias_tramos" => $tabla_sobrepotencias_tramos->dame_tabla(),
            "tipo_medida" => $tipo_medida_tarifa_electrica,
            "nombre_sensor" => $nombre_sensor);
        return ($resultado);
    }


    // Devuelve información de excesos de energía reactiva de un sensor
    function dame_excesos_energia_reactiva_sensor_electricidad_Espanya($parametros, $filas_valores_sensor_energia_activa)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

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
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Se obtiene el sensor de energía activa asociado (si no hay se devuelve error)
        $info_sensor_energia_activa = dame_info_sensor_energia_activa_asociado_sensor_energia_reactiva($id_sensor, NULL);
        if ($info_sensor_energia_activa === NULL)
        {
            $mensaje_error = $idiomas->_("No hay sensor de energía activa asociado");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        $id_sensor_energia_activa = $info_sensor_energia_activa['id'];
        $nombre_sensor_energia_activa = $info_sensor_energia_activa['nombre'];

        // Se obtiene el tipo de energía reactiva del sensor
        $tipo_energia_reactiva = dame_info_tipo_reactiva_sensor_energia_reactiva($id_sensor, NULL);

        // Tramos penalizables de energía reactiva
        $id_tarifa_sensor = dame_id_tarifa_id_sensor($id_sensor_energia_activa);

        // Variables
        $max_consumo = 0;
        $max_coseno_phi = 0;
        $min_coseno_phi = 1;
        $coste_total = 0;
        $datos_consumo_energia_activa = new VectorDatos();
        $datos_consumo_energia_reactiva = new VectorDatos();
        $datos_coseno_phi = new VectorDatos();
        $datos_penalizable = new VectorDatos();
        $grafica_consumos_energia = new VectorDatos();
        $grafica_coseno_phi = new VectorDatos();
        $grafica_penalizable = new VectorDatos();
        $etiquetas_consumos_energia = new VectorDatos();

        // Consulta de valores del sensor de energía activa
        if ($filas_valores_sensor_energia_activa === NULL)
        {
            $consulta_valores_sensor_energia_activa = dame_consulta_valores_sensor(
                $id_sensor_energia_activa,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                INTERVALO_VALORES_HORA,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                NULL);
            $res_valores_sensor_energia_activa = $bd_datos->ejecuta_consulta($consulta_valores_sensor_energia_activa);
            if ($res_valores_sensor_energia_activa == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor_energia_activa."'");
            }

            $filas_valores_sensor_energia_activa = array();
            while ($fila_valores_sensor_energia_activa = $res_valores_sensor_energia_activa->dame_siguiente_fila())
            {
                array_push($filas_valores_sensor_energia_activa, $fila_valores_sensor_energia_activa);
            }
        }

        // Se guardan los datos de energía activa por horas
        $datos_energia_activa_horas = array();
        foreach ($filas_valores_sensor_energia_activa as $fila_valores_sensor_energia_activa)
        {
            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor_energia_activa['fecha_hora'];
            $tramo = $fila_valores_sensor_energia_activa[CAMPO_TRAMO];
            $consumo = (float) $fila_valores_sensor_energia_activa[CAMPO_INCREMENTO];

            // Sólo se procesa la fila si se han calculado valores de clase
            if ($fila_valores_sensor_energia_activa["tramo"] === NULL)
            {
                continue;
            }

            $timestamp_fecha_hora_hora_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            array_push($datos_energia_activa_horas,
                array(
                    "timestamp_fecha_hora_utc" => $timestamp_fecha_hora_hora_utc,
                    "tramo" => $tramo,
                    "consumo" => $consumo)
            );
        }

        // Si no hay datos no se hace nada
        if (count($datos_energia_activa_horas) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se guardan los consumos de energía activa y reactiva (por tramo) (en las horas en las que hay datos de los dos sensores)
        $tramos = array();
        $consumos_energia_activa_tramos = array();
        $consumos_energia_reactiva_tramos = array();
		$tramos_penalizables_energia_reactiva = array();

        // Consulta de valores del sensor de energía reactiva
        $consulta_valores_sensor_energia_reactiva = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_HORA,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            NULL);
        $res_valores_sensor_energia_reactiva = $bd_datos->ejecuta_consulta($consulta_valores_sensor_energia_reactiva);
        if ($res_valores_sensor_energia_reactiva == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor_energia_reactiva."'");
        }

        // Si no hay datos no se hace nada
        if ($res_valores_sensor_energia_reactiva->dame_numero_filas() == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Número de datos de energía activa y reactiva en las mismas horas
        $numero_datos_energia_activa_reactiva_mismas_horas = 0;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, NULL);

        // Se recorren las filas de valores de energía reactiva
        $numero_dato_energia_activa_horas = 0;
        $timestamp_fecha_hora_energia_reactiva_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        $exceso_energia_reactiva_capacitiva = 0;
        while ($fila_valores_sensor_energia_reactiva = $res_valores_sensor_energia_reactiva->dame_siguiente_fila())
        {
            // Sólo se procesa la fila si se han calculado valores de clase
            if ($fila_valores_sensor_energia_reactiva["tramo"] === NULL)
            {
				$log = dame_log();
				$log -> error("TRAMO REACTIVA NULL Sensor " . $id_sensor . " FECHA " . $fila_valores_sensor_energia_reactiva['fecha_hora']);

				//continue;

				// Si el tramo de reactiva es Null, forzamos al tramo de activa para evitar que se muestren huecos en las gráficas.
				$fila_valores_sensor_energia_reactiva["tramo"] = $datos_energia_activa_horas[$numero_dato_energia_activa_horas +1]["tramo"];
				if (($fila_valores_sensor_energia_reactiva["tramo"] != 6 and $tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q1)
					or ($fila_valores_sensor_energia_reactiva["tramo"] == 6 and $tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q4))
				{
					$fila_valores_sensor_energia_reactiva["penalizable"] = VALOR_SI;
				}
				else {
					$fila_valores_sensor_energia_reactiva["penalizable"] = VALOR_NO;
				}
            }

            // Fecha y valores
            $cadena_fecha_hora_energia_reactiva_base_datos_utc = $fila_valores_sensor_energia_reactiva['fecha_hora'];
            $consumo_energia_reactiva = (float) $fila_valores_sensor_energia_reactiva[CAMPO_INCREMENTO];
            $tramo_energia_reactiva = $fila_valores_sensor_energia_reactiva[CAMPO_TRAMO];
            $coseno_phi = (float) $fila_valores_sensor_energia_reactiva[CAMPO_COSENO_PHI];
            $penalizable = $fila_valores_sensor_energia_reactiva[CAMPO_PENALIZABLE];

            // Timestamps
            $timestamp_fecha_hora_energia_reactiva_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_energia_reactiva_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_energia_reactiva_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Si la primera hora de energía activa es mayor que la fecha de energía reactiva, se pasa a la siguiente fila de energía reactiva
            if ($numero_dato_energia_activa_horas == 0)
            {
                $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
                if ($timestamp_fecha_hora_energia_activa_utc > $timestamp_fecha_hora_energia_reactiva_utc)
                {
                    continue;
                }
            }

            // Si la fecha de energía activa es menor, se incrementa el número de fila hasta que sea igual (si es mayor es que hay huecos)
            $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
            while ($timestamp_fecha_hora_energia_activa_utc < $timestamp_fecha_hora_energia_reactiva_utc)
            {
                $numero_dato_energia_activa_horas += 1;
                if (count($datos_energia_activa_horas) < ($numero_dato_energia_activa_horas + 1))
                {
                    break;
                }
                $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
            }

            // Si la fecha no es igual es que hay huecos (los datos son incompletos), se pasa a la siguiente fila de energía reactiva
            if ($timestamp_fecha_hora_energia_activa_utc != $timestamp_fecha_hora_energia_reactiva_utc)
            {
                continue;
            }

            // Hay datos de los dos sensores y la hora es la misma (se puede continuar)
            $numero_datos_energia_activa_reactiva_mismas_horas += 1;

            // Datos de energía activa
            $datos_energia_activa_hora = $datos_energia_activa_horas[$numero_dato_energia_activa_horas];
            $consumo_energia_activa = $datos_energia_activa_hora["consumo"];
            $tramo_energia_activa = $datos_energia_activa_hora["tramo"];

            // Si el tramo es diferente se devuelve error
            if ($tramo_energia_activa != $tramo_energia_reactiva)
            {
                $mensaje_error = $idiomas->_("Los tramos de energía activa y reactiva no coinciden").".\n".
                    $idiomas->_("Es posible que haya que recalcular los datos de la tarifa en la sección 'Tarifas'").".\n".
                    $idiomas->_("Si ya lo ha hecho, espere unos minutos y podrá ver el informe correctamente");
                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $mensaje_error);
                return ($resultado);
            }
            else
            {
                $tramo = $tramo_energia_activa;
            }

            // Consumo máximo
            if ($consumo_energia_reactiva > $max_consumo)
            {
                $max_consumo = $consumo_energia_reactiva;
            }
            if ($consumo_energia_activa > $max_consumo)
            {
                $max_consumo = $consumo_energia_activa;
            }

            // Se guardan los consumos de energía activa y reactiva por tramo (en todos los tramos aunque no sean penalizables)

            // Tramo
            if (in_array($tramo, $tramos) == false)
            {
                array_push($tramos, $tramo);
            }

            // Energía activa
            if (array_key_exists($tramo, $consumos_energia_activa_tramos) == false)
            {
                $consumos_energia_activa_tramos[$tramo] = $consumo_energia_activa;
            }
            else
            {
                $consumos_energia_activa_tramos[$tramo] += $consumo_energia_activa;
            }

            // Energía reactiva
            if (array_key_exists($tramo, $consumos_energia_reactiva_tramos) == false)
            {
                $consumos_energia_reactiva_tramos[$tramo] = $consumo_energia_reactiva;
            }
            else
            {
                $consumos_energia_reactiva_tramos[$tramo] += $consumo_energia_reactiva;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_energia_reactiva_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_energia_reactiva_utc - $timestamp_fecha_hora_energia_reactiva_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_consumo_energia_activa->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_consumo_energia_reactiva->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_coseno_phi->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_penalizable->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_energia_reactiva_anterior_utc = $timestamp_fecha_hora_energia_reactiva_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Datos para la gráfica de consumos (los timestamps son iguales)
            $datos_consumo_energia_activa->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_activa_utc, $consumo_energia_activa);
            $datos_consumo_energia_reactiva->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $consumo_energia_reactiva);

            // Datos para la gráfica del coseno de phi y penalizable
            $datos_coseno_phi->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $coseno_phi);
            if ($coseno_phi > $max_coseno_phi)
            {
                $max_coseno_phi = $coseno_phi;
            }
            if ($coseno_phi < $min_coseno_phi)
            {
                $min_coseno_phi = $coseno_phi;
            }
            if ($penalizable !== NULL)
            {
                $datos_penalizable->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $penalizable);
            }

            // Se incrementa el número de dato de energía activa
            $numero_dato_energia_activa_horas += 1;
            if (count($datos_energia_activa_horas) < ($numero_dato_energia_activa_horas + 1))
            {
                break;
            }

            // Se calcula la penalizacion de energia reactiva capacitiva
            if( ($tramo_energia_reactiva == 6) &&  $penalizable && $coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_CAPACITIVA)
            {
                $exceso_energia_reactiva_capacitiva += $consumo_energia_reactiva - ($consumo_energia_activa * 0.20);
            }

			// Si el valor es penalizable, lo añadimos al array de tramos penalizables
            if($penalizable == 1)
            {
                if(in_array($tramo, $tramos_penalizables_energia_reactiva) == false)
                {
                    array_push($tramos_penalizables_energia_reactiva,$tramo);
                }
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_datos_energia_activa_reactiva_mismas_horas == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Tabla de energía reactiva (por tramo)
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_TRAMOS_EXCESOS_ENERGIA_REACTIVA,
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Consumos"),
            $idiomas->_("Coseno de phi"),
            $idiomas->_("Exceso"),
            $idiomas->_("Coste")
        );
        $tabla_energia_reactiva_tramos = new TablaDatos(
            "tabla-energia-reactiva-tramos-excesos-energia-reactiva",
            $idiomas->_("Energía reactiva por tramo"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_energia_reactiva_tramos->anyade_cabecera("", $cabecera_tabla);

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Se rellena la tabla de energía reactiva (por tramo):
        // - Coseno de phi
        // - Exceso de energía reactiva
        // - Coste de energía reactiva
        $datos_coste_energia_reactiva_tramo = array();
        sort($tramos);
        foreach ($tramos as $tramo)
        {
            $consumo_energia_activa = $consumos_energia_activa_tramos[$tramo];
            $consumo_energia_reactiva = $consumos_energia_reactiva_tramos[$tramo];
            if (($consumo_energia_activa == 0) && ($consumo_energia_reactiva == 0))
            {
                $coseno_phi = 1;
            }
            else
            {
                $coseno_phi = $consumo_energia_activa / sqrt(pow($consumo_energia_activa, 2) + pow($consumo_energia_reactiva, 2));
                $coseno_phi = round($coseno_phi,2);
            }

            if ($tramo != 6)
            {
                if ($coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_1)
                {
                    // Nota: Para el coseno de phi de 0.95 la fórmula sería '* 0.328' pero en la factura es '* 0.33'
                    // (si el exceso está entre 0.328 y 0.33 puede ser negativo y se pone a 0)
                    $exceso_energia_reactiva = $consumo_energia_reactiva - ($consumo_energia_activa * 0.33);
                    if ($exceso_energia_reactiva < 0)
                    {
                        $exceso_energia_reactiva = 0;
                    }
                    if ($coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_2)
                    {
                        $coste_exceso_energia_reactiva = $exceso_energia_reactiva * PRECIO_EXCESO_ENERGIA_REACTIVA_2;
                    }
                    else
                    {
                        $coste_exceso_energia_reactiva = $exceso_energia_reactiva * PRECIO_EXCESO_ENERGIA_REACTIVA_1;
                    }
                }
                else
                {
                    $exceso_energia_reactiva = 0;
                    $coste_exceso_energia_reactiva = 0;
                }
            }
            # Calculo penalizacion capacitiva
            else
            {
                $coste_exceso_energia_reactiva = $exceso_energia_reactiva_capacitiva * PRECIO_EXCESO_ENERGIA_REACTIVA_CAPACITIVA;
                $exceso_energia_reactiva = $exceso_energia_reactiva_capacitiva;
            }



            $cadena_tramo = "P" . $tramo;
            $tramo_penalizable = true;
            if ($id_tarifa_sensor != ID_NINGUNO)
            {
                if( (($tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q1) && ($tramo == 6) )
                    || (($tipo_energia_reactiva == TIPO_ENERGIA_REACTIVA_Q4) && ($tramo != 6) )
                    || (in_array($tramo, $tramos_penalizables_energia_reactiva) == false) )
                {

                    $cadena_tramo .= " (".$idiomas->_("no penalizable").")";
                    $tramo_penalizable = false;
                }
            }
            $datos_coste_energia_reactiva_tramo[0] = $cadena_tramo;
            $datos_coste_energia_reactiva_tramo[1] =
                formatea_numero($consumo_energia_activa, 2)." ".$idiomas->_("kWh")." / ".
                formatea_numero($consumo_energia_reactiva, 2)." ".$idiomas->_("kVArh");
            $datos_coste_energia_reactiva_tramo[2] = formatea_numero($coseno_phi, 3);
            $datos_coste_energia_reactiva_tramo[3] = formatea_numero($exceso_energia_reactiva, 2)." ".$idiomas->_("kVArh");
            if ($tramo_penalizable == true)
            {
                $datos_coste_energia_reactiva_tramo[4] = formatea_numero($coste_exceso_energia_reactiva, 2, false)." ".$unidad_medida_coste;
            }
            else
            {
                $datos_coste_energia_reactiva_tramo[4] = $idiomas->_("NA");
            }
            $tabla_energia_reactiva_tramos->anyade_fila("", $datos_coste_energia_reactiva_tramo);

            // Coste total
            if ($tramo_penalizable == true)
            {
                $coste_total = $coste_total + $coste_exceso_energia_reactiva;
            }
        }
        $tabla_energia_reactiva_tramos->anyade_pie($idiomas->_("Coste total").": ".formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste);

        // Datos de gráficas
        $grafica_consumos_energia->anyade_dato($datos_consumo_energia_activa->dame_datos());
        $grafica_consumos_energia->anyade_dato($datos_consumo_energia_reactiva->dame_datos());
        $grafica_coseno_phi->anyade_dato($datos_coseno_phi->dame_datos());
        $grafica_penalizable->anyade_dato($datos_penalizable->dame_datos());

        // Etiquetas de la gráfica de consumos de energía
        $etiquetas_consumos_energia->anyade_etiqueta($idiomas->_("Energía activa")." (".$nombre_sensor_energia_activa.") (".$idiomas->_("kWh").")");
        $etiquetas_consumos_energia->anyade_etiqueta($idiomas->_("Energía reactiva")." (".$nombre_sensor.") (".$idiomas->_("kVArh").")");

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tipo_energia_reactiva" => $tipo_energia_reactiva,
            "grafica_consumos_energia" => $grafica_consumos_energia->dame_datos(),
            "grafica_coseno_phi" => $grafica_coseno_phi->dame_datos(),
            "grafica_penalizable" => $grafica_penalizable->dame_datos(),
            "tabla_energia_reactiva_tramos" => $tabla_energia_reactiva_tramos->dame_tabla(),
            "etiquetas_consumos_energia" => $etiquetas_consumos_energia->dame_datos(),
            "max_consumo" => $max_consumo,
            "min_coseno_phi" => $min_coseno_phi,
            "max_coseno_phi" => $max_coseno_phi);
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Calculo de sobrepotencias (excesos de potencia) de un sensor
    function dame_datos_sobrepotencias_sensor_fecha_electricidad_Espanya(
        $nombre_sensor,
        $id_tarifa,
        $cadena_fecha_hora_inicio_local_utc,
        $cadena_fecha_hora_fin_local_utc,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $milisegundos_desfase_zonas_horarias_cliente_local,
        $granularidad,
        &$tipo_calculo_coste_potencias_sensor,
        &$datos_sobrepotencias_absolutas_sensor,
        &$min_sobrepotencia_absoluta_sensor,
        &$max_sobrepotencia_absoluta_sensor,
        &$datos_potencias_sensor,
        &$datos_potencias_contratadas_sensor,
        &$total_potencia_sensor,
        &$max_potencia_sensor,
        &$total_sobrepotencia_sensor,
        &$info_sobrepotencias_tramos_sensor,
        &$numero_dias,
		&$prorrateo_potencias,
		&$tipo_tarifa_electrica)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Se recupera el tipo de cálculo de coste de potencias de la tarifa eléctrica del sensor
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $tipo_medida_tarifa_electrica = $fila_tarifa_electrica["tipo_medida"];
        $potencia_transformador = $fila_tarifa_electrica["potencia_nominal_transformador"];
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $tipo_calculo_coste_potencias_sensor = $caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
		$prorrateo_potencias = $caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"];

        // Si el tipo de cálculo de coste es sin excesos no se hace nada
        if ($tipo_calculo_coste_potencias_sensor == TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS)
        {
            return;
        }

        // - Las 'sobrepotencias_absolutas' son la diferencia de la potencia con respecto a los máximos de potencia contratados (pueden ser positivas o negativas)
        // - Las 'sobrepotencias' son las potencias en los cuales ha habido 'sobrepotencia positiva' (exceso de potencia)
        // - En 'info_sobrepotencia_tramos_sensor' se encuentra la siguiente información (por tramo de la tarifa eléctrica del sensor):
        //   - 1. Horas totales de potencia y 'sobrepotencia positivo'
        //   - 2. Potencia total y 'sobrepotencia positiva' total
        //   - 3. Hora, potencia y sobrepotencia de la sobrepotencia absoluto máximo (positiva o negativa)
        //   - 4. Coste de la sobrepotencia

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Nota: El número de días se obtiene de la diferencia entre la fecha de fin y la fecha de inicio
        // (aunque no haya valores en todas las fechas)
        $cadena_fecha_hora_inicio_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
        $cadena_fecha_hora_fin_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_inicio_local->setTime(0, 0, 0);
        $fecha_hora_fin_local->setTime(0, 0, 0);
        $diferencia_fechas = $fecha_hora_fin_local->diff($fecha_hora_inicio_local);
        $numero_dias = ($diferencia_fechas->days + 1);

        // Consulta de valores del sensor
        switch ($granularidad)
        {
            case GRANULARIDAD_HORARIA:
            {
                $tabla_datos_energia_activa = TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS;
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $tabla_datos_energia_activa = TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_CUARTOSHORA;
                break;
            }
        }
        $consulta_valores_sensor = "
            SELECT
                hora AS fecha_hora,
                YEAR(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS anyo,
                MONTH(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS mes,
                incremento,
                sobrepotencia,
                tramo,
                horas
            FROM ".$tabla_datos_energia_activa."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (incremento IS NOT NULL)
                AND (sobrepotencia IS NOT NULL)
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_valores_sensor .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se añade el orden y se ejecuta la consulta
        $consulta_valores_sensor .= "
            ORDER BY hora ASC";
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
		}

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        switch ($granularidad)
        {
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $intervalo_valores = INTERVALO_VALORES_HORA;
                break;
            }
        }
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, NULL);

        // Variables de información de sobrepotencia
        $datos_sobrepotencias_absolutas_sensor = new VectorDatos();
        $min_sobrepotencia_absoluta_sensor = 0;
        $max_sobrepotencia_absoluta_sensor = 0;
        $datos_potencias_sensor = new VectorDatos();
        $datos_potencias_contratadas_sensor = new VectorDatos();
        $total_potencia_sensor = 0;
        $max_potencia_sensor = 0;
        $total_sobrepotencia_sensor = 0;
        $info_sobrepotencias_tramos_sensor = array();
        $potencias_mensuales_tramos = array();
        $potencias_maximas_mensuales_tramos = array();
        $horas_mes_anyo = array();

        // Si no hay datos no se hace nada
        $numero_filas_sensor = $res_valores_sensor->dame_numero_filas();
        if ($numero_filas_sensor == 0)
        {
            return;
        }

        // Se recupera la información de los tramos de la tarifa eléctrica
        $info_tramos = dame_info_tramos_tarifa_electricidad_Espanya($id_tarifa);

        // Se recorren los datos del sensor
        $timestamp_fecha_hora_sensor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $incremento = $fila_valores_sensor['incremento'];
            $sobrepotencia = $fila_valores_sensor['sobrepotencia'];
            $tramo = $fila_valores_sensor['tramo'];
            $horas = $fila_valores_sensor['horas'];

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
                    $datos_sobrepotencias_absolutas_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_sensor_anterior_utc + 1, NULL);
                    $datos_potencias_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_sensor_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_sensor_anterior_utc = $timestamp_fecha_hora_sensor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Cadena de fecha local (para los tooltips personalizados)
            $cadena_fecha_hora_local_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_local_local_local = convierte_formato_fecha($cadena_fecha_hora_local_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);


            // Se añade la sobrepotencia absoluta
            $sobrepotencia_absoluta_sensor = (float) $sobrepotencia;
            $tooltip_sobrepotencia_absoluta_sensor = formatea_numero($sobrepotencia_absoluta_sensor, 2)." ".$idiomas->_("kW")." (".$idiomas->_("tramo").": ".$tramo.")".
                " (".$cadena_fecha_hora_local_local_local.")";
            $datos_sobrepotencias_absolutas_sensor->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_sensor_utc,
                $sobrepotencia_absoluta_sensor,
                $tooltip_sobrepotencia_absoluta_sensor);
            if ($sobrepotencia_absoluta_sensor < $min_sobrepotencia_absoluta_sensor)
            {
                $min_sobrepotencia_absoluta_sensor = $sobrepotencia_absoluta_sensor;
            }
            if ($sobrepotencia_absoluta_sensor > $max_sobrepotencia_absoluta_sensor)
            {
                $max_sobrepotencia_absoluta_sensor = $sobrepotencia_absoluta_sensor;
            }

            // Se añade la potencia (con el tooltip personalizado)           
            $potencia_sensor = $incremento / $horas;            
            // Se aplica la fórmula de la compensación para medidas de baja tensión
            if ($tipo_medida_tarifa_electrica == TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION) {                
                $potencia_sensor = $potencia_sensor * 1.04;
                $potencia_sensor += $potencia_transformador * 0.01;                
            }
            $tooltip_potencia_sensor = formatea_numero($potencia_sensor, 2)." ".$idiomas->_("kW")." (".$idiomas->_("tramo").": ".$tramo.")".
                " (".$cadena_fecha_hora_local_local_local.")";
            $datos_potencias_sensor->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_sensor_utc,
                $potencia_sensor,
                $tooltip_potencia_sensor);
            $total_potencia_sensor = $total_potencia_sensor + $potencia_sensor;
            $horas_potencia_sensor = $horas;

            // Se añade la potencia contratada
            $potencia_contratada_sensor = (float) $info_tramos[$tramo]["potencia"];
            $tooltip_potencia_contratada_sensor = formatea_numero($potencia_contratada_sensor, 2)." ".$idiomas->_("kW")." (".$idiomas->_("tramo").": ".$tramo.")".
                " (".$cadena_fecha_hora_local_local_local.")";
            $datos_potencias_contratadas_sensor->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_sensor_utc,
                $potencia_contratada_sensor,
                $tooltip_potencia_contratada_sensor);

            // Potencia máxima
            if ($potencia_sensor > $max_potencia_sensor)
            {
                $max_potencia_sensor = $potencia_sensor;
            }
            if ($potencia_contratada_sensor > $max_potencia_sensor)
            {
                $max_potencia_sensor = $potencia_contratada_sensor;
            }

            // Información de sobrepotencia
            if ($sobrepotencia_absoluta_sensor > 0)
            {
                $total_sobrepotencia_sensor = $total_sobrepotencia_sensor + $sobrepotencia_absoluta_sensor;
                $horas_sobrepotencia_sensor = $horas;
                $sobrepotencia_sensor = $sobrepotencia_absoluta_sensor;
            }
            else
            {
                $horas_sobrepotencia_sensor = 0;
                $sobrepotencia_sensor = 0;
            }

            // Se guarda información de sobrepotencia por tramo
            if (array_key_exists($tramo, $info_sobrepotencias_tramos_sensor) == false)
            {
                $info_sobrepotencias_tramos_sensor[$tramo] = array(
                    "tramo" => $tramo,
                    "potencia_tramo" => $info_tramos[$tramo]["potencia"],
                    "precio_potencia_tramo" => $info_tramos[$tramo]["precio_potencia"],
                    "hay_consumo" => true,
                    "horas_potencia" => $horas_potencia_sensor,
                    "horas_sobrepotencia" => $horas_sobrepotencia_sensor,
                    "potencia" => $potencia_sensor,
                    "sobrepotencia" => $sobrepotencia_sensor,
                    "cadena_fecha_hora_maxima_sobrepotencia_absoluta_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                    "maxima_potencia" => $potencia_sensor,
                    "maxima_sobrepotencia_absoluta" => $sobrepotencia_absoluta_sensor,
                    "tipo_medida" => $tipo_medida_tarifa_electrica);
            }
            else
            {
                $info_sobrepotencias_tramos_sensor[$tramo]["horas_potencia"] += $horas_potencia_sensor;
                $info_sobrepotencias_tramos_sensor[$tramo]["horas_sobrepotencia"] += $horas_sobrepotencia_sensor;
                $info_sobrepotencias_tramos_sensor[$tramo]["potencia"] += $potencia_sensor;
                $info_sobrepotencias_tramos_sensor[$tramo]["sobrepotencia"] += $sobrepotencia_sensor;
                if ($sobrepotencia_absoluta_sensor > $info_sobrepotencias_tramos_sensor[$tramo]["maxima_sobrepotencia_absoluta"])
                {
                    $info_sobrepotencias_tramos_sensor[$tramo]["cadena_fecha_hora_maxima_sobrepotencia_absoluta_base_datos_utc"] = $cadena_fecha_hora_base_datos_utc;
                    $info_sobrepotencias_tramos_sensor[$tramo]["maxima_potencia"] = $potencia_sensor;
                    $info_sobrepotencias_tramos_sensor[$tramo]["maxima_sobrepotencia_absoluta"] = $sobrepotencia_absoluta_sensor;
                }
            }

            // Se guardan los datos necesarios para calcular el coste de las sobrepotencias
            switch ($tipo_calculo_coste_potencias_sensor)
            {
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $anyo = $fila_valores_sensor["anyo"];
                    $mes = $fila_valores_sensor["mes"];
                    $mes_anyo = $mes."-".$anyo;

                    if (array_key_exists($mes_anyo, $horas_mes_anyo) == false)
                    {
                        $horas_mes_anyo[$mes_anyo] = $horas;
                    }
                    else
                    {
                        $horas_mes_anyo[$mes_anyo] += $horas;
                    }

                    if (array_key_exists($tramo, $potencias_maximas_mensuales_tramos) == false)
                    {
                        $potencias_maximas_mensuales_tramos[$tramo] = array();
                    }

                    if (array_key_exists($mes_anyo, $potencias_maximas_mensuales_tramos[$tramo]) == false)
                    {
                        $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo] = $potencia_sensor;
                    }
                    else
                    {
                        if ($potencia_sensor > $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo])
                        {
                            $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo] = $potencia_sensor;
                        }
                    }
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
                {
                    if ($sobrepotencia > 0)
                    {
                        $anyo = $fila_valores_sensor["anyo"];
                        $mes = $fila_valores_sensor["mes"];
                        $exceso_potencia = $sobrepotencia;
                        $horas_exceso_potencia = $horas;

                        if (array_key_exists($tramo, $potencias_mensuales_tramos) == false)
                        {
                            $potencias_mensuales_tramos[$tramo] = array();
                        }

                        $mes_anyo = $mes."-".$anyo;
                        if (array_key_exists($mes_anyo, $potencias_mensuales_tramos[$tramo]) == false)
                        {
                            $potencias_mensuales_tramos[$tramo][$mes_anyo] = array();
                        }

                        array_push($potencias_mensuales_tramos[$tramo][$mes_anyo], array(
                            "exceso_potencia" => $exceso_potencia,
                            "horas_exceso_potencia" => $horas_exceso_potencia));
                    }
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
                {
                    $anyo = $fila_valores_sensor["anyo"];
                    $mes = $fila_valores_sensor["mes"];
                    $mes_anyo = $mes."-".$anyo;

                    if (array_key_exists($mes_anyo, $horas_mes_anyo) == false)
                    {
                        $horas_mes_anyo[$mes_anyo] = $horas;
                    }
                    else
                    {
                        $horas_mes_anyo[$mes_anyo] += $horas;
                    }

                    if (array_key_exists($tramo, $potencias_maximas_mensuales_tramos) == false)
                    {
                        $potencias_maximas_mensuales_tramos[$tramo] = array();
                    }

                    if (array_key_exists($mes_anyo, $potencias_maximas_mensuales_tramos[$tramo]) == false)
                    {
                        $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo] = $potencia_sensor;
                    }
                    else
                    {
                        if ($potencia_sensor > $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo])
                        {
                            $potencias_maximas_mensuales_tramos[$tramo][$mes_anyo] = $potencia_sensor;
                        }
                    }
                    break;
                }
            }
        }

        // Se calcula la información de las sobrepotencias del sensor (si hay datos)
        if ($numero_filas_sensor == 0)
        {
            return;
        }
        switch ($tipo_calculo_coste_potencias_sensor)
        {
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
            {
                // Se recupera si hay bonificación de 85 %
                $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
                $bonificacion_85 = $fila_tarifa_electrica["bonificacion_85"];

                // Se calcula el coste de la sobrepotencia por tramo (puede ser negativo)
                foreach ($potencias_maximas_mensuales_tramos as $tramo => $potencias_maximas_mensuales_tramo)
                {
                    $precio_potencia_tramo = $info_tramos[$tramo]["precio_potencia"];
                    $potencia_contratada_tramo = $info_tramos[$tramo]["potencia"];

                    // Máximo de las potencias máximas mensuales del tramo
                    $max_potencia_maxima_mensual_tramo = 0;
                    foreach ($potencias_maximas_mensuales_tramo as $mes_anyo => $potencia_maxima_mensual_tramo)
                    {
                        if ($potencia_maxima_mensual_tramo > $max_potencia_maxima_mensual_tramo)
                        {
                            $max_potencia_maxima_mensual_tramo = $potencia_maxima_mensual_tramo;
                        }
                    }

                    // Cálculo del coste de la sobrepotencia del tramo
                    $valor_potencia_maxima_mes_tramo = $max_potencia_maxima_mensual_tramo;
                    if ($valor_potencia_maxima_mes_tramo > (1.05 * $potencia_contratada_tramo))
                    {
                        $potencia_facturada_mes_tramo = $valor_potencia_maxima_mes_tramo + 2 * ($valor_potencia_maxima_mes_tramo - 1.05 * $potencia_contratada_tramo);
                    }
                    else
                    {
                        if ($bonificacion_85 == BONIFICACION_85_TARIFA_ELECTRICA_MINIMO_100)
                        {
                            $potencia_facturada_mes_tramo = $potencia_contratada_tramo;
                        }
                        else
                        {
                            if (($valor_potencia_maxima_mes_tramo >= (0.85 * $potencia_contratada_tramo)) && ($valor_potencia_maxima_mes_tramo <= (1.05 * $potencia_contratada_tramo)))
                            {
                                if ($bonificacion_85 == BONIFICACION_85_TARIFA_ELECTRICA_SI)
                                {
                                    $potencia_facturada_mes_tramo = $valor_potencia_maxima_mes_tramo;
                                }
                                else
                                {
                                    $potencia_facturada_mes_tramo = $potencia_contratada_tramo;
                                }
                            }
                            else
                            {
                                $potencia_facturada_mes_tramo = 0.85 * $potencia_contratada_tramo;
                            }
                        }
                    }

                    $coste_potencia_mes_tramo = (($potencia_facturada_mes_tramo * $precio_potencia_tramo)) * $numero_dias;
                    $coste_potencia_sin_sobrepotencia_mes_tramo = (($potencia_contratada_tramo * $precio_potencia_tramo)) * $numero_dias;
                    $coste_potencia_tramo = $coste_potencia_mes_tramo;
                    $coste_potencia_sin_sobrepotencia_tramo = $coste_potencia_sin_sobrepotencia_mes_tramo;

                    $info_sobrepotencias_tramos_sensor[$tramo]["coste_sobrepotencia"] = $coste_potencia_tramo - $coste_potencia_sin_sobrepotencia_tramo;
                }
                break;
            }
            //Este caso se ejecuta cuando la potencia es mayor de 50kW (suministros con tipo de punto medida 1, 2 y 3 )
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
            {
                // Se calcula el Aei de los tramos
                foreach ($potencias_mensuales_tramos as $tramo => $potencias_mensuales_tramo)
                {
                    $aei_tramo = 0;
                    foreach ($potencias_mensuales_tramo as $potencias_mes_tramo)
                    {
                        $suma_cuadrado_sobrepotencias = 0;
                        foreach ($potencias_mes_tramo as $potencia_mes_tramo)
                        {
                            $exceso_potencia_tramo = $potencia_mes_tramo["exceso_potencia"];
                            $horas_exceso_potencia_tramo = $potencia_mes_tramo["horas_exceso_potencia"];
                            $suma_cuadrado_sobrepotencias += (pow($exceso_potencia_tramo, 2) * ($horas_exceso_potencia_tramo / 0.25));
                        }
                        $aei_tramo += sqrt($suma_cuadrado_sobrepotencias);
                    }
                    $info_sobrepotencias_tramos_sensor[$tramo]["aei"] = $aei_tramo;
                }

                // Se calcula el coste de la sobrepotencia
                foreach ($info_sobrepotencias_tramos_sensor as $tramo => $info_sobrepotencia_tramo_sensor)
                {
					$precio_penalizacion_sobrepotencia_tramo = TarifaElectrica_Espanya::dame_precio_penalizacion_sobrepotencia_Espanya($tipo_tarifa_electrica);
                    if (stripos($tipo_tarifa_electrica, "2025_abril") !== false || stripos($tipo_tarifa_electrica, "2026") !== false) {
                        $precio_penalizacion_sobrepotencia_tramo = $precio_penalizacion_sobrepotencia_tramo[$tramo - 1];
                    }
					$penalizacion_potencia_tarifa = TarifaElectrica_Espanya::dame_penalizacion_potencias_Espanya($tipo_tarifa_electrica);
                    $k_tramo = $penalizacion_potencia_tarifa[$tramo - 1];
                    $info_sobrepotencias_tramos_sensor[$tramo]["coste_sobrepotencia"] =
                        $info_sobrepotencia_tramo_sensor["aei"] * $precio_penalizacion_sobrepotencia_tramo * $k_tramo;
                }
                break;
            }
            //Este caso se ejecuta cuando la potencia es menor de 50kW (suministros con tipo de punto medida 4 y 5)
            case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
            {
                // Se calcula el coste de la sobrepotencia por tramo (puede ser negativo)
                foreach ($potencias_maximas_mensuales_tramos as $tramo => $potencias_maximas_mensuales_tramo)
                {
                    $precio_potencia_tramo = $info_tramos[$tramo]["precio_potencia"];
                    $potencia_contratada_tramo = $info_tramos[$tramo]["potencia"];

                    // Máximo de las potencias máximas mensuales del tramo
                    $max_potencia_maxima_mensual_tramo = 0;
										$exceso_potencia_mes_tramo = 0;
                    foreach ($potencias_maximas_mensuales_tramo as $mes_anyo => $potencia_maxima_mensual_tramo)
                    {
                        if ($potencia_maxima_mensual_tramo > $max_potencia_maxima_mensual_tramo)
                        {
                            $max_potencia_maxima_mensual_tramo = $potencia_maxima_mensual_tramo;
                        }

	                    // Cálculo del coste de la sobrepotencia del tramo
	                    $valor_potencia_maxima_mes_tramo = $max_potencia_maxima_mensual_tramo;
	                    if ($valor_potencia_maxima_mes_tramo > $potencia_contratada_tramo)
	                    {
                            if (stripos($tipo_tarifa_electrica, "2025_abril") !== false || stripos($tipo_tarifa_electrica, "2026") !== false){
                                $exceso_potencia_mes_tramo = $valor_potencia_maxima_mes_tramo - $potencia_contratada_tramo;
                            }
	                        else{
                                $exceso_potencia_mes_tramo = 2 * ($valor_potencia_maxima_mes_tramo - $potencia_contratada_tramo);
                            }
	                    }
	                    $info_sobrepotencias_tramos_sensor[$tramo]["exceso_potencia"] = $exceso_potencia_mes_tramo;

						$precio_penalizacion_sobrepotencia_tramo = TarifaElectrica_Espanya::dame_precio_penalizacion_sobrepotencia_Espanya($tipo_tarifa_electrica);
                        if (stripos($tipo_tarifa_electrica, "2025_abril") !== false || stripos($tipo_tarifa_electrica, "2026") !== false) {
                            $precio_penalizacion_sobrepotencia_tramo = $precio_penalizacion_sobrepotencia_tramo[$tramo - 1];
                        }
						$penalizacion_potencia_tarifa = TarifaElectrica_Espanya::dame_penalizacion_potencias_Espanya($tipo_tarifa_electrica);
	                    $k_tramo = $penalizacion_potencia_tarifa[$tramo - 1];
						if($caracteristicas_tipo_tarifa_electrica["precio_exceso_potencia_dia"])
						{
							$precio_penalizacion_sobrepotencia_tramo = $precio_penalizacion_sobrepotencia_tramo * $numero_dias;
						}
						$info_sobrepotencias_tramos_sensor[$tramo]["coste_sobrepotencia"] =
	                    	$exceso_potencia_mes_tramo * $precio_penalizacion_sobrepotencia_tramo * $k_tramo;
					}
                }
                break;
            }

        }

        // Se añaden las filas de los tramos en los cuales no hay consumo
        foreach ($info_tramos as $tramo => $info_tramo)
        {
            if (array_key_exists($tramo, $info_sobrepotencias_tramos_sensor) == false)
            {
                $info_sobrepotencias_tramos_sensor[$tramo] = array(
                    "tramo" => $tramo,
                    "potencia_tramo" => $info_tramo["potencia"],
                    "precio_potencia_tramo" => $info_tramo["precio_potencia"],
										"hay_consumo" => false);
            }
        }
	}


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_excesos_potencia_electricidad_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_POTENCIAS_POTENCIAS_CONTRATADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_SOBREPOTENCIAS_ABSOLUTAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_TABLA_SOBREPOTENCIAS_TRAMOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_CONSUMOS_ENERGIA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_COSENO_PHI);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_PENALIZABLE);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_TABLA_COSTES_ENERGIA_REACTIVA_TRAMOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_excesos_potencia_electricidad_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_POTENCIAS_POTENCIAS_CONTRATADAS:
            {
                $descripcion = "Gráfica de potencias y potencias contratadas";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_GRAFICA_SOBREPOTENCIAS_ABSOLUTAS:
            {
                $descripcion = "Gráfica de diferencias respecto a la potencia contratada";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ESPANYA_TABLA_SOBREPOTENCIAS_TRAMOS:
            {
                $descripcion = "Tabla de excesos de potencia por tramo";
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


    function dame_descripcion_elemento_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_CONSUMOS_ENERGIA:
            {
                $descripcion = "Gráfica de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_COSENO_PHI:
            {
                $descripcion = "Gráfica de coseno de phi";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_GRAFICA_PENALIZABLE:
            {
                $descripcion = "Gráfica de penalizable";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ESPANYA_TABLA_COSTES_ENERGIA_REACTIVA_TRAMOS:
            {
                $descripcion = "Tabla de energía reactiva por tramo";
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


    function dame_html_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-excesos-potencia'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-excesos-potencia' hidden>
                        <div class='grafica100' id='grafica-potencias-potencias-contratadas-excesos-potencia'></div>
                        <div class='grafica100' id='grafica-sobrepotencias-absolutas-excesos-potencia'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-sobrepotencias-tramos-excesos-potencia'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-excesos-potencia-excesos-potencia'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-excesos-potencia-excesos-potencia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-potencias-potencias-contratadas-excesos-potencia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-sobrepotencias-absolutas-excesos-potencia'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-sobrepotencias-tramos-excesos-potencia'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-excesos-energia-reactiva'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-excesos-energia-reactiva' hidden>
                        <div class='grafica100' id='grafica-consumos-energia-excesos-energia-reactiva'></div>
                        <div class='grafica100' id='grafica-coseno-phi-excesos-energia-reactiva'></div>
                        <div class='grafica100' id='grafica-penalizable-excesos-energia-reactiva'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de excesos de energía reactiva
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-excesos-energia-reactiva-excesos-energia-reactiva'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-excesos-energia-reactiva-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-energia-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coseno-phi-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-penalizable-excesos-energia-reactiva'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-potencias-potencias-contratadas-excesos-potencia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-sobrepotencias-absolutas-excesos-potencia'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-sobrepotencias-tramos-excesos-potencia'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-potencias-potencias-contratadas-excesos-potencia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-sobrepotencias-absolutas-excesos-potencia'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-sobrepotencias-tramos-excesos-potencia'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-energia-excesos-energia-reactiva'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-coseno-phi-excesos-energia-reactiva'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-penalizable-excesos-energia-reactiva'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-energia-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-coseno-phi-excesos-energia-reactiva'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-penalizable-excesos-energia-reactiva'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-tramos-excesos-energia-reactiva'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
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
        $parametros_informe["granularidad"] = $parametros_tipo_elemento["granularidad"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_sobrepotencias_sensor_electricidad_Espanya($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
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

        $datos_elemento = dame_excesos_energia_reactiva_sensor_electricidad_Espanya($parametros_informe);
        return ($datos_elemento);
    }
?>
