<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');


	class HistoricoRegla
	{
        // Funciones estáticas de histórico de regla


		// Devuelve la cabecera para el histórico de reglas
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Fecha"),
                $idiomas->_("Regla"),
				$idiomas->_("Activación"),
				$idiomas->_("Causa")
			));
        }


        // Devuelve la consulta para el histórico de reglas
        static function dame_consulta_historico_reglas(
            $filtro,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT *
                FROM activaciones_reglas
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($filtro != "")
            {
                $campos = array("regla");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            $consulta .= "
                    ORDER BY
                        hora DESC,
                        id DESC";
			return ($consulta);
        }


        // Devuelve la tabla de históricos de reglas
        static function dame_tabla_historico_reglas(
            $filtro,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            &$limite_elementos_tabla_historico_superado)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se crea la tabla
            $params_tabla = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HISTORICO_REGLAS,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-actuadores-historico-reglas",
                $idiomas->_("Histórico de reglas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = HistoricoRegla::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se recuperan los nombres de las reglas del usuario
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $nombres_reglas_usuario = HistoricoRegla::dame_nombres_reglas_usuario_actual();
            }

            // Se añade cada uno de los históricos de reglas a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_historicos_reglas = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                $consulta_historico_reglas = HistoricoRegla::dame_consulta_historico_reglas(
                    $filtro,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc);
                $res_historico_reglas = $bd_datos->ejecuta_consulta($consulta_historico_reglas);
                if ($res_historico_reglas == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_historico_reglas."'");
                }
                $limite_elementos_tabla_historico_superado = false;
                while (($fila_historico_regla = $res_historico_reglas->dame_siguiente_fila()) && ($limite_elementos_tabla_historico_superado == false))
                {
                    $anyadir_historico_regla = true;
                    if ($mostrar_todos_actuadores == false)
                    {
                        if (in_array($fila_historico_regla['regla'], $nombres_reglas_usuario) == false)
                        {
                            $anyadir_historico_regla = false;
                        }
                    }

                    if ($anyadir_historico_regla == true)
                    {
                        if ($numero_historicos_reglas == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                        {
                            $limite_elementos_tabla_historico_superado = true;
                            break;
                        }
                        else
                        {
                            $historico_regla = new HistoricoRegla($fila_historico_regla);
                            $tabla->anyade_fila(
                                "datosHistoricoRegla__".$fila_historico_regla['id'],
                                $historico_regla->dame_datos_tabla()
                            );
                            $numero_historicos_reglas += 1;
                        }
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de activaciones y desactivaciones de reglas").": ".$numero_historicos_reglas;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de histórico regla


        public $idiomas;

        public $id;
		public $params;


        // Funciones de histórico de reglas


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
            $nombre_regla = $icono_dato_erroneo;
            $activada = $icono_dato_erroneo;
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

                // Nombre de regla
                $nombre_regla = htmlspecialchars($this->params['regla'], ENT_QUOTES);

                // Activada
                if ($this->params['activada'] == VALOR_SI)
                {
                    $activada = "<i class='icon-circle color-verde'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Activación"), ENT_QUOTES)."</texto></i>";
                }
                else
                {
                    $activada = "<i class='icon-circle color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desactivación"), ENT_QUOTES)."</texto></i>";
                }

                // Causas de activación y desactivación de las reglas
                switch ($this->params['causa'])
                {
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_INICIALIZACION:
                    {
                        $causa = $this->idiomas->_("Inicialización");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_ADICION:
                    {
                        $causa = $this->idiomas->_("Adición");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_MODIFICACION:
                    {
                        $causa = $this->idiomas->_("Modificación");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_ELIMINACION:
                    {
                        $causa = $this->idiomas->_("Eliminación");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_CONFIGURACION:
                    {
                        $causa = $this->idiomas->_("Configuración");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_SUCESO:
                    {
                        $causa = $this->idiomas->_("Suceso");
                        break;
                    }
                    case CAUSA_ACTIVACION_DESACTIVACION_REGLA_HABILITACION:
                    {
                        $causa = $this->idiomas->_("Habilitación");
                        break;
                    }
                    default:
                    {
                        throw new Exception("Causa desconocida: '".$this->params['causa']."'");
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
                $nombre_regla,
				$activada,
				$causa
			));
		}


        function dame_detalles_tabla()
		{
            // Salto de línea (sólo si hay sucesos o sensores activados)
            if (($this->params["sucesos_activados"] == "") && ($this->params["nombres_sensores_activados"] == ""))
            {
                $info = "<i class='icon-info-sign color-azul'></i> ";
                $info .= $this->idiomas->_("No hay sucesos ni sensores activados");
            }
            else
            {
                // Sucesos activados
                $info = "<i class='icon-info-sign color-azul'></i> ";
                if ($this->params["sucesos_activados"] == "")
                {
                    $info .= $this->idiomas->_("No hay sucesos activados")."<br/>";
                }
                else
                {
                    $info .= $this->idiomas->_("Sucesos activados").": ";
                    $info .= "<ul>";
                    $sucesos_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["sucesos_activados"]);
                    foreach ($sucesos_activados as $suceso_activado)
                    {
                        $info .= "<li>".$suceso_activado."</li>";
                    }
                    $info .= "</ul>";
                }

                // Salto de línea (sólo si hay sucesos o sensores activados)
                $info .= "<br/>";

                // Sensores activados
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                if ($this->params["nombres_sensores_activados"] == "")
                {
                    $info .= $this->idiomas->_("No hay sensores activados");
                }
                else
                {
                    $info .= $this->idiomas->_("Sensores activados").": ";
                    $info .= "<ul>";
                    $nombres_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["nombres_sensores_activados"]);
                    $clases_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["clases_sensores_activados"]);
                    $parametros_clases_sensores_activados = explode(SEPARADOR_PARAMETROS_SUPERCOMPUESTOS, $this->params["parametros_clases_sensores_activados"]);
                    $incrementos_tiempo_real_horarios_sensores_activados = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params["incrementos_tiempo_real_horarios_sensores_activados"]);
                    if ($this->params["horas_valores_sensores_activados"] == "")
                    {
                        $horas_valores_sensores_activados = array();
                        $valores_sensores_activados = array();
                    }
                    else
                    {
                        $horas_valores_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["horas_valores_sensores_activados"]);
                        $valores_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["valores_sensores_activados"]);
                    }
                    if ($this->params["horas_valores_clase_cuartoshora_sensores_activados"] == "")
                    {
                        $horas_valores_clase_cuartoshora_sensores_activados = array();
                        $valores_clase_cuartoshora_sensores_activados = array();
                    }
                    else
                    {
                        $horas_valores_clase_cuartoshora_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["horas_valores_clase_cuartoshora_sensores_activados"]);
                        $valores_clase_cuartoshora_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["valores_clase_cuartoshora_sensores_activados"]);
                    }
                    if ($this->params["horas_valores_clase_horas_sensores_activados"] == "")
                    {
                        $horas_valores_clase_horas_sensores_activados = array();
                        $valores_clase_horas_sensores_activados = array();
                    }
                    else
                    {
                        $horas_valores_clase_horas_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["horas_valores_clase_horas_sensores_activados"]);
                        $valores_clase_horas_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["valores_clase_horas_sensores_activados"]);
                    }
                    $timeouts_envio_sensores_activados = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["timeouts_envio_sensores_activados"]);
                    for ($i = 0; $i < count($nombres_sensores_activados); $i++)
                    {
                        // Nombre de sensor
                        $cadena_sensor_activado = htmlspecialchars($nombres_sensores_activados[$i], ENT_QUOTES);
                        if ($timeouts_envio_sensores_activados[$i] == VALOR_SI)
                        {
                            $cadena_sensor_activado .= " (<i class='icon-bell-alt color-rojo'></i>)";
                        }
                        $cadena_sensor_activado .= ": ";

                        // Flag de valores
                        $hay_valores_sensor = false;
                        $hay_valores_clase_sensor = false;

                        // Valores del sensor (tiempo real)
                        $valores_sensor_activado = NULL;
                        $resultado_valores_sensor = NodoSensor::dame_cadenas_hora_valores_sensor(
                            NULL,
                            ID_NINGUNO,
                            $horas_valores_sensores_activados[$i],
                            $valores_sensores_activados[$i],
                            $clases_sensores_activados[$i],
                            $parametros_clases_sensores_activados[$i],
                            $incrementos_tiempo_real_horarios_sensores_activados[$i],
                            GRANULARIDAD_TIEMPO_REAL,
                            FORMATO_CADENA_VALORES_SENSOR_COMPLETO);
                        if ($resultado_valores_sensor !== NULL)
                        {
                            $hay_valores_sensor = true;
                            $cadena_fecha_hora_valores_local_local = $resultado_valores_sensor["cadena_fecha_hora_valores_local_local"];
                            $cadena_valores = $resultado_valores_sensor["cadena_valores"];
                            $valores_sensor_activado = $cadena_valores." (".$cadena_fecha_hora_valores_local_local.")";
                        }

                        // Valores de clase del sensor (cuartos de hora y horas)
                        $valores_clase_horas_sensor_activado = NULL;
                        $valores_clase_cuartoshora_sensor_activado = NULL;
                        $resultado_valores_clase_horas_sensor = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                            ID_NINGUNO,
                            ID_NINGUNO,
                            $horas_valores_clase_horas_sensores_activados[$i],
                            $valores_clase_horas_sensores_activados[$i],
                            $clases_sensores_activados[$i],
                            $parametros_clases_sensores_activados[$i],
                            GRANULARIDAD_CUARTOHORARIA);
                        if ($resultado_valores_clase_horas_sensor !== NULL)
                        {
                            $hay_valores_sensor = true;
                            $hay_valores_clase_sensor = true;
                            $cadena_fecha_hora_valores_clase_horas_local_local = $resultado_valores_clase_horas_sensor["cadena_fecha_hora_valores_clase_local_local"];
                            $cadena_valores_clase_horas = $resultado_valores_clase_horas_sensor["cadena_valores_clase"];
                            $valores_clase_horas_sensor_activado = $cadena_valores_clase_horas." (".$cadena_fecha_hora_valores_clase_horas_local_local.")";
                        }
                        $resultado_valores_clase_cuartoshora_sensor = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                            ID_NINGUNO,
                            ID_NINGUNO,
                            $horas_valores_clase_cuartoshora_sensores_activados[$i],
                            $valores_clase_cuartoshora_sensores_activados[$i],
                            $clases_sensores_activados[$i],
                            $parametros_clases_sensores_activados[$i],
                            GRANULARIDAD_HORARIA);
                        if ($resultado_valores_clase_cuartoshora_sensor !== NULL)
                        {
                            $hay_valores_sensor = true;
                            $hay_valores_clase_sensor = true;
                            $cadena_fecha_hora_valores_clase_cuartoshora_local_local = $resultado_valores_clase_cuartoshora_sensor["cadena_fecha_hora_valores_clase_local_local"];
                            $cadena_valores_clase_cuartoshora = $resultado_valores_clase_cuartoshora_sensor["cadena_valores_clase"];
                            $valores_clase_cuartoshora_sensor_activado = $cadena_valores_clase_cuartoshora." (".$cadena_fecha_hora_valores_clase_cuartoshora_local_local.")";
                        }

                        if ($hay_valores_sensor == false)
                        {
                            $cadena_sensor_activado .= $this->idiomas->_("Sin valores");
                        }
                        else
                        {
                            if ($valores_sensor_activado !== NULL)
                            {
                                $cadena_sensor_activado .= $valores_sensor_activado;
                            }
                            if ($hay_valores_clase_sensor == true)
                            {
                                $cadena_sensor_activado .= "<ul>";
                                if ($valores_clase_horas_sensor_activado !== NULL)
                                {
                                    $cadena_sensor_activado .= "<li>".$this->idiomas->_("Valores de clase")." (".$this->idiomas->_("horarios")."): ".$valores_clase_horas_sensor_activado."</li>";
                                }
                                if ($valores_clase_cuartoshora_sensor_activado !== NULL)
                                {
                                    $cadena_sensor_activado .= "<li>".$this->idiomas->_("Valores de clase")." (".$this->idiomas->_("cuartohorarios")."): ".$valores_clase_cuartoshora_sensor_activado."</li>";
                                }
                                $cadena_sensor_activado .= "</ul>";
                            }
                        }
                        $info .= "<li>".$cadena_sensor_activado."</li>";
                    }
                    $info .= "</ul>";
                }
            }

            // Sensor de desactivación (si no hay nombre de sensor de desactivación, la regla se ha desactivado por la desactivación de otra regla:
            // no se hace nada, ya se mostrará que no está el suceso de la regla en sucesos activados)
            if (($this->activada == 0) && ($this->causa == CAUSA_ACTIVACION_DESACTIVACION_REGLA_SUCESO) &&
                ($this->params["nombre_sensor_desactivacion"] != ""))
            {
                if ($this->params["nombres_sensores_activados"] == "")
                {
                    $info .= "<br/>";
                }
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $info .= $this->idiomas->_("Sensor de desactivación").": ";
                $info .= "<ul>";

                // Nombre de sensor
                $cadena_sensor_desactivacion = $this->params["nombre_sensor_desactivacion"];
                if ($timeouts_envio_sensores_activados[$i] == VALOR_SI)
                {
                    $cadena_sensor_desactivacion .= " (<i class='icon-bell-alt color-rojo'></i>)";
                }
                $cadena_sensor_desactivacion .= ": ";

                // Flag de valores
                $hay_valores_sensor = false;
                $hay_valores_clase_sensor = false;

                // Valores del sensor (tiempo real)
                $valores_sensor_desactivacion = NULL;
                $resultado_valores_sensor = NodoSensor::dame_cadenas_hora_valores_sensor(
                    NULL,
                    $this->params["hora_valores_sensor_desactivacion"],
                    $this->params["valores_sensor_desactivacion"],
                    $this->params["clase_sensor_desactivacion"],
                    $this->params["parametros_clase_sensor_desactivacion"],
                    $this->params["incrementos_tiempo_real_horarios_sensor_desactivacion"],
                    GRANULARIDAD_TIEMPO_REAL,
                    FORMATO_CADENA_VALORES_SENSOR_COMPLETO);
                if ($resultado_valores_sensor !== NULL)
                {
                    $hay_valores_sensor = true;
                    $cadena_fecha_hora_valores_local_local = $resultado_valores_sensor["cadena_fecha_hora_valores_local_local"];
                    $cadena_valores = $resultado_valores_sensor["cadena_valores"];
                    $valores_sensor_desactivacion = $cadena_valores." (".$cadena_fecha_hora_valores_local_local.")";
                }

                // Valores de clase del sensor (horas y cuartos de hora)
                $valores_clase_horas_sensor_desactivacion = NULL;
                $valores_clase_cuartoshora_sensor_desactivacion = NULL;
                $resultado_valores_clase_horas_sensor = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                    ID_NINGUNO,
                    ID_NINGUNO,
                    $this->params["hora_valores_clase_horas_sensor_desactivacion"],
                    $this->params["valores_clase_horas_sensor_desactivacion"],
                    $this->params["clase_sensor_desactivacion"],
                    $this->params["parametros_clase_sensor_desactivacion"],
                    GRANULARIDAD_HORARIA);
                if ($resultado_valores_clase_horas_sensor !== NULL)
                {
                    $hay_valores_sensor = true;
                    $hay_valores_clase_sensor = true;
                    $cadena_fecha_hora_valores_clase_horas_local_local = $resultado_valores_clase_horas_sensor["cadena_fecha_hora_valores_clase_local_local"];
                    $cadena_valores_clase_horas = $resultado_valores_clase_horas_sensor["cadena_valores_clase"];
                    $valores_clase_horas_sensor_desactivacion = $cadena_valores_clase_horas." (".$cadena_fecha_hora_valores_clase_horas_local_local.")";
                }
                $resultado_valores_clase_cuartoshora_sensor = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                    ID_NINGUNO,
                    ID_NINGUNO,
                    $this->params["hora_valores_clase_cuartoshora_sensor_desactivacion"],
                    $this->params["valores_clase_cuartoshora_sensor_desactivacion"],
                    $this->params["clase_sensor_desactivacion"],
                    $this->params["parametros_clase_sensor_desactivacion"],
                    GRANULARIDAD_CUARTOHORARIA);
                if ($resultado_valores_clase_cuartoshora_sensor !== NULL)
                {
                    $hay_valores_sensor = true;
                    $hay_valores_clase_sensor = true;
                    $cadena_fecha_hora_valores_clase_cuartoshora_local_local = $resultado_valores_clase_cuartoshora_sensor["cadena_fecha_hora_valores_clase_local_local"];
                    $cadena_valores_clase_cuartoshora = $resultado_valores_clase_cuartoshora_sensor["cadena_valores_clase"];
                    $valores_clase_cuartoshora_sensor_desactivacion = $cadena_valores_clase_cuartoshora." (".$cadena_fecha_hora_valores_clase_cuartoshora_local_local.")";
                }

                if ($hay_valores_sensor == false)
                {
                    $cadena_sensor_desactivacion .= $this->idiomas->_("Sin valores");
                }
                else
                {
                    if ($valores_sensor_desactivacion !== NULL)
                    {
                        $cadena_sensor_desactivacion .= $valores_sensor_desactivacion;
                    }
                    if ($hay_valores_clase_sensor == true)
                    {
                        $cadena_sensor_desactivacion .= "<ul>";
                        if ($valores_clase_horas_sensor_desactivacion !== NULL)
                        {
                            $cadena_sensor_desactivacion .= "<li>".$this->idiomas->_("Valores de clase")." (".$this->idiomas->_("horarios")."): ".$valores_clase_horas_sensor_desactivacion."</li>";
                        }
                        if ($valores_clase_cuartoshora_sensor_desactivacion !== NULL)
                        {
                            $cadena_sensor_desactivacion .= "<li>".$this->idiomas->_("Valores de clase")." (".$this->idiomas->_("cuartohorarios")."): ".$valores_clase_cuartoshora_sensor_desactivacion."</li>";
                        }
                        $cadena_sensor_desactivacion .= "</ul>";
                    }
                }
                $info .= "<li>".$cadena_sensor_desactivacion."</li>";
                $info .= "</ul>";
            }

			return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_nombres_reglas_usuario_actual()
        {
            // Nombres de reglas
            $nombres_reglas = array();

            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_reglas = "
                SELECT
                    id,
                    nombre
                FROM reglas";
            $res_reglas = $bd_red->ejecuta_consulta($consulta_reglas);
            if ($res_reglas == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_reglas."'");
            }

            $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(true);
            $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(true);
            $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores_usuario);
            $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_usuario);
            while ($fila_regla = $res_reglas->dame_siguiente_fila())
            {
                $consulta_acciones_reglas = "
                    SELECT
                        COUNT(*) AS numero_acciones
                    FROM acciones_reglas
                    WHERE
                        (regla = ".$bd_red->_($fila_regla["id"]).")
                        AND (((destino = '".DESTINO_ACCION_ACTUADOR."') AND (id_destino IN (".$cadena_ids_actuadores_consulta.")))
                            OR ((destino = '".DESTINO_ACCION_GRUPO_ACTUADORES."') AND (id_destino IN (".$cadena_ids_grupos_actuadores_consulta."))))";
                $res_acciones_reglas = $bd_red->ejecuta_consulta($consulta_acciones_reglas);
                if (($res_acciones_reglas == false) || ($res_acciones_reglas->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_acciones_reglas."'");
                }
                $fila_acciones_reglas = $res_acciones_reglas->dame_siguiente_fila();
                if ($fila_acciones_reglas["numero_acciones"] > 0)
                {
                    if (in_array($fila_regla["nombre"], $nombres_reglas) == false)
                    {
                        array_push($nombres_reglas, $fila_regla["nombre"]);
                    }
                }
            }
            return ($nombres_reglas);
        }
	}
?>
