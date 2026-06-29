<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/util_facturas_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/TarifaElectrica_Portugal.php');



				//
		    // Funciones de informes
		    //


    function dame_html_informe_tipo_smartmeter_simulador_factura_electricidad_Portugal($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-factura'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-factura' hidden>
                        <div class='titulo-tabla-datos100' id='titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100' id='grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100' id='grafica-porcentajes-reparto-costes-simulador-factura'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de factura (1)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-factura-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_facturas(TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-factura-1'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de simulación de factura (2)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-factura-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_facturas(TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-factura-2'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-reparto-costes-simulador-factura'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }



		//
    // Funciones de información de facturas (electricidad - España)
    //


    // Devuelve la información de simulación de factura de un sensor y tarifa
    function dame_simulacion_factura_sensor_tarifa_electricidad_Portugal($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        if (isset($parametros["exclusion_fechas"]))
        {
            $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        }
        else
        {
            $exclusion_fechas = NULL;
        }

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera el identificador de tarifa eléctrica si no hay tarifa eléctrica seleccionada
        // (realmente está seleccionada la tarifa actual)
        if ($id_tarifa == ID_NINGUNO)
        {
            $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
            $recalcular_costes_energia_activa = VALOR_NO;
        }
        else
        {
            $recalcular_costes_energia_activa = VALOR_SI;
        }
        // Si no hay tarifa, se devuelve error
        if ($id_tarifa == ID_NINGUNO)
        {
            $mensaje_error = $idiomas->_("El sensor no tiene tarifa eléctrica asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        // Información de tarifa
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);

        // Exclusión de fechas
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA,
                "medicion" => MEDICION_ELECTRICIDAD,
                "pais_tarifas" => PAIS_PORTUGAL,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa" => $id_tarifa,
                "recalcular_costes_energia_activa" => $recalcular_costes_energia_activa,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "exclusion_fechas" => $cadena_exclusion_fechas
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si no hay datos de consumo, no se hace nada
        $hay_datos_consumo = $resultado_funcion_externa["hay_datos_consumo"];
        if ($hay_datos_consumo == False)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }
        else
        {
						$resultado = dame_simulacion_factura_electricidad_Portugal($resultado_funcion_externa,$parametros);
        }

        // Se devuelve el resultado
        return ($resultado);
    }


		function dame_simulacion_factura_electricidad_Portugal($resultado_funcion_externa, $parametros)
		{
			$idiomas = new Idiomas();

			$hay_datos_energia_reactiva = false;

			// Características de tipo de la tarifa eléctrica
			//$caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
			// Unidad de medida de coste
			$unidad_medida_coste = $_SESSION["moneda"];

			$datos_tabla_datos = dibuja_tabla_datos_simulacion_factura($parametros, $resultado_funcion_externa);
			$datos_tabla_coste_consumo = dibuja_tabla_coste_consumo_simulacion_factura($resultado_funcion_externa);
			$datos_tabla_energia_activa_array = dibuja_tabla_energia_activa_simulacion_factura($resultado_funcion_externa);
			$datos_tabla_potencia = dibuja_tabla_potencia_simulacion_factura($resultado_funcion_externa);
			if ($resultado_funcion_externa["coste_reactiva_total"] != 0)
			{
				$hay_datos_energia_reactiva = true;
				$datos_tabla_energia_reactiva = dibuja_tabla_energia_reactiva_simulacion_factura($resultado_funcion_externa);
			}
			$datos_tabla_otros_conceptos = dibuja_tabla_otros_conceptos_simulacion_factura($resultado_funcion_externa);
			$grafica_etiqueta_porcentaje_coste_concepto_simulacion_factura = dibuja_grafica_porcentaje_coste_concepto_simulacion_factura($resultado_funcion_externa);

			// Mensaje de aviso
			$msg_aviso = "";
			$avisos = array();

			// Aviso de número de días de factura menor que número de días de consumo de energía activa
			$numero_dias = $resultado_funcion_externa["numero_dias"];
			$numero_dias_consumo_energia_activa = $resultado_funcion_externa["numero_dias_consumo_energia_activa"];

			if ($numero_dias > $numero_dias_consumo_energia_activa)
			{
					$cadena_hora_inicio_consumos_energia_activa_local = $resultado_funcion_externa["hora_inicio_consumos_energia_activa"];
					$cadena_hora_fin_consumos_energia_activa_local = $resultado_funcion_externa["hora_fin_consumos_energia_activa"];

					// Se añade el aviso
					$aviso = $idiomas->_("El número de horas de la factura es mayor que el número de horas de consumo de energía activa")."\n(".
							$idiomas->_("hora de inicio de consumo").": ".$cadena_hora_inicio_consumos_energia_activa_local.", ".
							$idiomas->_("hora de fin de consumo").": ".$cadena_hora_fin_consumos_energia_activa_local.")";
					array_push($avisos, $aviso);
			}

			// Se crea el mensaje de aviso
			$numero_avisos = count($avisos);
			if ($numero_avisos == 1)
			{
					$msg_aviso = $avisos[0];
			}
			else
			{
					foreach ($avisos as $aviso)
					{
							if ($msg_aviso != "")
							{
									$msg_aviso .= "\n";
							}
							$msg_aviso .= "- ".$aviso;
					}
			}

			// Resultado
			$resultado = array(
					"res" => "OK",
					"hay_datos" => true,
					"msg_aviso" => $msg_aviso,
					"tabla_datos" => $datos_tabla_datos,
					"tabla_coste_consumo" => $datos_tabla_coste_consumo,
					"tabla_energia_activa" => $datos_tabla_energia_activa_array[0],
					"tabla_energia_activa_directo" => $datos_tabla_energia_activa_array[1],
					"tabla_energia_activa_tarifa_acceso" => $datos_tabla_energia_activa_array[2],
					"tabla_potencia" => $datos_tabla_potencia,
					//"hay_datos_potencia_maxima_excesos_potencia" => True,
					//"tabla_potencia_maxima_excesos_potencia" => $datos_tabla_potencia_maxima_excesos_potencia,
					"hay_datos_energia_reactiva" => $hay_datos_energia_reactiva,
					"tabla_energia_reactiva" => $datos_tabla_energia_reactiva,
					"tabla_otros_conceptos" => $datos_tabla_otros_conceptos,
					"grafica_porcentajes_costes_conceptos" => $grafica_etiqueta_porcentaje_coste_concepto_simulacion_factura[0],
					"etiquetas_conceptos" => $grafica_etiqueta_porcentaje_coste_concepto_simulacion_factura[1],
					//"hay_datos_reparto_costes" => $hay_datos_reparto_costes,
					//"tabla_reparto_costes" => $datos_tabla_reparto_costes,
					//"grafica_porcentajes_reparto_costes" => $datos_grafica_porcentajes_reparto_costes,
					//"etiquetas_sensores_reparto_costes" => $datos_etiquetas_sensores_reparto_costes,
					"unidad_medida_coste" => $unidad_medida_coste);

			return ($resultado);
		}



		// Tabla de datos de la simulación de factura
		function dibuja_tabla_datos_simulacion_factura($parametros, $resultado_funcion_externa)
		{
				$idiomas = new Idiomas();
				$unidad_medida_coste = $_SESSION["moneda"];

				$cadena_hora_inicio_consumos_energia_activa_local = $resultado_funcion_externa["hora_inicio_consumos_energia_activa"];
				$cadena_hora_fin_consumos_energia_activa_local = $resultado_funcion_externa["hora_fin_consumos_energia_activa"];

				// Recuperamos los parámetros de la simulación.
				$id_sensor = $parametros["id_sensor"];
				$nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $resultado_funcion_externa["id_tarifa"];
				$fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $cadena_fecha_inicio_local_local = $cadena_hora_inicio_consumos_energia_activa_local;
        $cadena_fecha_fin_local_local = $cadena_hora_fin_consumos_energia_activa_local;

				$params_tabla_datos = array(
						"numero_columnas" => NUMERO_COLUMNAS_TABLA_DATOS_FACTURA,
						"generar_valores_xml" => true
				);
				$titulo_tabla_datos = $idiomas->_("Datos");
				$tabla_datos = new TablaDatos(
						"tabla-datos-simulador-factura-electrica",
						$titulo_tabla_datos,
						TIPO_TABLA_DATOS_LISTA,
						$params_tabla_datos
				);


				// Se recupera el CUPS del sensor
				$fila_sensor = dame_fila_sensor($id_sensor);
				$parametros_clase_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_clase']);
				$cups = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

				// Filas de la tabla de datos
				$datos_nombre_sensor = array($idiomas->_("Sensor"), $nombre_sensor);
				if ($cups != "")
				{
						$datos_cups_sensor = array($idiomas->_("CUPS"), $cups);
				}
				$datos_nombre_tarifa = array(
						$idiomas->_("Tarifa"),
						$fila_tarifa_electrica["nombre"]." (".TarifaElectrica_Portugal::dame_descripcion_tipo_tarifa_electrica($tipo_tarifa_electrica).")");
				$datos_fecha_inicio = array($idiomas->_("Fecha de inicio"), $cadena_fecha_inicio_local_local);
				$datos_fecha_fin = array($idiomas->_("Fecha de fin"), $cadena_fecha_fin_local_local);
				$tabla_datos->anyade_fila("fila-nombre-sensor", $datos_nombre_sensor);
				if ($cups != "")
				{
						$tabla_datos->anyade_fila("fila-cups-sensor", $datos_cups_sensor);
				}
				$tabla_datos->anyade_fila("fila-nombre-tarifa", $datos_nombre_tarifa);
				$tabla_datos->anyade_fila("fila-fecha-inicio", $datos_fecha_inicio);
				$tabla_datos->anyade_fila("fila-fecha-fin", $datos_fecha_fin);

				$datos_tabla_datos = $tabla_datos->dame_tabla();
				return ($datos_tabla_datos);
		}

		// Tabla resumen de factura con el coste y el consumo total
		function dibuja_tabla_coste_consumo_simulacion_factura($resultado_funcion_externa)
		{
				$idiomas = new Idiomas();
				$unidad_medida_coste = $_SESSION["moneda"];

			// Tabla coste y consumo (total y diario)
			$params_tabla_coste_consumo = array(
					"numero_columnas" => NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA,
					"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA),
					"generar_valores_xml" => true
			);

			$tabla_coste_consumo = new TablaDatos(
					"tabla-coste-consumo-simulador-factura-electrica",
					$idiomas->_("Coste y consumo"),
					TIPO_TABLA_DATOS_LISTA,
					$params_tabla_coste_consumo
			);
			$cabecera_tabla_coste_consumo = array(
					$idiomas->_("Coste total")." (". $idiomas->_("base imponible").")",
					$idiomas->_("Consumo total"),
					$idiomas->_("Coste diario"),
					$idiomas->_("Consumo diario")
			);
			$tabla_coste_consumo->anyade_cabecera("", $cabecera_tabla_coste_consumo);

			// Costes y consumos
			$coste_total = $resultado_funcion_externa["coste_total"];
			$base_imponible = $resultado_funcion_externa["base_imponible"];
			$consumo_total = $resultado_funcion_externa["consumo_energia_activa_total"];
			$numero_dias = $resultado_funcion_externa["numero_dias"];
			$coste_diario = $coste_total / $numero_dias;
			$consumo_diario = $consumo_total / $numero_dias;

			// Se crean los datos para la fila de la tabla y se añade
			$datos_fila_coste_consumo = array(
					formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste." (".
					formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste.")",
					formatea_numero($consumo_total, 2)." ".$idiomas->_("kWh"),
					formatea_numero($coste_diario, 2, false)." ".$unidad_medida_coste."/".$idiomas->_("día"),
					formatea_numero($consumo_diario, 2)." ".$idiomas->_("kWh")."/".$idiomas->_("día"));
			$tabla_coste_consumo->anyade_fila("fila-general", $datos_fila_coste_consumo);

			// Coste total de la factura
			$pie_tabla_coste_consumo = $idiomas->_("Coste total").": ".formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste;
			$tabla_coste_consumo->anyade_pie($pie_tabla_coste_consumo);

			$datos_tabla_coste_consumo = $tabla_coste_consumo -> dame_tabla();
			return ($datos_tabla_coste_consumo);

		}

		function dibuja_tabla_energia_activa_simulacion_factura($resultado_funcion_externa)
		{
				$idiomas = new Idiomas();
				$unidad_medida_coste = $_SESSION["moneda"];

				// Energía activa
				$numero_dias_consumo_energia_activa = $resultado_funcion_externa["numero_dias_consumo_energia_activa"];
				$datos_energia_activa_tramos = $resultado_funcion_externa["datos_energia_activa_tramos"];

				// Consumo y costes totales de energía activa
				$consumo_energia_activa_total = $resultado_funcion_externa["consumo_energia_activa_total"];
				$coste_energia_activa_total = $resultado_funcion_externa["coste_energia_activa_total"];
				$coste_energia_activa_tarifa_acceso = $resultado_funcion_externa["coste_energia_activa_tarifa_acceso"];
				$coste_energia_activa_directo = $resultado_funcion_externa["coste_energia_activa_directo"];


				// Tablas de energía activa
				$params_tablas_energia_activa = array(
						"numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
						"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
						"generar_valores_xml" => true
				);
				$cabecera_tablas_energia_activa = array(
						$idiomas->_("Tramo"),
						$idiomas->_("Consumo"),
						$idiomas->_("Coste")
				);

				// Se calculan el consumo y el coste total y se añaden datos de energía activa de tramos ordenados a las filas de la tabla
				// (si hay coste de consumo de tarifa de acceso y directo se muestran dos tablas de energía activa)
				$datos_tabla_energia_activa = NULL;
				$datos_tabla_energia_activa_directo = NULL;
				$datos_tabla_energia_activa_tarifa_acceso = NULL;

				// TABLA DE ENERGIA ACTIVA Y DE PEAJE DE ACCESO
				$tabla_energia_activa_directo = new TablaDatos(
						"tabla-energia-activa-directo-simulador-factura-electrica",
						$idiomas->_("Energía activa")." (".$idiomas->_("Energia Activa").")",
						TIPO_TABLA_DATOS_LISTA,
						$params_tablas_energia_activa
				);
				$tabla_energia_activa_directo->anyade_cabecera("", $cabecera_tablas_energia_activa);
				$tabla_energia_activa_tarifa_acceso = new TablaDatos(
						"tabla-energia-activa-tarifa-acceso-simulador-factura-electrica",
						$idiomas->_("Energía activa")." (".$idiomas->_("Redes").")",
						TIPO_TABLA_DATOS_LISTA,
						$params_tablas_energia_activa
				);
				$tabla_energia_activa_tarifa_acceso->anyade_cabecera("", $cabecera_tablas_energia_activa);

				// Se recorren los datos de los tramos
				foreach ($datos_energia_activa_tramos as $tramo => $datos_energia_activa_tramo)
				{
						$nombre_tramo = dame_nombre_tramo($tramo);

						// Consumo, precios y costes
						$consumo_energia_activa_tramo = $datos_energia_activa_tramo["consumo"];
						$precio_energia_activa_directo_tramo = $datos_energia_activa_tramo["precio_consumo_directo"];
						$precio_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["precio_consumo_tarifa_acceso"];
						$coste_energia_activa_directo_tramo = $datos_energia_activa_tramo["coste_directo"];
						$coste_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["coste_tarifa_acceso"];

						if ($coste_energia_activa_directo_tramo == 0)
						{
							$coste_energia_activa_directo_tramo = $precio_energia_activa_directo_tramo * $consumo_energia_activa_tramo;
							$coste_energia_activa_directo += $coste_energia_activa_directo_tramo;
						}
						if ($coste_energia_activa_tarifa_acceso_tramo == 0)
						{
							$coste_energia_activa_tarifa_acceso_tramo = $precio_energia_activa_tarifa_acceso_tramo * $consumo_energia_activa_tramo;
							$coste_energia_activa_tarifa_acceso += $coste_energia_activa_tarifa_acceso_tramo;
						}

						// Se crean los datos para la fila de la tabla y se añade
						$cadena_consumo_tramo = formatea_numero($consumo_energia_activa_tramo, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>";
						$cadena_consumo_directo_tramo = $cadena_consumo_tramo.formatea_numero($precio_energia_activa_directo_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_consumo_tarifa_acceso_tramo = $cadena_consumo_tramo.formatea_numero($precio_energia_activa_tarifa_acceso_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_coste_directo_tramo = formatea_numero($coste_energia_activa_directo_tramo, 2, false)." ".$unidad_medida_coste;
						$cadena_coste_tarifa_acceso_tramo = formatea_numero($coste_energia_activa_tarifa_acceso_tramo, 2, false)." ".$unidad_medida_coste;

						// Se añade la filas de las tablas
						$datos_fila_energia_activa_directo_tramo = array($nombre_tramo, $cadena_consumo_directo_tramo, $cadena_coste_directo_tramo);
						$tabla_energia_activa_directo->anyade_fila("fila-energia-activa-directo-tramo".$tramo, $datos_fila_energia_activa_directo_tramo);
						$datos_fila_energia_activa_tarifa_acceso_tramo = array($nombre_tramo, $cadena_consumo_tarifa_acceso_tramo, $cadena_coste_tarifa_acceso_tramo);
						$tabla_energia_activa_tarifa_acceso->anyade_fila("fila-energia-activa-tarifa-acceso-tramo".$tramo, $datos_fila_energia_activa_tarifa_acceso_tramo);
				}

				// Costes totales de energía activa
				$pie_tabla_energia_activa_directo = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_directo, 2, false)." ".$unidad_medida_coste;
				$tabla_energia_activa_directo->anyade_pie($pie_tabla_energia_activa_directo);
				$pie_tabla_energia_activa_tarifa_acceso = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_tarifa_acceso, 2, false)." ".$unidad_medida_coste;
				$tabla_energia_activa_tarifa_acceso->anyade_pie($pie_tabla_energia_activa_tarifa_acceso);

				// Datos de las tablas de energía activa
				$datos_tabla_energia_activa_directo = $tabla_energia_activa_directo->dame_tabla();
				$datos_tabla_energia_activa_tarifa_acceso = $tabla_energia_activa_tarifa_acceso->dame_tabla();

				$datos_tabla_energia_activa_array=[$datos_tabla_energia_activa, $datos_tabla_energia_activa_directo, $datos_tabla_energia_activa_tarifa_acceso];

				return ($datos_tabla_energia_activa_array);
		}

		function dibuja_tabla_potencia_simulacion_factura($resultado_funcion_externa)
		{
				$idiomas = new Idiomas();
				$unidad_medida_coste = $_SESSION["moneda"];

				// Datos de la tabla de potencias
				$params_tabla_potencia = array(
						"numero_columnas" => NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_PORTUGAL,
						"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_PORTUGAL),
						"generar_valores_xml" => true
				);
				$tabla_potencia = new TablaDatos(
						"tabla-potencia-simulador-factura-electrica",
						$idiomas->_("Potencia"),
						TIPO_TABLA_DATOS_LISTA,
						$params_tabla_potencia
				);

				$cabecera_tabla_potencia = array(
						$idiomas->_("Concepto"),
						$idiomas->_("Potencia"),
						$idiomas->_("Precio"),
						$idiomas->_("Número de días"),
						$idiomas->_("Coste")
				);

				$tabla_potencia->anyade_cabecera("", $cabecera_tabla_potencia);

				// Potencia contratada
				$potencia_contratada = formatea_numero($resultado_funcion_externa["potencia_contratada"], 4)." ".$idiomas->_("kW");
				$precio_potencia_contratada = formatea_numero($resultado_funcion_externa["precio_potencia_contratada"], 6)." ".$unidad_medida_coste;
				$coste_potencia_contratada = formatea_numero($resultado_funcion_externa["coste_potencia_contratada"], 2)." ".$unidad_medida_coste;

				$datos_fila_potencia_ponta = array(
						"Potencia Contratada",
						$potencia_contratada,
						$precio_potencia_contratada,
						$resultado_funcion_externa["numero_dias"] . " ".$idiomas->_("días"),
						$coste_potencia_contratada);
				$tabla_potencia->anyade_fila("fila-potencia-ponta", $datos_fila_potencia_ponta);


				// Potencia en tramo punta
				$potencia_ponta = formatea_numero($resultado_funcion_externa["potencia_ponta"], 4)." ".$idiomas->_("kW");
				$precio_potencia_ponta = formatea_numero($resultado_funcion_externa["precio_potencia_ponta"], 6)." ".$unidad_medida_coste;
				$coste_potencia_ponta = formatea_numero($resultado_funcion_externa["coste_potencia_ponta"], 2)." ".$unidad_medida_coste;

				if ($coste_potencia_ponta != 0)
				{
					$datos_fila_potencia_ponta = array(
							"Potencia Horas de Ponta",
							$potencia_ponta,
							$precio_potencia_ponta,
							$resultado_funcion_externa["numero_dias"] . " ".$idiomas->_("días"),
							$coste_potencia_ponta);
					$tabla_potencia->anyade_fila("fila-potencia-ponta", $datos_fila_potencia_ponta);
				}

				// Coste total de potencia
				$coste_potencia_total = $resultado_funcion_externa["coste_potencia_total"];
				$pie_tabla_potencia = $idiomas->_("Coste total").": ".formatea_numero($coste_potencia_total, 2, false)." ".$unidad_medida_coste;
				$tabla_potencia->anyade_pie($pie_tabla_potencia);

				$datos_tabla_potencia = $tabla_potencia->dame_tabla();
				return ($datos_tabla_potencia);
		}

		function dibuja_tabla_energia_reactiva_simulacion_factura($resultado_funcion_externa)
		{
			$idiomas = new Idiomas();
			$unidad_medida_coste = $_SESSION["moneda"];

			// Energia reactiva
			// Tabla de energía reactiva
			$params_tabla_energia_reactiva = array(
					"numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
					"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
					"generar_valores_xml" => true
					);
			$tabla_energia_reactiva = new TablaDatos(
					"tabla-energia-reactiva-simulador-factura-electrica",
					$idiomas->_("Energía reactiva"),
					TIPO_TABLA_DATOS_LISTA,
					$params_tabla_energia_reactiva
					);
			$cabecera_tabla_energia_reactiva = array(
					$idiomas->_("Tipo de energia reactiva"),
					$idiomas->_("Coseno de phi"),
					$idiomas->_("Exceso"),
					$idiomas->_("Coste")
					);
			$tabla_energia_reactiva->anyade_cabecera("", $cabecera_tabla_energia_reactiva);



			// Energia inductiva
			if ($resultado_funcion_externa["energia_reactiva_inductiva"] != 0)
			{
					$hay_datos_energia_reactiva = true;

					$energia_reactiva_inductiva = formatea_numero($resultado_funcion_externa["energia_reactiva_inductiva"], 4)." ".$idiomas->_("kVArh");
					$coseno_phi = formatea_numero($resultado_funcion_externa["coseno_phi"], 2);
					$exceso_energia_reactiva_tramo_1 = formatea_numero($resultado_funcion_externa["exceso_energia_reactiva_tramo_1"], 6)." ".$idiomas->_("kVArh");
					$exceso_energia_reactiva_tramo_2 = formatea_numero($resultado_funcion_externa["exceso_energia_reactiva_tramo_2"], 6)." ".$idiomas->_("kVArh");
					$exceso_energia_reactiva_tramo_3 = formatea_numero($resultado_funcion_externa["exceso_energia_reactiva_tramo_3"], 6)." ".$idiomas->_("kVArh");
					$coste_reactiva_inductiva_tramo_1 = formatea_numero($resultado_funcion_externa["coste_reactiva_inductiva_tramo_1"], 2)." ".$unidad_medida_coste;
					$coste_reactiva_inductiva_tramo_2 = formatea_numero($resultado_funcion_externa["coste_reactiva_inductiva_tramo_2"], 2)." ".$unidad_medida_coste;
					$coste_reactiva_inductiva_tramo_3 = formatea_numero($resultado_funcion_externa["coste_reactiva_inductiva_tramo_3"], 2)." ".$unidad_medida_coste;
					if ($exceso_energia_reactiva_tramo_1 != 0)
					{
							$datos_fila_energia_reactiva_capacitiva_tramo_1 = array(
									"Inductiva tramo 1",
									$coseno_phi,
									$exceso_energia_reactiva_tramo_1,
									$coste_reactiva_inductiva_tramo_1);
							$tabla_energia_reactiva->anyade_fila("fila-potencia-ponta", $datos_fila_energia_reactiva_capacitiva_tramo_1);
					}
					if ($exceso_energia_reactiva_tramo_2 != 0)
					{
							$datos_fila_energia_reactiva_capacitiva_tramo_2 = array(
									"Inductiva tramo 2",
									$coseno_phi,
									$exceso_energia_reactiva_tramo_2,
									$coste_reactiva_inductiva_tramo_2);
							$tabla_energia_reactiva->anyade_fila("fila-potencia-ponta", $datos_fila_energia_reactiva_capacitiva_tramo_2);
					}
					if ($exceso_energia_reactiva_tramo_3 != 0)
					{
							$datos_fila_energia_reactiva_capacitiva_tramo_3 = array(
									"Inductiva tramo 3",
									$coseno_phi,
									$exceso_energia_reactiva_tramo_3,
									$coste_reactiva_inductiva_tramo_3);
							$tabla_energia_reactiva->anyade_fila("fila-potencia-ponta", $datos_fila_energia_reactiva_capacitiva_tramo_3);
					}
			}

			// Energia capacitiva
			if ($resultado_funcion_externa["energia_reactiva_capacitiva"] != 0)
			{
					$hay_datos_energia_reactiva = true;

					$energia_reactiva_capacitiva = formatea_numero($resultado_funcion_externa["energia_reactiva_capacitiva"], 4)." ".$idiomas->_("kVArh");
					$precio_reactiva_capacitiva = formatea_numero($resultado_funcion_externa["precio_reactiva_capacitiva"], 6)." ".$unidad_medida_coste;
					$coste_reactiva_capacitiva = formatea_numero($resultado_funcion_externa["coste_reactiva_capacitiva"], 2)." ".$unidad_medida_coste;
					$datos_fila_energia_reactiva_capacitiva = array(
							"Capacitiva",
							"",
							$energia_reactiva_capacitiva,
							$coste_reactiva_capacitiva);
					$tabla_energia_reactiva->anyade_fila("fila-potencia-ponta", $datos_fila_energia_reactiva_capacitiva);
			}

			// Coste total de reactiva
			$coste_reactiva_total = $resultado_funcion_externa["coste_reactiva_total"];
			$pie_tabla_reactiva = $idiomas->_("Coste total").": ".formatea_numero($coste_reactiva_total, 2, false)." ".$unidad_medida_coste;
			$tabla_energia_reactiva->anyade_pie($pie_tabla_reactiva);

			$datos_tabla_energia_reactiva = $tabla_energia_reactiva->dame_tabla();
			return ($datos_tabla_energia_reactiva);

		}

		function dibuja_tabla_otros_conceptos_simulacion_factura($resultado_funcion_externa)
		{
			$idiomas = new Idiomas();
			$unidad_medida_coste = $_SESSION["moneda"];

			// Conceptos adicionales de factura
			// Tabla de otros conceptos
			$params_tabla_otros_conceptos = array(
					"numero_columnas" => NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA,
					"anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA),
					"generar_valores_xml" => true
			);
			$tabla_otros_conceptos = new TablaDatos(
					"tabla-otros-conceptos-simulador-factura-electrica",
					$idiomas->_("Otros conceptos"),
					TIPO_TABLA_DATOS_LISTA,
					$params_tabla_otros_conceptos
			);
			$cabecera_tabla_otros_conceptos = array(
					$idiomas->_("Concepto"),
					$idiomas->_("Cálculo"),
					$idiomas->_("Coste")
			);
			$tabla_otros_conceptos->anyade_cabecera("", $cabecera_tabla_otros_conceptos);

			$id_tarifa = $resultado_funcion_externa["id_tarifa"];
			$info_conceptos_adicionales_factura = dame_info_conceptos_adicionales_factura_tarifa(MEDICION_ELECTRICIDAD, $id_tarifa);
			$costes_conceptos_adicionales = $resultado_funcion_externa["costes_conceptos_adicionales"];
			$log = dame_log();
			$log->info("CONCEPTOS ADICIONALES " . count($info_conceptos_adicionales_factura));
			for ($i = 0; $i < count($info_conceptos_adicionales_factura); $i++)
			{
					$info_concepto_adicional_factura = $info_conceptos_adicionales_factura[$i];
					$coste_concepto_adicional_factura = $costes_conceptos_adicionales[$i];

					// Formateado del cálculo según el tipo de concepto adicional
					switch ($info_concepto_adicional_factura["tipo"])
					{
							case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
							{
									$cadena_calculo_coste_concepto_adicional = formatea_numero($info_concepto_adicional_factura["coste"], 2, false)." ".$unidad_medida_coste;
									break;
							}
							case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO:
							{
									$cadena_calculo_coste_concepto_adicional = formatea_numero($info_concepto_adicional_factura["coste"], 2)." ".$unidad_medida_coste."/".$idiomas->_("día").
											"<font class='color-gris-muy-claro'> x </font>".
									$numero_dias." ".$idiomas->_("días");
									break;
							}
					}

					// Se crean los datos para la fila de la tabla y se añade
					$datos_fila_concepto_adicional = array(
							$info_concepto_adicional_factura["nombre"],
							$cadena_calculo_coste_concepto_adicional,
							formatea_numero($coste_concepto_adicional_factura, 2, false)." ".$unidad_medida_coste);
					$tabla_otros_conceptos->anyade_fila("fila-concepto-adicional", $datos_fila_concepto_adicional);
			}

			$impuesto_electrico = $resultado_funcion_externa["impuesto_electrico"];
			$coste_impuesto_electrico = $resultado_funcion_externa["coste_impuesto_electrico"];
			$consumo_energia_activa_total = $resultado_funcion_externa["consumo_energia_activa_total"];
			// Impuesto eléctrico
			if ($impuesto_electrico > 0)
			{
					// Se crean los datos para la fila de la tabla y se añade
					$datos_fila_impuesto_electrico = array(
							$idiomas->_("Impuesto eléctrico"),
							formatea_numero($consumo_energia_activa_total, 2, false)." kWh <font class='color-gris-muy-claro'> x </font>".
									$impuesto_electrico." "."%",
							formatea_numero($coste_impuesto_electrico, 2, false)." ".$unidad_medida_coste);
					$tabla_otros_conceptos->anyade_fila("fila-impuesto-electrico", $datos_fila_impuesto_electrico);
			}

			$contribucion_adiovisual = $resultado_funcion_externa["contribucion_audiovisual"];
			$iva_reducido = $resultado_funcion_externa["iva_reducido"];
			$coste_iva_reducido = $resultado_funcion_externa["coste_iva_reducido"];
			if ($contribucion_adiovisual > 0)
			{
					// Se crean los datos para la fila de la tabla y se añade
					$datos_fila_contribucion_audiovisual = array(
							$idiomas->_("Contribucion Audiovisual"),
							formatea_numero($contribucion_adiovisual, 2, false)." ".$unidad_medida_coste,
							formatea_numero($contribucion_adiovisual, 2, false)." ".$unidad_medida_coste);
					$tabla_otros_conceptos->anyade_fila("fila-contribucion_audiovisual", $datos_fila_contribucion_audiovisual);

					// Se crean los datos para la fila de la tabla y se añade
					$datos_fila_iva_reducido = array(
							$idiomas->_("IVA 6%"),
							formatea_numero($contribucion_adiovisual, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
									$iva_reducido." "."%",
							formatea_numero($coste_iva_reducido, 2, false)." ".$unidad_medida_coste);
					$tabla_otros_conceptos->anyade_fila("fila-iva_reducido", $datos_fila_iva_reducido);
			}

			// Base imponible
			$base_imponible = $resultado_funcion_externa["base_imponible"];
			// IVA
			$iva = $resultado_funcion_externa["iva"];
			$coste_iva = $resultado_funcion_externa["coste_iva"];

			// Se crean los datos para la fila de la tabla y se añade
			$datos_fila_iva = array(
					$idiomas->_("IVA"),
					formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
							$iva." "."%",
					formatea_numero($coste_iva, 2, false)." ".$unidad_medida_coste);
			$tabla_otros_conceptos->anyade_fila("fila-iva", $datos_fila_iva);

			// Coste total de otros conceptos
			$coste_total_otros_conceptos = $resultado_funcion_externa["coste_total_otros_conceptos"];
			$pie_tabla_otros_conceptos = $idiomas->_("Coste total").": ".formatea_numero($coste_total_otros_conceptos, 2, false)." ".$unidad_medida_coste;
			$tabla_otros_conceptos->anyade_pie($pie_tabla_otros_conceptos);

			// Datos de la tabla
			$datos_tabla_otros_conceptos = $tabla_otros_conceptos->dame_tabla();

			return ($datos_tabla_otros_conceptos);
		}


		function dibuja_grafica_porcentaje_coste_concepto_simulacion_factura($resultado_funcion_externa)
		{
			$idiomas = new Idiomas();

			$coste_energia_activa_total = $resultado_funcion_externa["coste_energia_activa_total"];
			$coste_potencia_total = $resultado_funcion_externa["coste_potencia_total"];
			$coste_energia_reactiva_total = $resultado_funcion_externa["coste_reactiva_total"];

			// Gráfica de porcentajes de costes por concepto (y etiquetas de conceptos)
			$grafica_porcentajes_costes_conceptos = new VectorDatos();
			$etiquetas_conceptos = new VectorDatos();
			$datos_porcentajes_costes_conceptos = new VectorDatos();
			$datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Energía activa"), $coste_energia_activa_total);
			$etiquetas_conceptos->anyade_etiqueta($idiomas->_("Energía activa"));
			$datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Potencia"), $coste_potencia_total);
			$etiquetas_conceptos->anyade_etiqueta($idiomas->_("Potencia"));
			if ($coste_energia_reactiva_total != 0)
			{
					$datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Energía reactiva"), $coste_energia_reactiva_total);
					$etiquetas_conceptos->anyade_etiqueta($idiomas->_("Energía reactiva"));
			}
			if ($coste_total_otros_conceptos >= 0)
			{
					$coste_total_otros_conceptos_pocentajes_costes_conceptos = $coste_total_otros_conceptos;
			}
			else
			{
					$coste_total_otros_conceptos_pocentajes_costes_conceptos = 0;
			}
			$datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Otros conceptos"), $coste_total_otros_conceptos_pocentajes_costes_conceptos);
			$etiquetas_conceptos->anyade_etiqueta($idiomas->_("Otros conceptos"));
			$grafica_porcentajes_costes_conceptos->anyade_dato($datos_porcentajes_costes_conceptos->dame_datos());

/*
			// Información de reparto de costes
			$res_informacion_reparto_costes = dame_informacion_reparto_costes_simulacion_factura($parametros, $coste_total);
			$hay_datos_reparto_costes = $res_informacion_reparto_costes["hay_datos_reparto_costes"];
			$datos_tabla_reparto_costes = $res_informacion_reparto_costes["datos_tabla_reparto_costes"];
			$datos_grafica_porcentajes_reparto_costes = $res_informacion_reparto_costes["datos_grafica_porcentajes_reparto_costes"];
			$datos_etiquetas_sensores_reparto_costes = $res_informacion_reparto_costes["datos_etiquetas_sensores_reparto_costes"];
*/
			$datos_grafica_porcentaje_coste_concepto = $grafica_porcentajes_costes_conceptos->dame_datos();
			$datos_etiqueta_porcentaje_coste_concepto = $etiquetas_conceptos->dame_datos();
			$grafica_etiqueta_porcentaje_coste_concepto_simulacion_factura=[$datos_grafica_porcentaje_coste_concepto, $datos_etiqueta_porcentaje_coste_concepto];

			return ($grafica_etiqueta_porcentaje_coste_concepto_simulacion_factura);

		}

		// Función para representar el nombre de los tramos
		function dame_nombre_tramo($tramo)
		{
				$nombre_tramo = "";
				if ($tramo == 1)
					{
						$nombre_tramo = "Ponta";
					}
				else if ($tramo == 2)
				{
						$nombre_tramo = "Cheia";
				}
				else if ($tramo == 3)
				{
						$nombre_tramo = "Vazio Normal";
				}
				else if ($tramo == 4)
				{
						$nombre_tramo = "Super Vazio";
				}
				else{
						$nombre_tramo = "Desconocido";
				}
				return ($nombre_tramo);
		}

/*
            // Coste total de energía y potencia (sin otros conceptos)
            $coste_energia_potencia_total = $resultado_funcion_externa["coste_energia_potencia_total"];

            // Concepto pendiente MEFF REE
            if (array_key_exists("coste_concepto_pendiente_MEFF_REE", $resultado_funcion_externa) == true)
            {
                $hay_concepto_pendiente_MEFF_REE = true;
            }
            else
            {
                $hay_concepto_pendiente_MEFF_REE = false;
            }

            // Otros conceptos

            // Concepto pendiente 'MEFF-REE'
            if ($hay_concepto_pendiente_MEFF_REE == true)
            {
                // Se crean los datos para la fila de la tabla y se añade
                if ($coste_concepto_pendiente_MEFF_REE !== NULL)
                {
                    $cadena_coste_concepto_pendiente_MEFF_REE = formatea_numero($coste_concepto_pendiente_MEFF_REE, 2, false)." ".$unidad_medida_coste;
                }
                else
                {
                    $cadena_coste_concepto_pendiente_MEFF_REE = $idiomas->_("ND");
                }
                $datos_fila_concepto_pendiente_MEFF_REE = array(
                    $idiomas->_("Pendiente MEFF-REE"),
                    $cadena_coste_concepto_pendiente_MEFF_REE,
                    $cadena_coste_concepto_pendiente_MEFF_REE);
                $tabla_otros_conceptos->anyade_fila("fila-concepto-pendiente-MEFF-REE", $datos_fila_concepto_pendiente_MEFF_REE);
            }





*/

    //
    // Funciones auxiliares
    //

/*
    // Devuelve el coste de un concepto de simulación de factura de un sensor y tarifa
    function dame_coste_concepto_simulacion_factura_sensor_electricidad_Espanya($parametros)
    {
        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $concepto_factura = $parametros["concepto_factura"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];

        // Se recupera la tarifa eléctrica del sensor especificado
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
        if ($id_tarifa == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "tarifa_asignada" => false,
                "hay_datos" => false);
        }
        else
        {
            // Conversión de fechas
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
            $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA,
                    "medicion" => MEDICION_ELECTRICIDAD,
                    "pais_tarifas" => PAIS_ESPANYA,
                    "nombre_sensor" => $nombre_sensor,
                    "id_red" => $_SESSION["id_red"],
                    "id_tarifa" => $id_tarifa,
                    "recalcular_costes_energia_activa" => VALOR_NO,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                    "exclusion_fechas" => ""
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si no hay datos de consumo, no se hace nada
            $hay_datos_consumo = $resultado_funcion_externa["hay_datos_consumo"];
            if ($hay_datos_consumo == False)
            {
                $resultado = array(
                    "res" => "OK",
                    "tarifa_asignada" => true,
                    "hay_datos" => false);
            }
            else
            {
                // Se recupera el coste del concepto especificado de la factura eléctrica
                $coste_concepto_factura = NULL;
                switch ($concepto_factura)
                {
                    case CONCEPTO_FACTURA_ELECTRICA_TOTAL_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_POTENCIA_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_energia_potencia_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_ACTIVA_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_energia_activa_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_POTENCIA_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_potencia_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_EXCESOS_POTENCIA_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_excesos_potencia_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_REACTIVA_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_energia_reactiva_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_ELECTRICA_OTROS_CONCEPTOS_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_total_otros_conceptos"];
                        break;
                    }
                    default:
                    {
                        throw new Exception("Concepto de factura eléctrica desconocido: '".$concepto_factura."'");
                    }
                }

                // Unidad de medida de coste
                $unidad_medida_coste = $_SESSION["moneda"];

                // Resultado
                $resultado = array(
                    "res" => "OK",
                    "tarifa_asignada" => true,
                    "hay_datos" => true,
                    "coste_concepto_factura" => $coste_concepto_factura,
                    "unidad_medida_coste" => $unidad_medida_coste);
            }
        }

        // Se devuelve el resutlado
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_simulador_factura_electricidad_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_RESUMEN);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_COSTE_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DETALLES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_ACTIVA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_REACTIVA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_OTROS_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_simulacion_factura_electricidad_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DATOS:
            {
                $descripcion = "Título de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_DATOS:
            {
                $descripcion = "Tabla de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_RESUMEN:
            {
                $descripcion = "Título de resumen de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_COSTE_CONSUMO:
            {
                $descripcion = "Tabla de coste y consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DETALLES:
            {
                $descripcion = "Título de detalles de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_ACTIVA:
            {
                $descripcion = "Tabla de energía activa";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA:
            {
                $descripcion = "Tabla de potencia";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA:
            {
                $descripcion = "Tabla de potencia máxima y excesos de potencia";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_REACTIVA:
            {
                $descripcion = "Tabla de energía reactiva";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_OTROS_CONCEPTOS:
            {
                $descripcion = "Tabla de otros conceptos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS:
            {
                $descripcion = "Gráfica de porcentajes de costes por concepto";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_REPARTO_COSTES:
            {
                $descripcion = "Título de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES:
            {
                $descripcion = "Tabla de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES:
            {
                $descripcion = "Gráfica de porcentajes de reparto de costes";
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
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifa-asignada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifa asignada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-reparto-costes-simulador-factura'></div>
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifa-asignada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifa asignada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-reparto-costes-simulador-factura'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Parámetros de tipo de elemento
        $medicion = $parametros_tipo_elemento["medicion"];
        $id_sensor = $parametros_tipo_elemento["id_sensor"];
        $id_tarifa = $parametros_tipo_elemento["id_tarifa"];
        $ids_sensores_reparto_costes = $parametros_tipo_elemento["ids_sensores_reparto_costes"];
        if ($parametros_tipo_elemento["exclusion_fechas"] !== NULL)
        {
            $cadena_exclusion_fechas_json = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        }
        else
        {
            $cadena_exclusion_fechas_json = "";
        }

        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($id_sensor == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        // Se comprueba si el sensor tiene tarifa asignada (si es necesario)
        if ($id_tarifa == ID_NINGUNO)
        {
            $id_tarifa_sensor = dame_id_tarifa_id_sensor($id_sensor);
            if ($id_tarifa_sensor == ID_NINGUNO)
            {
                $resultado = array(
                    "res" => "OK",
                    "sin_tarifa_asignada" => true);
                return ($resultado);
            }
        }

        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $nombres_sensores_reparto_costes = dame_nombres_sensores($ids_sensores_reparto_costes);
        $parametros_informe["medicion"] = $medicion;
        $parametros_informe["id_sensor"] = $id_sensor;
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["id_tarifa"] = $id_tarifa;
        $parametros_informe["ids_sensores_reparto_costes"] = $ids_sensores_reparto_costes;
        $parametros_informe["nombres_sensores_reparto_costes"] = $nombres_sensores_reparto_costes;
        $parametros_informe["exclusion_fechas"] = $cadena_exclusion_fechas_json;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_simulacion_factura_sensor_tarifa_electricidad_Espanya($parametros_informe);
        return ($datos_elemento);
    }*/


?>
