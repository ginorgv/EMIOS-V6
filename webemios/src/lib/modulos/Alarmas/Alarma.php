<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


	class Alarma
	{
        // Funciones estáticas de alarma


		// Devuelve la cabecera para la tabla de alarmas
        static function dame_cabecera_tabla($mostrar_red)
		{
            $idiomas = new Idiomas();

            $cabecera_tabla = array();
            array_push($cabecera_tabla, $idiomas->_("Fecha"));
            if ($mostrar_red == true)
            {
                array_push($cabecera_tabla, $idiomas->_("Red"));
            }
            array_push($cabecera_tabla, $idiomas->_("Origen"));
            array_push($cabecera_tabla, $idiomas->_("Descripción"));
            array_push($cabecera_tabla, $idiomas->_("Estado"));
            return ($cabecera_tabla);
        }


        // Devuelve la consulta para la tabla de alarmas
        static function dame_consulta_alarmas(
            $mostrar_red,
            $filtro,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
				SELECT *
				FROM alarmas
				WHERE
					(hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
					AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($mostrar_red == false)
            {
                $consulta .= "
                    AND (red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $campos = array("origen", "descripcion");
                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                    $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                }
            }
            $consulta .= "
                ORDER BY
                    hora DESC,
                    id DESC";
			return ($consulta);
        }


        // Devuelve la tabla de alarmas
        static function dame_tabla_alarmas(
            $modulo,
            $filtro,
            $cadena_fecha_hora_inicio_base_datos_utc = null,
            $cadena_fecha_hora_fin_base_datos_utc = null,
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
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_ALARMAS_CON_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_ALARMAS_CON_RED);
                    $mostrar_red = true;
                    break;
                }
                default:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_ALARMAS_SIN_RED;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_ALARMAS_SIN_RED);
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
                $idiomas->_("Alarmas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Alarma::dame_cabecera_tabla($mostrar_red);
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las alarmas a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_alarmas = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                // Se realiza la consulta de las alarmas
                $consulta_alarmas = Alarma::dame_consulta_alarmas(
                    $mostrar_red,
                    $filtro,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc);
                $res_alarmas = $bd_datos->ejecuta_consulta($consulta_alarmas);
                if ($res_alarmas == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_alarmas."'");
                }

                // Filas de las alarmas
                $filas_alarmas = array();
                while ($fila_alarma = $res_alarmas->dame_siguiente_fila())
                {
                    array_push($filas_alarmas, $fila_alarma);
                }

                // Si el módulo de monitorización, se muestra el nombre de la red
                // (se añaden los nombres de las redes a los datos de las filas de las alarmas)
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
                    for ($i = 0; $i < count($filas_alarmas); $i++)
                    {
                        $id_red = $filas_alarmas[$i]["red"];
                        if ($id_red == ID_NINGUNO)
                        {
                            $nombre_red = $idiomas->_("Ninguna");
                        }
                        else
                        {
                            $nombre_red = $nombres_redes[$id_red];
                        }
                        $filas_alarmas[$i]["nombre_red"] = $nombre_red;
                    }
                }

                // Se recorren las filas de las alarmas y se añaden las que cumplan con el filtro
                $limite_elementos_tabla_historico_superado = false;
                foreach ($filas_alarmas as $fila_alarma)
                {
                    $alarma = new Alarma($fila_alarma);
                    $datos_tabla = $alarma->dame_datos_tabla($mostrar_red);

                    // Se realiza el filtrado (sólo si hay que mostrar la red)
                    if ($mostrar_red == true)
                    {
                        $nombre_red = $datos_tabla[1];
                        $origen = $datos_tabla[2];
                        $descripcion = $datos_tabla[3];
                        if (($filtro != "") &&
                            (stripos($nombre_red, $filtro) === false) &&
                            (stripos($origen, $filtro) === false) &&
                            (stripos($descripcion, $filtro) === false))
                        {
                            continue;
                        }
                    }

                    if ($numero_alarmas == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                    {
                        $limite_elementos_tabla_historico_superado = true;
                        break;
                    }
                    else
                    {
                        $tabla->anyade_fila(
                            "datosAlarma__".$fila_alarma['id'],
                            $datos_tabla
                        );
                        $numero_alarmas += 1;
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de alarmas").": ".$numero_alarmas;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de alarma


        public $idiomas;

		public $id;
		public $params;


        // Funciones de alarma


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
            if ($mostrar_red == true)
            {
                $nombre_red = $icono_dato_erroneo;
            }
            $origen = $icono_dato_erroneo;
            $descripcion = $icono_dato_erroneo;
            $estado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_correcta = true;

                // Red
                if ($mostrar_red == true)
                {
                    $nombre_red = htmlspecialchars($this->params["nombre_red"], ENT_QUOTES);
                }

                // Origen, descripción y estado
                $origen = htmlspecialchars($this->params["origen"], ENT_QUOTES);
                $descripcion = htmlspecialchars($this->params["descripcion"], ENT_QUOTES);
                switch ($this->params["tipo"])
                {
                    case "EVENTOS_ALARMA":
                    case "EVENTOS_CLASE_CUARTOSHORA_ALARMA":
                    case "EVENTOS_CLASE_HORAS_ALARMA":
                    {
                        $descripcion = str_replace(",", ", ", $descripcion);
                        break;
                    }
                }
                $estado = $this->dame_estado();
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
            if ($mostrar_red == true)
            {
                array_push($datos_tabla, $nombre_red);
            }
            array_push($datos_tabla, $origen);
            array_push($datos_tabla, $descripcion);
            array_push($datos_tabla, $estado);
            return ($datos_tabla);
		}


        function dame_detalles_tabla()
		{
            $info = "";

            // Se desactiva el ratio de los sensores
            // (para recuperar las cadenas de valores de los sensores sin ratio) (si es necesario)
            $id_ratio_sensores_anterior = $_SESSION["id_ratio_sensores"];
            $_SESSION["id_ratio_sensores"] = ID_NINGUNO;
            try
            {
                $nodo = NULL;
                switch ($this->params["tipo"])
                {
                    case "CREADO":
                    case "INICIADO":
                    case "FINALIZANDO":
                    case "FINALIZADO":
                    case "CONECTADO":
                    case "DESCONECTADO":
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Nombre del servicio").": ".$this->params["origen"].".<br/>";
                        return ($info);
                    }
                    case "UP_D":
                    case "DOWN_D":
                    case "DIE_D":
                    case "TIMEOUT_ENVIO_ESTADO":
                    {
                        $id_nodo = dame_id_nodo(TIPO_NODO_DISPOSITIVO, $this->params["origen"], $this->params["red"]);
                        if ($id_nodo != ID_NINGUNO)
                        {
                            $nodo = dame_nodo(TIPO_NODO_DISPOSITIVO, $id_nodo);
                        }
                        break;
                    }
                    case "UP_A":
                    case "DOWN_A":
                    case "DIE_A":
                    case "PONG":
                    {
                        $id_nodo = dame_id_nodo(TIPO_NODO_AXON, $this->params["origen"], $this->params["red"]);
                        if ($id_nodo != ID_NINGUNO)
                        {
                            $nodo = dame_nodo(TIPO_NODO_AXON, $id_nodo);
                        }
                        break;
                    }
                    case "TIMEOUT_ENVIO":
                    case "EVENTOS_ALARMA":
                    case "EVENTOS_CLASE_CUARTOSHORA_ALARMA":
                    case "EVENTOS_CLASE_HORAS_ALARMA":
                    {
                        $id_nodo = dame_id_nodo(TIPO_NODO_SENSOR, $this->params["origen"], $this->params["red"]);
                        if ($id_nodo != ID_NINGUNO)
                        {
                            $nodo = dame_nodo(TIPO_NODO_SENSOR, $id_nodo);
                        }
                        break;
                    }
                    case "EJECUCION_ULTIMA_ACCION_ERROR":
                    {
                        $id_nodo = dame_id_nodo(TIPO_NODO_ACTUADOR, $this->params["origen"], $this->params["red"]);
                        if ($id_nodo != ID_NINGUNO)
                        {
                            $nodo = dame_nodo(TIPO_NODO_ACTUADOR, $id_nodo);
                        }
                        break;
                    }
                    default:
                    {
                        $info .= $this->idiomas->_("Alarma desconocida")." ('".$this->params["evento"]."')";
                        return ($info);
                    }
                }

                if ($nodo === NULL)
                {
                    $info .= "
                        <i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Sin información")."<br/>";
                }
                else
                {
                    switch ($this->params["tipo"])
                    {
                        case "UP_D":
                        {
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "UP_A":
                        {
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "DOWN_A":
                        case "DIE_A":
                        {
                            if ($nodo->conexion == "ON")
                            {
                                $info .= "
                                    <i class='icon-thumbs-up color-verde'></i> ".
                                    $this->idiomas->_("La conexión está activa")."<br/><br/>";
                            }
                            else
                            {
                                $info .= "
                                    <i class='icon-thumbs-down color-rojo'></i> ".
                                    $this->idiomas->_("La conexión no está activa")."<br/><br/>";
                            }
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "PONG":
                        {
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "TIMEOUT_ENVIO":
                        {
                            if (NodoSensor::dame_timeout_envio_activado($nodo->params["timeout_envio"]) == true)
                            {
                                $info .= "
                                    <i class='icon-thumbs-down color-rojo'></i> ".
                                    $this->idiomas->_("El timeout de envío está activado")."<br/><br/>";
                            }
                            else
                            {
                                $info .= "
                                    <i class='icon-thumbs-up color-verde'></i> ".
                                    $this->idiomas->_("No hay timeout de envío")."<br/><br/>";
                            }
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "EVENTOS_ALARMA":
                        {
                            $cadena_valores_evento = NodoSensor::dame_cadena_valores_sensor(
                                ID_NINGUNO,
                                ID_NINGUNO,
                                $this->params["hora"],
                                $this->params["datos"],
                                $nodo->params["clase"],
                                $nodo->params["parametros_clase"],
                                $nodo->params["incrementos_tiempo_real_horarios"],
                                GRANULARIDAD_TIEMPO_REAL,
                                SEPARADOR_VALOR_INCREMENTO_SENSOR,
                                FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                                NULL);
                            $cadena_valores_actuales = NodoSensor::dame_cadena_valores_sensor(
                                ID_NINGUNO,
                                ID_NINGUNO,
                                $nodo->params["hora_ultimos_valores"],
                                $nodo->params["ultimos_valores"],
                                $nodo->params["clase"],
                                $nodo->params["parametros_clase"],
                                $nodo->params["incrementos_tiempo_real_horarios"],
                                GRANULARIDAD_TIEMPO_REAL,
                                SEPARADOR_VALOR_INCREMENTO_SENSOR,
                                FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                                NULL);
                            $info .= "
                                <i class='icon-info-sign color-azul color-azul'></i> ".
                                $this->idiomas->_("Valores del evento").": ".$cadena_valores_evento."<br/>";
                            $info .= "
                                <i class='icon-info-sign color-azul color-azul'></i> ".
                                $this->idiomas->_("Valores actuales").": ".$cadena_valores_actuales."<br/><br/>";
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "EVENTOS_CLASE_CUARTOSHORA_ALARMA":
                        case "EVENTOS_CLASE_HORAS_ALARMA":
                        {
                            $cadena_valores_evento = NodoSensor::dame_cadena_valores_clase_sensor(
                                ID_NINGUNO,
                                ID_NINGUNO,
                                $this->params["hora"],
                                $this->params["datos"],
                                $nodo->params["clase"],
                                $nodo->params["parametros_clase"],
                                NULL);
                            switch ($this->params["tipo"])
                            {
                                case EVENTOS_CLASE_CUARTOSHORA_ALARMA:
                                {
                                    $ultimos_valores_clase = $nodo->params["ultimos_valores_clase_cuartoshora"];
                                    break;
                                }
                                case EVENTOS_CLASE_HORAS_ALARMA:
                                {
                                    $ultimos_valores_clase = $nodo->params["ultimos_valores_clase_horas"];
                                    break;
                                }
                            }
                            $info .= "
                                <i class='icon-info-sign color-azul color-azul'></i> ".
                                $this->idiomas->_("Valores de clase del evento")." (";
                            switch ($this->params["tipo"])
                            {
                                case "EVENTOS_CLASE_CUARTOSHORA_ALARMA":
                                {
                                    $info .= $this->idiomas->_("cuartohorarios");
                                    break;
                                }
                                case "EVENTOS_CLASE_HORAS_ALARMA":
                                {
                                    $info .= $this->idiomas->_("horarios");
                                    break;
                                }
                            }
                            $info .= "): ".$cadena_valores_evento."<br/><br/>";
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "DOWN_D":
                        case "DIE_D":
                        case "TIMEOUT_ENVIO_ESTADO":
                        {
                            switch ($nodo->conexion)
                            {
                                case "ON":
                                {
                                    $info .= "
                                        <i class='icon-thumbs-up color-verde'></i> ".
                                        $this->idiomas->_("La conexión está activa")."<br/><br/>";
                                    break;
                                }
                                case "TIMEOUT":
                                {
                                    $info .= "
                                        <i class='icon-thumbs-down color-rojo'></i> ".
                                        $this->idiomas->_("El timeout de envío de estado está activado")."<br/><br/>";
                                    break;
                                }
                                default:
                                {
                                    $info .= "
                                        <i class='icon-thumbs-down color-rojo'></i> ".
                                        $this->idiomas->_("La conexión no está activa")."<br/><br/>";
                                    break;
                                }
                            }
                            $this->detalles .= $nodo->dame_detalles_tabla();
                            break;
                        }
                        case "EJECUCION_ULTIMA_ACCION_ERROR":
                        {
                            $cadena_imagen_accion = NodoActuador::dame_imagen_accion_clase(
                                $nodo->params["clase"],
                                $this->params["datos"]);
                            $info .= "
                                <i class='icon-info-sign color-azul color-azul'></i> ".
                                $this->idiomas->_("Última acción").": ".$cadena_imagen_accion."<br/><br/>";
                            $info .= $nodo->dame_detalles_tabla();
                            break;
                        }
                    }
                }
                // Nota: No hay finally en PHP (a partir de 5.5 sí lo hay)
                // Se restaura el ratio de los sensores anterior
                $_SESSION["id_ratio_sensores"] = $id_ratio_sensores_anterior;
            }
            catch (Exception $e)
            {
                // Se restaura el ratio de los sensores anterior
                $_SESSION["id_ratio_sensores"] = $id_ratio_sensores_anterior;

                // Se relanza la excepción
                throw $e;
            }

            return ($info);
		}


        //
        // Funciones auxiliares
        //


        function dame_estado()
		{
            $estado = "";
			switch ($this->params["tipo"])
			{
                case "CONECTADO":
                case "UP_D":
                case "UP_A":
                {
					$estado = "<i class='icon-off color-verde'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Conectado"), ENT_QUOTES)."</texto></i>";
					break;
                }
				case "DESCONECTADO":
                case "DOWN_D":
                case "DOWN_A":
                {
					$estado = "<i class='icon-off color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desconectado"), ENT_QUOTES)."</texto></i>";
					break;
                }
                case "FINALIZANDO":
                {
                    $estado = "<i class='icon-spinner color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Finalizando"), ENT_QUOTES)."</texto></i>";
					break;
                }
                case "FINALIZADO":
                case "DIE_D":
                case "DIE_A":
                {
                    $estado = "<i class='icon-remove-sign color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Finalizado"), ENT_QUOTES)."</texto></i>";
					break;
                }
                case "TIMEOUT_ENVIO_ESTADO":
                case "TIMEOUT_ENVIO":
                {
                    $estado = "<i class='icon-bell-alt color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Timeout"), ENT_QUOTES)."</texto></i>";
					break;
                }
                case "EVENTOS_ALARMA":
                case "EVENTOS_CLASE_CUARTOSHORA_ALARMA":
                case "EVENTOS_CLASE_HORAS_ALARMA":
                {
                    $estado = "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Alarma"), ENT_QUOTES)."</texto></i>";
					break;
                }
                case "EJECUCION_ULTIMA_ACCION_ERROR":
                {
                    $estado = "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Error"), ENT_QUOTES)."</texto></i>";
					break;
                }
				default:
                {
					$estado = "<i class='icon-info-sign color-azul'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Información"), ENT_QUOTES)."</texto></i>";
					break;
                }
			}
            return ($estado);
		}
	}
?>
