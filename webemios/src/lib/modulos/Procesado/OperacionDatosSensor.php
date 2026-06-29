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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    // Constantes

    // Indices de parámetros de tipo de operaciones
    define("INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_TIPO_TAREA_PROCESADO", 0);
    define("INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_TIPO_EJECUCION_PROCESADO", 1);
    define("INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_GRANULARIDAD", 2);

    define("INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_FUNCION_PROCESADO_NOMBRE_FUNCION_PROCESADO", 0);


    // Representa una operación de datos de sensor (en ejecución)
	class OperacionDatosSensor
	{
        // Funciones estáticas de operación de datos de sensor


		// Devuelve la cabecera para el histórico de procesado
        static function dame_cabecera_tabla($mostrar_red)
		{
            $idiomas = new Idiomas();

            $cabecera_tabla = array();
            array_push($cabecera_tabla, $idiomas->_("Sensor"));
            if ($mostrar_red == true)
            {
                array_push($cabecera_tabla, $idiomas->_("Red"));
            }
            array_push($cabecera_tabla, $idiomas->_("Operación"));
            array_push($cabecera_tabla, $idiomas->_("Fecha"));
            array_push($cabecera_tabla, $idiomas->_("Tiempo de ejecución")." (".$idiomas->_("s").")");
            return ($cabecera_tabla);
        }


        // Devuelve la consulta para el histórico de procesado
        static function dame_consulta_operaciones_datos_sensores($mostrar_red)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT *
                FROM operaciones_datos_sensores";
            if ($mostrar_red == false)
            {
                $consulta .= "
                    WHERE
                        red = '".$_SESSION["id_red"]."'";
            }
            $consulta .= "
                ORDER BY
                    hora DESC,
                    red DESC,
                    sensor DESC";
			return ($consulta);
        }


        // Devuelve la tabla de operaciones de datos de sensores (actuales)
        static function dame_tabla_operaciones_datos_sensores($modulo, $actualizacion_periodica_activada)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $opciones = array();
            // Nota: Se comprueba la actualización periódica también con la cadena 'false'
            // porque al llamar al script PHP desde JavaScript se convierten los booleanos a cadena
            if (($actualizacion_periodica_activada == false) || ($actualizacion_periodica_activada === "false"))
            {
                $icono_boton_actualizacion_periodica = "icon-play";
            }
            else
            {
                $icono_boton_actualizacion_periodica = "icon-pause";
            }
            $boton_actualizacion_periodica_tabla_operaciones_datos_sensores = "<i id='boton_actualizacion_periodica_tabla_operaciones_datos_sensores' class='".$icono_boton_actualizacion_periodica." color-blanco boton-tabla-datos boton_actualizacion_periodica_tabla_operaciones_datos_sensores'></i>";
            array_push($opciones, $boton_actualizacion_periodica_tabla_operaciones_datos_sensores);
            $boton_actualizar_tabla_operaciones_datos_sensores = "<i id='actualiza_operaciones_datos_sensores' class='icon-refresh color-blanco boton_actualizar_tabla_operaciones_datos_sensores boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_operaciones_datos_sensores);

            // Se crea la tabla
            switch ($modulo)
            {
                case MODULO_MONITORIZACION:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_CON_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_CON_RED);
                    $mostrar_red = true;
                    break;
                }
                default:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_SIN_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_OPERACIONES_DATOS_SENSORES_SIN_RED);
                    $mostrar_red = false;
                    break;
                }
            }
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-operaciones-datos-sensores",
                $idiomas->_("Operaciones de datos de sensores (en ejecución)"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = OperacionDatosSensor::dame_cabecera_tabla($mostrar_red);
            $tabla->anyade_cabecera("", $cabecera);

            // Se recuperan los nombres de los sensores del usuario
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
                $nombres_sensores_usuario = dame_nombres_sensores($ids_sensores_usuario);
            }

            // Se añade cada una de las operaciones de datos de sensores a la tabla
            $numero_operaciones_datos_sensores = 0;
            $consulta = OperacionDatosSensor::dame_consulta_operaciones_datos_sensores($mostrar_red);
            $res = $bd_datos->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            while ($fila = $res->dame_siguiente_fila())
            {
                $anyadir_operacion_datos_sensor = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($fila['sensor'], $nombres_sensores_usuario) == false)
                    {
                        $anyadir_operacion_datos_sensor = false;
                    }
                }

                if ($anyadir_operacion_datos_sensor == true)
                {
                    $operacion_datos_sensor = new OperacionDatosSensor($fila);
                    $tabla->anyade_fila(
                        "datoOperacionDatosSensor__".$fila['id'],
                        $operacion_datos_sensor->dame_datos_tabla($mostrar_red)
                    );
                    $numero_operaciones_datos_sensores += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Número de operaciones de datos de sensores").": ".$numero_operaciones_datos_sensores);

            return ($tabla->dame_tabla());
		}


		// Miembros de operación de datos de sensor


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


		function dame_datos_tabla($mostrar_red)
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre_sensor = $icono_dato_erroneo;
            $nombre_red = $icono_dato_erroneo;
            $descripcion_operacion = $icono_dato_erroneo;
            $cadena_fecha_hora_local_local = $icono_dato_erroneo;
            $cadena_segundos_ejecucion = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_sensor_correcto = false;
            try
            {
                // Nombre de sensor
                $nombre_sensor = $this->params['sensor'];
                if ($nombre_sensor == "")
                {
                    $nombre_sensor = $this->idiomas->_("Todos");
                }
                $nombre_sensor = htmlspecialchars($nombre_sensor, ENT_QUOTES);
                $nombre_sensor_correcto = true;

                // Red
                if ($mostrar_red == true)
                {
                    $nombre_red = dame_nombre_red($this->params['red']);
                    $nombre_red = htmlspecialchars($nombre_red, ENT_QUOTES);
                }

                // Descripción de operación
                $descripcion_tipo = OperacionDatosSensor::dame_descripcion_tipo_operacion_datos_sensor($this->params['tipo']);
                $descripcion_parametros_tipo = OperacionDatosSensor::dame_descripcion_parametros_tipo_operacion_datos_sensor(
                    $this->params['tipo'],
                    $this->params['parametros_tipo']);
                $descripcion_tipo_sensor = OperacionDatosSensor::dame_descripcion_tipo_sensor_operacion_datos_sensor($this->params['tipo_sensor']);
                $descripcion_operacion = $descripcion_tipo;
                if ($descripcion_parametros_tipo != "")
                {
                    $descripcion_operacion .= " (".$descripcion_parametros_tipo.")";
                }
                if ($descripcion_tipo_sensor != "")
                {
                    $descripcion_operacion .= " (".$descripcion_tipo_sensor.")";
                }

                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                // Segundos de ejecución
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
                $timestamp_ahora_utc = dame_timestamp_ahora_milisegundos_utc();
                $timestamp_fecha_hora_utc = dame_timestamp_fecha_milisegundos($fecha_hora_utc);
                $segundos_ejecucion = ($timestamp_ahora_utc - $timestamp_fecha_hora_utc) / 1000;
                $cadena_segundos_ejecucion = formatea_numero($segundos_ejecucion, 2);
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre de sensor
                if ($nombre_sensor_correcto == true)
                {
                    $nombre_sensor = "[".$icono_fila_con_errores."] ".$nombre_sensor;
                }
            }

            // Se devuelven los datos de la tabla
            $datos_tabla = array();
            array_push($datos_tabla, $nombre_sensor);
            if ($mostrar_red == true)
            {
                array_push($datos_tabla, $nombre_red);
            }
            array_push($datos_tabla, $descripcion_operacion);
            array_push($datos_tabla, $cadena_fecha_hora_local_local);
            array_push($datos_tabla, $cadena_segundos_ejecucion);
            return ($datos_tabla);
		}


        //
        // Funciones auxiliares
        //


        static function dame_descripcion_tipo_operacion_datos_sensor($tipo_operacion_datos_sensor)
        {
            switch ($tipo_operacion_datos_sensor)
            {
                case TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO:
                {
                    $descripcion_tipo_operacion = "Tarea";
                    break;
                }
                case TIPO_OPERACION_DATOS_SENSOR_FUNCION_PROCESADO:
                {
                    $descripcion_tipo_operacion = "Función";
                    break;
                }
                case TIPO_OPERACION_DATOS_SENSOR_FICHERO_CSV:
                {
                    $descripcion_tipo_operacion = "Fichero CSV";
                    break;
                }
                default:
                {
                    $descripcion_tipo_operacion = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_operacion));
        }


        static function dame_descripcion_parametros_tipo_operacion_datos_sensor(
            $tipo_operacion_datos_sensor,
            $cadena_parametros_tipo_operacion_datos_sensor)
        {
            switch ($tipo_operacion_datos_sensor)
            {
                case TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO:
                {
                    $parametros_tipo_operacion_datos_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_tipo_operacion_datos_sensor);
                    $tipo_tarea_procesado = $parametros_tipo_operacion_datos_sensor[INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_TIPO_TAREA_PROCESADO];
                    $tipo_ejecucion_procesado = $parametros_tipo_operacion_datos_sensor[INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_TIPO_EJECUCION_PROCESADO];
                    $granularidad = $parametros_tipo_operacion_datos_sensor[INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_TAREA_PROCESADO_GRANULARIDAD];

                    $descripcion_tipo_tarea_procesado = dame_descripcion_tipo_tarea_procesado($tipo_tarea_procesado);
                    $descripcion_tipo_ejecucion_procesado = strtolower(dame_descripcion_tipo_ejecucion_procesado($tipo_ejecucion_procesado));
                    $descripcion_parametros_tipo = $descripcion_tipo_tarea_procesado.", ".$descripcion_tipo_ejecucion_procesado."";
                    if ($granularidad != GRANULARIDAD_NINGUNA)
                    {
                        $descripcion_parametros_tipo .= ", ".strtolower(dame_descripcion_granularidad($granularidad));
                    }
                    break;
                }
                case TIPO_OPERACION_DATOS_SENSOR_FUNCION_PROCESADO:
                {
                    $parametros_tipo_operacion_datos_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_tipo_operacion_datos_sensor);
                    $nombre_funcion_procesado = $parametros_tipo_operacion_datos_sensor[INDICE_PARAMETRO_TIPO_OPERACION_DATOS_SENSOR_FUNCION_PROCESADO_NOMBRE_FUNCION_PROCESADO];

                    $descripcion_nombre_funcion_procesado = dame_descripcion_nombre_funcion_procesado($nombre_funcion_procesado);
                    $descripcion_parametros_tipo = $descripcion_nombre_funcion_procesado;
                    break;
                }
                case TIPO_OPERACION_DATOS_SENSOR_FICHERO_CSV:
                {
                    $descripcion_parametros_tipo = "";
                    break;
                }
                default:
                {
                    $idiomas = new Idiomas();
                    $descripcion_parametros_tipo = $idiomas->_("desconocido");
                    break;
                }
            }
            return ($descripcion_parametros_tipo);
        }


        static function dame_descripcion_tipo_sensor_operacion_datos_sensor($tipo_sensor)
        {
            $idiomas = new Idiomas();

            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_OPERACION_DATOS_SENSOR_PRINCIPAL:
                case TIPO_SENSOR_OPERACION_DATOS_SENSORES_RED:
                {
                    $descripcion_tipo_sensor = "";
                    break;
                }
                case TIPO_SENSOR_OPERACION_DATOS_SENSOR_HIJO:
                {
                    $descripcion_tipo_sensor = "sensor hijo";
                    break;
                }
                case TIPO_SENSOR_OPERACION_DATOS_SENSOR_ASOCIADO:
                {
                    $descripcion_tipo_sensor = "sensor asociado";
                    break;
                }
                default:
                {
                    $descripcion_tipo_sensor = "desconocido";
                    break;
                }
            }
            if ($descripcion_tipo_sensor != "")
            {
                $descripcion_tipo_sensor = $idiomas->_($descripcion_tipo_sensor);
            }
            return ($descripcion_tipo_sensor);
        }
	}
?>
