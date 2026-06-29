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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');


    //
    // Funciones de información de facturas (gas - España)
    //


    // Devuelve la información de simulación de factura de un sensor y tarifa
    function dame_simulacion_factura_sensor_tarifa_gas_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        // TO DO
        // EMG_ACCIONA : Si el cliente es acciona, establecemos las 00 como fecha de inicio de la factura.
        // Como en el python directamente aplica un +6 a la hora, lo que hacemos es restarle 6 horas para la simulación y así se hará el cálculo con las 00:00

        $id_red = $_SESSION["id_red"];
        $bd_red = BaseDatosRed::dame_base_datos();
        $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
        $res = $bd_red->ejecuta_consulta($consulta);
        $fila = $res->dame_siguiente_fila();
        $nombre_cliente = $fila["nombre"];

        if($nombre_cliente == 'Acciona')
        {
            $zona_horaria = dame_zona_horaria_local();
            $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"],$zona_horaria);
            $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"],$zona_horaria);

            date_modify($fecha_hora_inicio_local,'-6 hour');
            date_modify($fecha_hora_fin_local,'-6 hour');

            $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

            $parametros_informe["fecha_hora_inicio"] = $cadena_fecha_hora_inicio_local_local;
            $parametros_informe["fecha_hora_fin"] = $cadena_fecha_hora_fin_local_local;
        }

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

        // Se recupera el identificador de tarifa de gas si no hay tarifa de gas seleccionada
        if ($id_tarifa == ID_NINGUNO)
        {
            $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
            $recalcular_consumos_costes = VALOR_NO;
        }
        else
        {
            $recalcular_consumos_costes = VALOR_SI;
        }

        // Si no hay tarifa, se devuelve error
        if ($id_tarifa == ID_NINGUNO)
        {
            $mensaje_error = $idiomas->_("El sensor no tiene tarifa de gas asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }

        // Información de tarifa
        $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_gas = $fila_tarifa_gas["tipo"];

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
                "medicion" => MEDICION_GAS,
                "pais_tarifas" => PAIS_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa" => $id_tarifa,
                "recalcular_consumos_costes" => $recalcular_consumos_costes,
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
            // Tabla de datos
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
            $cups = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];

            // Filas de la tabla de datos
            $datos_nombre_sensor = array($idiomas->_("Sensor"), $nombre_sensor);
            if ($cups != "")
            {
                $datos_cups_sensor = array($idiomas->_("CUPS"), $cups);
            }
            $datos_nombre_tarifa = array(
                $idiomas->_("Tarifa"),
                $fila_tarifa_gas["nombre"]." (".TarifaGas_Espanya::dame_descripcion_tipo_tarifa_gas($tipo_tarifa_gas).")");
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

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Número de días
            $numero_dias = $resultado_funcion_externa["numero_dias"];

            // Consumo y coste de consumo
            $numero_dias_consumo_gas = $resultado_funcion_externa["numero_dias_consumo_gas"];
            $cadena_hora_inicio_consumos_gas_funciones_utc = $resultado_funcion_externa["hora_inicio_consumos_gas"];
            $cadena_hora_fin_consumos_gas_funciones_utc = $resultado_funcion_externa["hora_fin_consumos_gas"];
            $factor_conversion = $resultado_funcion_externa["factor_conversion"];
            $precio_consumo = $resultado_funcion_externa["precio_consumo"];
            $incremento = $resultado_funcion_externa["incremento"];
            $consumo = $resultado_funcion_externa["consumo"];
            $coste_consumo = $resultado_funcion_externa["coste_consumo"];

            // Tabla de consumo
            $params_tabla_consumo = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_consumo = new TablaDatos(
                "tabla-consumo-simulador-factura-gas",
                $idiomas->_("Consumo"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_consumo
            );
            $cabecera_tabla_consumo = array(
                $idiomas->_("Consumo")." (".$idiomas->_("volumen").")",
                $idiomas->_("Consumo")." (".$idiomas->_("energía").")",
                $idiomas->_("Coste"),
            );
            $tabla_consumo->anyade_cabecera("", $cabecera_tabla_consumo);

            // Se crean los datos para la fila de la tabla y se añade
            $datos_fila_consumo = array(
                formatea_numero($incremento, 2)." ".$idiomas->_("m3")."<font class='color-gris-muy-claro'> x </font>".
                    formatea_numero($factor_conversion, 4)." ".$idiomas->_("kWh")."/".$idiomas->_("m3"),
                formatea_numero($consumo, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>".
                    formatea_numero($precio_consumo, 8)." ".$idiomas->_("€")."/".$idiomas->_("kWh"),
                formatea_numero($coste_consumo, 2, false)." ".$unidad_medida_coste);
            $tabla_consumo->anyade_fila("fila-consumo", $datos_fila_consumo);

            // Coste de consumo
            $pie_tabla_consumo = $idiomas->_("Coste").": ".formatea_numero($coste_consumo, 2, false)." ".$unidad_medida_coste;
            $tabla_consumo->anyade_pie($pie_tabla_consumo);

            // Término fijo
            $tipo_calculo_coste_termino_fijo = $resultado_funcion_externa["tipo_calculo_coste_termino_fijo"];
            $datos_caudales = $resultado_funcion_externa["datos_caudales"];

            // Tabla de término fijo
            switch ($tipo_calculo_coste_termino_fijo)
            {
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_EXCESOS_MAXIMOS_MENSUALES_ESPANYA);
                    break;
                }
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_SIN_EXCESOS_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_SIN_EXCESOS_ESPANYA);
					break;
                }
				case TIPO_CALCULO_COSTE_TARIFAS_2021:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA);
                    break;
                }
				case TIPO_CALCULO_COSTE_POR_CLIENTE:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_POR_CLIENTE;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_TERMINO_FIJO_SENSOR_TARIFA_GAS_TARIFAS_2021_ESPANYA);
                    break;
                }
            }

            $params_tabla_termino_fijo = array(
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "generar_valores_xml" => true
            );
            $tabla_termino_fijo = new TablaDatos(
                "tabla-termino-fijo-simulador-factura-gas",
                $idiomas->_("Término fijo"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_termino_fijo
            );
            switch ($tipo_calculo_coste_termino_fijo)
            {
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $cabecera_tabla_termino_fijo_sensor_gas = array(
                        $idiomas->_("Caudal diario contratado"),
                        $idiomas->_("Caudal diario máximo"),
                        $idiomas->_("Caudal diario facturado"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
                {
                    $cabecera_tabla_termino_fijo_sensor_gas = array(
                        $idiomas->_("Término fijo"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
				case TIPO_CALCULO_COSTE_TARIFAS_2021:
                {
					$cabecera_tabla_termino_fijo_sensor_gas = array(
						$idiomas->_("Caudal diario contratado"),
						$idiomas->_("Cálculo"),
						$idiomas->_("Coste")
					);
                break;
                }
				case TIPO_CALCULO_COSTE_POR_CLIENTE:
                {
					$cabecera_tabla_termino_fijo_sensor_gas = array(
						$idiomas->_("Término fijo por cliente (€/cliente y año)"),
						$idiomas->_("Cálculo"),
						$idiomas->_("Coste")
					);
                break;
                }

            }
            $tabla_termino_fijo->anyade_cabecera("", $cabecera_tabla_termino_fijo_sensor_gas);

            // Se añade la información del término fijo a la tabla
            $caudal_diario_contratado = $datos_caudales["caudal_diario_contratado"];
            $caudal_diario_maximo = $datos_caudales["caudal_diario_maximo"];
            $caudal_diario_facturado = $datos_caudales["caudal_diario_facturado"];
            $precio_caudal_diario = $datos_caudales["precio_caudal_diario"];
            $precio_termino_fijo_diario = $datos_caudales["precio_termino_fijo_diario"];
            $coste_termino_fijo = $resultado_funcion_externa["coste_termino_fijo"];
			$coste_capacidad_demandada = $datos_caudales["coste_capacidad_demandada"];

            // Se crean los datos para la fila de la tabla y se añade
            switch ($tipo_calculo_coste_termino_fijo)
            {
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $datos_fila_termino_fijo = array(
                        formatea_numero($caudal_diario_contratado, 2)." ".$idiomas->_("kWh"),
                        formatea_numero($caudal_diario_maximo, 2)." ".$idiomas->_("kWh"),
                        formatea_numero($caudal_diario_facturado, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>".
                            formatea_numero($precio_caudal_diario, 8)." ".$idiomas->_("€")."/".$idiomas->_("kWh")."-".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
                            $numero_dias." ".$idiomas->_("días"),
                        formatea_numero($coste_termino_fijo, 2, false)." ".$unidad_medida_coste);
                    break;
                }
                case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
                {
                    $datos_fila_termino_fijo = array(
                        formatea_numero($precio_termino_fijo_diario, 8)." ".$idiomas->_("€")."-".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
                            $numero_dias." ".$idiomas->_("días"),
                        formatea_numero($coste_termino_fijo, 2, false)." ".$unidad_medida_coste);
                    break;
                }
				case TIPO_CALCULO_COSTE_TARIFAS_2021:
                {
					$datos_fila_termino_fijo = array(
						formatea_numero($caudal_diario_contratado, 2)." ".$idiomas->_("kWh"),
						formatea_numero($caudal_diario_contratado, 2)." ".$idiomas->_("kWh"). "<font class='color-gris-muy-claro'> x </font>".formatea_numero($precio_termino_fijo_diario, 8)." ".$idiomas->_("€")."/".$idiomas->_("kWh")."-".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
							$numero_dias." ".$idiomas->_("días"),
						formatea_numero($coste_termino_fijo, 2, false)." ".$unidad_medida_coste);
					break;
                }
				case TIPO_CALCULO_COSTE_POR_CLIENTE:
                {
					$datos_fila_termino_fijo = array(
						formatea_numero($precio_caudal_diario, 10)." ".$idiomas->_("€")."/".$idiomas->_("cliente")."/".$idiomas->_("año"),
						formatea_numero($precio_termino_fijo_diario, 8)." ".$idiomas->_("€")."/".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
							$numero_dias." ".$idiomas->_("días"),
						formatea_numero($coste_termino_fijo, 2, false)." ".$unidad_medida_coste);
					break;
                }
            }
            $tabla_termino_fijo->anyade_fila("fila-termino-fijo", $datos_fila_termino_fijo);

            // Coste de término fijo
            $pie_tabla_termino_fijo = $idiomas->_("Coste").": ".formatea_numero($coste_termino_fijo, 2, false)." ".$unidad_medida_coste;
            $tabla_termino_fijo->anyade_pie($pie_tabla_termino_fijo);

            // Coste de consumo y término fijo (sin otros conceptos)
            $coste_consumo_termino_fijo = $resultado_funcion_externa["coste_consumo_termino_fijo"];


			// Exceso de caudal (Capacidad demandada)
			$mostrar_tabla_capacidad_demandada = false;
			$numero_columnas = NUMERO_COLUMNAS_TABLA_CAPACIDAD_DEMANDADA_TARIFA_GAS_TARIFAS_2021_ESPANYA;
			$anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_CAPACIDAD_DEMANDADA_TARIFA_GAS_TARIFAS_2021_ESPANYA);
			$params_tabla_capacidad_demandada = array(
				"numero_columnas" => $numero_columnas,
				"anchuras_columnas" => $anchuras_columnas,
				"generar_valores_xml" => true
			);
			$tabla_capacidad_demandada = new TablaDatos(
				"tabla-capacidad-demandada-simulador-factura-gas",
				$idiomas->_("Capacidad demandada"),
				TIPO_TABLA_DATOS_LISTA,
				$params_tabla_capacidad_demandada
			);
			if ($tipo_calculo_coste_termino_fijo == TIPO_CALCULO_COSTE_TARIFAS_2021)
			{
				$mostrar_tabla_capacidad_demandada = true;

				$cabecera_tabla_capacidad_demandada = array(
					$idiomas->_("Caudal máximo"),
					$idiomas->_("Exceso de caudal"),
					$idiomas->_("Coste")
				);
				$tabla_capacidad_demandada->anyade_cabecera("", $cabecera_tabla_capacidad_demandada);
				$datos_fila_capacidad_demandada = array(
					formatea_numero($caudal_diario_maximo, 2)." ".$idiomas->_("kWh"),
					formatea_numero($caudal_diario_facturado, 2)." ".$idiomas->_("kWh"). "<font class='color-gris-muy-claro'> x </font>".formatea_numero($precio_caudal_diario, 8)." ".$idiomas->_("€")."/".$idiomas->_("kWh")."-".$idiomas->_("día"),
					formatea_numero($coste_capacidad_demandada, 2, false)." ".$unidad_medida_coste);
				$tabla_capacidad_demandada->anyade_fila("fila-capacidad-demandada", $datos_fila_capacidad_demandada);

				$pie_tabla_capacidad_demandada = $idiomas->_("Coste").": ".formatea_numero($coste_capacidad_demandada, 2, false)." ".$unidad_medida_coste;
			    $tabla_capacidad_demandada->anyade_pie($pie_tabla_capacidad_demandada);

			}

            // Otros conceptos
            $impuesto_gas = $resultado_funcion_externa["impuesto_gas"];
            $tipo_alquiler_contador = $resultado_funcion_externa["tipo_alquiler_contador"];
            $alquiler_contador = $resultado_funcion_externa["alquiler_contador"];
            $costes_conceptos_adicionales = $resultado_funcion_externa["costes_conceptos_adicionales"];
            $iva = $resultado_funcion_externa["iva"];
            $coste_impuesto_gas = $resultado_funcion_externa["coste_impuesto_gas"];
            $coste_alquiler_contador = $resultado_funcion_externa["coste_alquiler_contador"];
            $coste_iva = $resultado_funcion_externa["coste_iva"];
            $coste_total_otros_conceptos = $resultado_funcion_externa["coste_total_otros_conceptos"];

            // Tabla de otros conceptos
            $params_tabla_otros_conceptos = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_GAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_GAS_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_otros_conceptos = new TablaDatos(
                "tabla-otros-conceptos-simulador-factura-gas",
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

            // Impuesto de gas
            if ($impuesto_gas > 0)
            {
                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_impuesto_gas = array(
                    $idiomas->_("Impuesto de gas"),
                    formatea_numero($consumo, 6)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>".
                        formatea_numero($impuesto_gas, 5)." ".$idiomas->_("€")."/".$idiomas->_("kWh"),
                    formatea_numero($coste_impuesto_gas, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-impuesto-gas", $datos_fila_impuesto_gas);
            }

            // Alquiler del contador
            if ($alquiler_contador > 0)
            {
                // Coste del alquiler del contador
                switch ($tipo_alquiler_contador)
                {
                    case TIPO_ALQUILER_CONTADOR_DIARIO:
                    {
                        $texto_calculo_alquiler_contador = formatea_numero($alquiler_contador, 2, false)." ".$unidad_medida_coste."/".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
                            $numero_dias." ".$idiomas->_("días");
                        break;
                    }
                    case TIPO_ALQUILER_CONTADOR_FIJO:
                    {
                        $texto_calculo_alquiler_contador = formatea_numero($alquiler_contador, 2, false)." ".$unidad_medida_coste;
                        break;
                    }
                }

                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_alquiler_contador = array(
                    $idiomas->_("Alquiler de contador"),
                    $texto_calculo_alquiler_contador,
                    formatea_numero($coste_alquiler_contador, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-alquiler-contador", $datos_fila_alquiler_contador);
            }
            else
            {
                $coste_alquiler_contador = 0;
            }

            // Conceptos adicionales de factura
            $info_conceptos_adicionales_factura = dame_info_conceptos_adicionales_factura_tarifa(MEDICION_GAS, $id_tarifa);
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

            // Base imponible
            $base_imponible =
                $coste_consumo_termino_fijo +
                $coste_impuesto_gas +
                $coste_capacidad_demandada +
                $coste_alquiler_contador;

            // Corregido 05-05-2026 V.M: No se sumaban los costes de los conceptos adicionales a la base imponible
            // Añadido igual que modificación C.V 30-03-2026 en util_informes_factura_agua_Espanya.php
            for ($i = 0; $i < count($info_conceptos_adicionales_factura); $i++) {
                $base_imponible += $costes_conceptos_adicionales[$i];
            }

            // IVA
            if ($iva > 0)
            {
                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_iva = array(
                    $idiomas->_("IVA"),
                    formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
                        $iva." "."%",
                    formatea_numero($coste_iva, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-iva", $datos_fila_iva);
            }

            // Coste total de otros conceptos
            $pie_tabla_otros_conceptos = $idiomas->_("Coste total").": ".formatea_numero($coste_total_otros_conceptos, 2, false)." ".$unidad_medida_coste;
            $tabla_otros_conceptos->anyade_pie($pie_tabla_otros_conceptos);

            // Datos de la tabla
            $datos_tabla_otros_conceptos = $tabla_otros_conceptos->dame_tabla();

            // Tabla coste y consumo (total y diario)
            $params_tabla_coste_consumo = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_GAS_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_coste_consumo = new TablaDatos(
                "tabla-coste-consumo-simulador-factura-gas",
                $idiomas->_("Coste y consumo"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_coste_consumo
            );
            $cabecera_tabla_coste_consumo = array(
                $idiomas->_("Coste total")." (". $idiomas->_("base imponible").")",
                $idiomas->_("Consumo"),
                $idiomas->_("Coste diario"),
                $idiomas->_("Consumo diario")
            );
            $tabla_coste_consumo->anyade_cabecera("", $cabecera_tabla_coste_consumo);

            // Costes y consumos
            $coste_total = $resultado_funcion_externa["coste_total"];
            $coste_diario = $coste_total / $numero_dias;
            $consumo_diario = $consumo / $numero_dias;

            // Se crean los datos para la fila de la tabla y se añade
            $datos_fila_coste_consumo = array(
                formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste." (".
                    formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste.")",
                formatea_numero($consumo, 2)." ".$idiomas->_("kWh"),
                formatea_numero($coste_diario, 2, false)." ".$unidad_medida_coste."/".$idiomas->_("día"),
                formatea_numero($consumo_diario, 2)." ".$idiomas->_("kWh")."/".$idiomas->_("día"));
            $tabla_coste_consumo->anyade_fila("fila-general", $datos_fila_coste_consumo);

            // Coste total de la factura
            $pie_tabla_coste_consumo = $idiomas->_("Coste total").": ".formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste;
            $tabla_coste_consumo->anyade_pie($pie_tabla_coste_consumo);

            // Gráfica de porcentajes de costes por concepto (y etiquetas de conceptos)
            $grafica_porcentajes_costes_conceptos = new VectorDatos();
            $etiquetas_conceptos = new VectorDatos();
            $datos_porcentajes_costes_conceptos = new VectorDatos();
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Consumo"), $coste_consumo);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Consumo"));
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Término fijo"), $coste_termino_fijo);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Término fijo"));
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Otros conceptos"), $coste_total_otros_conceptos);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Otros conceptos"));
            $grafica_porcentajes_costes_conceptos->anyade_dato($datos_porcentajes_costes_conceptos->dame_datos());

            // Información de reparto de costes
            $res_informacion_reparto_costes = dame_informacion_reparto_costes_simulacion_factura($parametros, $coste_total);
            $hay_datos_reparto_costes = $res_informacion_reparto_costes["hay_datos_reparto_costes"];
            $datos_tabla_reparto_costes = $res_informacion_reparto_costes["datos_tabla_reparto_costes"];
            $datos_grafica_porcentajes_reparto_costes = $res_informacion_reparto_costes["datos_grafica_porcentajes_reparto_costes"];
            $datos_etiquetas_sensores_reparto_costes = $res_informacion_reparto_costes["datos_etiquetas_sensores_reparto_costes"];

            // Mensaje de aviso
            $msg_aviso = "";
            $avisos = array();

            // Aviso de número de días de factura menor que número de días de consumo de gas
            if ($numero_dias > $numero_dias_consumo_gas)
            {
                // Conversión de fechas
                $cadena_hora_inicio_consumos_gas_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_consumos_gas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_fin_consumos_gas_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_consumos_gas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_inicio_consumos_gas_local_local = convierte_formato_fecha($cadena_hora_inicio_consumos_gas_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_fin_consumos_gas_local_local = convierte_formato_fecha($cadena_hora_fin_consumos_gas_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);

                // Se añade el aviso
                $aviso = $idiomas->_("El número de horas de la factura es mayor que el número de horas de consumo de gas")."\n(".
                    $idiomas->_("hora de inicio de consumo").": ".$cadena_hora_inicio_consumos_gas_local_local.", ".
                    $idiomas->_("hora de fin de consumo").": ".$cadena_hora_fin_consumos_gas_local_local.")";
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

            // Mensaje de información
            $msg_informacion = "";
            if ($msg_aviso == "")
            {
                $msg_informacion = $idiomas->_("La factura de gas no se calcula con días naturales")."\n(".
                    $idiomas->_("se calcula a partir de las 06:00 de la fecha inicial hasta las 05:59 del día posterior a la fecha final").")";
            }

            // Resultado
            $resultado = array(
                "res" => "OK",
                "hay_datos" => true,
                "msg_aviso" => $msg_aviso,
                "tabla_datos" => $tabla_datos->dame_tabla(),
                "msg_informacion" => $msg_informacion,
                "tabla_coste_consumo" => $tabla_coste_consumo->dame_tabla(),
                "tabla_consumo" => $tabla_consumo->dame_tabla(),
                "tabla_termino_fijo" => $tabla_termino_fijo->dame_tabla(),
				"tabla_capacidad_demandada" => $tabla_capacidad_demandada->dame_tabla(),
				"mostrar_tabla_capacidad_demandada" => $mostrar_tabla_capacidad_demandada,
				"tabla_otros_conceptos" => $datos_tabla_otros_conceptos,
                "grafica_porcentajes_costes_conceptos" => $grafica_porcentajes_costes_conceptos->dame_datos(),
                "etiquetas_conceptos" => $etiquetas_conceptos->dame_datos(),
                "hay_datos_reparto_costes" => $hay_datos_reparto_costes,
                "tabla_reparto_costes" => $datos_tabla_reparto_costes,
                "grafica_porcentajes_reparto_costes" => $datos_grafica_porcentajes_reparto_costes,
                "etiquetas_sensores_reparto_costes" => $datos_etiquetas_sensores_reparto_costes,
                "unidad_medida_coste" => $unidad_medida_coste);
        }

        // Se devuelve el resultado
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve el coste de un concepto de simulación de factura de un sensor y tarifa
    function dame_coste_concepto_simulacion_factura_sensor_gas_Espanya($parametros)
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
                    "medicion" => MEDICION_GAS,
                    "pais_tarifas" => PAIS_ESPANYA,
                    "nombre_sensor" => $nombre_sensor,
                    "id_red" => $_SESSION["id_red"],
                    "id_tarifa" => $id_tarifa,
                    "recalcular_consumos_costes" => VALOR_NO,
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
                // Se recupera el coste del concepto especificado de la factura de gas
                $coste_concepto_factura = NULL;
                switch ($concepto_factura)
                {
                    case CONCEPTO_FACTURA_GAS_TOTAL_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_total"];
                        break;
                    }
                    case CONCEPTO_FACTURA_GAS_CONSUMO_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_consumo"];
                        break;
                    }
                    case CONCEPTO_FACTURA_GAS_TERMINO_FIJO_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_termino_fijo"];
                        break;
                    }
                    case CONCEPTO_FACTURA_GAS_EXCESOS_CAUDAL_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_excesos_caudal"];
                        break;
                    }
                    case CONCEPTO_FACTURA_GAS_OTROS_CONCEPTOS_ESPANYA:
                    {
                        $coste_concepto_factura = $resultado_funcion_externa["coste_total_otros_conceptos"];
                        break;
                    }
                    default:
                    {
                        throw new Exception("Concepto de factura de gas desconocido: '".$concepto_factura."'");
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


    function dame_elementos_informe_smartmeter_simulador_factura_gas_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_RESUMEN);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_COSTE_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DETALLES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_TERMINO_FIJO);
        
        // 05-05-2026 VM: Se deja esta línea comentada al unificar este archivo en todos los servidores.
        // El servidor 301 era el único que tenía esta línea sin comentar. En los otros servidores no aparece.
        // Esta línea permite que en la configuración de la plantilla del informe automático salga disponible para seleccionar
        // este apartado de capacidad demanndada.
        // Por lo observado, este apartado se muestra en el informe cuando la tarifa es por capacidad se seleccione para mostrar o no se seleccione.
        // Por eso, quizás, se quitó en el pasado para que el cliente no se confundiera al ver que la selección de este apartado no tenía efecto.
        // Se debería condicionar la aparición de este apartado a la selección en la plantilla del informe.
        //array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_CAPACIDAD_DEMANDADA);
        
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_OTROS_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_simulacion_factura_gas_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DATOS:
            {
                $descripcion = "Título de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_DATOS:
            {
                $descripcion = "Tabla de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_RESUMEN:
            {
                $descripcion = "Título de resumen de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_COSTE_CONSUMO:
            {
                $descripcion = "Tabla de coste y consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_DETALLES:
            {
                $descripcion = "Título de detalles de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_CONSUMO:
            {
                $descripcion = "Tabla de consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_TERMINO_FIJO:
            {
                $descripcion = "Tabla de término fijo";
                break;
            }
			case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_CAPACIDAD_DEMANDADA:
            {
                $descripcion = "Tabla de capacidad demandada";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_OTROS_CONCEPTOS:
            {
                $descripcion = "Tabla de otros conceptos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS:
            {
                $descripcion = "Gráfica de porcentajes de costes por concepto";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TITULO_REPARTO_COSTES:
            {
                $descripcion = "Título de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_REPARTO_COSTES:
            {
                $descripcion = "Tabla de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES:
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
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_simulador_factura_gas_Espanya($tipo_informe)
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
                        <div class='tabla-datos100' id='contenedor-tabla-consumo-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-termino-fijo-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-capacidad-demandada-simulador-factura'></div>
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
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-termino-fijo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-capacidad-demandada-simulador-factura'></div>
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
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_gas_Espanya(
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
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumo-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-termino-fijo-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-capacidad-demandada-simulador-factura'></div>
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
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-termino-fijo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-capacidad-demandada-simulador-factura'></div>
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


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_gas_Espanya(
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

        $datos_elemento = dame_simulacion_factura_sensor_tarifa_gas_Espanya($parametros_informe);
        return ($datos_elemento);
    }
?>
