<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    // Indices de parámetros de importaciones de valores de sensor
	define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FICHERO", 0);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CARACTER_SEPARADOR", 1);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_PUNTO_DECIMAL", 2);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CABECERAS", 3);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_LINEAS_CABECERAS", 4);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_FECHA", 5);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FECHA", 6);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORA", 7);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_HORA", 8);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_ZONA_HORARIA", 9);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO", 10);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_VALORES", 11);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_SEGUNDOS_INCREMENTOS", 12);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_TIPO_INCREMENTOS", 13);

    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_COLUMNA", 0);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_BIT_INICIAL", 1);
    define("INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_BITS_VALOR", 2);


	class ImportacionValoresSensorPendiente
	{
        // Funciones estáticas de importación pendiente


		// Devuelve la cabecera para la tabla de importaciones pendientes
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
            array_push($cabecera, $idiomas->_("Estado"));
            array_push($cabecera, $idiomas->_("Último error"));
            return ($cabecera);
        }


        // Devuelve la consulta para la tabla de importaciones pendientes
        static function dame_consulta_importaciones_pendientes($mostrar_red)
        {
            $consulta = "
                SELECT *
                FROM importaciones_valores_sensores_pendientes";
            if ($mostrar_red == false)
            {
                $consulta .= "
                WHERE
                    red = '".$_SESSION["id_red"]."'";
            }
            $consulta .= "
                ORDER BY
                    FIELD(
                        estado,
                        '".ESTADO_IMPORTACION_PENDIENTE_REALIZADA."',
                        '".ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION."',
                        '".ESTADO_IMPORTACION_PENDIENTE_PREPARADO."',
                        '".ESTADO_IMPORTACION_PENDIENTE_ESPERANDO_REINTENTO."',
                        '".ESTADO_IMPORTACION_PENDIENTE_EN_ESPERA."',
                        '".ESTADO_IMPORTACION_PENDIENTE_DESCONOCIDO."'),
                    hora ASC";
            return ($consulta);
        }


        // Devuelve la tabla de importaciones pendientes
        static function dame_tabla_importaciones_pendientes($modulo)
		{
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crea la tabla
            switch ($modulo)
            {
                case MODULO_MONITORIZACION:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_CON_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_CON_RED);
                    $mostrar_red = true;
                    break;
                }
                default:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_SIN_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES_SIN_RED);
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
                "tabla-importaciones-valores-sensores-pendientes",
                $idiomas->_("Importaciones de valores de sensores pendientes"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = ImportacionValoresSensorPendiente::dame_cabecera_tabla($mostrar_red);
            $tabla->anyade_cabecera("", $cabecera);

            // Se recuperan los nombres de los sensores del usuario
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            }

            // Se realiza la consulta de las importaciones pendientes
            $consulta_importaciones_pendientes = ImportacionValoresSensorPendiente::dame_consulta_importaciones_pendientes($mostrar_red);
            $res_importaciones_pendientes = $bd_red->ejecuta_consulta($consulta_importaciones_pendientes);
            if ($res_importaciones_pendientes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_importaciones_pendientes."'");
            }

            // Se añade cada una de las importaciones pendientes a la tabla y el pie de tabla
            $numero_importaciones_pendientes = 0;
            while ($fila_importacion_pendiente = $res_importaciones_pendientes->dame_siguiente_fila())
            {
                $anyadir_importacion_pendiente = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($fila_importacion_pendiente['sensor'], $ids_sensores_usuario) == false)
                    {
                        $anyadir_importacion_pendiente = false;
                    }
                }

                if ($anyadir_importacion_pendiente == true)
                {
                    $importacion_pendiente = new ImportacionValoresSensorPendiente($fila_importacion_pendiente);
                    $datos_tabla = $importacion_pendiente->dame_datos_tabla($mostrar_red);
                    $opciones_tabla = $importacion_pendiente->dame_opciones_tabla();
                    $params_fila = array(
                        "opciones" => $opciones_tabla
                    );
                    $tabla->anyade_fila(
                        "datosImportacionValoresSensorPendiente__".$fila_importacion_pendiente['id'],
                        $datos_tabla,
                        $params_fila
                    );
                    $numero_importaciones_pendientes += 1;
                }
            }
            $texto_pie = $idiomas->_("Número de importaciones de valores de sensores pendientes").": ".$numero_importaciones_pendientes;
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de importación pendiente


        public $idiomas;

        public $id;
        public $params;


        // Funciones de importación pendiente


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
            $estado = $icono_dato_erroneo;
            $descripcion_ultimo_error = $icono_dato_erroneo;

            // Parámetros para el mensaje del botón de eliminar
            $this->params["cadena_fecha_hora_local_local"] = "?";
            $this->params["nombre_sensor"] = "?";

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $this->params["cadena_fecha_hora_local_local"] = $cadena_fecha_hora_local_local;
                $fecha_hora_correcta = true;

                // Nombre de sensor
                $nombre_sensor = dame_nombre_sensor($this->params["sensor"]);
                $nombre_sensor = htmlspecialchars($nombre_sensor, ENT_QUOTES);
                $this->params["nombre_sensor"] = $nombre_sensor;

                // Red
                if ($mostrar_red == true)
                {
                    $nombre_red = htmlspecialchars($this->params["nombre_red"], ENT_QUOTES);
                }

                // Usuario
                $id_usuario = $this->params["usuario"];

                // Conversión de fechas
                $cadena_fecha_hora_ultimo_estado_local_utc = convierte_formato_fecha($this->params['hora_ultimo_estado'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_ultimo_estado_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimo_estado_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                // Estado y descripción de último error
                $estado = ImportacionValoresSensorPendiente::dame_icono_estado_importacion_pendiente($this->params["estado"]);
                $estado .= " (".ImportacionValoresSensorPendiente::dame_descripcion_estado_importacion_pendiente($this->params["estado"]).")";
                $estado .= " (".$cadena_fecha_hora_ultimo_estado_local_local.")";
                $descripcion_ultimo_error = ImportacionValoresSensorPendiente::dame_descripcion_error_importacion_pendiente($this->params["ultimo_error"]);
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
            array_push($datos_tabla, $estado);
            array_push($datos_tabla, $descripcion_ultimo_error);
			return ($datos_tabla);
		}


        function dame_opciones_tabla()
		{
            if ($this->params['estado'] != ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION)
            {
                $borrar = "<i id='elimina_importacion_valores_sensor_pendiente__".$this->id."' ".
                    "hora='".$this->params["cadena_fecha_hora_local_local"]."' ".
                    "nombre_sensor='".$this->params["nombre_sensor"]."' ".
                    "class='icon-remove color-gris boton_eliminar_importacion_valores_sensor_pendiente boton-tabla-datos'></i>";
            }
            else
            {
                $borrar = "<i class='icon-remove color-gris-muy-claro'></i>";
            }
            $opciones = array($borrar);
			return ($opciones);
		}


        function dame_detalles_tabla()
		{
            $info = "";

            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

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

			return ($info);
		}


        //
        // Funciones de datos de tabla de importaciones
        //


        // Devuelve la descripción del estado de la importación pendiente
        static function dame_descripcion_estado_importacion_pendiente($estado_importacion)
        {
            switch ($estado_importacion)
            {
                case ESTADO_IMPORTACION_PENDIENTE_EN_ESPERA:
                {
                    $descripcion = "en espera";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_PREPARADO:
                {
                    $descripcion = "preparado";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION:
                {
                    $descripcion = "en ejecución";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_REALIZADA:
                {
                    $descripcion = "realizada";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_ESPERANDO_REINTENTO:
                {
                    $descripcion = "esperando reintento";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_DESCONOCIDO:
                {
                    $descripcion = "desconocido";
                    break;
                }
                default:
                {
                    $descripcion = "desconocido";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        // Devuelve el icono del estado de la importación pendiente
        static function dame_icono_estado_importacion_pendiente($estado_importacion)
        {
            $descripcion_estado = ImportacionValoresSensorPendiente::dame_descripcion_estado_importacion_pendiente($estado_importacion);
            switch ($estado_importacion)
            {
                case ESTADO_IMPORTACION_PENDIENTE_EN_ESPERA:
                {
                    $icono = "<i class='icon-time color-gris-claro'>";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_PREPARADO:
                {
                    $icono = "<i class='icon-pause color-gris-claro'>";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION:
                {
                    $icono = "<i class='icon-play color-gris'>";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_REALIZADA:
                {
                    $icono = "<i class='icon-stop color-gris-claro'>";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_ESPERANDO_REINTENTO:
                {
                    $icono = "<i class='icon-repeat color-gris-claro'>";
                    break;
                }
                case ESTADO_IMPORTACION_PENDIENTE_DESCONOCIDO:
                {
                    $icono = "<i class='icon-question-sign color-rojo'>";
                    break;
                }
                default:
                {
                    $icono = "<i class='icon-question-sign color-rojo'>";
                    break;
                }
            }
            $icono .= "<texto class='elemento-oculto'>".htmlspecialchars($descripcion_estado)."</texto></i>";
            return ($icono);
        }


        // Devuelve la descripción del error de la importación pendiente
        static function dame_descripcion_error_importacion_pendiente($error_importacion)
        {
            switch ($error_importacion)
            {
                case ERROR_IMPORTACION_PENDIENTE_NINGUNO:
                {
                    $descripcion = "Ninguno";
                    break;
                }
                case ERROR_IMPORTACION_PENDIENTE_DATOS_SENSOR_BLOQUEADOS:
                {
                    $descripcion = "Datos de sensor bloqueados";
                    break;
                }
                case ERROR_IMPORTACION_PENDIENTE_DESCONOCIDO:
                {
                    $descripcion = "Desconocido";
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
        // Funciones de parámetros de importaciones
        //


        static function dame_nombres_valores_parametros_opciones_fichero_csv_importacion_valores_sensor($tipo_valores, $cadena_opciones_fichero_csv)
        {
            // Se recuperan los parámetros de opciones de fichero CSV de la importación de valores del sensor
            $nombres_valores_parametros_opciones_fichero_csv = array();
            $parametros_opciones_fichero_csv = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_fichero_csv);
            $formato_fichero = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FICHERO];
            $nombres_valores_parametros_opciones_fichero_csv["formato_fichero"] = $formato_fichero;

            $caracter_separador_sustituto_separador = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CARACTER_SEPARADOR];
            $caracter_separador = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $caracter_separador_sustituto_separador);
            $nombres_valores_parametros_opciones_fichero_csv["caracter_separador"] = $caracter_separador;

            $punto_decimal_sustituto_separador = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_PUNTO_DECIMAL];
            $punto_decimal = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $punto_decimal_sustituto_separador);
            $id_punto_decimal = dame_id_punto_decimal($punto_decimal);
            $nombres_valores_parametros_opciones_fichero_csv["id_punto_decimal"] = $id_punto_decimal;

            $cadena_cabeceras = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CABECERAS];
            $numero_lineas_cabeceras = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_LINEAS_CABECERAS];
            if ($cadena_cabeceras == FICHERO_CSV_SIN_CABECERAS)
            {
                $numero_lineas_cabeceras = 0;
            }
            $nombres_valores_parametros_opciones_fichero_csv["numero_lineas_cabeceras"] = $numero_lineas_cabeceras;

            $numero_columna_fecha = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_FECHA] + 1;
            $formato_fecha_python_sustituto_separador = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FECHA];
            $formato_fecha_python = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $formato_fecha_python_sustituto_separador);
            $formato_fecha = convierte_formato_hora_python_a_formato_fecha_hora($formato_fecha_python);
            $numero_columna_hora = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORA];
            $formato_hora_python_sustituto_separador = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_HORA];
            $formato_hora_python = str_replace(SUSTITUTO_SEPARADOR, SEPARADOR_PARAMETROS_SIMPLES, $formato_hora_python_sustituto_separador);
            if (($numero_columna_hora != "") && ($formato_hora_python != ""))
            {
                $numero_columna_hora += 1;
                $hora_columna_independiente = VALOR_SI;
                $formato_hora = convierte_formato_hora_python_a_formato_fecha_hora($formato_hora_python);
            }
            else
            {
                $hora_columna_independiente = VALOR_NO;
            }
            $nombres_valores_parametros_opciones_fichero_csv["numero_columna_fecha"] = $numero_columna_fecha;
            $nombres_valores_parametros_opciones_fichero_csv["formato_fecha"] = $formato_fecha;
            $nombres_valores_parametros_opciones_fichero_csv["hora_columna_independiente"] = $hora_columna_independiente;
            $nombres_valores_parametros_opciones_fichero_csv["numero_columna_hora"] = $numero_columna_hora;
            $nombres_valores_parametros_opciones_fichero_csv["formato_hora"] = $formato_hora;

            $zona_horaria = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_ZONA_HORARIA];
            $numero_columna_horario_verano = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO];
            if ($numero_columna_horario_verano != "")
            {
                $numero_columna_horario_verano += 1;
            }
            $nombres_valores_parametros_opciones_fichero_csv["zona_horaria"] = $zona_horaria;
            $nombres_valores_parametros_opciones_fichero_csv["numero_columna_horario_verano"] = $numero_columna_horario_verano;

            $numero_valores = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_VALORES];
            if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
            {
                $segundos_incrementos = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_SEGUNDOS_INCREMENTOS];
                $horas_incrementos = (float) ($segundos_incrementos / 3600);
                $tipo_incrementos = $parametros_opciones_fichero_csv[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_TIPO_INCREMENTOS];
            }
            $nombres_valores_parametros_opciones_fichero_csv["numero_valores"] = $numero_valores;
            $nombres_valores_parametros_opciones_fichero_csv["horas_incrementos"] = $horas_incrementos;
            $nombres_valores_parametros_opciones_fichero_csv["tipo_incrementos"] = $tipo_incrementos;
            return ($nombres_valores_parametros_opciones_fichero_csv);
        }


        static function dame_nombres_valores_parametros_opciones_valores_fichero_csv_importacion_valores_sensor($cadena_opciones_valores_fichero_csv)
        {
            // Se recuperan los parámetros de opciones de valores del fichero CSV de la importación de valores del sensor
            $nombres_valores_parametros_opciones_valores_fichero_csv = array();

            // Columnas de valores
            $parametros_opciones_valores_fichero_csv = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_opciones_valores_fichero_csv);
            $columnas_valores = "";
            for ($i = 0; $i < count($parametros_opciones_valores_fichero_csv); $i++)
            {
                if ($i > 0)
                {
                    $columnas_valores .= " ".SEPARADOR_PARAMETROS_VALORES." ";
                }
                $parametros_opciones_valor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_opciones_valores_fichero_csv[$i]);
                $numero_columna_valor = $parametros_opciones_valor[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_COLUMNA] + 1;
                $cadena_opciones_valor = $numero_columna_valor;

                // Número de bit inicial
                if (count($parametros_opciones_valor) > (INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_COLUMNA + 1))
                {
                    $numero_bit_inicial_valor = $parametros_opciones_valor[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_BIT_INICIAL] + 1;
                    $cadena_opciones_valor .= SEPARADOR_PARAMETROS_SIMPLES.$numero_bit_inicial_valor;
                }
                $columnas_valores .= $cadena_opciones_valor;
            }
            $nombres_valores_parametros_opciones_valores_fichero_csv["columnas_valores"] = $columnas_valores;
            return ($nombres_valores_parametros_opciones_valores_fichero_csv);
        }


        static function dame_descripcion_parametros_opciones_fichero_csv_importacion_valores_sensor(
            $tipo_valores,
            $cadena_opciones_fichero_csv,
            $tipo_descripcion)
        {
            $idiomas = new Idiomas();

            // Delimitadores de descripción de parámetros
            $cadena_inicio_lista_parametros = "";
            $cadena_fin_lista_parametros = "";
            $cadena_inicio_primer_parametro = "";
            $cadena_inicio_parametro = "";
            $cadena_fin_parametro = "";
            establece_delimitadores_descripcion_parametros(
                $tipo_descripcion,
                $cadena_inicio_lista_parametros,
                $cadena_fin_lista_parametros,
                $cadena_inicio_primer_parametro,
                $cadena_inicio_parametro,
                $cadena_fin_parametro);

            // Opciones de fichero CSV
            $parametros_opciones_fichero_csv = ImportacionValoresSensorPendiente::dame_nombres_valores_parametros_opciones_fichero_csv_importacion_valores_sensor(
                $tipo_valores,
                $cadena_opciones_fichero_csv);
            $formato_fichero = $parametros_opciones_fichero_csv["formato_fichero"];
            $caracter_separador = $parametros_opciones_fichero_csv["caracter_separador"];
            $id_punto_decimal = $parametros_opciones_fichero_csv["id_punto_decimal"];
            $numero_lineas_cabeceras = $parametros_opciones_fichero_csv["numero_lineas_cabeceras"];
            $numero_columna_fecha = $parametros_opciones_fichero_csv["numero_columna_fecha"];
            $formato_fecha = $parametros_opciones_fichero_csv["formato_fecha"];
            $hora_columna_independiente = $parametros_opciones_fichero_csv["hora_columna_independiente"];
            $numero_columna_hora = $parametros_opciones_fichero_csv["numero_columna_hora"];
            $formato_hora = $parametros_opciones_fichero_csv["formato_hora"];
            $zona_horaria = $parametros_opciones_fichero_csv["zona_horaria"];
            $numero_columna_horario_verano = $parametros_opciones_fichero_csv["numero_columna_horario_verano"];
            $numero_valores = $parametros_opciones_fichero_csv["numero_valores"];
            $horas_incrementos = $parametros_opciones_fichero_csv["horas_incrementos"];
            $tipo_incrementos = $parametros_opciones_fichero_csv["tipo_incrementos"];

            $html = $cadena_inicio_lista_parametros;
            $html .= $cadena_inicio_parametro.$idiomas->_("Formato de fichero de valores").": ".dame_descripcion_formato_fichero_valores($formato_fichero).$cadena_fin_parametro;
            $html .= $cadena_inicio_parametro.$idiomas->_("Carácter separador").": ".$caracter_separador.$cadena_fin_parametro;
            $html .= $cadena_inicio_parametro.$idiomas->_("Punto decimal").": ".dame_descripcion_id_punto_decimal($id_punto_decimal).$cadena_fin_parametro;
            $html .= $cadena_inicio_parametro.$idiomas->_("Número de líneas de cabecera").": ".$numero_lineas_cabeceras.$cadena_fin_parametro;
            $html .= $cadena_inicio_parametro.$idiomas->_("Columna de fecha").": ".$numero_columna_fecha.$cadena_fin_parametro;
            $html .= $cadena_inicio_parametro.$idiomas->_("Formato de fecha").": ".$formato_fecha.$cadena_fin_parametro;
            if ($hora_columna_independiente == VALOR_SI)
            {
                $html .= $cadena_inicio_parametro.$idiomas->_("Columna de hora").": ".$numero_columna_hora.$cadena_fin_parametro;
                $html .= $cadena_inicio_parametro.$idiomas->_("Formato de hora").": ".$formato_hora.$cadena_fin_parametro;
            }
            $html .= $cadena_inicio_parametro.$idiomas->_("Zona horaria").": ".dame_nombre_zona_horaria($zona_horaria).$cadena_fin_parametro;
            if ($numero_columna_horario_verano != "")
            {
                $html .= $cadena_inicio_parametro.$idiomas->_("Columna de horario de verano").": ".$numero_columna_horario_verano.$cadena_fin_parametro;
            }
            $html .= $cadena_inicio_parametro.$idiomas->_("Número de valores").": ".$numero_valores.$cadena_fin_parametro;
            if ($tipo_valores == TIPO_VALORES_SENSOR_INCREMENTALES)
            {
                if ($horas_incrementos == 0)
                {
                    $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE).$cadena_fin_parametro;
                }
                else
                {
                    $html .= $cadena_inicio_parametro.$idiomas->_("Horas de incrementos").": ".NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO).$cadena_fin_parametro;
                    $html .= $cadena_inicio_parametro.$idiomas->_("Número de horas de incrementos").": ".$horas_incrementos.$cadena_fin_parametro;
                }
                $html .= $cadena_inicio_parametro.$idiomas->_("Tipo de incrementos").": ".NodoSensor::dame_descripcion_tipo_incrementos_valores_sensor($tipo_incrementos).$cadena_fin_parametro;
            }
            $html .= $cadena_fin_lista_parametros;
            return ($html);
        }


        static function dame_descripcion_parametros_opciones_valores_fichero_csv_importacion_valores_sensor(
            $cadena_opciones_valores_fichero_csv,
            $tipo_descripcion)
        {
            $idiomas = new Idiomas();

            // Delimitadores de descripción de parámetros
            $cadena_inicio_lista_parametros = "";
            $cadena_fin_lista_parametros = "";
            $cadena_inicio_primer_parametro = "";
            $cadena_inicio_parametro = "";
            $cadena_fin_parametro = "";
            establece_delimitadores_descripcion_parametros(
                $tipo_descripcion,
                $cadena_inicio_lista_parametros,
                $cadena_fin_lista_parametros,
                $cadena_inicio_primer_parametro,
                $cadena_inicio_parametro,
                $cadena_fin_parametro);

            // Opciones de valores de fichero CSV
            $parametros_opciones_valores_fichero_csv = ImportacionValoresSensorPendiente::dame_nombres_valores_parametros_opciones_valores_fichero_csv_importacion_valores_sensor(
                $cadena_opciones_valores_fichero_csv);
            $columnas_valores = $parametros_opciones_valores_fichero_csv["columnas_valores"];

            $html = $cadena_inicio_lista_parametros;
            $html .= $cadena_inicio_parametro.$idiomas->_("Columnas de valores").": ".$columnas_valores.$cadena_fin_parametro;
            $html .= $cadena_fin_lista_parametros;
            return ($html);
        }
    }
?>
