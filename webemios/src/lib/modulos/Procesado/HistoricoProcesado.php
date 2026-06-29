<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');


	class HistoricoProcesado
	{
        // Funciones estáticas de histórico de procesado


		// Devuelve la cabecera para el histórico de procesado
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Fecha"),
                $idiomas->_("Tipo de ejecución"),
                $idiomas->_("Tipo"),
                $idiomas->_("Clase")." / ".$idiomas->_("tipo"),
                $idiomas->_("Granularidad"),
                $idiomas->_("Tiempo de ejecución")." (".$idiomas->_("s").")",
                $idiomas->_("Causa")
			));
        }


        // Devuelve la consulta para el histórico de procesado
        static function dame_consulta_historico_procesado(
            $tipo_ejecucion_procesado,
            $clase_sensor,
            $tipo_sensor,
            $granularidad,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT *
                FROM ejecuciones_procesado
                WHERE
                    (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($tipo_ejecucion_procesado != TIPO_EJECUCION_PROCESADO_TODOS)
            {
                $consulta .= "
                    AND (tipo_ejecucion_procesado = '".$tipo_ejecucion_procesado."')";
            }
            switch ($clase_sensor)
            {
                case CLASE_TODAS:
                {
                    break;
                }
                case CLASE_NINGUNA:
                {
                    $consulta .= "
                        AND (tipo_procesado <> '".TIPO_PROCESADO_CLASE_SENSOR."')";
                    break;
                }
                default:
                {
                    $consulta .= "
                        AND (tipo_procesado = '".TIPO_PROCESADO_CLASE_SENSOR."')
                        AND (clase_tipo = '".$bd_datos->_($clase_sensor)."')";
                    break;
                }
            }
            switch ($tipo_sensor)
            {
                case TIPO_TODOS:
                {
                    break;
                }
                case TIPO_NINGUNO:
                {
                    $consulta .= "
                        AND (tipo_procesado <> '".TIPO_PROCESADO_TIPO_SENSOR."')";
                    break;
                }
                default:
                {
                    $consulta .= "
                        AND (tipo_procesado = '".TIPO_PROCESADO_TIPO_SENSOR."')
                        AND (clase_tipo = '".$bd_datos->_($tipo_sensor)."')";
                    break;
                }
            }
            if ($granularidad != GRANULARIDAD_TODAS)
            {
                $consulta .= "
                    AND (granularidad = '".$bd_datos->_($granularidad)."')";
            }
            $consulta .= "
                ORDER BY
                    hora DESC,
                    id DESC";
			return ($consulta);
        }


        // Devuelve la tabla de histórico de procesado
        static function dame_tabla_historico_procesado(
            $tipo_ejecucion_procesado,
            $clase_sensor,
            $tipo_sensor,
            $granularidad,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            &$limite_elementos_tabla_historico_superado)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se crea la tabla
            $params_tabla = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HISTORICO_PROCESADO,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_HISTORICO_PROCESADO),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-monitorizacion-historico-procesado",
                $idiomas->_("Histórico de procesado de datos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = HistoricoProcesado::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los históricos de procesado a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_historicos_procesado = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                $consulta_historico_procesado = HistoricoProcesado::dame_consulta_historico_procesado(
                    $tipo_ejecucion_procesado,
                    $clase_sensor,
                    $tipo_sensor,
                    $granularidad,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc);
                $res_historico_procesado = $bd_datos->ejecuta_consulta($consulta_historico_procesado);
                if ($res_historico_procesado == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_historico_procesado."'");
                }
                $limite_elementos_tabla_historico_superado = false;
                while (($fila_historico_procesado = $res_historico_procesado->dame_siguiente_fila()) && ($limite_elementos_tabla_historico_superado == false))
                {
                    if ($numero_historicos_procesado == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                    {
                        $limite_elementos_tabla_historico_superado = true;
                        break;
                    }
                    else
                    {
                        $historico_procesado = new HistoricoProcesado($fila_historico_procesado);
                        $tabla->anyade_fila(
                            "datosHistoricoProcesado__".$fila_historico_procesado['id'],
                            $historico_procesado->dame_datos_tabla()
                        );
                        $numero_historicos_procesado += 1;
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de ejecuciones de procesado de datos").": ".$numero_historicos_procesado;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de histórico de procesado


        public $idiomas;

        public $id;
        public $params;


        // Funciones de histórico de procesado


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
            $tipo_ejecucion_procesado = $icono_dato_erroneo;
            $tipo_procesado = $icono_dato_erroneo;
            $nombre_clase_tipo = $icono_dato_erroneo;
            $granularidad = $icono_dato_erroneo;
            $cadena_segundos_ejecucion = $icono_dato_erroneo;
            $causa = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_correcta = true;

                // Datos de la tabla
                $tipo_ejecucion_procesado = dame_descripcion_tipo_ejecucion_procesado($this->params['tipo_ejecucion_procesado']);
                $tipo_procesado = dame_descripcion_tipo_procesado($this->params['tipo_procesado']);
                switch ($this->params['tipo_procesado'])
                {
                    case TIPO_PROCESADO_CLASE_SENSOR:
                    {
                        $nombre_clase_tipo = NodoSensor::dame_descripcion_clase_sensor($this->params['clase_tipo']);
                        break;
                    }
                    case TIPO_PROCESADO_TIPO_SENSOR:
                    {
                        $nombre_clase_tipo = NodoSensor::dame_descripcion_tipo_sensor($this->params['clase_tipo']);
                        break;
                    }
                }
                if ($this->params['granularidad'] == "")
                {
                    $granularidad = $this->idiomas->_("ND");
                }
                else
                {
                    $granularidad = dame_descripcion_granularidad($this->params['granularidad']);
                }
                $segundos_ejecucion = $this->params['segundos_ejecucion'];
                if ($segundos_ejecucion === NULL)
                {
                    $cadena_segundos_ejecucion = $this->idiomas->_("En ejecución");
                }
                else
                {
                    $cadena_segundos_ejecucion = formatea_numero($this->params['segundos_ejecucion'], 2, false);
                }
                switch ($this->params['causa'])
                {
                    case CAUSA_EJECUCION_PROCESADO_AUTOMATICA:
                    {
                        $causa = $this->idiomas->_("Automática");
                        break;
                    }
                    case CAUSA_EJECUCION_PROCESADO_MANUAL:
                    {
                        $causa = $this->idiomas->_("Manual");
                        break;
                    }
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
                $tipo_ejecucion_procesado,
                $tipo_procesado,
                $nombre_clase_tipo,
                $granularidad,
				$cadena_segundos_ejecucion,
                $causa
			));
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de histórico de procesado
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_historico_procesado'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

			return ($herramientas);
		}


        function dame_detalles_tabla()
		{
            // Se muestra la tabla de las tareas
            $id_elemento_tareas_historico_procesado = "tareas-historico-procesado".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $info .= "<div id='".$id_elemento_tareas_historico_procesado."' class='contenedor-detalle-tabla-datos'>".
                $this->dame_tabla_tareas_historico_procesado()."</div>";

            return ($info);
        }


        //
        // Funciones auxiliares
        //


        function dame_tabla_tareas_historico_procesado()
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se recuperan las tareas de esta ejecución de procesado
			$consulta_tareas = "
				SELECT
                    id,
                    tarea,
                    hora,
                    segundos_ejecucion,
                    estado_ejecucion,
                    numero_datos_tratados
				FROM ejecuciones_tareas_procesado
				WHERE
                    ejecucion_procesado = '".$bd_datos->_($this->id)."'
                ORDER BY id ASC";
			$res_tareas = $bd_datos->ejecuta_consulta($consulta_tareas);
            if ($res_tareas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tareas."'");
            }

            // Se crea la tabla de tareas
            $params_tabla_tareas = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_TAREAS_PROCESADO,
                "generar_valores_xml" => true
            );
            $tabla_tareas = new TablaDatos(
                "tabla-procesos-procesado".$this->id,
                $this->idiomas->_("Tareas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_tareas
            );
            $cabecera_tabla_tareas = array(
				$this->idiomas->_("Fecha"),
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Tiempo de ejecución")." (".$this->idiomas->_("s").")",
                $this->idiomas->_("Número de datos tratados"),
                $this->idiomas->_("Ejecución")
			);
            $tabla_tareas->anyade_cabecera("", $cabecera_tabla_tareas);

            $numero_tareas = $res_tareas->dame_numero_filas();
            $segundos_ejecucion_tareas = 0;
            while ($fila_tarea = $res_tareas->dame_siguiente_fila())
            {
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_tarea_local_utc = convierte_formato_fecha($fila_tarea["hora"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_tarea_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_tarea_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                switch ($fila_tarea["estado_ejecucion"])
                {
                    case ID_NINGUNO:
                    {
                        $resultado_ejecucion = "<i class='icon-spinner color-gris'></i>";
                        break;
                    }
                    case ESTADO_EJECUCION_OK:
                    {
                        $resultado_ejecucion = "<i class='icon-ok color-verde'></i>";
                        break;
                    }
                    case ESTADO_EJECUCION_ERROR:
                    {
                        $resultado_ejecucion = "<i class='icon-remove color-rojo'></i>";
                        break;
                    }
                }
                if ($fila_tarea["estado_ejecucion"] == ID_NINGUNO)
                {
                    $cadena_segundos_ejecucion = $this->idiomas->_("ND");
                    $cadena_numero_datos_tratados = $this->idiomas->_("ND");
                }
                else
                {
                    $cadena_segundos_ejecucion = formatea_numero($fila_tarea["segundos_ejecucion"], 2);
                    $cadena_numero_datos_tratados = formatea_numero($fila_tarea["numero_datos_tratados"], 2);
                }
                $datos_fila_tarea = array(
                    $cadena_fecha_hora_tarea_local_local,
                    $fila_tarea["tarea"],
                    $cadena_segundos_ejecucion,
                    $cadena_numero_datos_tratados,
                    $resultado_ejecucion
                );
                $tabla_tareas->anyade_fila(
                    "datosTarea__".$fila_tarea['id'],
                    $datos_fila_tarea
                );

                $segundos_ejecucion_tareas += $fila_tarea["segundos_ejecucion"];
            }
            $cadena_segundos_ejecucion_tareas = formatea_numero($segundos_ejecucion_tareas, 2);
            $tabla_tareas->anyade_pie($this->idiomas->_("Tareas").": ".$numero_tareas." (".$this->idiomas->_("tiempo de ejecución").": ".$cadena_segundos_ejecucion_tareas." ".$this->idiomas->_("s").")");

            return ($tabla_tareas->dame_tabla(false));
		}
	}
?>
