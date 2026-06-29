<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


	class HistoricoEvento
	{
        // Funciones estáticas de histórico de evento


		// Devuelve la cabecera para el histórico de eventos
        static function dame_cabecera_tabla($tipo_fecha)
		{
            $idiomas = new Idiomas();

            switch ($tipo_fecha)
            {
                case TIPO_FECHA_HISTORICO_EVENTOS_EVENTO:
                {
                    $titulo_fecha .= $idiomas->_("Fecha")." (".$idiomas->_("evento").")";
                    break;
                }
                case TIPO_FECHA_HISTORICO_EVENTOS_VALORES:
                {
                    $titulo_fecha .= $idiomas->_("Fecha")." (".$idiomas->_("valores").")";
                    break;
                }
            }

            // Nota: No se muestra la tabla de instantáneos porque actualmente no hay tipos de eventos instantáneos
            // (antes había de clase solar, pero ya no existen)
            return (array(
                $titulo_fecha,
                $idiomas->_("Sensor"),
				$idiomas->_("Inicios"),
				$idiomas->_("Fines"),
				$idiomas->_("Alarma")
			));
        }


        // Devuelve la consulta para el histórico de eventos
        static function dame_consulta_historico_eventos(
            $filtro,
            $clase_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $tipo_fecha)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            switch ($tipo_fecha)
            {
                case TIPO_FECHA_HISTORICO_EVENTOS_EVENTO:
                {
                    $campo_hora .= "hora";
                    break;
                }
                case TIPO_FECHA_HISTORICO_EVENTOS_VALORES:
                {
                    $campo_hora .= "hora_valores";
                    break;
                }
            }
            $consulta .= "
                SELECT
                    id,
                    ".$campo_hora." AS campo_hora,
                    sensor,
                    nombres_eventos_activados,
                    alarma_eventos_activados,
                    nombres_eventos_desactivados
                FROM activaciones_eventos
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (".$campo_hora." >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (".$campo_hora." <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($tipo_fecha == TIPO_FECHA_HISTORICO_EVENTOS_VALORES)
            {
                $consulta .= "
                    AND (valores IS NOT NULL)";
            }
            if ($clase_sensor != CLASE_TODAS)
            {
                $consulta .= "
                    AND (clase = '".$bd_datos->_($clase_sensor)."')";
            }
            if ($filtro != "")
            {
                $campos = array(
                    "sensor",
                    "nombres_eventos_activados",
                    "nombres_eventos_desactivados");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            $consulta .= "
                ORDER BY ".$campo_hora." DESC, id DESC";
			return ($consulta);
        }


        // Devuelve la tabla de históricos de eventos
        static function dame_tabla_historico_eventos(
            $filtro,
            $clase_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $tipo_fecha,
            &$limite_elementos_tabla_historico_superado)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se crea la tabla
            $params_tabla = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HISTORICO_EVENTOS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_HISTORICO_EVENTOS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-historico-eventos",
                $idiomas->_("Histórico de eventos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = HistoricoEvento::dame_cabecera_tabla($tipo_fecha);
            $tabla->anyade_cabecera("", $cabecera);

            // Se recuperan los nombres de los sensores del usuario
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $nombres_sensores_usuario = dame_todos_nombres_sensores_usuario_actual();
            }

            // Se añade cada uno de los históricos de eventos a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_historicos_eventos = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                $consulta_historico_eventos = HistoricoEvento::dame_consulta_historico_eventos(
                    $filtro,
                    $clase_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $tipo_fecha);
                $res_historico_eventos = $bd_datos->ejecuta_consulta($consulta_historico_eventos);
                if ($res_historico_eventos == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_historico_eventos."'");
                }

                // Se recorren las filas de los históricos de eventos
                $limite_elementos_tabla_historico_superado = false;
                while (($fila_historico_evento = $res_historico_eventos->dame_siguiente_fila()) && ($limite_elementos_tabla_historico_superado == false))
                {
                    $anyadir_historico_evento = true;
                    if ($mostrar_todos_sensores == false)
                    {
                        if (in_array($fila_historico_evento['sensor'], $nombres_sensores_usuario) == false)
                        {
                            $anyadir_historico_evento = false;
                        }
                    }

                    if ($anyadir_historico_evento == true)
                    {
                        if ($numero_historicos_eventos == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                        {
                            $limite_elementos_tabla_historico_superado = true;
                            break;
                        }
                        else
                        {
                            $historico_evento = new HistoricoEvento($fila_historico_evento);
                            $tabla->anyade_fila(
                                "datosHistoricoEvento__".$fila_historico_evento['clase']."__".$fila_historico_evento['id'],
                                $historico_evento->dame_datos_tabla()
                            );
                            $numero_historicos_eventos += 1;
                        }
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de datos con información de eventos").": ".$numero_historicos_eventos;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de histórico de eventos


        public $idiomas;

        public $id;
        public $params;


        // Funciones de histórico de eventos


		function __construct($params)
		{
			$this->idiomas = new Idiomas();

			$this->id = $params['id'];
            $this->params = $params;
		}


		function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $cadena_fecha_hora_local_local = $icono_dato_erroneo;
            $nombre_sensor = $icono_dato_erroneo;
            $nombres_eventos_activados = $icono_dato_erroneo;
            $nombres_eventos_desactivados = $icono_dato_erroneo;
            $icono_alarma = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['campo_hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_correcta = true;

                // Nombre de sensor
                $nombre_sensor = htmlspecialchars($this->params['sensor'], ENT_QUOTES);

                // Nombres de eventos
                $nombres_eventos_activados = str_replace(",", ", ", $this->params['nombres_eventos_activados']);
                $nombres_eventos_desactivados = str_replace(",", ", ", $this->params['nombres_eventos_desactivados']);
                if ($nombres_eventos_activados == "")
                {
                    $nombres_eventos_activados = $this->idiomas->_("Ninguno");
                }
                if ($nombres_eventos_desactivados == "")
                {
                    $nombres_eventos_desactivados = $this->idiomas->_("Ninguno");
                }
                $nombres_eventos_activados = htmlspecialchars($nombres_eventos_activados, ENT_QUOTES);
                $nombres_eventos_desactivados = htmlspecialchars($nombres_eventos_desactivados, ENT_QUOTES);

                // Icono de alarma
                $alarma = ($this->params['alarma_eventos_instantaneos'] == 1) || ($this->params['alarma_eventos_activados']);
                if ($alarma == true)
                {
                    $icono_alarma = "<i class='icon-warning-sign color-rojo'></i>";
                }
                else
                {
                    $icono_alarma = "";
                }
			}
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en la fecha
                if ($fecha_hora_correcta == true)
                {
                    $cadena_fecha_hora_local_local = "[".$icono_fila_con_errores."] ".$cadena_fecha_hora_local_local;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
				$cadena_fecha_hora_local_local,
                $nombre_sensor,
				$nombres_eventos_activados,
				$nombres_eventos_desactivados,
                $icono_alarma
			));
		}


        function dame_detalles_tabla()
		{
            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

            switch ($this->params['granularidad'])
            {
                case GRANULARIDAD_TIEMPO_REAL:
                {
                    $cadena_valores = NodoSensor::dame_cadena_valores_sensor(
                        ID_NINGUNO,
                        ID_NINGUNO,
                        $this->params['hora_valores'],
                        $this->params['valores'],
                        $this->params['clase'],
                        $this->params['parametros_clase'],
                        $this->params['incrementos_tiempo_real_horarios'],
                        GRANULARIDAD_TIEMPO_REAL,
                        SEPARADOR_VALOR_INCREMENTO_SENSOR,
                        FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                        NULL);
                    break;
                }
                case GRANULARIDAD_CUARTOHORARIA:
                case GRANULARIDAD_HORARIA:
                {
                    $cadena_valores = NodoSensor::dame_cadena_valores_clase_sensor(
                        ID_NINGUNO,
                        ID_NINGUNO,
                        $this->params['hora_valores'],
                        $this->params['valores'],
                        $this->params['clase'],
                        $this->params['parametros_clase'],
                        $this->params['granularidad'],
                        NULL);
                    break;
                }
            }
            $info = "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Fecha de evento").": ".$cadena_hora_local_local."<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Valores del sensor").": ".$cadena_valores;
            if ($this->params['hora_valores'] !== NULL)
            {
                $cadena_hora_valores_local_utc = convierte_formato_fecha($this->params['hora_valores'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_valores_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $info .= " (".$cadena_hora_valores_local_local.")";
            }

			return ($info);
		}
	}
?>
