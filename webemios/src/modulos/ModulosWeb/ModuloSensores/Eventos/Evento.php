<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/Periodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/RangoDias.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de eventos
    define("INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_CAMPO_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO", 1);
    define("INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PARAMETROS_CAMPO", 2);

    define("INDICE_PARAMETRO_TIPO_EVENTO_LINEA_BASE_ID_LINEA_BASE", 0);
    define("INDICE_PARAMETRO_TIPO_EVENTO_LINEA_BASE_PARAMETROS_CAMPO", 1);

    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_CAMPO_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_INTERVALO_VALORES", 1);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_NUMERO_DIAS_PERFIL_HORARIO", 2);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_TIPO_PERFIL_HORARIO", 3);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_AGRUPACIONES_DIAS_SEMANA", 4);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_HORARIO_SEMANAL", 5);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_EXCLUSION_FECHAS", 6);
    define("INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_PARAMETROS_CAMPO", 7);


	// Clase que representa un evento de un sensor o grupo de sensores
	class Evento
	{
        // Funciones estáticas de evento


        // Devuelve la cabecera para la tabla de eventos
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Clase de sensor"),
                $idiomas->_("Origen"),
                $idiomas->_("Tipo"),
                $idiomas->_("Parámetros"),
                $idiomas->_("Alarma"),
                $idiomas->_("Activado")
			));
        }


        // Devuelve la consulta para la tabla de eventos
        static function dame_consulta_eventos($filtro, $clase_sensor, $alarma)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM eventos
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($clase_sensor != CLASE_TODAS)
            {
                $consulta .= "
                    AND (clase = '".$bd_red->_($clase_sensor)."')";
            }
            switch ($alarma)
            {
                case ALARMA_EVENTO_SI:
                {
                    $consulta .= "
                        AND (alarma = '".VALOR_SI."')";
                    break;
                }
                case ALARMA_EVENTO_SI:
                {
                    $consulta .= "
                        AND (alarma = '".VALOR_NO."')";
                    break;
                }
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de eventos
        static function dame_tabla_eventos(
            $filtro,
            $clase_sensor,
            $alarma,
            $activacion,
            $actualizacion_periodica_activada)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_eventos = Evento::dame_administracion_eventos();
            if ($administracion_eventos == true)
            {
                $boton_anyadir_evento = "<i id='anyade_modifica_evento' class='icon-plus color-blanco boton_sensores_mostrar_ventana_anyadir_modificar_evento boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_evento);
            }

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
            $boton_actualizacion_periodica_tabla_eventos = "<i id='boton_actualizacion_periodica_tabla_eventos' class='".$icono_boton_actualizacion_periodica." color-blanco boton-tabla-datos boton_sensores_actualizacion_periodica_tabla_eventos'></i>";
            array_push($opciones, $boton_actualizacion_periodica_tabla_eventos);
            $boton_actualizar_tabla_eventos = "<i id='actualiza_eventos' class='icon-refresh color-blanco boton_sensores_actualizar_tabla_eventos boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_eventos);
            $boton_ayuda_tabla_eventos = "<i id='ayuda_eventos' class='icon-question-sign color-blanco boton_sensores_ayuda_tabla_eventos boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_eventos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVENTOS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_EVENTOS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-eventos",
                $idiomas->_("Eventos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Evento::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se realiza la consulta de los eventos
            $consulta_eventos = Evento::dame_consulta_eventos($filtro, $clase_sensor, $alarma);
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }

            // Identificadores de eventos del usuario actual
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_eventos_usuario = Evento::dame_ids_eventos_usuario_actual();
            }

            // Filas de los eventos
            $filas_eventos = array();
            while ($fila_evento = $res_eventos->dame_siguiente_fila())
            {
                $anyadir_evento = true;
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($fila_evento["id"], $ids_eventos_usuario) == false)
                    {
                        $anyadir_evento = false;
                    }
                }
                if ($anyadir_evento == true)
                {
                    array_push($filas_eventos, $fila_evento);
                }
            }

            // Se guardan las filas de los sensores y los nombres de los grupos de sensores
            $consulta_sensores = "
                SELECT
                    id,
                    nombre,
                    eventos_activados,
                    eventos_activados_clase_cuartoshora,
                    eventos_activados_clase_horas
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($mostrar_todos_sensores == false)
            {
                $consulta_sensores .= "
                    AND ".dame_condicion_consulta_sensores_usuario_actual(true);
            }
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            $filas_sensores = array();
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $id_sensor = $fila_sensor["id"];
                $filas_sensores[$id_sensor] = $fila_sensor;
            }
            $consulta_grupos_sensores = "
                SELECT
                    id,
                    nombre
                FROM grupos_sensores
                WHERE
                    red = '".$_SESSION["id_red"]."'";
            $res_grupos_sensores = $bd_red->ejecuta_consulta($consulta_grupos_sensores);
            $nombres_grupos_sensores = array();
            while ($fila_grupo_sensores = $res_grupos_sensores->dame_siguiente_fila())
            {
                $nombres_grupos_sensores[$fila_grupo_sensores["id"]] = $fila_grupo_sensores["nombre"];
            }

            // Se guarda la información de sensores con los eventos activados
            $info_sensores_eventos_activados = array();
            foreach ($filas_sensores as $fila_sensor)
            {
                $cadena_ids_eventos_activados_tiempo_real_sensor = $fila_sensor["eventos_activados"];
                $cadena_ids_eventos_activados_clase_cuartoshora_sensor = $fila_sensor["eventos_activados_clase_cuartoshora"];
                $cadena_ids_eventos_activados_clase_horas_sensor = $fila_sensor["eventos_activados_clase_horas"];
                $ids_eventos_activados_sensor = array();
                if ($cadena_ids_eventos_activados_tiempo_real_sensor != "")
                {
                    $ids_eventos_activados_tiempo_real_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_tiempo_real_sensor);
                    $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_tiempo_real_sensor);
                }
                if ($cadena_ids_eventos_activados_clase_cuartoshora_sensor != "")
                {
                    $ids_eventos_activados_clase_cuartoshora_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_clase_cuartoshora_sensor);
                    $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_clase_cuartoshora_sensor);
                }
                if ($cadena_ids_eventos_activados_clase_horas_sensor != "")
                {
                    $ids_eventos_activados_clase_horas_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_clase_horas_sensor);
                    $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_clase_horas_sensor);
                }
                foreach ($ids_eventos_activados_sensor as $id_evento_activado_sensor)
                {
                    if (array_key_exists($id_evento_activado_sensor, $info_sensores_eventos_activados) == false)
                    {
                        $info_sensores_eventos_activados[$id_evento_activado_sensor] = array($fila_sensor);
                    }
                    else
                    {
                        array_push($info_sensores_eventos_activados[$id_evento_activado_sensor], $fila_sensor);
                    }
                }
            }

            // Se añade la siguiente información a las filas de los eventos:
            // - Nombres de los sensores y de los grupos a los datos de las filas de los eventos
            // - Número de sensores con el evento activado
            for ($i = 0; $i < count($filas_eventos); $i++)
            {
                $id_evento = $filas_eventos[$i]["id"];
                $origen = $filas_eventos[$i]["origen"];
                $id_origen = $filas_eventos[$i]["id_origen"];
                switch ($origen)
                {
                    case ORIGEN_EVENTO_SENSOR:
                    {
                        if (array_key_exists($id_origen, $filas_sensores) == true)
                        {
                            $nombre_origen = $filas_sensores[$id_origen]["nombre"];
                        }
                        else
                        {
                            continue;
                        }
                        break;
                    }
                    case ORIGEN_EVENTO_GRUPO_SENSORES:
                    {
                        if (array_key_exists($id_origen, $nombres_grupos_sensores) == true)
                        {
                            $nombre_origen = $nombres_grupos_sensores[$id_origen]." (".$idiomas->_("grupo").")";
                        }
                        else
                        {
                            continue;
                        }
                        break;
                    }
                }
                $filas_eventos[$i]["nombre_origen"] = $nombre_origen;
                if (array_key_exists($id_evento, $info_sensores_eventos_activados) == false)
                {
                    $numero_sensores_evento_activado = 0;
                }
                else
                {
                    $numero_sensores_evento_activado = count($info_sensores_eventos_activados[$id_evento]);
                }
                $filas_eventos[$i]["numero_sensores_evento_activado"] = $numero_sensores_evento_activado;
            }

            // Se añaden los eventos
            $numero_eventos = 0;
            foreach ($filas_eventos as $fila_evento)
            {
                $anyadir_evento = true;
                switch ($activacion)
                {
                    case ACTIVACION_EVENTO_ACTIVADO:
                    {
                        if ($fila_evento["numero_sensores_evento_activado"] == 0)
                        {
                            $anyadir_evento = false;
                        }
                        break;
                    }
                    case ACTIVACION_EVENTO_DESACTIVADO:
                    {
                        if ($fila_evento["numero_sensores_evento_activado"] > 0)
                        {
                            $anyadir_evento = false;
                        }
                        break;
                    }
                }

                if ($anyadir_evento == true)
                {
                    $evento = new Evento($fila_evento);
                    $params_fila = array(
                        "opciones" => $evento->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosEvento__".$fila_evento['id'],
                        $evento->dame_datos_tabla(),
                        $params_fila
                    );
                    $numero_eventos += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Eventos").": ".$numero_eventos);

            return ($tabla->dame_tabla());
        }


        // Miembros de evento


		public $idiomas;

		public $id;
        public $params;


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

			$this->id = $params["id"];
            $this->params = $params;
		}


        function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $nombre_clase = $icono_dato_erroneo;
            $nombre_origen = $icono_dato_erroneo;
            $descripcion_tipo = $icono_dato_erroneo;
            $descripcion_parametros = $icono_dato_erroneo;
            $descripcion_alarma = $icono_dato_erroneo;
            $activado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Clase y tipo de evento
                $nombre_clase = NodoSensor::dame_descripcion_clase_sensor($this->params["clase"]);
                $descripcion_tipo = $this->dame_descripcion_tipo_evento($this->params["tipo"]);

                // Se añade la granularidad si no es en tiempo real
                switch ($this->params["granularidad"])
                {
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $descripcion_tipo .= " (".strtolower(dame_descripcion_granularidad($this->params["granularidad"])).")";
                        break;
                    }
                }

                // En la columna 'origen' se muestra el nombre del sensor/grupo y el tipo (sensor/grupo)
                if (array_key_exists("nombre_origen", $this->params) == true)
                {
                    $nombre_origen = $this->params["nombre_origen"];
                }
                else
                {
                    switch ($this->params["origen"])
                    {
                        case ORIGEN_EVENTO_SENSOR:
                        {
                            $nombre_sensor = dame_nombre_sensor($this->params["id_origen"]);
                            $nombre_origen = $nombre_sensor;
                            break;
                        }
                        case ORIGEN_EVENTO_GRUPO_SENSORES:
                        {
                            $nombre_grupo = dame_nombre_grupo_sensores($this->params["id_origen"]);
                            $nombre_origen = $nombre_grupo." (".$this->idiomas->_("grupo").")";
                            break;
                        }
                    }
                }

                // Se recuperan el campo y los parámetros del campo del evento
                $descripcion_parametros = Evento::dame_descripcion_parametros_evento(
                    $this->params["clase"],
                    $this->params["granularidad"],
                    $this->params["tipo"],
                    $this->params["parametros"]);

                // Alarma
                $descripcion_alarma = dame_descripcion_valores_si_no($this->params["alarma"]);

                // Estado
                if (array_key_exists("numero_sensores_evento_activado", $this->params) == true)
                {
                    $numero_sensores_evento_activado = $this->params["numero_sensores_evento_activado"];
                }
                else
                {
                    $filas_sensores_evento_activado = $this->dame_filas_sensores_evento_activado();
                    $numero_sensores_evento_activado = count($filas_sensores_evento_activado);
                }
                switch ($numero_sensores_evento_activado)
                {
                    case 0:
                    {
                        $activado = "<i class='icon-circle color-rojo'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desactivado"), ENT_QUOTES)."</texto></i>";
                        break;
                    }
                    default:
                    {
                        $activado = "<i class='icon-circle color-verde'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Activado"), ENT_QUOTES)."</texto></i>";
                        if ($this->params["origen"] == ORIGEN_EVENTO_GRUPO_SENSORES)
                        {
                            $activado .= " (".$numero_sensores_evento_activado.")";
                        }
                        break;
                    }
                }
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre
                if ($nombre_correcto == true)
                {
                    $nombre = "[".$icono_fila_con_errores."] ".$nombre;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
				$nombre,
                $nombre_clase,
                $nombre_origen,
				$descripcion_tipo,
                $descripcion_parametros,
                $descripcion_alarma,
                $activado
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_eventos = Evento::dame_administracion_eventos();
            if ($administracion_eventos == true)
            {
                $editar = "<i id='anyade_modifica_evento__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_sensores_mostrar_ventana_anyadir_modificar_evento boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_evento__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_sensores_mostrar_ventana_anyadir_modificar_evento boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_evento__".$this->id."' nombre_evento='".$nombre."' ".
                    "class='icon-remove color-gris boton_sensores_eliminar_evento boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de evento
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_sensores_refrescar_tabla_evento'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";
			return ($herramientas);
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

            // Descripción
            if ($this->params["descripcion"] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($this->params["descripcion"], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Persistente
            // (Nota: Actualmente todos los eventos son persistentes - no instantáneos)
            if (Evento::dame_tipo_evento_persistente($this->params["tipo"]) == false)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Persistente").": ".$this->idiomas->_("No")."<br/>";
                $info .= "<br/>";
            }

            // Zona horaria
            $zona_horaria = dame_zona_horaria_local();

            // Sensores con el evento activado
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            $filas_sensores_evento_activado = $this->dame_filas_sensores_evento_activado();
            $numero_filas_sensores_evento_activado = count($filas_sensores_evento_activado);
            if ($numero_filas_sensores_evento_activado > 0)
            {
                if ($numero_filas_sensores_evento_activado == 1)
                {
                    $texto_titulo_sensores = " ".$this->idiomas->_("Sensor con el evento activado");
                }
                else
                {
                    $texto_titulo_sensores = " ".$this->idiomas->_("Sensores con el evento activado");
                }
                $texto_sensores = "<ul>";
                foreach ($filas_sensores_evento_activado as $fila_sensor_evento_activado)
                {
                    $nombre_sensor = $fila_sensor_evento_activado["nombre"];
                    switch ($this->params["granularidad"])
                    {
                        case GRANULARIDAD_TIEMPO_REAL:
                        {
                            $cadena_hora_valores_base_datos_utc = $fila_sensor_evento_activado["hora_ultimos_valores"];
                            $cadena_valores = $fila_sensor_evento_activado["ultimos_valores"];
                            break;
                        }
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            $cadena_hora_valores_base_datos_utc = $fila_sensor_evento_activado["hora_ultimos_valores_clase_cuartoshora"];
                            $cadena_valores = $fila_sensor_evento_activado["ultimos_valores_clase_cuartoshora"];
                            break;
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $cadena_hora_valores_base_datos_utc = $fila_sensor_evento_activado["hora_ultimos_valores_clase_horas"];
                            $cadena_valores = $fila_sensor_evento_activado["ultimos_valores_clase_horas"];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Granularidad desconocida o incorrecta: '".$this->params["granularidad"]."'");
                        }
                    }

                    // Nota: Comprobación de que realmente hay valores del sensor
                    if ($cadena_hora_valores_base_datos_utc === NULL)
                    {
                        continue;
                    }

                    // Conversión de hora de valores a la zona horaria de la red
                    $cadena_hora_valores_local_utc = convierte_formato_fecha($cadena_hora_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_valores_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                    // Cadena de valores
                    switch ($this->params["granularidad"])
                    {
                        case GRANULARIDAD_TIEMPO_REAL:
                        {
                            $cadena_valores_sensor = NodoSensor::dame_cadena_valores_sensor(
								ID_NINGUNO,
                                ID_NINGUNO,
                                $cadena_hora_valores_base_datos_utc,
                                $cadena_valores,
                                $fila_sensor_evento_activado["clase"],
                                $fila_sensor_evento_activado["parametros_clase"],
                                $fila_sensor_evento_activado["incrementos_tiempo_real_horarios"],
                                $this->params["granularidad"],
                                SEPARADOR_VALOR_INCREMENTO_SENSOR,
                                FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                                NULL);
                            break;
                        }
                        case GRANULARIDAD_CUARTOHORARIA:
                        case GRANULARIDAD_HORARIA:
                        {
                            $cadena_valores_sensor = NodoSensor::dame_cadena_valores_clase_sensor(
                                ID_NINGUNO,
                                ID_NINGUNO,
                                $cadena_hora_valores_base_datos_utc,
                                $cadena_valores,
                                $fila_sensor_evento_activado["clase"],
                                $fila_sensor_evento_activado["parametros_clase"],
                                $this->params["granularidad"],
                                NULL);
                            break;
                        }
                    }
                    $texto_sensor = "<li>".
                    $texto_sensor = htmlspecialchars($nombre_sensor, ENT_QUOTES).": ".$cadena_valores_sensor." (".$cadena_hora_valores_local_local.")";
                    $descripcion_granularidad = NULL;
                    switch ($this->params["granularidad"])
                    {
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            $descripcion_granularidad .= $this->idiomas->_("cuartohorarios");
                            break;
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $descripcion_granularidad .= $this->idiomas->_("horarios");
                            break;
                        }
                    }
                    if ($descripcion_granularidad !== NULL)
                    {
                        $texto_sensor .= " (".$descripcion_granularidad.")";
                    }
                    $texto_sensor .= "</li>".
                    $texto_sensores .= $texto_sensor;
                }
                $info .= $texto_titulo_sensores.": ".$texto_sensores;
                $info .= "</ul>";
            }
            else
            {
                $info .= $this->idiomas->_("No hay sensores con el evento activado")."<br/>";
            }
            $info .= "<br/>";

            // Tablas de rangos de días y de periodos
            $info .= $this->dame_tabla_rangos_dias();
            $info .= "<br/>";
            $info .= $this->dame_tabla_periodos();

            return ($info);
		}


        function dame_conf()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $conf = array();
            $conf["ID"] = $this->id;
            $conf["GRANULARIDAD"] = $this->params["granularidad"];
            $conf["TIPO"] = $this->params["tipo"];
            $conf["PARAMS"] = $this->params["parametros"];

            $rangos_dias = array();
			$consulta_rangos_dias = "
				SELECT *
				FROM rangos_dias
				WHERE
                    (origen = '".ORIGEN_RANGOS_DIAS_EVENTO."')
                    AND (id_origen = '".$bd_red->_($this->id)."')";
			$res_rangos_dias = $bd_red->ejecuta_consulta($consulta_rangos_dias);
            if ($res_rangos_dias == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_rangos_dias."'");
            }
			while ($fila_rango_dias = $res_rangos_dias->dame_siguiente_fila())
			{
				$rango_dias = new RangoDias($fila_rango_dias);
				array_push($rangos_dias, $rango_dias->dame_conf());
			}
			$conf["RANGOS_DIAS"] = $rangos_dias;

            $periodos = array();
			$consulta_periodos = "
				SELECT *
				FROM periodos
				WHERE
                    (origen = '".ORIGEN_PERIODOS_EVENTO."')
                    AND (id_origen = '".$bd_red->_($this->id)."')";
			$res_periodos = $bd_red->ejecuta_consulta($consulta_periodos);
            if ($res_periodos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_periodos."'");
            }
			while ($fila_periodo = $res_periodos->dame_siguiente_fila())
			{
				$periodo = new Periodo($fila_periodo);
				array_push($periodos, $periodo->dame_conf());
			}
			$conf["PERIODOS"] = $periodos;

            return ($conf);
        }


        //
        // Funciones de parámetros de eventos
        //


        // Devuelve las granularidades de eventos correspondientes a una clase de sensor
        static function dame_granularidades_evento_clase_sensor($clase_sensor)
        {
            $granularidades_evento = array();
            switch ($clase_sensor)
            {
                case CLASE_NINGUNA:
                case CLASE_TODAS:
                {
                    array_push($granularidades_evento, GRANULARIDAD_TIEMPO_REAL);
                    array_push($granularidades_evento, GRANULARIDAD_CUARTOHORARIA);
                    array_push($granularidades_evento, GRANULARIDAD_HORARIA);
                    break;
                }
                default:
                {
                    $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                    $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                    $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

                    array_push($granularidades_evento, GRANULARIDAD_TIEMPO_REAL);
                    if ($clase_granularidad_cuartohoraria == true)
                    {
                        array_push($granularidades_evento, GRANULARIDAD_CUARTOHORARIA);
                    }
                    if ($clase_procesado_valores == true)
                    {
                        array_push($granularidades_evento, GRANULARIDAD_HORARIA);
                    }
                    break;
                }
            }

            return ($granularidades_evento);
        }


        // Devuelve los tipos de eventos correspondientes a una clase de sensor, origen y granularidad
        static function dame_tipos_evento_clase_sensor_origen_granularidad($clase_sensor, $origen, $granularidad)
        {
            // Si la clase es ninguna no hay tipos de evento
            $tipos_evento = array();
            if ($clase_sensor == CLASE_NINGUNA)
            {
                return ($tipos_evento);
            }

            // Eventos según clase de sensor
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_TEMPERATURA:
                case CLASE_SENSOR_HUMEDAD:
                case CLASE_SENSOR_LUZ_INTERIOR:
                case CLASE_SENSOR_VIENTO:
                {
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALORES_MINIMO_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INTERVALO_VALORES);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_EXACTO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_DIFERENTE);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_REPETIDO);
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_COMPRA_ENERGIA:
                case CLASE_SENSOR_GAS:
                case CLASE_SENSOR_AGUA:
                {
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALORES_MINIMO_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INTERVALO_VALORES);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_REPETIDO);
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALORES_MINIMO_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INTERVALO_VALORES);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_EXACTO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_DIFERENTE);
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MINIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_VALORES_MINIMO_MAXIMO);
                    array_push($tipos_evento, TIPO_EVENTO_INTERVALO_VALORES);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_EXACTO);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_DIFERENTE);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_EXACTO_BITS);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_DIFERENTE_BITS);
                    array_push($tipos_evento, TIPO_EVENTO_VALOR_REPETIDO);
                    break;
                }
                default:
                {
                    throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
                }
            }

            // Si la granularidad no es tiempo real, la clase de sensor es incremental y tiene procesado de valores,
            // se añaden los tipos de evento de incrementos acumulados máximos
            if ($granularidad != GRANULARIDAD_TIEMPO_REAL)
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $tipo_clase = $caracteristicas_clase_sensor["tipo"];
                $procesado_valores_clase = $caracteristicas_clase_sensor["procesado_valores"];

                if (($tipo_clase == TIPO_CLASE_SENSOR_INCREMENTAL) && ($procesado_valores_clase == true))
                {
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL);
                    array_push($tipos_evento, TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO);
                }
            }

            // Si el origen es sensor y la granularidad es horaria, se añaden los tipos de evento de línea base y de perfil horario
            if (($origen == ORIGEN_EVENTO_SENSOR) && ($granularidad == GRANULARIDAD_HORARIA))
            {
                array_push($tipos_evento, TIPO_EVENTO_LINEA_BASE);
                array_push($tipos_evento, TIPO_EVENTO_PERFIL_HORARIO);
            }

            return ($tipos_evento);
        }


        // Devuelve si un tipo de evento es persistente
        static function dame_tipo_evento_persistente($tipo_evento)
        {
            switch ($tipo_evento)
            {
                default:
                {
                    return (true);
                }
            }
        }


        // Devuelve la descripción del tipo de evento
        static function dame_descripcion_tipo_evento($tipo_evento)
        {
            switch ($tipo_evento)
            {
                case TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO:
                {
                    $descripcion = "Incremento temporal mínimo";
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO:
                {
                    $descripcion = "Incremento temporal máximo";
                    break;
                }
                case TIPO_EVENTO_VALOR_MINIMO:
                {
                    $descripcion = "Valor mínimo";
                    break;
                }
                case TIPO_EVENTO_VALOR_MAXIMO:
                {
                    $descripcion = "Valor máximo";
                    break;
                }
                case TIPO_EVENTO_VALORES_MINIMO_MAXIMO:
                {
                    $descripcion = "Valores mínimo y máximo";
                    break;
                }
                case TIPO_EVENTO_INTERVALO_VALORES:
                {
                    $descripcion = "Intervalo de valores";
                    break;
                }
                case TIPO_EVENTO_VALOR_EXACTO:
                {
                    $descripcion = "Valor exacto";
                    break;
                }
                case TIPO_EVENTO_VALOR_DIFERENTE:
                {
                    $descripcion = "Valor diferente";
                    break;
                }
                case TIPO_EVENTO_VALOR_EXACTO_BITS:
                {
                    $descripcion = "Valor exacto (bits)";
                    break;
                }
                case TIPO_EVENTO_VALOR_DIFERENTE_BITS:
                {
                    $descripcion = "Valor diferente (bits)";
                    break;
                }
                case TIPO_EVENTO_VALOR_REPETIDO:
                {
                    $descripcion = "Valor repetido";
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                {
                    $descripcion = "Incremento acumulado máximo (periodo de tiempo actual)";
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                {
                    $descripcion = "Incremento acumulado máximo (últimos periodos de tiempo)";
                    break;
                }
                case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                {
                    $descripcion = "Incremento acumulado máximo (últimos periodos de tiempo)";
                    break;
                }
                case TIPO_EVENTO_LINEA_BASE:
                {
                    $descripcion = "Línea base";
                    break;
                }
                case TIPO_EVENTO_PERFIL_HORARIO:
                {
                    $descripcion = "Perfil horario";
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


        // Devuelve la descripción del origen del evento
        static function dame_descripcion_origen_evento($origen_evento)
        {
            switch ($origen_evento)
            {
                case ORIGEN_EVENTO_SENSOR:
                {
                    $descripcion = "Sensor";
                    break;
                }
                case ORIGEN_EVENTO_GRUPO_SENSORES:
                {
                    $descripcion = "Grupo";
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


        // Devuelve el nombre del origen del evento
        static function dame_nombre_origen_evento($origen_evento, $id_origen_evento)
        {
            $idiomas = new Idiomas();

            switch ($origen_evento)
            {
                case ORIGEN_EVENTO_SENSOR:
                {
                    $nombre_origen = dame_nombre_sensor($id_origen_evento);
                    break;
                }
                case ORIGEN_EVENTO_GRUPO_SENSORES:
                {
                    $nombre_origen = dame_nombre_grupo_sensores($id_origen_evento);
                    break;
                }
                default:
                {
                    $nombre_origen = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($nombre_origen);
        }


        // Devuelve la descripción del periodo de tiempo de un evento
        static function dame_descripcion_periodo_tiempo_evento($periodo_tiempo_evento)
        {
            switch ($periodo_tiempo_evento)
            {
                case PERIODO_TIEMPO_EVENTO_HORA:
                {
                    $descripcion = "Hora";
                    break;
                }
                case PERIODO_TIEMPO_EVENTO_DIA:
                {
                    $descripcion = "Día";
                    break;
                }
                case PERIODO_TIEMPO_EVENTO_SEMANA:
                {
                    $descripcion = "Semana";
                    break;
                }
                case PERIODO_TIEMPO_EVENTO_MES:
                {
                    $descripcion = "Mes";
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


        // Devuelve la descripción de los parámetros del evento
        static function dame_descripcion_parametros_evento($clase, $granularidad, $tipo, $cadena_parametros)
        {
            $idiomas = new Idiomas();

            // Parámetros del evento
            $parametros = Evento::dame_nombres_valores_parametros_evento(
                $clase,
                $granularidad,
                $tipo,
                $cadena_parametros);
            if ($parametros !== NULL)
            {
                switch ($tipo)
                {
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                    {
                        $descripcion_periodo_tiempo = $parametros["descripcion_periodo_tiempo"];
                        $cadena_parametros_campo = $parametros["cadena_parametros_campo"];
                        $descripcion_campo = $parametros["descripcion_campo"];
                        $descripcion_parametros .= "(".strtolower($descripcion_periodo_tiempo).") ".$cadena_parametros_campo." (".strtolower($descripcion_campo).")";
                        break;
                    }
                    case TIPO_EVENTO_LINEA_BASE:
                    {
                        $nombre_linea_base = $parametros["nombre_linea_base"];
                        $cadena_parametros_campo = $parametros["cadena_parametros_campo"];
                        $descripcion_parametros = "(".$nombre_linea_base.") ".$cadena_parametros_campo;
                        break;
                    }
                    default:
                    {
                        $cadena_parametros_campo = $parametros["cadena_parametros_campo"];
                        $descripcion_campo = $parametros["descripcion_campo"];
                        $descripcion_parametros = $cadena_parametros_campo." (".strtolower($descripcion_campo).")";
                        break;
                    }
                }
            }
            else
            {
                $descripcion_parametros = $idiomas->_("Ninguno");
            }
            return ($descripcion_parametros);
        }


        //
        // Funciones auxiliares
        //


        static function dame_administracion_eventos()
        {
            $administracion_eventos = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_eventos"] == VALOR_SI);
            return ($administracion_eventos);
        }


        static function dame_ids_eventos_usuario_actual(
            $ids_sensores_usuario = NULL,
            $ids_grupos_sensores_usuario = NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los ids de sensores y de grupos de sensores visibles por el usuario actual
            if ($ids_sensores_usuario === NULL)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            }
            if ($ids_grupos_sensores_usuario === NULL)
            {
                $ids_grupos_sensores_usuario = dame_ids_grupos_sensores_usuario_actual(true);
            }

            // Identificadores de eventos
            $ids_eventos = array();

            // Consulta de eventos
            $consulta_eventos = "
                SELECT
                    id,
                    origen,
                    id_origen
                FROM eventos
                WHERE
                    red = '".$_SESSION["id_red"]."'
                ORDER BY nombre ASC";
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }
            while ($fila_evento = $res_eventos->dame_siguiente_fila())
            {
                $id = $fila_evento["id"];
                $origen = $fila_evento["origen"];
                $id_origen = $fila_evento["id_origen"];

                $anyadir_evento = true;
                switch ($origen)
                {
                    case ORIGEN_EVENTO_SENSOR:
                    {
                        if (in_array($id_origen, $ids_sensores_usuario) == false)
                        {
                            $anyadir_evento = false;
                        }
                        break;
                    }
                    case ORIGEN_EVENTO_GRUPO_SENSORES:
                    {
                        if (in_array($id_origen, $ids_grupos_sensores_usuario) == false)
                        {
                            $anyadir_evento = false;
                        }
                        break;
                    }
                }

                if ($anyadir_evento == true)
                {
                    array_push($ids_eventos, $id);
                }
            }

            return ($ids_eventos);
        }


        function dame_filas_sensores_evento_activado()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan las filas de los sensores del evento
            $consulta_sensores = "
                SELECT *
                FROM sensores
                WHERE";
            switch ($this->params["origen"])
            {
                case ORIGEN_EVENTO_SENSOR:
                {
                    $consulta_sensores .= "
                        id = '".$this->params["id_origen"]."'";
                    break;
                }
                case ORIGEN_EVENTO_GRUPO_SENSORES:
                {
                    $consulta_sensores .= "
                        grupo = '".$this->params["id_origen"]."'";
                    break;
                }
            }
            $consulta_sensores .= "
                ORDER BY nombre ASC";
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);

            // Se guardan las filas de los sensores con el evento activado
            $filas_sensores_evento_activado = array();
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $cadena_ids_eventos_activados_tiempo_real_sensor = $fila_sensor["eventos_activados"];
                $cadena_ids_eventos_activados_clase_cuartoshora_sensor = $fila_sensor["eventos_activados_clase_cuartoshora"];
                $cadena_ids_eventos_activados_clase_horas_sensor = $fila_sensor["eventos_activados_clase_horas"];
                $ids_eventos_activados_sensor = array();
                switch ($this->params["granularidad"])
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        if ($cadena_ids_eventos_activados_tiempo_real_sensor != "")
                        {
                            $ids_eventos_activados_tiempo_real_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_tiempo_real_sensor);
                            $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_tiempo_real_sensor);
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    {
                        if ($cadena_ids_eventos_activados_clase_cuartoshora_sensor != "")
                        {
                            $ids_eventos_activados_clase_cuartoshora_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_clase_cuartoshora_sensor);
                            $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_clase_cuartoshora_sensor);
                        }
                        break;
                    }
                    case GRANULARIDAD_HORARIA:
                    {
                        if ($cadena_ids_eventos_activados_clase_horas_sensor != "")
                        {
                            $ids_eventos_activados_clase_horas_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_eventos_activados_clase_horas_sensor);
                            $ids_eventos_activados_sensor = array_merge($ids_eventos_activados_sensor, $ids_eventos_activados_clase_horas_sensor);
                        }
                        break;
                    }
                }
                if (in_array($this->id, $ids_eventos_activados_sensor) == true)
                {
                    array_push($filas_sensores_evento_activado, $fila_sensor);
                }
            }
            return ($filas_sensores_evento_activado);
        }


        function dame_tabla_rangos_dias()
        {
            $tabla_rangos_dias = "";
            if ((Evento::dame_administracion_eventos() == true) || (RangoDias::dame_numero_rangos_dias(ORIGEN_RANGOS_DIAS_EVENTO, $this->id) > 0))
            {
                $id_elemento_rangos_dias_evento = "rangos-dias-".ORIGEN_RANGOS_DIAS_EVENTO.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $tabla_rangos_dias .= "<div id='".$id_elemento_rangos_dias_evento."' class='contenedor-detalle-tabla-datos'>".
                    RangoDias::dame_tabla_rangos_dias(ORIGEN_RANGOS_DIAS_EVENTO, $this->id)."</div>";
            }
            return ($tabla_rangos_dias);
        }


        function dame_tabla_periodos()
        {
            $tabla_periodos = "";
            if ((Evento::dame_administracion_eventos() == true) || (Periodo::dame_numero_periodos(ORIGEN_PERIODOS_EVENTO, $this->id) > 0))
            {
                $id_elemento_periodos_evento = "periodos-".ORIGEN_PERIODOS_EVENTO.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $tabla_periodos .= "<div id='".$id_elemento_periodos_evento."' class='contenedor-detalle-tabla-datos'>".
                    Periodo::dame_tabla_periodos(ORIGEN_PERIODOS_EVENTO, $this->id)."</div>";
            }
            return ($tabla_periodos);
        }


        static function dame_nombres_valores_parametros_evento(
            $clase_sensor,
            $granularidad,
            $tipo_evento,
            $cadena_parametros_evento)
        {
            $idiomas = new Idiomas();

            if ($cadena_parametros_evento == "")
            {
                return (NULL);
            }

            // Nombres y valores de parámetros de evento (generales y específicos de tipo)
            $nombres_valores_parametros_evento = array();

            // Parámetros comunes a todos los eventos
            $cadena_parametros_campo_configurado = NULL;
            $campo_configurado = NULL;
            $descripcion_campo_configurado = NULL;

            // Campos según la granularidad
            switch ($granularidad)
            {
                case GRANULARIDAD_TIEMPO_REAL:
                {
                    $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                    $campos_puntuales_clase_sensor = dame_campos_puntuales_clase_sensor($clase_sensor);
                    $campos_incrementos_clase_sensor = dame_campos_incrementos_clase_sensor($clase_sensor);
                    break;
                }
                case GRANULARIDAD_CUARTOHORARIA:
                case GRANULARIDAD_HORARIA:
                {
                    $campos_clase_sensor = dame_campos_horarios_clase_sensor($clase_sensor);
                    break;
                }
                default:
                {
                    throw new Exception("Granularidad incorrecta: '".$granularidad."'");
                }
            }

            // Si la granularidad en tiempo real y hay campos puntuales e incrementos, la lista de valores va por 'parejas' (valor / incremento valor)
            // Nota: Si hay campos puntuales e incrementos, debe haber el mismo número de campos puntuales e incrementos
            if (($granularidad == GRANULARIDAD_TIEMPO_REAL) &&
                (count($campos_puntuales_clase_sensor) > 0) &&
                (count($campos_incrementos_clase_sensor) > 0))
            {
                if (count($campos_puntuales_clase_sensor) == 1)
                {
                    $cadena_parametros_campo = $cadena_parametros_evento;
                    $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_evento);
                    $cadena_parametros_campo_configurado = $cadena_parametros_campo_formateada;
                    $campo_puntual = $campos_puntuales_clase_sensor[0];
                    $campo_incremento = $campos_incrementos_clase_sensor[0];
                    $campo_configurado = $campo_puntual."-".$campo_incremento;
                    $descripcion_campo_puntual = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_puntual);
                    $descripcion_campo_incremento = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_incremento);
                    $descripcion_campo_configurado = $descripcion_campo_puntual." / ".$descripcion_campo_incremento;
                }
                else
                {
                    // Si hay más de un campo puntual de clase de sensor:
                    // - Si sólo hay configurado un campo se devuelven los parámetros y el campo correspondiente,
                    // - Si hay más de un campo configurado se devuelve NULL (para utilizar todos los parámetros)
                    $numero_parametros_campo = 0;
                    $cadenas_parametros_campos = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_parametros_evento);
                    $indice_campo = 0;
                    $cadena_parametros_primer_campo_configurado = NULL;
                    $primer_campo_configurado = NULL;
                    $descripcion_primer_campo_configurado = NULL;
                    foreach ($cadenas_parametros_campos as $cadena_parametros_campo)
                    {
                        if ($cadena_parametros_campo != "")
                        {
                            $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_campo);
                            $numero_parametros_campo += 1;
                            if ($numero_parametros_campo == 1)
                            {
                                $cadena_parametros_primer_campo_configurado = $cadena_parametros_campo_formateada;
                                $campo_puntual = $campos_puntuales_clase_sensor[$indice_campo];
                                $campo_incremento = $campos_incrementos_clase_sensor[$indice_campo];
                                $primer_campo_configurado = $campo_puntual."-".$campo_incremento;
                                $descripcion_campo_puntual = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_puntual);
                                $descripcion_campo_incremento = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_incremento);
                                $descripcion_primer_campo_configurado = $descripcion_campo_puntual." / ".$descripcion_campo_incremento;
                            }
                        }
                        else
                        {
                            $indice_campo += 1;
                        }
                    }
                    if ($numero_parametros_campo > 1)
                    {
                        return (NULL);
                    }
                    $cadena_parametros_campo_configurado = $cadena_parametros_primer_campo_configurado;
                    $campo_configurado = $primer_campo_configurado;
                    $descripcion_campo_configurado = $descripcion_primer_campo_configurado;
                }
            }
            else
            {
                switch ($tipo_evento)
                {
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                    {
                        $parametros_evento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_evento);
                        $campo_configurado = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_CAMPO_SENSOR];
                        $periodo_tiempo = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO];
                        $cadena_parametros_campo = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PARAMETROS_CAMPO];
                        $descripcion_periodo_tiempo = Evento::dame_descripcion_periodo_tiempo_evento($periodo_tiempo);
                        $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_campo);
                        $cadena_parametros_campo_configurado = $cadena_parametros_campo_formateada;
                        $descripcion_campo_configurado = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_configurado);

                        $nombres_valores_parametros_evento["periodo_tiempo"] = $periodo_tiempo;
                        $nombres_valores_parametros_evento["descripcion_periodo_tiempo"] = $descripcion_periodo_tiempo;
                        break;
                    }
                    case TIPO_EVENTO_LINEA_BASE:
                    {
                        $parametros_evento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_evento);
                        $id_linea_base = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_LINEA_BASE_ID_LINEA_BASE];
                        $cadena_parametros_campo = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_LINEA_BASE_PARAMETROS_CAMPO];
                        $nombre_linea_base = dame_nombre_linea_base($id_linea_base);
                        $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_campo);
                        $cadena_parametros_campo_configurado = $cadena_parametros_campo_formateada;

                        $nombres_valores_parametros_evento["id_linea_base"] = $id_linea_base;
                        $nombres_valores_parametros_evento["nombre_linea_base"] = $nombre_linea_base;
                        break;
                    }
                    case TIPO_EVENTO_PERFIL_HORARIO:
                    {
                        $parametros_evento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_evento);
                        $campo_configurado = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_CAMPO_SENSOR];
                        $intervalo_valores = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_INTERVALO_VALORES];
                        $numero_dias_perfil_horario = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_NUMERO_DIAS_PERFIL_HORARIO];
                        $tipo_perfil_horario = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_TIPO_PERFIL_HORARIO];
                        $cadena_agrupaciones_dias_semana = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_AGRUPACIONES_DIAS_SEMANA];
                        $cadena_horario_semanal = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_HORARIO_SEMANAL];
                        $cadena_exclusion_fechas = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_EXCLUSION_FECHAS];
                        $cadena_parametros_campo = $parametros_evento[INDICE_PARAMETRO_TIPO_EVENTO_PERFIL_HORARIO_PARAMETROS_CAMPO];
                        $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_campo);
                        $cadena_parametros_campo_configurado = $cadena_parametros_campo_formateada;
                        $descripcion_campo_configurado = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_configurado);

                        $nombres_valores_parametros_evento["intervalo_valores"] = $intervalo_valores;
                        $nombres_valores_parametros_evento["numero_dias_perfil_horario"] = $numero_dias_perfil_horario;
                        $nombres_valores_parametros_evento["tipo_perfil_horario"] = $tipo_perfil_horario;
                        $nombres_valores_parametros_evento["agrupaciones_dias_semana"] = dame_agrupaciones_dias_semana($cadena_agrupaciones_dias_semana);
                        $nombres_valores_parametros_evento["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                        $nombres_valores_parametros_evento["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                        break;
                    }
                    default:
                    {
                        if (count($campos_clase_sensor) == 1)
                        {
                            $cadena_parametros_campo = $cadena_parametros_evento;
                            $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_evento);
                            $cadena_parametros_campo_configurado = $cadena_parametros_campo_formateada;
                            $campo_configurado = $campos_clase_sensor[0];
                            $descripcion_campo_configurado = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_configurado);
                        }
                        else
                        {
                            // Si hay más de un campo de clase de sensor:
                            // - Si sólo hay configurado un campo se devuelven los parámetros y el campo correspondiente,
                            // - Si hay más de un campo configurado se devuelve NULL (para utilizar todos los parámetros)
                            $numero_parametros_campo = 0;
                            $cadenas_parametros_campos = explode(SEPARADOR_PARAMETROS_VALORES, $cadena_parametros_evento);
                            $indice_campo = 0;
                            $cadena_parametros_campo_configurado = "";
                            $campo_configurado = NULL;
                            $descripcion_campo_configurado = NULL;
                            foreach ($cadenas_parametros_campos as $cadena_parametros_campo)
                            {
                                if ($cadena_parametros_campo != "")
                                {
                                    $numero_parametros_campo += 1;
                                    $cadena_parametros_campo_formateada = Evento::formatea_parametros_campo_evento($cadena_parametros_campo);
                                    if ($numero_parametros_campo > 1)
                                    {
                                        $cadena_parametros_campo_configurado .= " ".SEPARADOR_PARAMETROS_VALORES." ";
                                    }
                                    $cadena_parametros_campo_configurado .= $cadena_parametros_campo_formateada;
                                    if ($numero_parametros_campo == 1)
                                    {
                                        $campo_configurado = $campos_clase_sensor[$indice_campo];
                                        $descripcion_campo_configurado = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_configurado);
                                    }
                                    else
                                    {
                                        $campo_configurado = CAMPO_TODOS;
                                        $descripcion_campo_configurado = $idiomas->_("Todos");
                                    }
                                }
                                else
                                {
                                    $indice_campo += 1;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // Parámetros comunes a todos los eventos
            $nombres_valores_parametros_evento["cadena_parametros_campo"] = $cadena_parametros_campo_configurado;
            $nombres_valores_parametros_evento["campo"] = $campo_configurado;
            $nombres_valores_parametros_evento["descripcion_campo"] = $descripcion_campo_configurado;

            // Se devuelven los parámetros
            return ($nombres_valores_parametros_evento);
        }


        static function formatea_parametros_campo_evento($parametros_evento)
        {
            $parametros_evento_sin_espacios = str_replace(" ", "", $parametros_evento);
            $parametros_evento_formateado = str_replace(SEPARADOR_PARAMETROS_SIMPLES, SEPARADOR_PARAMETROS_SIMPLES." ", $parametros_evento_sin_espacios);
            return ($parametros_evento_formateado);
        }
	}
?>
