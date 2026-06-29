<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


	class HistoricoImportacionValoresSensor
	{
        // Funciones estáticas de histórico de importación de valores de sensor


		// Devuelve la cabecera para el histórico de importación de valores de sensor
        static function dame_cabecera_tabla($mostrar_red)
		{
            $idiomas = new Idiomas();

            $cabecera = array();
            array_push($cabecera, $idiomas->_("Fecha"));
            array_push($cabecera, $idiomas->_("Sensor"));
            if ($mostrar_red == true)
            {
                array_push($cabecera, $idiomas->_("Red"));
            }
            array_push($cabecera, $idiomas->_("Usuario"));
            array_push($cabecera, $idiomas->_("Fecha de petición"));
            array_push($cabecera, $idiomas->_("Correcta"));
            return ($cabecera);
        }


        // Devuelve la consulta para el histórico de importaciones de valores de sensores
        static function dame_consulta_historico_importaciones_valores_sensores(
            $mostrar_red,
            $filtro,
            $clase_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $resultado_ejecucion)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT *
                FROM importaciones_valores_sensores
                WHERE
                    (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($clase_sensor != CLASE_TODAS)
            {
                $consulta .= "
                    AND (clase_sensor = '".$bd_datos->_($clase_sensor)."')";
            }
            if ($resultado_ejecucion != RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_TODOS)
            {
                switch ($resultado_ejecucion)
                {
                    case RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK:
                    {
                        $consulta .= "AND (correcta = '".VALOR_SI."')";
                        break;
                    }
                    case RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_SIN_VALORES_ERRONEOS:
                    {
                        $consulta .= "AND ((correcta = '".VALOR_SI."') AND (hay_valores_erroneos = '".VALOR_NO."'))";
                        break;
                    }
                    case RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_CON_VALORES_ERRONEOS:
                    {
                        $consulta .= "AND ((correcta = '".VALOR_SI."') AND (hay_valores_erroneos = '".VALOR_SI."'))";
                        break;
                    }
                    case RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_ERROR:
                    {
                        $consulta .= "AND (correcta = '".VALOR_NO."')";
                        break;
                    }
                }
            }
            if ($mostrar_red == false)
            {
                $consulta .= "
                    AND (red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $campos = array("sensor");
                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                    $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                }
            }
            $consulta .= "
                ORDER BY
                    hora DESC";
			return ($consulta);
        }


        // Devuelve la tabla de histórico de importaciones de valores de sensores
        static function dame_tabla_historico_importaciones_valores_sensores(
            $modulo,
            $filtro,
            $clase_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc = null,
            $cadena_fecha_hora_fin_base_datos_utc = null,
            $resultado_ejecucion = null,
            &$limite_elementos_tabla_historico_superado = null)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se crea la tabla
            switch ($modulo)
            {
                case MODULO_MONITORIZACION:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_CON_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_CON_RED);
                    $mostrar_red = true;
                    break;
                }
                default:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_SIN_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES_SIN_RED);
                    $mostrar_red = false;
                    break;
                }
            }
            $params_tabla = array(
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-alarmas",
                $idiomas->_("Histórico de importaciones de valores de sensores"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = HistoricoImportacionValoresSensor::dame_cabecera_tabla($mostrar_red);
            $tabla->anyade_cabecera("", $cabecera);

            // Se recuperan los nombres de los sensores del usuario
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
                $nombres_sensores_usuario = dame_nombres_sensores($ids_sensores_usuario);
            }

            // Se añade cada uno de los históricos de importaciones de valores de sensor a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_historicos_importaciones = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                // Se realiza la consulta de las importaciones de valores de sensores
                $consulta_historico_importaciones = HistoricoImportacionValoresSensor::dame_consulta_historico_importaciones_valores_sensores(
                    $mostrar_red,
                    $filtro,
                    $clase_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $resultado_ejecucion);
                $res_historico_importaciones = $bd_datos->ejecuta_consulta($consulta_historico_importaciones);
                if ($res_historico_importaciones == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_historico_importaciones."'");
                }

                // Filas de los históricos de importaciones
                $filas_historicos_importaciones = array();
                while ($fila_historico_importacion = $res_historico_importaciones->dame_siguiente_fila())
                {
                    array_push($filas_historicos_importaciones, $fila_historico_importacion);
                }

                // Si el módulo de monitorización, se muestra el nombre de la red
                // (se añaden los nombres de las redes a los datos de las filas de los históricos de importaciones)
                if ($modulo == MODULO_MONITORIZACION)
                {
                    $consulta_redes = "
                        SELECT
                            id,
                            nombre
                        FROM redes";
                    $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
                    $nombres_redes = array();
                    while ($fila_red = $res_redes->dame_siguiente_fila())
                    {
                        $nombres_redes[$fila_red["id"]] = $fila_red["nombre"];
                    }
                    for ($i = 0; $i < count($filas_historicos_importaciones); $i++)
                    {
                        $id_red = $filas_historicos_importaciones[$i]["red"];
                        $nombre_red = $nombres_redes[$id_red];
                        $filas_historicos_importaciones[$i]["nombre_red"] = $nombre_red;
                    }
                }

                // Se recorren las filas de los históricos de importaciones y se añaden las que cumplan con el filtro
                $limite_elementos_tabla_historico_superado = false;
                foreach ($filas_historicos_importaciones as $fila_historico_importacion)
                {
                    $anyadir_historico_importacion = true;
                    if ($mostrar_todos_sensores == false)
                    {
                        if (in_array($fila_historico_importacion['sensor'], $nombres_sensores_usuario) == false)
                        {
                            $anyadir_historico_importacion = false;
                        }
                    }

                    if ($anyadir_historico_importacion == true)
                    {
                        $historico_importacion = new HistoricoImportacionValoresSensor($fila_historico_importacion);
                        $datos_tabla = $historico_importacion->dame_datos_tabla($mostrar_red);

                        // Se realiza el filtrado (sólo si hay que mostrar la red)
                        if ($mostrar_red == true)
                        {
                            $nombre_sensor = $datos_tabla[1];
                            $nombre_red = $datos_tabla[2];
                            if (($filtro != "") &&
                                (stripos($nombre_sensor, $filtro) === false) &&
                                (stripos($nombre_red, $filtro) === false))
                            {
                                continue;
                            }
                        }

                        if ($numero_historicos_importaciones == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                        {
                            $limite_elementos_tabla_historico_superado = true;
                            break;
                        }
                        else
                        {
                            $tabla->anyade_fila(
                                "datosHistoricoImportacionValoresSensor__".$fila_historico_importacion['id'],
                                $datos_tabla
                            );
                            $numero_historicos_importaciones += 1;
                        }
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de históricos de importaciones de valores de sensor").": ".$numero_historicos_importaciones;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de histórico de importación de valores de sensor


        public $idiomas;

        public $id;
        public $params;


        // Funciones de histórico de importación de valores de sensor


		function __construct($params)
		{
			$this->idiomas = new Idiomas();

			$this->id = $params['id'];
            $this->params = $params;
		}


		function dame_datos_tabla($mostrar_red)
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $cadena_fecha_hora_local_local = $icono_dato_erroneo;
            $nombre_sensor = $icono_dato_erroneo;
            $nombre_red = $icono_dato_erroneo;
            $id_usuario = $icono_dato_erroneo;
            $cadena_fecha_hora_peticion_local_local = $icono_dato_erroneo;
            $descripcion_correcta = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_correcta = true;

                // Nombre de sensor
                $nombre_sensor = htmlspecialchars($this->params["sensor"], ENT_QUOTES);

                // Red
                if ($mostrar_red == true)
                {
                    $nombre_red = htmlspecialchars($this->params["nombre_red"], ENT_QUOTES);
                }

                // Usuario
                $id_usuario = $this->params["usuario"];

                // Conversión de fechas
                $cadena_fecha_hora_peticion_local_utc = convierte_formato_fecha($this->params['hora_peticion'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_peticion_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_peticion_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                // Descripcion correcta
                $descripcion_correcta = HistoricoImportacionValoresSensor::dame_icono_correcta_importacion_valores_sensor($this->params["correcta"]);
                $descripcion_correcta .= " (".$this->params["segundos_ejecucion"]." ".$this->idiomas->_("segundos").")";
                if ($this->params["hay_valores_erroneos"] == VALOR_SI)
                {
                    $descripcion_correcta .= " [<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("hay valores erróneos"), ENT_QUOTES)."</texto></i>]";
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
            $datos_tabla = array();
            array_push($datos_tabla, $cadena_fecha_hora_local_local);
            array_push($datos_tabla, $nombre_sensor);
            if ($mostrar_red == true)
            {
                array_push($datos_tabla, $nombre_red);
            }
            array_push($datos_tabla, $id_usuario);
            array_push($datos_tabla, $cadena_fecha_hora_peticion_local_local);
            array_push($datos_tabla, $descripcion_correcta);
			return ($datos_tabla);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Mostrado de ventana para repetir la importación de valores del sensor
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_mostrar_ventana_repetir_importacion_valores_sensor__".$this->id."' class='btn-mini btn btn-success boton_mostrar_ventana_repetir_importacion_valores_sensor'>
                        <i class='icon-repeat color-blanco'></i>
                    </button>
                </span>";

            return ($herramientas);
		}


        function dame_detalles_tabla()
		{
            $info = "";

            $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Parámetros").":";
            $info .= "<ul>";
            $info .= "<li>".$this->idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($this->params["clase_sensor"])."</li>";
            $info .= "<li>".$this->idiomas->_("Tipo de valores").": ".NodoSensor::dame_descripcion_tipo_valores_sensor($this->params["tipo_valores"])."</li>";
            $info .= "<li>".$this->idiomas->_("Nombre de fichero").": ".htmlspecialchars($this->params["nombre_fichero_csv"], ENT_QUOTES)."</li>";
            $info .= "<li>".$this->idiomas->_("Opciones de fichero").": ".
                ImportacionValoresSensorPendiente::dame_descripcion_parametros_opciones_fichero_csv_importacion_valores_sensor(
                    $this->params["tipo_valores"],
                    $this->params["opciones_fichero_csv"],
                    TIPO_DESCRIPCION_HTML)."</li>";
            $info .= "<li>".$this->idiomas->_("Opciones de valores de fichero").": ".
                ImportacionValoresSensorPendiente::dame_descripcion_parametros_opciones_valores_fichero_csv_importacion_valores_sensor(
                    $this->params["opciones_valores_fichero_csv"],
                    TIPO_DESCRIPCION_HTML)."</li>";
            $info .= "<li>".$this->idiomas->_("Aplicar calibración").": ".dame_descripcion_valores_si_no($this->params["aplicar_calibracion"])."</li>";
            $info .= "</ul>";

            $info .= "<br/>";
            $descripcion_resultado_funcion_importacion = HistoricoImportacionValoresSensor::dame_descripcion_resultado_funcion_importacion_valores_sensor_detalles_tabla(
                $this->params["tipo_valores"],
                $this->params["resultado_funcion_json"]);
            $info .= $descripcion_resultado_funcion_importacion;

			return ($info);
        }


        //
        // Funciones auxiliares
        //


        // Devuelve el icono de ejecución correcta de la importación de valores del sensor
        static function dame_icono_correcta_importacion_valores_sensor($correcta)
        {
            $descripcion_correcta = dame_descripcion_valores_si_no($correcta);
            switch ($correcta)
            {
                case VALOR_SI:
                {
                    $icono = "<i class='icon-thumbs-up-alt color-verde'></i>";
                    break;
                }
                case VALOR_NO:
                {
                    $icono = "<i class='icon-thumbs-down-alt color-rojo'>";
                    break;
                }
                default:
                {
                    $icono = "<i class='icon-question-sign color-gris-claro'>";
                    break;
                }
            }
            $icono .= "<texto class='elemento-oculto'>".htmlspecialchars($descripcion_correcta)."</texto></i>";
            return ($icono);
        }


        // Devuelve la descripción del resultado de la función de importación de valores del sensor para los detalles de la tabla
        static function dame_descripcion_resultado_funcion_importacion_valores_sensor_detalles_tabla($tipo_valores, $resultado_funcion_json)
        {
            $idiomas = new Idiomas();

            $resultado_funcion = json_decode($resultado_funcion_json, true);
            if ($resultado_funcion["res"] == "OK")
            {
                // Tipo de valores
                switch ($tipo_valores)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        // Comprobación de valores insertados
                        $numero_valores_insertados = $resultado_funcion["numero_valores_insertados"];
                        if ($numero_valores_insertados == 0)
                        {
                            $descripcion = "<i class='icon-warning-sign color-rojo'></i> ".
                                $idiomas->_("No se han importado valores");
                        }
                        else
                        {
                            $descripcion = "<i class='icon-info-sign color-azul'></i> ";

                            // Se recupera la información del resultado de la función
                            $zona_horaria = dame_zona_horaria_local();
                            $numero_valores_erroneos = $resultado_funcion["numero_valores_erroneos"];
                            $cadena_hora_inicio_valores_insertados_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_valores_insertados"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                            $cadena_hora_fin_valores_insertados_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_valores_insertados"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                            $cadena_hora_inicio_valores_insertados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_valores_insertados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                            $cadena_hora_fin_valores_insertados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_valores_insertados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                            $numero_valores_borrados = $resultado_funcion["numero_valores_borrados"];
                            $numero_incrementos_valores_borrados = $resultado_funcion["numero_incrementos_valores_borrados"];
                            $numero_valores_insertados_cuartoshora = $resultado_funcion["numero_valores_insertados_cuartoshora"];
                            if ($numero_valores_insertados_cuartoshora > 0)
                            {
                                $cadena_hora_inicio_valores_insertados_cuartoshora_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_valores_insertados_cuartoshora"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_fin_valores_insertados_cuartoshora_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_valores_insertados_cuartoshora"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_inicio_valores_insertados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_valores_insertados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $cadena_hora_fin_valores_insertados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_valores_insertados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $numero_valores_clase_sensor_calculados_cuartoshora = $resultado_funcion["numero_valores_clase_sensor_calculados_cuartoshora"];
                            }
                            $numero_valores_insertados_horas = $resultado_funcion["numero_valores_insertados_horas"];
                            if ($numero_valores_insertados_horas > 0)
                            {
                                $cadena_hora_inicio_valores_insertados_horas_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_valores_insertados_horas"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_fin_valores_insertados_horas_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_valores_insertados_horas"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_inicio_valores_insertados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_valores_insertados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $cadena_hora_fin_valores_insertados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_valores_insertados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $numero_valores_clase_sensor_calculados_horas = $resultado_funcion["numero_valores_clase_sensor_calculados_horas"];
                            }
                            $numero_valores_borrados_cuartoshora = $resultado_funcion["numero_valores_borrados_cuartoshora"];
                            $numero_valores_borrados_horas = $resultado_funcion["numero_valores_borrados_horas"];
                            $numero_valores_borrados_dias = $resultado_funcion["numero_valores_borrados_dias"];
                            $numero_valores_borrados_meses = $resultado_funcion["numero_valores_borrados_meses"];

                            // Título de la descripción
                            $descripcion .= $idiomas->_("Valores importados correctamente").":";
                            $descripcion .= "<ul>";

                            // Valores en tiempo real
                            $descripcion .= "<li>";
                            $descripcion .= $idiomas->_("Número de valores importados").": ".$numero_valores_insertados;
                            if ($numero_valores_erroneos > 0)
                            {
                                $descripcion .= " (<i class='icon-warning-sign color-rojo'></i>: ".$idiomas->_("Número de valores erróneos").": ".$numero_valores_erroneos.")";
                            }
                            $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_valores_insertados_local_local.", ";
                            $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_valores_insertados_local_local.")";
                            if ($numero_valores_borrados > 0)
                            {
                                $descripcion .= " (".$idiomas->_("número de valores borrados").": ".$numero_valores_borrados.")";
                            }
                            if ($numero_incrementos_valores_borrados > 0)
                            {
                                $descripcion .= " (".$idiomas->_("número de incrementos de valores borrados").": ".$numero_incrementos_valores_borrados.")";
                            }
                            $descripcion .= "</li>";

                            // Valores cuartohorarios
                            if ($numero_valores_insertados_cuartoshora > 0)
                            {
                                $descripcion .= "<li>";
                                $descripcion .= $idiomas->_("Número de valores por cuartos de hora añadidos").": ".$numero_valores_insertados_cuartoshora;
                                $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_valores_insertados_cuartoshora_local_local.", ";
                                $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_valores_insertados_cuartoshora_local_local.")";
                                if ($numero_valores_clase_sensor_calculados_cuartoshora > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores de clase de sensor por cuartos de hora calculados").": ".$numero_valores_clase_sensor_calculados_cuartoshora.")";
                                }
                                if ($numero_valores_borrados_cuartoshora > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por cuartos de hora borrados").": ".$numero_valores_borrados_cuartoshora.")";
                                }
                                $descripcion .= "</li>";
                            }

                            // Valores horarios
                            if ($numero_valores_insertados_horas > 0)
                            {
                                $descripcion .= "<li>";
                                $descripcion .= $idiomas->_("Número de valores por horas añadidos").": ".$numero_valores_insertados_horas;
                                $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_valores_insertados_horas_local_local.", ";
                                $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_valores_insertados_horas_local_local.")";
                                if ($numero_valores_clase_sensor_calculados_horas > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores de clase de sensor por horas calculados").": ".$numero_valores_clase_sensor_calculados_horas.")";
                                }
                                if ($numero_valores_borrados_horas > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por horas borrados").": ".$numero_valores_borrados_horas.")";
                                }
                                if ($numero_valores_borrados_dias > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por días borrados").": ".$numero_valores_borrados_dias.")";
                                }
                                if ($numero_valores_borrados_meses > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por meses borrados").": ".$numero_valores_borrados_meses.")";
                                }
                                $descripcion .= "</li>";
                            }
                            $descripcion .= "</ul>";
                        }
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        // Comprobación de incrementos de valores insertados
                        $numero_incrementos_valores_insertados = $resultado_funcion["numero_incrementos_valores_insertados"];
                        if ($numero_incrementos_valores_insertados == 0)
                        {
                            $descripcion = "<i class='icon-warning-sign color-rojo'></i> ".
                                $idiomas->_("No se han importado incrementos de valores");
                        }
                        else
                        {
                            $descripcion = "<i class='icon-info-sign color-azul'></i> ";

                            // Se recupera la información del resultado de la función
                            $zona_horaria = dame_zona_horaria_local();
                            $numero_incrementos_valores_erroneos = $resultado_funcion["numero_incrementos_valores_erroneos"];
                            $cadena_hora_inicio_incrementos_valores_insertados_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_incrementos_valores_insertados"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                            $cadena_hora_fin_incrementos_valores_insertados_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_incrementos_valores_insertados"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                            $cadena_hora_inicio_incrementos_valores_insertados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_incrementos_valores_insertados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                            $cadena_hora_fin_incrementos_valores_insertados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_incrementos_valores_insertados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                            $numero_valores_borrados = $resultado_funcion["numero_valores_borrados"];
                            $numero_incrementos_valores_borrados = $resultado_funcion["numero_incrementos_valores_borrados"];
                            $numero_incrementos_valores_insertados_cuartoshora = $resultado_funcion["numero_incrementos_valores_insertados_cuartoshora"];
                            if ($numero_incrementos_valores_insertados_cuartoshora > 0)
                            {
                                $cadena_hora_inicio_incrementos_valores_insertados_cuartoshora_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_incrementos_valores_insertados_cuartoshora"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_fin_incrementos_valores_insertados_cuartoshora_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_incrementos_valores_insertados_cuartoshora"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_inicio_incrementos_valores_insertados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_incrementos_valores_insertados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $cadena_hora_fin_incrementos_valores_insertados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_incrementos_valores_insertados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $numero_valores_clase_sensor_calculados_cuartoshora = $resultado_funcion["numero_valores_clase_sensor_calculados_cuartoshora"];
                            }
                            $numero_incrementos_valores_insertados_horas = $resultado_funcion["numero_incrementos_valores_insertados_horas"];
                            if ($numero_incrementos_valores_insertados_horas > 0)
                            {
                                $cadena_hora_inicio_incrementos_valores_insertados_horas_local_utc = convierte_formato_fecha($resultado_funcion["hora_inicio_incrementos_valores_insertados_horas"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_fin_incrementos_valores_insertados_horas_local_utc = convierte_formato_fecha($resultado_funcion["hora_fin_incrementos_valores_insertados_horas"], FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                                $cadena_hora_inicio_incrementos_valores_insertados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_incrementos_valores_insertados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $cadena_hora_fin_incrementos_valores_insertados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_incrementos_valores_insertados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                                $numero_valores_clase_sensor_calculados_horas = $resultado_funcion["numero_valores_clase_sensor_calculados_horas"];
                            }
                            $numero_incrementos_valores_borrados_cuartoshora = $resultado_funcion["numero_incrementos_valores_borrados_cuartoshora"];
                            $numero_incrementos_valores_borrados_horas = $resultado_funcion["numero_incrementos_valores_borrados_horas"];
                            $numero_incrementos_valores_borrados_dias = $resultado_funcion["numero_incrementos_valores_borrados_dias"];
                            $numero_incrementos_valores_borrados_meses = $resultado_funcion["numero_incrementos_valores_borrados_meses"];

                            // Título de la descripción
                            $descripcion .= $idiomas->_("Incrementos de valores importados correctamente").":";
                            $descripcion .= "<ul>";

                            // Incrementos de valores en tiempo real
                            $descripcion .= "<li>";
                            $descripcion .= $idiomas->_("Número de incrementos de valores importados").": ".$numero_incrementos_valores_insertados;
                            if ($numero_incrementos_valores_erroneos > 0)
                            {
                                $descripcion .= " (<i class='icon-warning-sign color-rojo'></i>: ".$idiomas->_("Número de incrementos de valores erróneos").": ".$numero_incrementos_valores_erroneos.")";
                            }
                            $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_incrementos_valores_insertados_local_local.", ";
                            $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_incrementos_valores_insertados_local_local.")";
                            if ($numero_valores_borrados > 0)
                            {
                                $descripcion .= " (".$idiomas->_("número de valores borrados").": ".$numero_valores_borrados.")";
                            }
                            if ($numero_incrementos_valores_borrados > 0)
                            {
                                $descripcion .= " (".$idiomas->_("número de incrementos de valores borrados").": ".$numero_incrementos_valores_borrados.")";
                            }
                            $descripcion .= "</li>";

                            // Valores cuartohorarios
                            if ($numero_incrementos_valores_insertados_cuartoshora > 0)
                            {
                                $descripcion .= "<li>";
                                $descripcion .= $idiomas->_("Número de valores por cuartos de hora añadidos").": ".$numero_incrementos_valores_insertados_cuartoshora;
                                $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_incrementos_valores_insertados_cuartoshora_local_local.", ";
                                $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_incrementos_valores_insertados_cuartoshora_local_local.")";
                                if ($numero_valores_clase_sensor_calculados_cuartoshora > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores de clase de sensor por cuartos de hora calculados").": ".$numero_valores_clase_sensor_calculados_cuartoshora.")";
                                }
                                if ($numero_incrementos_valores_borrados_cuartoshora > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por cuartos de hora borrados").": ".$numero_incrementos_valores_borrados_cuartoshora.")";
                                }
                                $descripcion .= "</li>";
                            }

                            // Valores horarios
                            if ($numero_incrementos_valores_insertados_horas > 0)
                            {
                                $descripcion .= "<li>";
                                $descripcion .= $idiomas->_("Número de valores por horas añadidos").": ".$numero_incrementos_valores_insertados_horas;
                                $descripcion .= " (".$idiomas->_("hora de inicio").": ".$cadena_hora_inicio_incrementos_valores_insertados_horas_local_local.", ";
                                $descripcion .= $idiomas->_("hora de fin").": ".$cadena_hora_fin_incrementos_valores_insertados_horas_local_local.")";
                                if ($numero_valores_clase_sensor_calculados_horas > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores de clase de sensor por horas calculados").": ".$numero_valores_clase_sensor_calculados_horas.")";
                                }
                                if ($numero_incrementos_valores_borrados_horas > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por horas borrados").": ".$numero_incrementos_valores_borrados_horas.")";
                                }
                                if ($numero_incrementos_valores_borrados_dias > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por días borrados").": ".$numero_incrementos_valores_borrados_dias.")";
                                }
                                if ($numero_incrementos_valores_borrados_meses > 0)
                                {
                                    $descripcion .= " (".$idiomas->_("número de valores por meses borrados").": ".$numero_incrementos_valores_borrados_meses.")";
                                }
                                $descripcion .= "</li>";
                            }
                            $descripcion .= "</ul>";
                        }
                        break;
                    }
                }
            }
            else
            {
                $descripcion = "<i class='icon-warning-sign color-rojo'></i> ";

                // Descripción del error
                $descripcion_error = dame_descripcion_error_valores_fichero_csv($resultado_funcion["error"]);
                $cadena_parametros_error = modifica_cadena_parametros_error_valores_fichero_csv(
                    $resultado_funcion["error"],
                    $resultado_funcion["cadena_parametros_error"]);

                $descripcion .= $idiomas->_("Error al importar valores").":";
                $descripcion .= "<ul>";
                $descripcion .= "<li>".$descripcion_error;
                if ($cadena_parametros_error != "")
                {
                    $descripcion .= " (".$cadena_parametros_error.")";
                }
                $descripcion .= "</li>";
                $descripcion .= "</ul>";
            }

            // Se devuelve la descripción
            return ($descripcion);
        }
    }
?>
