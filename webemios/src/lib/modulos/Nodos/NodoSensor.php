<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_html.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Portugal/util_facturas_electricidad_Portugal.php');
	  include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    // Constantes

    // Indices de parámetros de tipo de sensor real
	define("INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON", 0);
    define("INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ", 1);
    define("INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ", 2);
    define("INDICE_PARAMETRO_TIPO_SENSOR_REAL_OPCIONES_INTERFAZ", 3);

    // Indices de parámetros de tipo de sensor virtual
	define("INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL", 0);

    // Índices de parámetros de hijo de sensor virtual
    define("INDICE_PARAMETRO_HIJO_SENSOR_VIRTUAL_OPERACION", 0);

    // Indices de parámetros de tipo de sensor de procesado
	define("INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO", 0);
    define("INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_HORARIA", 1);
    define("INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_MISMA_FUNCION_VALORES_CUARTOHORARIA", 2);
    define("INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_CUARTOHORARIA", 3);

    // Indices de parámetros de hijo de sensor de procesado
    define("INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_CAMPOS", 0);
    define("INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_FUNCION", 1);
    define("INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_PARAMETROS_FUNCION", 2);
    define("INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE", 3);
    define("INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VALORES_OBLIGATORIOS", 4);

    // Indices de parámetros de tipo de sensor externo
	define("INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO", 0);
    define("INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO", 1);
    define("INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES", 2);
    define("INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES", 3);

    // Indices de parámetros de clase de sensor de energía activa (España)
	define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA", 3);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA", 4);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL", 5);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS", 6);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS", 7);

    // Indices de parámetros de clase de sensor de energía reactiva
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA", 1);


    // Indices de parámetros de clase de sensor de cortes de tensión
	define("INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA", 0);

    // Indices de parámetros de clase de sensor de compra de energía
	define("INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO", 1);

    // Indices de parámetros de clase de sensor de gas (España)
	define("INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS", 2);

    // Indices de parámetros de clase de sensor de agya (España)
	define("INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA", 1);
    define("INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS", 2);

	// Indices de parámetros de clase de sensor genérica
	define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA", 0);
    define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA", 1);
	define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO", 2);
	define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR", 3);
    define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO", 4);
    define("INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS", 5);


    // Clase NodoSensor
	class NodoSensor extends Nodo
	{
        function calcula_conexion()
		{
            // Si no se ha pasado la conexión como parámetro, Se recupera el estado de conexión de la base de datos
            // (se recupera tambien el nombre del axón para evitar tener que volver a realizar la misma consulta)
            if (array_key_exists("conexion", $this->params))
            {
                $this->conexion = $this->params["conexion"];
            }
            else
            {
                switch ($this->params["tipo"])
                {
                    case TIPO_SENSOR_REAL:
                    {
                        $bd_red = BaseDatosRed::dame_base_datos();

                        $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
                        $id_axon = $parametros_tipo_sensor[0];

                        $consulta = "
                            SELECT conexion
                            FROM axones
                            WHERE
                                id = '".$bd_red->_($id_axon)."'";
                        $res = $bd_red->ejecuta_consulta($consulta);
                        if ($res == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta."'");
                        }
                        if ($res->dame_numero_filas() > 0)
                        {
                            $fila = $res->dame_siguiente_fila();
                            $this->conexion = $fila["conexion"];
                        }
                        else
                        {
                            $this->conexion = "OFF";
                        }
                        break;
                    }
                    default:
                    {
                        $this->nombre_axon = $this->idiomas->_("Ninguno");
                        $this->conexion = $this->params["tipo"];
                        break;
                    }
                }
            }
		}


		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

            // Flag para mostrar la localización
            $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);

            // Se devuelve la cabecera
            if ($mostrar_localizacion == true)
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Localización"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Clase"),
                    $idiomas->_("Grupo"),
                    $idiomas->_("Últimos valores"));
            }
            else
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Clase"),
                    $idiomas->_("Grupo"),
                    $idiomas->_("Últimos valores"));
            }
            return ($cabecera_tabla);
		}


		function dame_datos_tabla()
		{
            // Flag para mostrar la localización
            if (array_key_exists("mostrar_localizacion", $this->params) == true)
            {
                $mostrar_localizacion = $this->params["mostrar_localizacion"];
            }
            else
            {
                $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);
            }

            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $nombre_localizacion = $icono_dato_erroneo;
            $icono_conexion = $icono_dato_erroneo;
            $nombre_clase = $icono_dato_erroneo;
            $nombre_grupo = $icono_dato_erroneo;
            $ultimos_valores = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Localización
                if ($mostrar_localizacion == true)
                {
                    if (array_key_exists("nombre_localizacion", $this->params) == true)
                    {
                        $nombre_localizacion = $this->params["nombre_localizacion"];
                    }
                    else
                    {
                        $nombre_localizacion = dame_nombre_localizacion($this->params["localizacion"]);
                    }
                    $nombre_localizacion = htmlspecialchars($nombre_localizacion, ENT_QUOTES);
                }

                // Icono de conexión
                $icono_conexion = $this->icono_conexion;

                // Nombre de clase
                $nombre_clase = NodoSensor::dame_descripcion_clase_sensor($this->params['clase']);
                switch ($this->params['clase'])
                {
                    case CLASE_SENSOR_GENERICA:
                    {
                        $nombre_medida = NodoSensor::dame_parametro_clase_generica(
                            $this->params["parametros_clase"],
                            INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA);
                        if ($nombre_medida != "")
                        {
                            $nombre_clase .= " (".$nombre_medida.")";
                        }
                        break;
                    }
                }

                // Grupo de sensores
                if (array_key_exists("nombre_grupo", $this->params) == true)
                {
                    $nombre_grupo = $this->params["nombre_grupo"];
                }
                else
                {
                    $nombre_grupo = dame_nombre_grupo_sensores($this->params["grupo"]);
                }
                $nombre_grupo = htmlspecialchars($nombre_grupo, ENT_QUOTES);

                // Iconos de alarma
                $iconos_alarma = "";
                $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($this->params["timeout_envio"]);
                if ($timeout_envio_activado == true)
                {
                    if ($iconos_alarma != "")
                    {
                        $iconos_alarma .= " ";
                    }
                    $iconos_alarma .= "<i class='icon-bell-alt color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("timeout"), ENT_QUOTES)."</texto></i>";
                }
                $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                    $this->params["eventos_alarma_activados"],
                    $this->params["eventos_alarma_activados_clase_cuartoshora"],
                    $this->params["eventos_alarma_activados_clase_horas"]);
                if ($hay_eventos_alarma_activados == true)
                {
                    if ($iconos_alarma != "")
                    {
                        $iconos_alarma .= " ";
                    }
                    $iconos_alarma .= "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("alarma"), ENT_QUOTES)."</texto></i>";
                }
                $error_valores = (($this->params["ultimo_error_valores_tiempo_real_json"] != "") ||
                    ($this->params["ultimo_error_valores_horarios_json"] != "") ||
                    ($this->params["ultimo_error_valores_cuartohorarios_json"] != "") ||
                    ($this->params["ultimo_error_valores_clase_horarios_json"] != "") ||
                    ($this->params["ultimo_error_valores_clase_cuartohorarios_json"] != ""));
                if ($error_valores == true)
                {
                    if ($iconos_alarma != "")
                    {
                        $iconos_alarma .= " ";
                    }
                    $iconos_alarma .= "<i class='icon-flag color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("error de valores"), ENT_QUOTES)."</texto></i>";
                }

                // Iconos de procesado de datos
                $iconos_procesado_datos = "";
                if (array_key_exists("hay_importaciones_valores_pendientes", $this->params) == true)
                {
                    $hay_importaciones_valores_pendientes = $this->params["hay_importaciones_valores_pendientes"];
                }
                else
                {
                    $ids_sensores_importaciones_pendientes = dame_ids_sensores_importaciones_pendientes();
                    $hay_importaciones_valores_pendientes = (in_array($this->params["id"], $ids_sensores_importaciones_pendientes) == true);
                }
                if ($hay_importaciones_valores_pendientes == true)
                {
                    $iconos_procesado_datos .= "<i class='icon-upload color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("importaciones"), ENT_QUOTES)."</texto></i>";
                }
                if (array_key_exists("hay_recalculos_valores_clase_pendientes", $this->params) == true)
                {
                    $hay_recalculos_valores_clase_pendientes = $this->params["hay_recalculos_valores_clase_pendientes"];
                }
                else
                {
                    $nombres_sensores_recalculos_pendientes = dame_nombres_sensores_recalculos_pendientes();
                    $hay_recalculos_valores_clase_pendientes = (in_array($this->params["nombre"], $nombres_sensores_recalculos_pendientes) == true);
                }
                if ($hay_recalculos_valores_clase_pendientes == true)
                {
                    $iconos_procesado_datos .= "<i class='icon-cogs color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("recálculos"), ENT_QUOTES)."</texto></i>";
                }
                if (array_key_exists("ultimo_valor_antiguo_procesado", $this->params) == true)
                {
                    $ultimo_valor_antiguo_procesado = $this->params["ultimo_valor_antiguo_procesado"];
                }
                else
                {
                    switch ($this->params["tipo"])
                    {
                        case TIPO_SENSOR_PROCESADO:
                        {
                            $ultimo_valor_antiguo_procesado = false;
                            $cadena_fecha_hora_ultimos_valores_base_datos_utc = $this->params["hora_ultimos_valores"];
                            if ($cadena_fecha_hora_ultimos_valores_base_datos_utc === NULL)
                            {
                                $ultimo_valor_antiguo_procesado = true;
                            }
                            if ($ultimo_valor_antiguo_procesado == false)
                            {
                                $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                                $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                                $periodo_antiguedad_valores = $fecha_hora_actual_utc->diff($fecha_hora_ultimos_valores_utc);
                                $numero_dias_antiguedad_valores = $periodo_antiguedad_valores->days;
                                if ($numero_dias_antiguedad_valores > NUMERO_MAXIMO_DIAS_CALCULO_VALORES_SENSORES_PROCESADO_EJECUCION_SIN_RECALCULOS)
                                {
                                    $ultimo_valor_antiguo_procesado = true;
                                }
                            }
                            break;
                        }
                        default:
                        {
                            $ultimo_valor_antiguo_procesado = false;
                            break;
                        }
                    }
                }
                if ($ultimo_valor_antiguo_procesado == true)
                {
                    $iconos_procesado_datos .= "<i class='icon-cog color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("procesado pendiente"), ENT_QUOTES)."</texto></i>";
                }

                // Últimos valores del sensor
                if ($this->params["ultimos_valores"] === NULL)
                {
                    $ultimos_valores = $this->idiomas->_("Sin valores recibidos");
                }
                else
                {
                    $resultado_ultimos_valores_sensor = NodoSensor::dame_cadenas_hora_valores_sensor(
                        $this->id,
                        $_SESSION["id_ratio_sensores"],
                        $this->params["hora_ultimos_valores"],
                        $this->params["ultimos_valores"],
                        $this->params["clase"],
                        $this->params["parametros_clase"],
                        $this->params["incrementos_tiempo_real_horarios"],
                        GRANULARIDAD_TIEMPO_REAL,
                        FORMATO_CADENA_VALORES_SENSOR_REDUCIDO);
                    if ($resultado_ultimos_valores_sensor !== NULL)
                    {
                        $cadena_fecha_hora_ultimos_valores_local_local = $resultado_ultimos_valores_sensor["cadena_fecha_hora_valores_local_local"];
                        $cadena_ultimos_valores = $resultado_ultimos_valores_sensor["cadena_valores"];
                        $ultimos_valores = $cadena_ultimos_valores;
                        $ultimos_valores .= " <cadena_fecha class='cadena-fecha'>(".$cadena_fecha_hora_ultimos_valores_local_local.")</cadena_fecha>";
                    }
                }

                // Iconos de alarma y de procesado de datos
                if ($iconos_alarma != "")
                {
                    $ultimos_valores .= " <iconos-dato class='iconos-dato'>[".$iconos_alarma."]</iconos-dato>";
                }
                if ($iconos_procesado_datos != "")
                {
                    $ultimos_valores .= " <iconos-dato class='iconos-dato'>[".$iconos_procesado_datos."]</iconos-dato>";
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
            $datos_tabla = array();
            array_push($datos_tabla, $nombre);
            if ($mostrar_localizacion == true)
            {
                array_push($datos_tabla, $nombre_localizacion);
            }
            array_push($datos_tabla, $icono_conexion);
            array_push($datos_tabla, $nombre_clase);
            array_push($datos_tabla, $nombre_grupo);
            array_push($datos_tabla, $ultimos_valores);
            return ($datos_tabla);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Se recupera la fila del sensor
			$fila_sensor = dame_fila_sensor($this->id);

            // Permisos de sensores
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            $administracion_comentarios_sensores = NodoSensor::dame_administracion_comentarios_sensores();
            $lectura_sensores = NodoSensor::dame_lectura_sensores();
            $exportacion_sensores = NodoSensor::dame_exportacion_sensores();
            $envio_valores_manuales_sensores = NodoSensor::dame_envio_valores_manuales_sensores();

            // Herramientas de detalles de sensor
            $herramientas = "<div class='fila-botones-herramientas-detalle-tabla-datos'>";

            // Recargar la fila del sensor
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->tipo."__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_nodo'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Adición de comentario al sensor
            if ($administracion_comentarios_sensores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button objeto='".$fila_sensor["nombre"]."' origen_comentario='".ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES."' ".
                            "class='btn-mini btn btn-success boton_mostrar_ventana_anyadir_modificar_comentario'>".
                            $this->idiomas->_("Añadir comentario")."
                        </button>
                    </span>";
            }
            /*
            // Lectura de sensores (sólo en sensores reales y virtuales)
            if ($lectura_sensores == true)
            {
                switch ($fila_sensor['tipo'])
                {
                    case TIPO_SENSOR_REAL:
                    case TIPO_SENSOR_VIRTUAL:
                    {
                        $herramientas .= "
                            <span class='boton-herramientas-detalle-tabla-datos'>
                                <button id='boton_leer_sensor__".$this->id."__".$fila_sensor['tipo']."' class='btn-mini btn btn-success boton_sensores_envia_accion_herramientas_sensor'>".
                                    $this->idiomas->_("Leer sensor")."
                                </button>
                            </span>";
                        break;
                    }
                }
            }
            */
            // Flag para mostrar el botón de enviar valores manuales
            $mostrar_boton_envio_valores_manuales = false;
            if ($envio_valores_manuales_sensores == true)
            {
                if ($fila_sensor["tipo"] == TIPO_SENSOR_EXTERNO)
                {
                    $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_tipo']);
                    if ($parametros_tipo_sensor[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO] == CLASE_SENSOR_EXTERNO_NINGUNA)
                    {
                        $mostrar_boton_envio_valores_manuales = true;
                    }
                }
            }

            // Acciones de administrador de sensores
            if ($administracion_sensores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_recargar_configuracion__".$this->id."__".$fila_sensor['tipo']."' class='btn-mini btn btn-success boton_sensores_envia_accion_herramientas_sensor'>".
                            $this->idiomas->_("Recargar configuración")."
                        </button>
                    </span>
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_importar_valores__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_importacion_valores_sensor'>".
                            $this->idiomas->_("Importar valores")."
                        </button>
                    </span>
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_exportar_valores__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_exportacion_valores_sensor'>".
                            $this->idiomas->_("Exportar valores")."
                        </button>
                    </span>
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_borrar_valores__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_borrado_valores_sensor'>".
                            $this->idiomas->_("Borrar valores")."
                        </button>
                    </span>";

                // Recálculo de valores de clase
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($fila_sensor['clase']);
                if ($caracteristicas_clase_sensor["valores_clase"] == true)
                {
                    $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_recalcular_valores_clase__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor'>".
                                $this->idiomas->_("Recalcular valores de clase")."
                            </button>
                        </span>";
                }
            }
            else
            {
                if ($exportacion_sensores == true)
                {
                    $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_exportar_valores__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_exportacion_valores_sensor'>".
                                $this->idiomas->_("Exportar valores")."
                            </button>
                        </span>";
                }
            }

            // Envío de valores manuales
            if ($mostrar_boton_envio_valores_manuales == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_enviar_valores_manuales__".$this->id."__".$fila_sensor['clase']."' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_envio_valores_manuales_sensor'>".
                            $this->idiomas->_("Enviar valores manuales")."
                        </button>
                    </span>";
            }

            $herramientas .= "</div>";

            // Se devuelven las herramientas
            return ($herramientas);
		}


        function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
            $bd_datos = BaseDatosDatos::dame_base_datos();

			$info = "";
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            $administracion_eventos = Evento::dame_administracion_eventos();

			// Se recupera la fila del sensor
			$fila_sensor = dame_fila_sensor($this->id);

            // Características de clase de sensor
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($fila_sensor['clase']);

            // Información para administradores:
            // - Identificador
            //if ($administracion_sensores == true)
            //{
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            //}

            // Descripción
            if ($fila_sensor['descripcion'] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($fila_sensor['descripcion'], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Flag para añadir salto de línea
            // (de bloque de información para administradores, frecuencias de envío y muestreo)
            $anyadir_salto_linea = false;

            // Información para administradores:
            // - Tipo de valores
            // - Cambio de valores puntuales
            // - Incrementos en tiempo real horarios
            // - Incrementos negativos válidos
            // - Granularidad cuartohoraria
            // - Guardar valores en base de datos
            // - Notificar todos los eventos
            if ($administracion_sensores == true)
            {
                if (NodoSensor::dame_mostrar_tipo_valores($fila_sensor["tipo"]) == true)
                {
                    $tipo_valores = $fila_sensor['tipo_valores'];
                    $descripcion_tipo_valores = NodoSensor::dame_descripcion_tipo_valores_sensor($tipo_valores);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Tipo de valores").": ".$descripcion_tipo_valores."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_cambio_valores_puntuales($fila_sensor["clase"], $fila_sensor["tipo_valores"]) == true)
                {
                    $cambio_valores_puntuales = $fila_sensor['cambio_valores_puntuales'];
                    $descripcion_cambio_valores_puntuales = NodoSensor::dame_descripcion_cambio_valores_puntuales_sensor($cambio_valores_puntuales);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Cambio de valores puntuales").": ".$descripcion_cambio_valores_puntuales."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_incrementos_tiempo_real_horarios($fila_sensor["tipo"], $fila_sensor["clase"]) == true)
                {
                    $incrementos_tiempo_real_horarios = $fila_sensor['incrementos_tiempo_real_horarios'];
                    $descripcion_incrementos_tiempo_real_horarios = dame_descripcion_valores_si_no($incrementos_tiempo_real_horarios);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Incrementos en tiempo real horarios").": ".$descripcion_incrementos_tiempo_real_horarios."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_incrementos_negativos_validos($fila_sensor["clase"]) == true)
                {
                    $incrementos_negativos_validos = $fila_sensor['incrementos_negativos_validos'];
                    $descripcion_incrementos_negativos_validos = dame_descripcion_valores_si_no($incrementos_negativos_validos);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Incrementos negativos válidos").": ".$descripcion_incrementos_negativos_validos."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_granularidad_cuartohoraria($fila_sensor["clase"]) == true)
                {
                    $granularidad_cuartohoraria = $fila_sensor['granularidad_cuartohoraria'];
                    $descripcion_granularidad_cuartohoraria = dame_descripcion_valores_si_no($granularidad_cuartohoraria);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Granularidad cuartohoraria").": ".$descripcion_granularidad_cuartohoraria."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_guardar_valores_base_datos($fila_sensor["tipo"]) == true)
                {
                    $guardar_valores_base_datos = $fila_sensor['guardar_valores_base_datos'];
                    $descripcion_guardar_valores_base_datos = dame_descripcion_valores_si_no($guardar_valores_base_datos);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Guardar valores en base de datos").": ".$descripcion_guardar_valores_base_datos."<br/>";
                    $anyadir_salto_linea = true;
                }
                if (NodoSensor::dame_mostrar_notificar_todos_eventos($fila_sensor["tipo"], $fila_sensor["clase"]) == true)
                {
                    $notificar_todos_eventos = $fila_sensor['notificar_todos_eventos'];
                    $descripcion_notificar_todos_eventos = dame_descripcion_valores_si_no($notificar_todos_eventos);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Notificar todos los eventos").": ".$descripcion_notificar_todos_eventos."<br/>";
                    $anyadir_salto_linea = true;
                }
            }

            // Frecuencias de muestreo y de envío
            if (NodoSensor::dame_mostrar_frecuencia_muestreo($fila_sensor["tipo"]) == true)
            {
                if ($fila_sensor["frecuencia_muestreo"] != 0)
                {
                    $texto_periodo_frecuencia_muestreo = dame_texto_periodo($fila_sensor['frecuencia_muestreo']);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Frecuencia de muestreo").": ".$texto_periodo_frecuencia_muestreo."<br/>";
                    $anyadir_salto_linea = true;
                }
            }
            if (NodoSensor::dame_mostrar_frecuencia_envio($fila_sensor["tipo"]) == true)
            {
                if ($fila_sensor["frecuencia_envio"] != 0)
                {
                    $texto_periodo_frecuencia_envio = dame_texto_periodo($fila_sensor['frecuencia_envio']);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Frecuencia de envío").": ".$texto_periodo_frecuencia_envio."<br/>";
                    $anyadir_salto_linea = true;
                }
            }

            // Salto de línea
            // (de bloque de información para administradores, frecuencias de envío y muestreo)
            if ($anyadir_salto_linea == true)
            {
                $info .= "<br/>";
            }

            // Si hay timeout de envío, se muestra un aviso
            $timeout_envio = ($fila_sensor['timeout_envio'] == VALOR_SI);
            if ($timeout_envio == true && $fila_sensor['hora_ultimos_valores'] != NULL)
            {
                $anyadir_salto_linea = true;
                $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                switch ($fila_sensor['tipo'])
                {
                    case TIPO_SENSOR_REAL:
                    {
                        $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($fila_sensor['hora_ultimos_valores'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_ultimos_valores_utc->getTimestamp();
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("No se han recibido valores del sensor en")." ".dame_texto_periodo($segundos_transcurridos)."<br/>";
                        break;
                    }
                    case TIPO_SENSOR_VIRTUAL:
                    {
                        $fecha_hora_timeout_envio_utc = convierte_cadena_a_fecha($fila_sensor['hora_timeout_envio'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_timeout_envio_utc->getTimestamp();
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("Todos los sensores hijos tienen timeout de envío desde hace")." ".dame_texto_periodo($segundos_transcurridos)."<br/>";
                        break;
                    }
                    case TIPO_SENSOR_PROCESADO:
                    {
                        $fecha_hora_timeout_envio_utc = convierte_cadena_a_fecha($fila_sensor['hora_timeout_envio'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_timeout_envio_utc->getTimestamp();
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("Al menos un sensor hijo tiene timeout de envío desde hace")." ".dame_texto_periodo($segundos_transcurridos)."<br/>";
                        break;
                    }
                    case TIPO_SENSOR_EXTERNO:
                    {
                        $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($fila_sensor['hora_ultimos_valores'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_ultimos_valores_utc->getTimestamp();
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("No hay valores del sensor en")." ".dame_texto_periodo($segundos_transcurridos)."<br/>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor desconocido: '".$fila_sensor['tipo']."'");
                    }
                }
                if ($anyadir_salto_linea == true)
                {
                    $info .= "<br/>";
                }
            }

            // Localización
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $id_localizacion = $fila_sensor["localizacion"];
                if ($id_localizacion != ID_NINGUNO)
                {
                    // Visible en localizaciones hijas
                    $visible_localizaciones_hijas = $fila_sensor['visible_localizaciones_hijas'];
                    $descripcion_visible_localizaciones_hijas = dame_descripcion_valores_si_no($visible_localizaciones_hijas);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Visible en localizaciones hijas").": ".$descripcion_visible_localizaciones_hijas."<br/>";

                    // Se muestra la instalación y el equipo de la instalación del sensor (si está asignado a alguno)
                    $fila_equipo_instalacion = dame_fila_equipo_instalacion_localizacion_nodo($id_localizacion, TIPO_NODO_SENSOR, $this->id);
                    if ($fila_equipo_instalacion !== NULL)
                    {
                        $nombre_instalacion = dame_nombre_instalacion($fila_equipo_instalacion["instalacion"]);
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Instalación").": ".htmlspecialchars($nombre_instalacion, ENT_QUOTES)."<br/>";
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Equipo").": ".htmlspecialchars($fila_equipo_instalacion["nombre"], ENT_QUOTES)."</li>";
                        $info .= "</ul>";
                    }

                    // Salto de línea
                    $info .= "<br/>";
                }
            }

            // Se muestran los eventos configurados si es administrador de eventos
            if ($administracion_eventos == true)
            {
                // Se muestran los eventos configurados (si los hay)
                $eventos_configurados = NodoSensor::dame_nombres_eventos_configurados($fila_sensor['id'], $fila_sensor['grupo']);
                $numero_eventos_configurados = count($eventos_configurados);
                if ($numero_eventos_configurados > 0)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    if ($numero_eventos_configurados == 1)
                    {
                        $info .= $this->idiomas->_("Este sensor tiene el siguiente evento configurado").":";
                    }
                    else
                    {
                        $info .= $this->idiomas->_("Este sensor tiene los siguientes eventos configurados").":";
                    }
                    $nombres_eventos = "<ul>";
                    foreach ($eventos_configurados as $evento_configurado)
                    {
                        $nombres_eventos .= "<li>".htmlspecialchars($evento_configurado, ENT_QUOTES)."</li>";
                    }
                    $nombres_eventos .= "</ul>";
                    $info .= $nombres_eventos;
                    $info .= "<br/>";
                }
            }

            // Se muestran los eventos activados (si los hay)
            $eventos_activados = NodoSensor::dame_nombres_eventos_activados(
                $fila_sensor["eventos_activados"],
                $fila_sensor["eventos_activados_clase_cuartoshora"],
                $fila_sensor["eventos_activados_clase_horas"]);
            $numero_eventos_activados = count($eventos_activados);
            if ($numero_eventos_activados > 0)
            {
                $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                    $fila_sensor["eventos_alarma_activados"],
                    $fila_sensor["eventos_alarma_activados_clase_cuartoshora"],
                    $fila_sensor["eventos_alarma_activados_clase_horas"]);
                $this->params['eventos_alarma_activados'] = $fila_sensor['eventos_alarma_activados'];
                if ($hay_eventos_alarma_activados == true)
                {
                    $info .= "<i class='icon-warning-sign color-rojo'></i> ";
                }
                else
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                }
                if ($numero_eventos_activados == 1)
                {
                    $info .= $this->idiomas->_("Este sensor tiene el siguiente evento activo").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Este sensor tiene los siguientes eventos activos").":";
                }
                $nombres_eventos = "<ul>";
                foreach ($eventos_activados as $evento_activado)
                {
                    $nombres_eventos .= "<li>".$evento_activado."</li>";
                }
                $nombres_eventos .= "</ul>";
                $info .= $nombres_eventos;
                $info .= "<br/>";
            }

            // Flag para añadir salto de línea (de bloque de fecha de primeros valores y últimos valores)
            $anyadir_salto_linea = false;

            // Fecha de primeros valores recibidos
            if ($fila_sensor["hora_ultimos_valores"] !== NULL)
            {
                $tabla_datos = dame_nombre_tabla_datos_clase_sensor($fila_sensor["clase"]);
                switch ($fila_sensor["tipo"])
                {
                    case TIPO_SENSOR_PROCESADO:
                    {
                        $tabla_datos .= SUFIJO_TABLA_HORAS;
                        break;
                    }
                    default:
                    {
                        if ($fila_sensor["tipo_valores"] == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $tabla_datos .= SUFIJO_TABLA_INCREMENTOS;
                            $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                            $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];
                            if (($clase_procesado_valores == false) || ($incrementos_tiempo_real_horarios == VALOR_NO))
                            {
                                $tabla_datos .= SUFIJO_TABLA_TIEMPO_REAL;
                            }
                        }
                        break;
                    }
                }
                $consulta_hora_primeros_valores = "
                    SELECT hora
                    FROM ".$tabla_datos."
                    WHERE
                        (sensor = '".$bd_datos->_($fila_sensor["nombre"])."')
                        AND (red = '".$bd_datos->_($fila_sensor["red"])."')
                    ORDER BY hora ASC
                    LIMIT 1";
                $res_hora_primeros_valores = $bd_datos->ejecuta_consulta($consulta_hora_primeros_valores);
                if ($res_hora_primeros_valores == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_hora_primeros_valores."'");
                }
                if ($res_hora_primeros_valores->dame_numero_filas() > 0)
                {
                    $fila_hora_primeros_valores = $res_hora_primeros_valores->dame_siguiente_fila();
                    $cadena_hora_primeros_valores_base_datos_utc = $fila_hora_primeros_valores["hora"];

                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_primeros_valores_local_utc = convierte_formato_fecha($cadena_hora_primeros_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_primeros_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_primeros_valores_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Fecha de primeros valores").": ".$cadena_hora_primeros_valores_local_local."<br/>";
                    $anyadir_salto_linea = true;
                }
            }

            // Se muestran los últimos valores (si la clase muestra los valores en la tabla en formato reducido)
            if ($caracteristicas_clase_sensor["formato_valores_reducido"] == true)
            {
                // Últimos valores del sensor
                if ($fila_sensor["ultimos_valores"] === NULL)
                {
                    $ultimos_valores = $this->idiomas->_("Sin valores recibidos");
                }
                else
                {
                    $resultado_ultimos_valores_sensor = NodoSensor::dame_cadenas_hora_valores_sensor(
                        $this->id,
                        $_SESSION["id_ratio_sensores"],
                        $fila_sensor["hora_ultimos_valores"],
                        $fila_sensor["ultimos_valores"],
                        $fila_sensor["clase"],
                        $fila_sensor["parametros_clase"],
                        $fila_sensor["incrementos_tiempo_real_horarios"],
                        GRANULARIDAD_TIEMPO_REAL,
                        FORMATO_CADENA_VALORES_SENSOR_COMPLETO);
                    if ($resultado_ultimos_valores_sensor !== NULL)
                    {
                        $cadena_fecha_hora_ultimos_valores_local_local = $resultado_ultimos_valores_sensor["cadena_fecha_hora_valores_local_local"];
                        $cadena_ultimos_valores = $resultado_ultimos_valores_sensor["cadena_valores"];
                        $ultimos_valores = $cadena_ultimos_valores." (".$cadena_fecha_hora_ultimos_valores_local_local.")";
                    }
                }

                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $info .= $this->idiomas->_("Últimos valores").": ".$ultimos_valores."<br/>";
                $anyadir_salto_linea = true;
            }

            // Se recuperan los últimos valores de clase del sensor (horas y cuartos de hora)
            $ultimos_valores_clase_horas = NULL;
            $ultimos_valores_clase_cuartoshora = NULL;
            $resultado_ultimos_valores_clase_horas = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                $_SESSION["id_ratio_sensores"],
                $this->id,
                $fila_sensor["hora_ultimos_valores_clase_horas"],
                $fila_sensor["ultimos_valores_clase_horas"],
                $fila_sensor["clase"],
                $fila_sensor["parametros_clase"],
                GRANULARIDAD_HORARIA);
            if ($resultado_ultimos_valores_clase_horas !== NULL)
            {
                $cadena_fecha_hora_ultimos_valores_clase_horas_local_local = $resultado_ultimos_valores_clase_horas["cadena_fecha_hora_valores_clase_local_local"];
                $cadena_ultimos_valores_clase_horas = $resultado_ultimos_valores_clase_horas["cadena_valores_clase"];
                $ultimos_valores_clase_horas = $cadena_ultimos_valores_clase_horas." (".$cadena_fecha_hora_ultimos_valores_clase_horas_local_local.")";
            }
            if ($fila_sensor['granularidad_cuartohoraria'] == true)
            {
                $resultado_ultimos_valores_clase_cuartoshora = NodoSensor::dame_cadenas_hora_valores_clase_sensor(
                    $_SESSION["id_ratio_sensores"],
                    $this->id,
                    $fila_sensor["hora_ultimos_valores_clase_cuartoshora"],
                    $fila_sensor["ultimos_valores_clase_cuartoshora"],
                    $fila_sensor["clase"],
                    $fila_sensor["parametros_clase"],
                    GRANULARIDAD_CUARTOHORARIA);
                if ($resultado_ultimos_valores_clase_cuartoshora !== NULL)
                {
                    $cadena_fecha_hora_ultimos_valores_clase_cuartoshora_local_local = $resultado_ultimos_valores_clase_cuartoshora["cadena_fecha_hora_valores_clase_local_local"];
                    $cadena_ultimos_valores_clase_cuartoshora = $resultado_ultimos_valores_clase_cuartoshora["cadena_valores_clase"];
                    $ultimos_valores_clase_cuartoshora = $cadena_ultimos_valores_clase_cuartoshora." (".$cadena_fecha_hora_ultimos_valores_clase_cuartoshora_local_local.")";
                }
            }

            // Se añaden los últimos valores de clase del sensor (cuartos de hora y horas)
            if (($ultimos_valores_clase_cuartoshora !== NULL) || ($ultimos_valores_clase_horas !== NULL))
            {
                if ($caracteristicas_clase_sensor["valores_clase"] == false)
                {
                    $texto_valores_clase = $this->idiomas->_("Últimos valores");
                }
                else
                {
                    $texto_valores_clase = $this->idiomas->_("Últimos valores de clase");
                }
                if ($ultimos_valores_clase_horas !== NULL)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $texto_valores_clase." (".$this->idiomas->_("horarios")."): ".$ultimos_valores_clase_horas."<br/>";
                }
                if ($ultimos_valores_clase_cuartoshora !== NULL)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $texto_valores_clase." (".$this->idiomas->_("cuartohorarios")."): ".$ultimos_valores_clase_cuartoshora."<br/>";
                }
                $anyadir_salto_linea = true;
            }

            // Salto de línea (de bloque de fecha de primeros valores y últimos valores)
            if ($anyadir_salto_linea == true)
            {
                $info .= "<br/>";
            }

            // Comentarios anterior y siguiente de sensor
            $filas_comentarios = Comentario::dame_filas_comentarios_anterior_posterior_objeto(
                ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES,
                $fila_sensor["nombre"]);
            $fila_comentario_anterior = $filas_comentarios["anterior"];
            $fila_comentario_posterior = $filas_comentarios["posterior"];
            if (($fila_comentario_anterior !== NULL) || ($fila_comentario_posterior !== NULL))
            {
                // Comentario anterior
                if ($fila_comentario_anterior !== NULL)
                {
                    $cadenas_comentario_anterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_anterior, false);
                    $cadena_hora_comentario_anterior_local_local = $cadenas_comentario_anterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_anterior = $cadenas_comentario_anterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_anterior = $cadenas_comentario_anterior["descripcion_comentario"];
                    $comentario_anterior = $descripcion_tipo_comentario_anterior." (".$cadena_hora_comentario_anterior_local_local.") ".
                        "[".$descripcion_comentario_anterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario anterior").": ".$comentario_anterior."<br/>";
                }

                // Comentario posterior
                if ($fila_comentario_posterior !== NULL)
                {
                    $cadenas_comentario_posterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_posterior, false);
                    $cadena_hora_comentario_posterior_local_local = $cadenas_comentario_posterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_posterior = $cadenas_comentario_posterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_posterior = $cadenas_comentario_posterior["descripcion_comentario"];
                    $comentario_posterior = $descripcion_tipo_comentario_posterior." (".$cadena_hora_comentario_posterior_local_local.") ".
                        "[".$descripcion_comentario_posterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario siguiente").": ".$comentario_posterior."<br/>";
                }

                // Salto de línea
                $info .= "<br/>";
            }

            // Parámetros específicos de clase de sensor
            switch ($fila_sensor['clase'])
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $info .= $this->dame_info_clase_sensor_energia_activa_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $info .= $this->dame_info_clase_sensor_energia_reactiva_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $info .= $this->dame_info_clase_sensor_cortes_tension_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $info .= $this->dame_info_clase_sensor_compra_energia_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $info .= $this->dame_info_clase_sensor_gas_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $info .= $this->dame_info_clase_sensor_agua_detalles_tabla($fila_sensor);
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    $info .= $this->dame_info_clase_sensor_generica_detalles_tabla($fila_sensor);
                    break;
                }
            }

            // Parámetros del tipo de sensor
            switch ($fila_sensor['tipo'])
            {
                case TIPO_SENSOR_REAL:
                {
                    $info .= $this->dame_info_tipo_sensor_real_detalles_tabla($fila_sensor);
                    $info .= "<br/>";
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $info .= $this->dame_info_tipo_sensor_virtual_detalles_tabla($fila_sensor);
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $info .= $this->dame_info_tipo_sensor_externo_detalles_tabla($fila_sensor);
                    $info .= "<br/>";
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $info .= $this->dame_info_tipo_sensor_procesado_detalles_tabla($fila_sensor);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de sensor desconocido: '".$fila_sensor['tipo']."'");
                }
            }

            // Padres del sensor (sólo para administradores de sensores)
            if ($administracion_sensores == true)
            {
                $info .= $this->dame_info_padres_sensor_detalles_tabla();
            }

            // Órden de procesado e hijos de sensor
            switch ($fila_sensor['tipo'])
            {
                case TIPO_SENSOR_VIRTUAL:
                case TIPO_SENSOR_PROCESADO:
                {
                    // Orden de procesado (sólo para administradores)
                    if ($administracion_sensores == true)
                    {
                        if ($fila_sensor["administrable"] == VALOR_SI)
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Orden de procesado").": ".$fila_sensor["orden"]."<br/>";
                        }
                    }
                    $info .= "<br/>";

                    // Se muestra la tabla de los hijos
                    $id_elemento_hijos_sensor = "hijos-sensor".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                    $info .= "<div id='".$id_elemento_hijos_sensor."' class='contenedor-detalle-tabla-datos'>".
                        $this->dame_tabla_hijos(
                            $fila_sensor["tipo"],
                            $fila_sensor["parametros_tipo"],
                            $fila_sensor["administrable"])."</div>";
                    $info .= "<br/>";
                    break;
                }
            }

            // Último error en recuperación de valores en tiempo real
            if ($fila_sensor['ultimo_error_valores_tiempo_real_json'] <> "")
            {
                switch ($fila_sensor['tipo'])
                {
                    case TIPO_SENSOR_EXTERNO:
                    {
                        $info .= $this->dame_info_ultimo_error_valores_tiempo_real_tipo_sensor_externo_detalles_tabla($fila_sensor);
                        $info .= "<br/>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor incorrecto: '".$fila_sensor['tipo']."'");
                    }
                }
            }

            // Último error en cálculo de valores
            if (($fila_sensor['ultimo_error_valores_horarios_json'] <> "") ||
                ($fila_sensor['ultimo_error_valores_cuartohorarios_json'] <> ""))
            {
                switch ($fila_sensor['tipo'])
                {
                    case TIPO_SENSOR_PROCESADO:
                    {
                        $info .= $this->dame_info_ultimo_error_valores_tipo_sensor_procesado_detalles_tabla($fila_sensor, GRANULARIDAD_HORARIA);
                        $info .= $this->dame_info_ultimo_error_valores_tipo_sensor_procesado_detalles_tabla($fila_sensor, GRANULARIDAD_CUARTOHORARIA);
                        $info .= "<br/>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de sensor incorrecto: '".$fila_sensor['tipo']."'");
                    }
                }
            }

            // Último error en cálculo de valores de clase de sensor
            if (($fila_sensor['ultimo_error_valores_clase_horarios_json'] <> "") ||
                ($fila_sensor['ultimo_error_valores_clase_cuartohorarios_json'] <> ""))
            {
                $info .= $this->dame_info_ultimo_error_valores_clase_sensor_detalles_tabla($fila_sensor, GRANULARIDAD_HORARIA, $caracteristicas_clase_sensor["valores_clase"]);
                $info .= $this->dame_info_ultimo_error_valores_clase_sensor_detalles_tabla($fila_sensor, GRANULARIDAD_CUARTOHORARIA, $caracteristicas_clase_sensor["valores_clase"]);
                $info .= "<br/>";
            }

            // Calibración solo para administradores
            if ($administracion_sensores == true)
            {
                if (NodoSensor::dame_mostrar_calibracion($fila_sensor["tipo"]) == true)
                {
                    if ($fila_sensor['calibracion'] != "")
                    {
                        $calibracion = formatea_calibracion($fila_sensor['calibracion']);
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Calibración").": ".$calibracion."<br/>";
                        $info .= "<br/>";
                    }
                }
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
		}


        function dame_duplicacion_tabla()
        {
            return (true);
        }


		function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
            return (array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $this->dame_tooltip_topologia_red(),
				"color_nodo" => $this->dame_color_nodo_topologia_red()
			));
		}


        function dame_info_tooltip_mapa($id_mapa)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Info
            $info = "";

            // Clase
            $nombre_clase = NodoSensor::dame_descripcion_clase_sensor($this->params['clase']);
			$info .= $this->idiomas->_("Clase").": ".$nombre_clase."<br/>";

            // Localización
            if ($_SESSION["id_localizacion"] != ID_DESACTIVADO)
            {
                // Localización
                if ($this->params["localizacion"] != ID_NINGUNO)
                {
                    $consulta_localizacion = "
                        SELECT nombre
                        FROM localizaciones
                        WHERE
                            id = '".$bd_red->_($this->params["localizacion"])."'";
                    $res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
                    if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
                    }
                    $fila_localizacion = $res_localizacion->dame_siguiente_fila();
                    $info .= $this->idiomas->_("Localización").": ".$fila_localizacion["nombre"];
                }
                else
                {
                    $info .= $this->idiomas->_("Sin localización");
                }
                $info .= "<br/>";
            }

            // Grupo de sensores
            if ($this->params["grupo"] != ID_NINGUNO)
            {
                $consulta_grupo = "
                    SELECT nombre
                    FROM grupos_sensores
                    WHERE
                        id = '".$bd_red->_($this->params["grupo"])."'";
                $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                }
                $fila_grupo = $res_grupo->dame_siguiente_fila();
                $info .= $this->idiomas->_("Grupo").": ".$fila_grupo["nombre"];
            }
            else
            {
                $info .= $this->idiomas->_("Sin grupo");
            }
            $info .= "<br/>";

            // Últimos valores del sensor
            if ($this->params["ultimos_valores"] === NULL)
            {
                $info .= "<i class='icon-info-sign color-azul'></i>: ".
                    $this->idiomas->_("Sin valores recibidos");
            }
            else
            {
                $cadena_ultimos_valores = NodoSensor::dame_cadena_valores_sensor(
                    $_SESSION["id_ratio_sensores"],
                    $this->id,
                    $this->params["hora_ultimos_valores"],
                    $this->params["ultimos_valores"],
                    $this->params["clase"],
                    $this->params["parametros_clase"],
                    $this->params["incrementos_tiempo_real_horarios"],
                    GRANULARIDAD_TIEMPO_REAL,
                    SEPARADOR_VALOR_INCREMENTO_SENSOR,
                    FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                    NULL);
                if ($this->params["timeout_envio"] == VALOR_SI)
                {
                    $info .= "<i class='icon-bell-alt color-rojo'></i>: ";
                }
                else
                {
                    $info .= "<i class='icon-info-sign color-azul'></i>: ";
                }
                $zona_horaria = dame_zona_horaria_local();
                $cadena_hora_ultimos_valores_base_datos_utc = convierte_formato_fecha($this->params["hora_ultimos_valores"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_ultimos_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimos_valores_base_datos_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $info .= $this->idiomas->_("Últimos valores").": ".$cadena_ultimos_valores." (".$cadena_hora_ultimos_valores_local_local.")";
            }

            // Se muestran los eventos activados (si hay eventos activados con alarma)
            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $this->params["eventos_alarma_activados"],
                $this->params["eventos_alarma_activados_clase_cuartoshora"],
                $this->params["eventos_alarma_activados_clase_horas"]);
            if ($hay_eventos_alarma_activados == true)
            {
                $eventos_activados = NodoSensor::dame_nombres_eventos_activados(
                    $this->params["eventos_activados"],
                    $this->params["eventos_activados_clase_cuartoshora"],
                    $this->params["eventos_activados_clase_horas"]);
                $numero_eventos_activados = count($eventos_activados);
                if ($numero_eventos_activados > 0)
                {
                    $info .= "<br/>";
                    $info .= "<i class='icon-warning-sign color-rojo'></i> ";
                    if ($numero_eventos_activados == 1)
                    {
                        $info .= $this->idiomas->_("Este sensor tiene el siguiente evento activo").":";
                    }
                    else
                    {
                        $info .= $this->idiomas->_("Este sensor tiene los siguientes eventos activos").":";
                    }

                    $nombres_eventos = "<ul>";
                    foreach ($eventos_activados as $evento_activado)
                    {
                        $nombres_eventos .= "<li>".$evento_activado."</li>";
                    }
                    $nombres_eventos .= "</ul>";

                    $info .= $nombres_eventos;
                }
                else
                {
                    $info .= "<br/>";
                }
            }
            else
            {
                $info .= "<br/>";
            }

            // Conexión
            if ($this->conexion != "NA")
            {
                $descripcion_conexion = Nodo::dame_descripcion_conexion($this->conexion);
                $info .= $this->icono_conexion.": ".$descripcion_conexion."<br/>";
            }

            // Botón de envío de valores manuales
            $mostrar_boton_envio_valores_manuales = false;
            $envio_valores_manuales_sensores = NodoSensor::dame_envio_valores_manuales_sensores();
            if ($envio_valores_manuales_sensores == true)
            {
                if ($this->params["tipo"] == TIPO_SENSOR_EXTERNO)
                {
                    $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
                    if ($parametros_tipo_sensor[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO] == CLASE_SENSOR_EXTERNO_NINGUNA)
                    {
                        $mostrar_boton_envio_valores_manuales = true;
                    }
                }
            }

            if ($mostrar_boton_envio_valores_manuales == true)
            {
                $info .= "
                    <div class='contenedor-botones-tooltip-mapa'>
                        <button id='boton_mostrar_ventana_envio_valores_manuales__".$this->id."__".$this->params["clase"]."__".$id_mapa."'
                            class='btn-mini btn btn-success boton-tooltip-mapa boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_mapa'>".
                            $this->idiomas->_("Enviar valores manuales")."
                        </button>
                    </div>";
            }

            return ($info);
		}


		function dame_conf()
		{
            $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
            $clase_interfaz = $parametros_tipo_sensor[1];
            $ubicacion_interfaz = $parametros_tipo_sensor[2];
            $opciones_interfaz = $parametros_tipo_sensor[3];

            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($this->params['clase']);
            $numero_valores = $caracteristicas_clase_sensor["numero_valores"];

			$conf = array(
				"ID" => $this->params['id'],
                "GRUPO" => $this->params['grupo'],
				"TIPO" => $this->params['tipo'],
                "CLASE" => $this->params['clase'],
				"NUM_VALORES" => $numero_valores,
				"FREC_ENVIO" => $this->params['frecuencia_envio'],
				"FREC_MUESTREO" => $this->params['frecuencia_muestreo'],
                "CLASE_INTERFAZ" => $clase_interfaz,
                "UBICACION_INTERFAZ" => $ubicacion_interfaz,
                "OPCIONES_INTERFAZ" => $opciones_interfaz,
                "CALIBRACION" => $this->params['calibracion']
			);

            // Se añade la configuración de los eventos
			$conf["EVENTOS"] = $this->dame_conf_eventos();

			return ($conf);
		}


        //
        // Funciones para los hijos (hijos de sensores virtuales y variables de la función de valores de sensores de procesado)
        //


        function dame_tabla_hijos($tipo_sensor, $parametros_tipo, $valor_sensor_administrable)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los identificadores de nodos administrables y no administrables
            // (Nota: Se recuperan por separado porque p.e. en sensores y actuadores sólo se comprueba
            //  si son administrables si hay localizaciones, si no se devuelve NULL, y la comprobación de no administables
            //  se hace sólo en sensores por un campo en la base de datos)
            $sensor_administrable = ($valor_sensor_administrable == VALOR_SI);
            if ($sensor_administrable == true)
            {
                $ids_nodos_administrables = dame_ids_nodos_administrables(TIPO_NODO_SENSOR);
                $sensor_administrable = ($ids_nodos_administrables === NULL) || (in_array($this->id, $ids_nodos_administrables) == true);
            }

            // Se crean las opciones de la tabla
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            $boton_actualizar_tabla_hijos_sensor = "<i id='actualiza_tabla_hijos_sensor__".$this->id."' class='icon-refresh color-blanco boton_actualizar_tabla_hijos_sensor boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_tabla_hijos_sensor);
            if (($administracion_sensores == true) && ($sensor_administrable == true))
            {
                $boton_anyadir_hijo_sensor = "<i id='anyade_modifica_hijo_sensor__".$this->id."' class='icon-plus color-blanco boton_mostrar_ventana_anyadir_modificar_hijo_sensor boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_hijo_sensor);
            }

            // Se crea la tabla
            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_VIRTUAL:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_VIRTUALES;
                    $clase_virtual = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
                    if ($clase_virtual == CLASE_SENSOR_VIRTUAL_SUMA_VALORES)
                    {
                        $numero_columnas += 1;
                    }
                    $params_tabla = array(
                        "opciones" => $opciones,
                        "numero_columnas" => $numero_columnas,
                        "generar_valores_xml" => true
                    );
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO;
                    $clase_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO];
                    $mostrar_variables = (($clase_procesado == CLASE_SENSOR_PROCESADO_FUNCION_VALORES) ||
                        ($sensor_administrable == true));
                    if ($mostrar_variables == true)
                    {
                        $numero_columnas = NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_CON_VARIABLES;
                        $anchuras_columnas = ANCHURAS_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_CON_VARIABLES;
                    }
                    else
                    {
                        $numero_columnas = NUMERO_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_SIN_VARIABLES;
                        $anchuras_columnas = ANCHURAS_COLUMNAS_TABLA_HIJOS_SENSORES_PROCESADO_SIN_VARIABLES;
                    }
                    $params_tabla = array(
                        "opciones" => $opciones,
                        "numero_columnas" => $numero_columnas,
                        "anchuras_columnas" => unserialize($anchuras_columnas),
                        "generar_valores_xml" => true
                    );
                    break;
                }
            }
            $tabla = new TablaDatos(
                "tabla-hijos-sensor",
                $this->idiomas->_("Hijos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Tipo"));
            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_VIRTUAL:
                {
                    $clase_virtual = $parametros_tipo;
                    if ($clase_virtual == CLASE_SENSOR_VIRTUAL_SUMA_VALORES)
                    {
                        array_push($cabecera, $this->idiomas->_("Operación"));
                    }
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    array_push($cabecera, $this->idiomas->_("Campos"));
                    array_push($cabecera, $this->idiomas->_("Función"));
                    if ($mostrar_variables == true)
                    {
                        array_push($cabecera, $this->idiomas->_("Variable"));
                    }
                    array_push($cabecera, $this->idiomas->_("Valores obligatorios"));
                    break;
                }
            }
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los sensores hijos a la tabla y el pie de tabla
            $consulta = "
                SELECT
                    hijos_sensores.id AS id,
                    hijos_sensores.sensor_hijo AS sensor_hijo,
                    sensores.nombre AS nombre,
                    sensores.clase AS clase,
                    sensores.tipo AS tipo,
                    hijos_sensores.parametros_tipo AS parametros_tipo
                FROM
                    hijos_sensores,
                    sensores
                WHERE
                    (hijos_sensores.sensor_padre = '".$bd_red->_($this->id)."')
                    AND (hijos_sensores.sensor_hijo = sensores.id)
                ORDER BY sensores.nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_hijos = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $descripcion_tipo = NodoSensor::dame_descripcion_tipo_sensor($fila['tipo']);
                $datos = array(
                    $fila['nombre'],
                    $descripcion_tipo
                );
                switch ($tipo_sensor)
                {
                    case TIPO_SENSOR_VIRTUAL:
                    {
                        $clase_virtual = $parametros_tipo;
                        if ($clase_virtual == CLASE_SENSOR_VIRTUAL_SUMA_VALORES)
                        {
                            $operacion = $fila['parametros_tipo'];
                            $descripcion_operacion = NodoSensor::dame_descripcion_operacion_hijo_sensor_virtual($operacion);
                            array_push($datos, $descripcion_operacion);
                        }
                        break;
                    }
                    case TIPO_SENSOR_PROCESADO:
                    {
                        $clase_hijo_sensor_procesado = $fila['clase'];
                        $parametros_tipo_hijo_sensor_procesado = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
                        $campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_hijo_sensor_procesado[0]);
                        $descripcion_campos = "";
                        foreach ($campos as $campo)
                        {
                            if ($descripcion_campos == "")
                            {
                                $descripcion_campos .= dame_descripcion_campo_clase_sensor($clase_hijo_sensor_procesado, $campo);
                            }
                            else
                            {
                                $descripcion_campos .= ", ".strtolower(dame_descripcion_campo_clase_sensor($clase_hijo_sensor_procesado, $campo));
                            }
                        }
                        $funcion = $parametros_tipo_hijo_sensor_procesado[1];
                        $parametros_funcion = $parametros_tipo_hijo_sensor_procesado[2];
                        $descripcion_funcion = NodoSensor::dame_descripcion_funcion_hijo_sensor_procesado($funcion);
                        if ($parametros_funcion != "")
                        {
                            $horas_periodo_funcion = $parametros_funcion;
                            $segundos_periodo_funcion = $horas_periodo_funcion * 3600;
                            $texto_periodo = dame_texto_periodo($segundos_periodo_funcion);
                            $descripcion_funcion .= " (".$texto_periodo.")";
                        }
                        $variable = $parametros_tipo_hijo_sensor_procesado[3];
                        $valores_obligatorios = dame_descripcion_valores_si_no($parametros_tipo_hijo_sensor_procesado[4]);
                        array_push($datos, $descripcion_campos);
                        array_push($datos, $descripcion_funcion);
                        if ($mostrar_variables == true)
                        {
                            array_push($datos, $variable);
                        }
                        array_push($datos, $valores_obligatorios);
                        break;
                    }
                }

                $opciones = array();
                if (($administracion_sensores == true) && ($sensor_administrable == true))
                {
                    $editar = "<i id='anyade_modifica_hijo_sensor__".$this->id."__".$fila['sensor_hijo']."__".$fila['id']."' class='icon-pencil color-gris boton_mostrar_ventana_anyadir_modificar_hijo_sensor boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_hijo_sensor__".$this->id."__".$fila['sensor_hijo']."__".$fila['id']."__".$tipo_sensor."' class='icon-remove color-gris boton_eliminar_hijo_sensor boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosHijoSensor__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Hijos").": ".$numero_hijos);

            return ($tabla->dame_tabla(false));
		}


        //
        // Funciones de iconos del mapa
        //


        function dame_nombre_icono_base()
		{
            switch ($this->params["clase"])
            {
                case CLASE_SENSOR_GENERICA:
                {
                    $icono = NodoSensor::dame_parametro_clase_generica(
                        $this->params["parametros_clase"],
                        INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO);
                    break;
                }
                default:
                {
                    $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($this->params["clase"]);
                    $icono = $caracteristicas_clase_sensor["icono"];
                }
            }
            return ($icono);
        }


        function crea_imagen_texto_auxiliar(&$ruta_imagen_texto_auxiliar)
		{
            // Ruta de la imagen con el texto auxiliar
            $directorio_absoluto_imagen_mapa_actual = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
            $ruta_imagen_texto_auxiliar = $directorio_absoluto_imagen_mapa_actual."/"."texto_auxiliar_tmp_mapa.png";

            // Si se ha establecido una cadena con el texto auxiliar, se utiliza ese texto auxiliar
            if ($this->texto_auxiliar_mapa != "")
            {
                // Se crea la etiqueta auxiliar (el valor actual del sensor)
                crea_imagen_texto("(".$this->texto_auxiliar_mapa.")", $ruta_imagen_texto_auxiliar);
            }
            else
            {
                // Cadena con los valores actuales del sensor
                if ($this->params["ultimos_valores"] === NULL)
                {
                    $cadena_ultimos_valores_unidad = $this->idiomas->_("Sin datos");
                }
                else
                {
                    $cadena_ultimos_valores_unidad = NodoSensor::dame_cadena_valores_sensor(
                        $_SESSION["id_ratio_sensores"],
                        $this->id,
                        $this->params["hora_ultimos_valores"],
                        $this->params["ultimos_valores"],
                        $this->params["clase"],
                        $this->params["parametros_clase"],
                        $this->params["incrementos_tiempo_real_horarios"],
                        GRANULARIDAD_TIEMPO_REAL,
                        SEPARADOR_VALOR_INCREMENTO_SENSOR,
                        FORMATO_CADENA_VALORES_SENSOR_REDUCIDO,
                        NULL);
                }

                // Se crea la etiqueta auxiliar (el valor actual del sensor)
                crea_imagen_texto("(".$cadena_ultimos_valores_unidad.")", $ruta_imagen_texto_auxiliar);
            }
        }


        function anyade_rutas_imagenes_satelite(&$rutas_fila_imagenes_satelite_1, &$rutas_fila_imagenes_satelite_2)
		{
            $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($this->params["timeout_envio"]);
            if ($timeout_envio_activado == true)
            {
                $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/reloj.png";
                array_push($rutas_fila_imagenes_satelite_1, $ruta_imagen_satelite);
            }

            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $this->params["eventos_alarma_activados"],
                $this->params["eventos_alarma_activados_clase_cuartoshora"],
                $this->params["eventos_alarma_activados_clase_horas"]);
            if ($hay_eventos_alarma_activados == true)
            {
                $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/exclamacion.png";
                array_push($rutas_fila_imagenes_satelite_1, $ruta_imagen_satelite);
            }
        }


        //
        //  Funciones auxiliares
        //


        function dame_administracion_nodo($ids_nodos_administrables)
        {
            // Si se trabaja con localizaciones y hay localización seleccionada, el sensor se puede administrar sólo si el usuario ve el sensor
            // por sus localizaciones asignadas y descendientes (no por las localizaciones ascendientes)
            switch ($_SESSION["id_localizacion"])
            {
                case ID_DESACTIVADO:
                case ID_NINGUNO:
                {
                    $administracion_nodo = true;
                    break;
                }
                default:
                {
                    $administracion_nodo = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                        (in_array($this->id, $ids_nodos_administrables) == true);
                    break;
                }
            }
            return ($administracion_nodo);
        }


        static function dame_administracion_sensores()
        {
            $administracion_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI);
            return ($administracion_sensores);
        }


        static function dame_administracion_comentarios_sensores()
        {
            $administracion_comentarios_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_comentarios_sensores"] == VALOR_SI);
            return ($administracion_comentarios_sensores);
        }


        static function dame_lectura_sensores()
        {
            $lectura_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_sensores"]["lectura_sensores"] == VALOR_SI);
            return ($lectura_sensores);
        }


        static function dame_exportacion_sensores()
        {
            $exportacion_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_sensores"]["exportacion_sensores"] == VALOR_SI);
            return ($exportacion_sensores);
        }


        static function dame_envio_valores_manuales_sensores()
        {
            $envio_valores_manuales_sensores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_sensores"]["envio_valores_manuales_sensores"] == VALOR_SI);
            return ($envio_valores_manuales_sensores);
        }


        function dame_conf_eventos()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $eventos_configurados = array();
            $consulta_eventos = "
                SELECT
                    id,
                    granularidad,
                    tipo,
                    parametros
                FROM eventos
                WHERE
                    (granularidad = '".GRANULARIDAD_TIEMPO_REAL."')
                    AND ((origen = '".ORIGEN_EVENTO_SENSOR."') AND (id_origen = ".$bd_red->_($this->params['id'])."))
                        OR ((origen = '".ORIGEN_EVENTO_GRUPO_SENSORES."') AND (id_origen = ".$bd_red->_($this->params['grupo'])."))";
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }
            while ($fila_eventos = $res_eventos->dame_siguiente_fila())
            {
                $evento = new Evento($fila_eventos);
                array_push($eventos_configurados, $evento->dame_conf());
            }
            return ($eventos_configurados);
        }


        static function dame_nombres_eventos_configurados($id_sensor, $id_grupo_sensores)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $eventos_configurados = array();
            $consulta_eventos = "
                SELECT nombre
                FROM eventos
                WHERE
                    ((origen = '".ORIGEN_EVENTO_SENSOR."') AND (id_origen = ".$bd_red->_($id_sensor)."))
                    OR ((origen = '".ORIGEN_EVENTO_GRUPO_SENSORES."') AND (id_origen = ".$bd_red->_($id_grupo_sensores)."))";
            $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
            if ($res_eventos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_eventos."'");
            }
            while ($fila_evento = $res_eventos->dame_siguiente_fila())
            {
                array_push($eventos_configurados, $fila_evento["nombre"]);
            }
            return ($eventos_configurados);
        }


        static function dame_timeout_envio_activado($timeout_envio)
        {
            $timeout_envio_activado = ($timeout_envio == VALOR_SI);
            return ($timeout_envio_activado);
        }


        static function dame_hay_eventos_alarma_activados(
            $eventos_alarma_activados,
            $eventos_alarma_activados_clase_cuartoshora,
            $eventos_alarma_activados_clase_horas)
        {
            $hay_eventos_alarma_activados = (
                ($eventos_alarma_activados == VALOR_SI) ||
                ($eventos_alarma_activados_clase_cuartoshora == VALOR_SI) ||
                ($eventos_alarma_activados_clase_horas == VALOR_SI));
            return ($hay_eventos_alarma_activados);
        }


        static function dame_nombres_eventos_activados(
            $cadena_eventos_activados_tiempo_real,
            $cadena_eventos_activados_clase_cuartoshora,
            $cadena_eventos_activados_clase_horas)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $nombres_eventos_activados = array();
            $ids_eventos_activados_tiempo_real = array();
            $ids_eventos_activados_clase_cuartoshora = array();
            $ids_eventos_activados_clase_horas = array();
            if ($cadena_eventos_activados_tiempo_real != "")
            {
                $ids_eventos_activados_tiempo_real = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_eventos_activados_tiempo_real);
            }
            if ($cadena_eventos_activados_clase_cuartoshora != "")
            {
                $ids_eventos_activados_clase_cuartoshora = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_eventos_activados_clase_cuartoshora);
            }
            if ($cadena_eventos_activados_clase_horas != "")
            {
                $ids_eventos_activados_clase_horas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_eventos_activados_clase_horas);
            }
            $ids_eventos_activados = array_merge(
                $ids_eventos_activados_tiempo_real,
                $ids_eventos_activados_clase_cuartoshora,
                $ids_eventos_activados_clase_horas);
            foreach ($ids_eventos_activados as $id_evento_activado)
            {
                $consulta_evento = "
                    SELECT nombre
                    FROM eventos
                    WHERE
                        id = '".$bd_red->_($id_evento_activado)."'";
                $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
                if ($res_evento == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_evento."'");
                }
                if ($res_evento->dame_numero_filas() > 0)
                {
                    $fila_evento = $res_evento->dame_siguiente_fila();
                    array_push($nombres_eventos_activados, $fila_evento["nombre"]);
                }
            }
            return ($nombres_eventos_activados);
        }


        static function dame_cadenas_hora_valores_sensor(
            $id_sensor,
            $id_ratio,
            $cadena_fecha_hora_valores_base_datos_utc,
            $valores,
            $clase,
            $parametros_clase,
            $incrementos_horarios,
            $granularidad_valores,
            $formato_cadena_valores)
        {
            if ((($cadena_fecha_hora_valores_base_datos_utc === NULL) || ($cadena_fecha_hora_valores_base_datos_utc == "")) ||
                (($valores === NULL) || ($valores == "")))
            {
                return (NULL);
            }

            $cadena_valores = NodoSensor::dame_cadena_valores_sensor(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_valores_base_datos_utc,
                $valores,
                $clase,
                $parametros_clase,
                $incrementos_horarios,
                $granularidad_valores,
                SEPARADOR_VALOR_INCREMENTO_SENSOR,
                $formato_cadena_valores,
                NULL);

            // Conversión de fechas a local
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

            return (array(
                "cadena_fecha_hora_valores_local_local" => $cadena_fecha_hora_valores_local_local,
                "cadena_valores" => $cadena_valores
            ));
        }


        static function dame_cadenas_hora_valores_clase_sensor(
            $id_ratio,
            $id_sensor,
            $cadena_fecha_hora_valores_clase_base_datos_utc,
            $valores_clase,
            $clase,
            $parametros_clase,
            $granularidad_valores)
        {
            if ((($cadena_fecha_hora_valores_clase_base_datos_utc === NULL) || ($cadena_fecha_hora_valores_clase_base_datos_utc == "")) ||
                (($valores_clase === NULL) || ($valores_clase == "")))
            {
                return (NULL);
            }

            $cadena_valores_clase = NodoSensor::dame_cadena_valores_clase_sensor(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_valores_clase_base_datos_utc,
                $valores_clase,
                $clase,
                $parametros_clase,
                $granularidad_valores,
                NULL);

            // Conversión de fechas a local
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_valores_clase_local_utc = convierte_formato_fecha($cadena_fecha_hora_valores_clase_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_valores_clase_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valores_clase_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);

            return (array(
                "cadena_fecha_hora_valores_clase_local_local" => $cadena_fecha_hora_valores_clase_local_local,
                "cadena_valores_clase" => $cadena_valores_clase
            ));
        }


        static function dame_cadena_valores_sensor(
            $id_ratio,
            $id_sensor,
            $cadena_fecha_hora_valores_base_datos_utc,
            $valores_sensor,
            $clase,
            $parametros_clase,
            $incrementos_horarios,
            $granularidad_valores,
            $separador_valor_incremento,
            $formato_cadena_valores,
            $clase_css_texto_pequenyo)
        {
            // Nota: El formato de cadena de valores (reducido o completo) no se utiliza actualmente en ninguna clase de sensor

            // Comprobación de valores válidos
            $idiomas = new Idiomas();
            if ($valores_sensor === NULL)
            {
                return ($idiomas->_("Sin valores"));
            }

            // Incrementos horarios
            if ($granularidad_valores != GRANULARIDAD_TIEMPO_REAL)
            {
                $incrementos_horarios = VALOR_SI;
            }

            // Nota: Si los segundos de incremento no son horarios, se muestra el texto del periodo de incremento
            // (si hay clase CSS para el texto pequeño no se muestra el texto del periodo - p.e. widgets)

            // Ratio a aplicar en los valores del sensor
            if ($id_ratio != ID_NINGUNO)
            {
                $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase, CAMPO_TODOS);
                if ($aplicar_ratio == true)
                {
                    $info_ratio = dame_info_ratio_sensor_fecha(
                        $id_ratio,
                        $id_sensor,
                        $cadena_fecha_hora_valores_base_datos_utc,
                        INTERVALO_VALORES_TIEMPO_REAL);
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_valores_base_datos_utc, false);
                    if ($valor_ratio === NULL)
                    {
                        $aplicar_ratio = false;
                    }
                }
            }
            else
            {
                $aplicar_ratio = false;
            }

            // Clase de sensor
            switch ($clase)
            {
                case CLASE_SENSOR_TEMPERATURA:
                {
                    if ($valores_sensor == "")
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $temperatura = formatea_numero($valores_sensor, 2);
                        $cadena_valores_sensor = $temperatura." ".dame_html_cadena_clase_css($_SESSION["unidad_medida_temperatura"], $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_HUMEDAD:
                {
                    if ($valores_sensor == "")
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $humedad = formatea_numero($valores_sensor, 2);
                        $cadena_valores_sensor = $humedad." ".dame_html_cadena_clase_css("%", $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR:
                {
                    $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                    if (($valores_sensor[0] == "") || ($valores_sensor[1] == ""))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $iluminacion = intval($valores_sensor[0]);
                        $luz_artificial = $valores_sensor[1];

                        // Nota: Si el valor de luz artificial no es 0 o 1, se asume que es un porcentaje (entre 0 y 1)
                        $cadena_valores_sensor = $iluminacion." ".dame_html_cadena_clase_css($idiomas->_("luxes"), $clase_css_texto_pequenyo);
                        switch ($luz_artificial)
                        {
                            case 0:
                            {
                                break;
                            }
                            case 1:
                            {
                                $cadena_valores_sensor .= " (".$idiomas->_("luz artificial").")";
                                break;
                            }
                            default:
                            {
                                $porcentaje_luz_artificial = formatea_numero($luz_artificial * 100, 2);
                                $cadena_valores_sensor .= " (".$porcentaje_luz_artificial." % ".$idiomas->_("luz artificial").")";
                                break;
                            }
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_VIENTO:
                {
                    $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                    if (($valores_sensor[0] == "") || ($valores_sensor[1] == ""))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $velocidad = formatea_numero($valores_sensor[0], 2);
                        $direccion = formatea_numero($valores_sensor[1], 2);
                        $cadena_valores_sensor =
                            $velocidad." ".dame_html_cadena_clase_css($_SESSION["unidad_medida_velocidad"], $clase_css_texto_pequenyo).", ".
                            $direccion.dame_html_cadena_clase_css(" º", $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $absoluto = $valor_incremento_sensor[0];
                    $incremento = $valor_incremento_sensor[1];
                    if ((($absoluto == "?") || ($absoluto == "")) &&
                        (($incremento == "?") || ($incremento == "")))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        switch ($clase)
                        {
                            case CLASE_SENSOR_ENERGIA_ACTIVA:
                            {
                                $unidad_medida_absoluto = $idiomas->_("kWh");
                                $unidad_medida_incremento_horario = $idiomas->_("kW");
                                break;
                            }
                            case CLASE_SENSOR_ENERGIA_REACTIVA:
                            {
                                $unidad_medida_absoluto = $idiomas->_("kVArh");
                                $unidad_medida_incremento_horario = $idiomas->_("kVAr");
                                break;
                            }
                        }
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento_horario);
                        }
                        if (($incremento != "?") && ($incremento != ""))
                        {
                            switch ($granularidad_valores)
                            {
                                case GRANULARIDAD_TIEMPO_REAL:
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    break;
                                }
                                case GRANULARIDAD_CUARTOHORARIA:
                                {
                                    $segundos_incremento = 900;
                                    break;
                                }
                                case GRANULARIDAD_HORARIA:
                                {
                                    $segundos_incremento = 3600;
                                    break;
                                }
                                default:
                                {
                                    throw new Exception("Granularidad de valores desconocida o incorrecta: '".$granularidad_valores."'");
                                }
                            }
                            $incremento *= (3600 / $segundos_incremento);
                            if ($aplicar_ratio == true)
                            {
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_base_datos_utc, $incremento);
                            }
                            $cadena_incremento_horario = formatea_numero($incremento, 2);
                            if (($granularidad_valores == GRANULARIDAD_TIEMPO_REAL) && ($clase_css_texto_pequenyo === NULL))
                            {
                                $texto_periodo_incremento = dame_texto_periodo($segundos_incremento);
                            }
                            else
                            {
                                $texto_periodo_incremento = NULL;
                            }
                        }
                        else
                        {
                            $cadena_incremento_horario = NULL;
                            $texto_periodo_incremento = NULL;
                        }
                        if (($absoluto != "?") && ($absoluto != ""))
                        {
                            $cadena_valores_sensor = formatea_numero($absoluto, 2);
                            $cadena_valores_sensor .= " ".dame_html_cadena_clase_css($unidad_medida_absoluto, $clase_css_texto_pequenyo);
                            if ($cadena_incremento_horario !== NULL)
                            {
                                $cadena_valores_sensor .= " [".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                            }
                        }
                        else
                        {
                            $cadena_valores_sensor = "[".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                        }
                        if ($texto_periodo_incremento !== NULL)
                        {
                            $cadena_valores_sensor .= " (".$texto_periodo_incremento.")";
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $cortes_tension = intval($valor_incremento_sensor[1]);
                    if ($cortes_tension == VALOR_SI)
                    {
                        $cadena_valores_sensor .= $idiomas->_("Cortes de tensión");
                    }
                    else
                    {
                        $cadena_valores_sensor .= $idiomas->_("Tensión correcta");
                    }
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $incremento = $valor_incremento_sensor[1];
                    if (($incremento == "?") || ($incremento == ""))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_consumo = $idiomas->_("kWh");
                        $consumo_estimado = $incremento;
                        switch ($granularidad_valores)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                $segundos_consumo_estimado = $valor_incremento_sensor[2];
                                break;
                            }
                            case GRANULARIDAD_HORARIA:
                            {
                                $segundos_consumo_estimado = 3600;
                                break;
                            }
                            default:
                            {
                                throw new Exception("Granularidad de valores desconocida o incorrecta: '".$granularidad_valores."'");
                            }
                        }
                        $cadena_consumo_estimado = formatea_numero($consumo_estimado, 2);
                        if (($granularidad_valores == GRANULARIDAD_TIEMPO_REAL) && ($clase_css_texto_pequenyo === NULL))
                        {
                            $texto_periodo_consumo_estimado = dame_texto_periodo($segundos_consumo_estimado);
                        }
                        else
                        {
                            $texto_periodo_consumo_estimado = NULL;
                        }
                        $cadena_valores_sensor = "[".$cadena_consumo_estimado." ".dame_html_cadena_clase_css($unidad_medida_consumo, $clase_css_texto_pequenyo)."]";
                        if ($texto_periodo_consumo_estimado !== NULL)
                        {
                            $cadena_valores_sensor .= " (".$texto_periodo_consumo_estimado.")";
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    // Nota: En la cadena de últimos valores (valor e incremento), el incremento es horario
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $absoluto = $valor_incremento_sensor[0];
                    $incremento = $valor_incremento_sensor[1];
                    if ((($absoluto == "?") || ($absoluto == "")) &&
                        (($incremento == "?") || ($incremento == "")))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_absoluto = $idiomas->_("m3");
                        $unidad_medida_incremento_horario = $unidad_medida_absoluto."/".$idiomas->_("h");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento_horario);
                        }
                        if (($incremento != "?") && ($incremento != ""))
                        {
                            switch ($granularidad_valores)
                            {
                                case GRANULARIDAD_TIEMPO_REAL:
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    break;
                                }
                                case GRANULARIDAD_CUARTOHORARIA:
                                {
                                    $segundos_incremento = 900;
                                    break;
                                }
                                case GRANULARIDAD_HORARIA:
                                {
                                    $segundos_incremento = 3600;
                                    break;
                                }
                                default:
                                {
                                    throw new Exception("Granularidad de valores desconocida o incorrecta: '".$granularidad_valores."'");
                                }
                            }
                            $incremento *= (3600 / $segundos_incremento);
                            if ($aplicar_ratio == true)
                            {
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_base_datos_utc, $incremento);
                            }
                            $cadena_incremento_horario = formatea_numero($incremento, 2);
                            if (($granularidad_valores == GRANULARIDAD_TIEMPO_REAL) && ($clase_css_texto_pequenyo === NULL))
                            {
                                $texto_periodo_incremento = dame_texto_periodo($segundos_incremento);
                            }
                            else
                            {
                                $texto_periodo_incremento = NULL;
                            }
                        }
                        else
                        {
                            $cadena_incremento_horario = NULL;
                            $texto_periodo_incremento = NULL;
                        }
                        if (($absoluto != "?") && ($absoluto != ""))
                        {
                            $cadena_valores_sensor = formatea_numero($absoluto, 2);
                            $cadena_valores_sensor .= " ".dame_html_cadena_clase_css($unidad_medida_absoluto, $clase_css_texto_pequenyo);
                            if ($cadena_incremento_horario !== NULL)
                            {
                                $cadena_valores_sensor .= " [".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                            }
                        }
                        else
                        {
                            $cadena_valores_sensor = "[".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                        }
                        if ($texto_periodo_incremento !== NULL)
                        {
                            $cadena_valores_sensor .= " (".$texto_periodo_incremento.")";
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    // Nota: En la cadena de últimos valores (valor e incremento), el incremento es horario
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $absoluto = $valor_incremento_sensor[0];
                    $incremento = $valor_incremento_sensor[1];
                    if ((($absoluto == "?") || ($absoluto == "")) &&
                        (($incremento == "?") || ($incremento == "")))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_absoluto = $idiomas->_("m3");
                        $unidad_medida_incremento_horario = $unidad_medida_absoluto."/".$idiomas->_("h");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento_horario);
                        }
                        if (($incremento != "?") && ($incremento != ""))
                        {
                            switch ($granularidad_valores)
                            {
                                case GRANULARIDAD_TIEMPO_REAL:
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    break;
                                }
                                case GRANULARIDAD_CUARTOHORARIA:
                                {
                                    $segundos_incremento = 900;
                                    break;
                                }
                                case GRANULARIDAD_HORARIA:
                                {
                                    $segundos_incremento = 3600;
                                    break;
                                }
                                default:
                                {
                                    throw new Exception("Granularidad de valores desconocida o incorrecta: '".$granularidad_valores."'");
                                }
                            }
                            $incremento *= (3600 / $segundos_incremento);
                            if ($aplicar_ratio == true)
                            {
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_base_datos_utc, $incremento);
                            }
                            $cadena_incremento_horario = formatea_numero($incremento, 2);
                            if (($granularidad_valores == GRANULARIDAD_TIEMPO_REAL) && ($clase_css_texto_pequenyo === NULL))
                            {
                                $texto_periodo_incremento = dame_texto_periodo($segundos_incremento);
                            }
                            else
                            {
                                $texto_periodo_incremento = NULL;
                            }
                        }
                        else
                        {
                            $cadena_incremento_horario = NULL;
                            $texto_periodo_incremento = NULL;
                        }
                        if (($absoluto != "?") && ($absoluto != ""))
                        {
                            $cadena_valores_sensor = formatea_numero($absoluto, 2);
                            $cadena_valores_sensor .= " ".dame_html_cadena_clase_css($unidad_medida_absoluto, $clase_css_texto_pequenyo);
                            if ($cadena_incremento_horario !== NULL)
                            {
                                $cadena_valores_sensor .= " [".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                            }
                        }
                        else
                        {
                            $cadena_valores_sensor = "[".$cadena_incremento_horario." ".dame_html_cadena_clase_css($unidad_medida_incremento_horario, $clase_css_texto_pequenyo)."]";
                        }
                        if ($texto_periodo_incremento !== NULL)
                        {
                            $cadena_valores_sensor .= " (".$texto_periodo_incremento.")";
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    // Nota: En la cadena de últimos valores (valor e incremento), el incremento es horario
                    $valor_incremento_sensor = explode($separador_valor_incremento, $valores_sensor);
                    $valor = $valor_incremento_sensor[0];
                    $incremento = $valor_incremento_sensor[1];
                    if ((($valor == "?") || ($valor == "")) &&
                        (($incremento == "?") || ($incremento == "")))
                    {
                        $cadena_valores_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida = NodoSensor::dame_parametro_clase_generica($parametros_clase, INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA);
                        $mostrar_incrementos_calculados = NodoSensor::dame_parametro_clase_generica($parametros_clase, INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS);
                        if (($incremento != "") && ($incremento != "?"))
                        {
                            if ($incrementos_horarios == VALOR_SI)
                            {
                                switch ($granularidad_valores)
                                {
                                    case GRANULARIDAD_TIEMPO_REAL:
                                    {
                                        $segundos_incremento = $valor_incremento_sensor[2];
                                        break;
                                    }
                                    case GRANULARIDAD_CUARTOHORARIA:
                                    {
                                        $segundos_incremento = 900;
                                        break;
                                    }
                                    case GRANULARIDAD_HORARIA:
                                    {
                                        $segundos_incremento = 3600;
                                        break;
                                    }
                                    default:
                                    {
                                        throw new Exception("Granularidad de valores desconocida o incorrecta: '".$granularidad_valores."'");
                                    }
                                }
                                $incremento *= (3600 / $segundos_incremento);
                            }
                            else
                            {
                                $segundos_incremento = $valor_incremento_sensor[2];
                            }
                            if ($aplicar_ratio == true)
                            {
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_base_datos_utc, $incremento);
                            }
                            $cadena_incremento = formatea_numero($incremento, 2);
                            if (($granularidad_valores == GRANULARIDAD_TIEMPO_REAL) && ($clase_css_texto_pequenyo === NULL))
                            {
                                $texto_periodo_incremento = dame_texto_periodo($segundos_incremento);
                            }
                            else
                            {
                                $texto_periodo_incremento = NULL;
                            }
                        }
                        else
                        {
                            $cadena_incremento = NULL;
                            $texto_periodo_incremento = NULL;
                        }
                        if (($valor != "?") && ($valor != ""))
                        {
                            $cadena_valores_sensor = formatea_numero($valor, 2);
                            if ($unidad_medida != "")
                            {
                                $cadena_valores_sensor .= " ".dame_html_cadena_clase_css($unidad_medida, $clase_css_texto_pequenyo);
                            }
                            if (($cadena_incremento !== NULL) && ($mostrar_incrementos_calculados == VALOR_SI))
                            {
                                if ($unidad_medida != "")
                                {
                                    $unidad_medida_incremento = $unidad_medida;
                                    if ($incrementos_horarios == VALOR_SI)
                                    {
                                        $unidad_medida_incremento .= "/".$idiomas->_("h");
                                    }
                                }
                                else
                                {
                                    $unidad_medida_incremento = "";
                                    if ($incrementos_horarios == VALOR_SI)
                                    {
                                        $unidad_medida_incremento .= $idiomas->_("por hora");
                                    }
                                }
                                if ($aplicar_ratio == true)
                                {
                                    modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                                }
                                $cadena_valores_sensor .= " [".$cadena_incremento." ".dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                                if ($texto_periodo_incremento !== NULL)
                                {
                                    $cadena_valores_sensor .= " (".$texto_periodo_incremento.")";
                                }
                            }
                        }
                        else
                        {
                            if ($unidad_medida != "")
                            {
                                $unidad_medida_incremento = $unidad_medida;
                                if ($incrementos_horarios == VALOR_SI)
                                {
                                    $unidad_medida_incremento .= "/".$idiomas->_("h");
                                }
                            }
                            else
                            {
                                $unidad_medida_incremento = "";
                                if ($incrementos_horarios == VALOR_SI)
                                {
                                    $unidad_medida_incremento .= $idiomas->_("por hora");
                                }
                            }
                            if ($aplicar_ratio == true)
                            {
                                modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            }
                            $cadena_valores_sensor = "[".$cadena_incremento;
                            if ($unidad_medida_incremento != "")
                            {
                                $cadena_valores_sensor .= " ";
                            }
                            $cadena_valores_sensor .= dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                            if ($texto_periodo_incremento !== NULL)
                            {
                                $cadena_valores_sensor .= " (".$texto_periodo_incremento.")";
                            }
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Clase desconocida: '".$clase."'");
                }
            }

            return ($cadena_valores_sensor);
        }


        static function dame_cadena_valores_clase_sensor(
            $id_ratio,
            $id_sensor,
            $cadena_fecha_hora_valores_clase_base_datos_utc,
            $valores_clase_sensor,
            $clase,
            $parametros_clase,
            $granularidad_valores,
            $clase_css_texto_pequenyo)
        {
            // Clases sin procesado (sin valores de clase ni valores periódicos)
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
            if ($caracteristicas_clase_sensor["procesado_valores"] == false)
            {
                throw Exception("Clase sin procesado de valores: '".$clase."'");
            }

            // Sin valores
            $idiomas = new Idiomas();
            if ($valores_clase_sensor === NULL)
            {
                return ($idiomas->_("Sin valores"));
            }

            // Flag para recuperar los valores periódicos de la clase de sensor
            $recuperar_valores_periodicos_clase_sensor = false;

            // Clase de sensor
            switch ($clase)
            {
                // Clases con valores de clase específicos
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                    if ($pais_tarifas_electricas == PAIS_NINGUNO)
                    {
                        $recuperar_valores_periodicos_clase_sensor = true;
                    }
                    else
                    {
                        $cadena_valores_clase_sensor = NodoSensor::dame_cadena_valores_clase_sensor_energia_activa(
                            $id_sensor,
                            $id_ratio,
                            $cadena_fecha_hora_valores_clase_base_datos_utc,
                            $valores_clase_sensor,
                            $granularidad_valores,
                            $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                    if ($pais_tarifas_electricas == PAIS_NINGUNO)
                    {
                        $recuperar_valores_periodicos_clase_sensor = true;
                    }
                    else
                    {
                        $cadena_valores_clase_sensor = NodoSensor::dame_cadena_valores_clase_sensor_energia_reactiva(
                            $id_sensor,
                            $id_ratio,
                            $cadena_fecha_hora_valores_clase_base_datos_utc,
                            $valores_clase_sensor,
                            $granularidad_valores,
                            $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                    if ($pais_tarifas_electricas == PAIS_NINGUNO)
                    {
                        $recuperar_valores_periodicos_clase_sensor = true;
                    }
                    else
                    {
                        $cadena_valores_clase_sensor = NodoSensor::dame_cadena_valores_clase_sensor_compra_energia(
                            $valores_clase_sensor,
                            $clase_css_texto_pequenyo);
                    }
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                    if ($pais_tarifas_gas == PAIS_NINGUNO)
                    {
                        $recuperar_valores_periodicos_clase_sensor = true;
                    }
                    else
                    {
                        $cadena_valores_clase_sensor = NodoSensor::dame_cadena_valores_clase_sensor_gas(
                            $id_sensor,
                            $id_ratio,
                            $cadena_fecha_hora_valores_clase_base_datos_utc,
                            $valores_clase_sensor,
                            $granularidad_valores,
                            $clase_css_texto_pequenyo);
                    }
                    break;
                }
                // Clases sin valores de clase (se utilizan los valores periódicos)
                case CLASE_SENSOR_TEMPERATURA:
                case CLASE_SENSOR_HUMEDAD:
                case CLASE_SENSOR_LUZ_INTERIOR:
                case CLASE_SENSOR_VIENTO:
                case CLASE_SENSOR_AGUA:
                case CLASE_SENSOR_GENERICA:
                {
                    $recuperar_valores_periodicos_clase_sensor = true;
                    break;
                }
                // Clases sin procesado (sin valores de clase ni valores periódicos)
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $cadena_valores_clase_sensor = NULL;
                    break;
                }
                default:
                {
                    throw new Exception("Clase desconocida: '".$clase."'");
                }
            }

            // Se recuperan los valores periódicos de clase de sensor (si es necesario)
            if ($recuperar_valores_periodicos_clase_sensor == true)
            {
                $cadena_valores_clase_sensor = NodoSensor::dame_cadena_valores_sensor(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_valores_clase_base_datos_utc,
                    $valores_clase_sensor,
                    $clase,
                    $parametros_clase,
                    NULL,
                    $granularidad_valores,
                    SEPARADOR_VALORES_SENSOR,
                    FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                    $clase_css_texto_pequenyo);
            }

            // Se devuelve la cadena de valores de clase de sensor
            return ($cadena_valores_clase_sensor);
        }


        static function dame_cadena_valores_clase_sensor_energia_activa(
            $id_sensor,
            $id_ratio,
            $cadena_fecha_hora_valores_clase_base_datos_utc,
            $valores_clase_sensor,
            $granularidad_valores,
            $clase_css_texto_pequenyo)
        {
            $idiomas = new Idiomas();

            // Ratio a aplicar en los valores del sensor
            if ($id_ratio != ID_NINGUNO)
            {
                $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TODOS);
                if ($aplicar_ratio == true)
                {
                    switch ($granularidad_valores)
                    {
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            $intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                            break;
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $intervalo_valores = INTERVALO_VALORES_HORA;
                            break;
                        }
                    }
                    $info_ratio = dame_info_ratio_sensor_fecha(
                        $id_ratio,
                        $id_sensor,
                        $cadena_fecha_hora_valores_clase_base_datos_utc,
                        $intervalo_valores);
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, false);
                    if ($valor_ratio === NULL)
                    {
                        $aplicar_ratio = false;
                    }
                }
            }
            else
            {
                $aplicar_ratio = false;
            }

            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                // España
                case PAIS_ESPANYA:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $incremento = $valores_clase_sensor[0];
                    $tramo = $valores_clase_sensor[1];
                    $coste = $valores_clase_sensor[2];
                    $sobrepotencia = $valores_clase_sensor[3];

                    if ($incremento == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_incremento = $idiomas->_("kWh");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $incremento);
                        }
                        $cadena_valores_clase_sensor = "[".formatea_numero($incremento, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                        if ($tramo != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("tramo").": ", $clase_css_texto_pequenyo).formatea_numero($tramo, 0);
                        }
                        if ($coste != "")
                        {
                            $unidad_medida_coste = $_SESSION["moneda"];
                            if ($aplicar_ratio == true)
                            {
                                modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $coste);
                            }
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coste").": ", $clase_css_texto_pequenyo).formatea_numero($coste, 2, false)." ".
                                dame_html_cadena_clase_css($unidad_medida_coste, $clase_css_texto_pequenyo);
                        }
                        if ($sobrepotencia != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("sobrepotencia").": ", $clase_css_texto_pequenyo).formatea_numero($sobrepotencia, 2)." ".
                                dame_html_cadena_clase_css($idiomas->_("kW"), $clase_css_texto_pequenyo);
                        }
                    }
                    break;
                }
								case PAIS_PORTUGAL:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $incremento = $valores_clase_sensor[0];
                    $tramo = $valores_clase_sensor[1];
                    $coste = $valores_clase_sensor[2];
                    $sobrepotencia = $valores_clase_sensor[3];

                    if ($incremento == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_incremento = $idiomas->_("kWh");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $incremento);
                        }
                        $cadena_valores_clase_sensor = "[".formatea_numero($incremento, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                        if ($tramo != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("tramo").": ", $clase_css_texto_pequenyo).formatea_numero($tramo, 0);
                        }
                        if ($coste != "")
                        {
                            $unidad_medida_coste = $_SESSION["moneda"];
                            if ($aplicar_ratio == true)
                            {
                                modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $coste);
                            }
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coste").": ", $clase_css_texto_pequenyo).formatea_numero($coste, 2, false)." ".
                                dame_html_cadena_clase_css($unidad_medida_coste, $clase_css_texto_pequenyo);
                        }
                        if ($sobrepotencia != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("sobrepotencia").": ", $clase_css_texto_pequenyo).formatea_numero($sobrepotencia, 2)." ".
                                dame_html_cadena_clase_css($idiomas->_("kW"), $clase_css_texto_pequenyo);
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                }
            }

            return ($cadena_valores_clase_sensor);
        }


        static function dame_cadena_valores_clase_sensor_energia_reactiva(
            $id_sensor,
            $id_ratio,
            $cadena_fecha_hora_valores_clase_base_datos_utc,
            $valores_clase_sensor,
            $granularidad_valores,
            $clase_css_texto_pequenyo)
        {
            $idiomas = new Idiomas();

            // Ratio a aplicar en los valores del sensor
            if ($id_ratio != ID_NINGUNO)
            {
                $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TODOS);
                if ($aplicar_ratio == true)
                {
                    switch ($granularidad_valores)
                    {
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            throw new Exception("Granularidad de valores incorrecta: '".$granularidad_valores."'");
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $intervalo_valores = INTERVALO_VALORES_HORA;
                            break;
                        }
                    }
                    $info_ratio = dame_info_ratio_sensor_fecha(
                        $id_ratio,
                        $id_sensor,
                        $cadena_fecha_hora_valores_clase_base_datos_utc,
                        $intervalo_valores);
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, false);
                    if ($valor_ratio === NULL)
                    {
                        $aplicar_ratio = false;
                    }
                }
            }
            else
            {
                $aplicar_ratio = false;
            }

            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                // España
                case PAIS_ESPANYA:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $incremento = $valores_clase_sensor[0];
                    $tramo = $valores_clase_sensor[1];
                    $coseno_phi = $valores_clase_sensor[2];
                    $penalizable = $valores_clase_sensor[3];

                    if ($incremento == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_incremento = $idiomas->_("kVArh");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $incremento);
                        }
                        $cadena_valores_clase_sensor = "[".formatea_numero($incremento, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                        if ($tramo != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("tramo").": ", $clase_css_texto_pequenyo).formatea_numero($tramo, 0);
                        }
                        if ($coseno_phi != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coseno de phi").": ", $clase_css_texto_pequenyo).formatea_numero($coseno_phi, 2);
                        }
                        if ($penalizable != "")
                        {
                            switch ($penalizable)
                            {
                                case 0:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("no penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                                case 1:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                            }
                        }
                    }
                    break;
                }
								// Portugal
                case PAIS_PORTUGAL:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $incremento = $valores_clase_sensor[0];
                    $tramo = $valores_clase_sensor[1];
                    $coseno_phi = $valores_clase_sensor[2];
                    $penalizable = $valores_clase_sensor[3];

                    if ($incremento == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_incremento = $idiomas->_("kVArh");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $incremento);
                        }
                        $cadena_valores_clase_sensor = "[".formatea_numero($incremento, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                        if ($tramo != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("tramo").": ", $clase_css_texto_pequenyo).$tramo;
                        }
                        if ($coseno_phi != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coseno de phi").": ", $clase_css_texto_pequenyo).formatea_numero($coseno_phi, 2);
                        }
                        if ($penalizable != "")
                        {
                            switch ($penalizable)
                            {
                                case 0:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("no penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                                case 1:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                            }
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                }
            }

            return ($cadena_valores_clase_sensor);
        }


        static function dame_cadena_valores_clase_sensor_compra_energia(
            $valores_clase_sensor,
            $clase_css_texto_pequenyo)
        {
            $idiomas = new Idiomas();

            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                // España
                case PAIS_ESPANYA:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $consumo_estimado = $valores_clase_sensor[0];
                    $consumo_real = $valores_clase_sensor[1];
                    $desvio_consumo = $valores_clase_sensor[2];
                    $coste_desvio = $valores_clase_sensor[3];
                    $penalizable = $valores_clase_sensor[4];

                    if ($consumo_estimado == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_consumo = $idiomas->_("kWh");
                        $unidad_medida_coste = $_SESSION["moneda"];
                        $cadena_valores_clase_sensor = "[".formatea_numero($consumo_estimado, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_consumo, $clase_css_texto_pequenyo)."]";
                        if ($consumo_real != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("consumo real").": ", $clase_css_texto_pequenyo).formatea_numero($consumo_real, 2)." ".
                                dame_html_cadena_clase_css($unidad_medida_consumo, $clase_css_texto_pequenyo);
                        }
                        if ($desvio_consumo != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("desvío de consumo").": ", $clase_css_texto_pequenyo).formatea_numero($desvio_consumo, 2)." ".
                                dame_html_cadena_clase_css($unidad_medida_consumo, $clase_css_texto_pequenyo);
                        }
                        if ($coste_desvio != "")
                        {
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coste de desvío").": ", $clase_css_texto_pequenyo).formatea_numero($coste_desvio, 2)." ".
                                dame_html_cadena_clase_css($unidad_medida_coste, $clase_css_texto_pequenyo);
                        }
                        if ($penalizable != "")
                        {
                            switch ($penalizable)
                            {
                                case 0:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("no penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                                case 1:
                                {
                                    $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(" (".$idiomas->_("penalizable").")", $clase_css_texto_pequenyo);
                                    break;
                                }
                            }
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                }
            }

            return ($cadena_valores_clase_sensor);
        }


        static function dame_cadena_valores_clase_sensor_gas(
            $id_sensor,
            $id_ratio,
            $cadena_fecha_hora_valores_clase_base_datos_utc,
            $valores_clase_sensor,
            $granularidad_valores,
            $clase_css_texto_pequenyo)
        {
            $idiomas = new Idiomas();

            // Ratio a aplicar en los valores del sensor
            if ($id_ratio != ID_NINGUNO)
            {
                $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TODOS);
                if ($aplicar_ratio == true)
                {
                    switch ($granularidad_valores)
                    {
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            throw new Exception("Granularidad de valores incorrecta: '".$granularidad_valores."'");
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $intervalo_valores = INTERVALO_VALORES_HORA;
                            break;
                        }
                    }
                    $info_ratio = dame_info_ratio_sensor_fecha(
                        $id_ratio,
                        $id_sensor,
                        $cadena_fecha_hora_valores_clase_base_datos_utc,
                        $intervalo_valores);
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, false);
                    if ($valor_ratio === NULL)
                    {
                        $aplicar_ratio = false;
                    }
                }
            }
            else
            {
                $aplicar_ratio = false;
            }

            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                // España
                case PAIS_ESPANYA:
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    $incremento = $valores_clase_sensor[0];
                    $consumo = $valores_clase_sensor[1];
                    $coste = $valores_clase_sensor[2];

                    if ($incremento == "")
                    {
                        $cadena_valores_clase_sensor = $idiomas->_("ND");
                    }
                    else
                    {
                        $unidad_medida_incremento = $idiomas->_("m3");
                        if ($aplicar_ratio == true)
                        {
                            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_incremento);
                            aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $incremento);
                        }
                        $cadena_valores_clase_sensor = "[".formatea_numero($incremento, 2)." ".
                            dame_html_cadena_clase_css($unidad_medida_incremento, $clase_css_texto_pequenyo)."]";
                        if ($consumo != "")
                        {
                            $unidad_medida_consumo = $idiomas->_("kWh");
                            if ($aplicar_ratio == true)
                            {
                                modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $consumo);
                            }
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ", $clase_css_texto_pequenyo)."[".formatea_numero($consumo, 2)." ".
                                dame_html_cadena_clase_css($unidad_medida_consumo, $clase_css_texto_pequenyo)."]";
                        }
                        if ($coste != "")
                        {
                            $unidad_medida_coste = $_SESSION["moneda"];
                            if ($aplicar_ratio == true)
                            {
                                modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
                                aplica_ratio_fecha_valor($info_ratio, $cadena_fecha_hora_valores_clase_base_datos_utc, $coste);
                            }
                            $cadena_valores_clase_sensor .= dame_html_cadena_clase_css(", ".$idiomas->_("coste").": ", $clase_css_texto_pequenyo).formatea_numero($coste, 2, false)." ".
                                dame_html_cadena_clase_css($unidad_medida_coste, $clase_css_texto_pequenyo);
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }

            return ($cadena_valores_clase_sensor);
        }


        static function dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo)
        {
            $idiomas = new Idiomas();
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_TEMPERATURA:
                {
                    $unidad_medida = $_SESSION["unidad_medida_temperatura"];
                    break;
                }
                case CLASE_SENSOR_HUMEDAD:
                {
                    $unidad_medida = "%";
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR:
                {
                    switch ($campo)
                    {
                        case CAMPO_ILUMINACION:
                        {
                            $unidad_medida = $idiomas->_("luxes");
                            break;
                        }
                        case CAMPO_LUZ_ARTIFICIAL:
                        {
                            $unidad_medida = $idiomas->_("luz artificial");
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_VIENTO:
                {
                    switch ($campo)
                    {
                        case CAMPO_VELOCIDAD:
                        {
                            $unidad_medida = $_SESSION["unidad_medida_velocidad"];
                            break;
                        }
                        case CAMPO_DIRECCION:
                        {
                            $unidad_medida = "º";
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    switch ($campo)
                    {
                        case CAMPO_ABSOLUTO:
                        case CAMPO_INCREMENTO:
                        {
                            $unidad_medida = $idiomas->_("kWh");
                            break;
                        }
                        case CAMPO_INCREMENTO_POTENCIA:
                        {
                            $unidad_medida = $idiomas->_("kW");
                            break;
                        }
                        case CAMPO_TRAMO:
                        {
                            $unidad_medida = "";
                            break;
                        }
                        case CAMPO_COSTE:
                        {
                            $unidad_medida = $_SESSION["moneda"];
                            break;
                        }
                        case CAMPO_SOBREPOTENCIA:
                        {
                            $unidad_medida = $idiomas->_("kW");
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    switch ($campo)
                    {
                        case CAMPO_ABSOLUTO:
                        case CAMPO_INCREMENTO:
                        {
                            $unidad_medida = $idiomas->_("kVArh");
                            break;
                        }
                        case CAMPO_INCREMENTO_POTENCIA:
                        {
                            $unidad_medida = $idiomas->_("kVAr");
                            break;
                        }
                        case CAMPO_TRAMO:
                        case CAMPO_COSENO_PHI:
                        case CAMPO_PENALIZABLE:
                        {
                            $unidad_medida = "";
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $unidad_medida = $idiomas->_("cortes");
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    switch ($campo)
                    {
                        case CAMPO_CONSUMO_ESTIMADO:
                        case CAMPO_CONSUMO_REAL:
                        case CAMPO_DESVIO_CONSUMO:
                        {
                            $unidad_medida = $idiomas->_("kWh");
                            break;
                        }
                        case CAMPO_COSTE_DESVIO:
                        {
                            $unidad_medida = $_SESSION["moneda"];
                            break;
                        }
                        case CAMPO_PENALIZABLE:
                        {
                            $unidad_medida = "";
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    switch ($campo)
                    {
                        case CAMPO_ABSOLUTO:
                        case CAMPO_INCREMENTO:
                        {
                            $unidad_medida = $idiomas->_("m3");
                            break;
                        }
                        case CAMPO_CONSUMO:
                        {
                            $unidad_medida = $idiomas->_("kWh");
                            break;
                        }
                        case CAMPO_COSTE:
                        {
                            $unidad_medida = $_SESSION["moneda"];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    switch ($campo)
                    {
                        case CAMPO_ABSOLUTO:
                        case CAMPO_INCREMENTO:
                        {
                            $unidad_medida = $idiomas->_("m3");
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo de clase de sensor desconocido: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    $bd_red = BaseDatosRed::dame_base_datos();

                    $consulta = "
                        SELECT parametros_clase
                        FROM sensores
                        WHERE
                            id = '".$bd_red->_($id_sensor)."'";
                    $res = $bd_red->ejecuta_consulta($consulta);
                    if (($res == false) || ($res->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                    }
                    $fila = $res->dame_siguiente_fila();
                    $unidad_medida = NodoSensor::dame_parametro_clase_generica($fila["parametros_clase"], INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA);
                    break;
                }
                default:
                {
                    throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
                }
            }

            return ($unidad_medida);
        }


        static function dame_descripcion_tipo_valores_sensor($tipo_valores_sensor)
        {
            switch ($tipo_valores_sensor)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $descripcion = "Puntuales";
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $descripcion = "Incrementales";
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

        static function dame_descripcion_api_seleccionada($tipo_valores_sensor)
        {
            switch ($tipo_valores_sensor)
            {
                case AXONTIME:
                {
                    $descripcion = "Axon Time";
                    break;
                }
                case SGCLIMA:
                {
                    $descripcion = "Sgclima";
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

        static function dame_descripcion_tipo_curva_sensor($tipo_curva_sensor)
        {
            switch ($tipo_curva_sensor)
            {
                case TIPO_CURVA_HORARIA:
                {
                    $descripcion = "Horaria";
                    break;
                }
                case TIPO_CURVA_CUARTO_HORARIA:
                {
                    $descripcion = "Cuarto-horaria";
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

        static function dame_descripcion_tipo_energia_sensor($tipo_energia_sensor)
        {
            switch ($tipo_energia_sensor)
            {
                case ENERGIA_ACTIVA:
                {
                    $descripcion = "Activa";
                    break;
                }
                case ENERGIA_REACTIVA_INDUCTIVA:
                {
                    $descripcion = "Reactiva Inductiva";
                    break;
                }
                case ENERGIA_REACTIVA_CAPACITIVA:
                   {
                    $descripcion = "Reactiva Capacitiva";
                    break;
                }
                case ENERGIA_EXPORTADA:
                {
                    $descripcion = "Exportada";
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


        static function dame_descripcion_cambio_valores_puntuales_sensor($cambio_valores_puntuales_sensor)
        {
            switch ($cambio_valores_puntuales_sensor)
            {
                case CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL:
                {
                    $descripcion = "Gradual";
                    break;
                }
                case CAMBIO_VALORES_PUNTUALES_SENSOR_INSTANTANEO:
                {
                    $descripcion = "Instantáneo";
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


        static function dame_descripcion_tipo_horas_incrementos_valores_sensor($tipo_horas_incrementos_valores_sensor)
        {
            switch ($tipo_horas_incrementos_valores_sensor)
            {
                case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO:
                {
                    $descripcion = "Fijas";
                    break;
                }
                case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE:
                {
                    $descripcion = "Variables";
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

        static function dame_descripcion_tipo_energia_reactiva($tipo_energia_reactiva)
        {
            switch ($tipo_energia_reactiva)
            {
                case TIPO_ENERGIA_REACTIVA_Q1:
                    {
                        $descripcion = "Inductiva (Q1)";
                        break;
                    }
                case TIPO_ENERGIA_REACTIVA_Q2:
                    {
                        $descripcion = "Q2";
                        break;
                    }
                case TIPO_ENERGIA_REACTIVA_Q3:
                    {
                        $descripcion = "Q3";
                        break;
                    }
                case TIPO_ENERGIA_REACTIVA_Q4:
                    {
                        $descripcion = "Capacitiva (Q4)";
                        break;
                    }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_descripcion_tipo_incrementos_valores_sensor($tipo_incrementos_valores_sensor)
        {
            switch ($tipo_incrementos_valores_sensor)
            {
                case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL:
                {
                    $descripcion = "Fecha inicial";
                    break;
                }
                case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL:
                {
                    $descripcion = "Fecha final";
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


        static function dame_parametros_clase_generica($cadena_parametros_clase_generica)
        {
            $parametros_clase_generica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_generica);
            return ($parametros_clase_generica);
        }


        static function dame_parametro_clase_generica($cadena_parametros_clase_generica, $indice_parametro)
        {
            $parametros_clase_generica = NodoSensor::dame_parametros_clase_generica($cadena_parametros_clase_generica);
            if (($cadena_parametros_clase_generica == "") || (count($parametros_clase_generica) < ($indice_parametro + 1)))
            {
                switch ($indice_parametro)
                {
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA:
                    {
                        $parametro = "";
                        break;
                    }
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA:
                    {
                        $parametro = "";
                        break;
                    }
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO:
                    {
                        $parametro = "Sensor";
                        break;
                    }
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR:
                    {
                        $parametro = COLORES_AZUL_ROJO;
                        break;
                    }
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO:
                    {
                        $parametro = COLORES_AZUL_ROJO;
                        break;
                    }
                    case INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS:
                    {
                        $parametro = VALOR_SI;
                        break;
                    }
                    default:
                    {
                        throw new Exception("Indice de parámetro incorrecto: '".$indice_parametro."'");
                    }
                }
            }
            else
            {
                $parametro = $parametros_clase_generica[$indice_parametro];
            }
            return ($parametro);
        }


        static function dame_descripcion_clase_sensor_virtual($clase_sensor_virtual)
        {
            switch ($clase_sensor_virtual)
            {
                case CLASE_SENSOR_VIRTUAL_SUMA_VALORES:
                {
                    $descripcion = "Suma de valores";
                    break;
                }
                case CLASE_SENSOR_VIRTUAL_MEDIA_VALORES:
                {
                    $descripcion = "Media de valores";
                    break;
                }
                case CLASE_SENSOR_VIRTUAL_VALOR_MINIMO:
                {
                    $descripcion = "Valor mínimo";
                    break;
                }
                case CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO:
                {
                    $descripcion = "Valor máximo";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_descripcion_clase_sensor_procesado($clase_sensor_procesado)
        {
            switch ($clase_sensor_procesado)
            {
                case CLASE_SENSOR_PROCESADO_FUNCION_VALORES:
                {
                    $descripcion = "Función de valores";
                    break;
                }
                case CLASE_SENSOR_PROCESADO_SUMA_VALORES:
                {
                    $descripcion = "Suma de valores";
                    break;
                }
                case CLASE_SENSOR_PROCESADO_MEDIA_VALORES:
                {
                    $descripcion = "Media de valores";
                    break;
                }
                case CLASE_SENSOR_PROCESADO_VALOR_MINIMO:
                {
                    $descripcion = "Valor mínimo";
                    break;
                }
                case CLASE_SENSOR_PROCESADO_VALOR_MAXIMO:
                {
                    $descripcion = "Valor máximo";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_descripcion_clase_sensor_externo($clase_sensor_externo)
        {
            switch ($clase_sensor_externo)
            {
                case CLASE_SENSOR_EXTERNO_NINGUNA:
                {
                    $descripcion = "Ninguna";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
                {
                    $descripcion = "Ficheros CSV";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
                {
                    $descripcion = "HTTP Emios";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
                {
                    $descripcion = "HTTP XML PowerStudio";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_MODBUS_IP:
                {
                    $descripcion = "ModBus IP";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_WIBEEE:
                {
                    $descripcion = "Wibeee";
                    break;
                }
                case CLASE_SENSOR_EXTERNO_API:
                    {
                        $descripcion = "API";
                        break;
                    }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_descripcion_operacion_hijo_sensor_virtual($operacion_hijo_sensor_virtual)
        {
            switch ($operacion_hijo_sensor_virtual)
            {
                case OPERACION_HIJO_SENSOR_VIRTUAL_SUMA:
                {
                    $descripcion = "Suma";
                    break;
                }
                case OPERACION_HIJO_SENSOR_VIRTUAL_RESTA:
                {
                    $descripcion = "Resta";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_descripcion_funcion_hijo_sensor_procesado($funcion_hijo_sensor_procesado)
        {
            switch ($funcion_hijo_sensor_procesado)
            {
                case FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD:
                {
                    $descripcion = "Identidad";
                    break;
                }
                case FUNCION_HIJO_SENSOR_PROCESADO_MEDIA:
                {
                    $descripcion = "Media";
                    break;
                }
                case FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR:
                {
                    $descripcion = "Desviación estándar";
                    break;
                }
                case FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO:
                {
                    $descripcion = "Acumulado";
                    break;
                }
                case FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO:
                {
                    $descripcion = "Incremento";
                    break;
                }
                case FUNCION_HIJO_SENSOR_PROCESADO_CONSUMO_ENERGIA_BRUTO:
                {
                    $descripcion = "Consumo de energía bruto";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        //
        // Funciones de clases y tipos de sensor
        //


        static function dame_clases_sensor()
        {
            // Nota: Hay clases que sólo se muestran si la "funcionalidad" está disponible en las tarifas del país correspondientes
            if ($_SESSION["id_red"] != ID_NINGUNO)
            {
                $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_pais_medicion(MEDICION_ELECTRICIDAD);
                $mostrar_clase_compra_energia = ($caracteristicas_tarifas_electricas["compra_energia"] == true);
            }
            else
            {
                $mostrar_clase_compra_energia = true;
            }

            $clases_sensor = array();
            array_push($clases_sensor, CLASE_SENSOR_TEMPERATURA);
            array_push($clases_sensor, CLASE_SENSOR_HUMEDAD);
            array_push($clases_sensor, CLASE_SENSOR_LUZ_INTERIOR);
            array_push($clases_sensor, CLASE_SENSOR_VIENTO);
            array_push($clases_sensor, CLASE_SENSOR_ENERGIA_ACTIVA);
            array_push($clases_sensor, CLASE_SENSOR_ENERGIA_REACTIVA);
            array_push($clases_sensor, CLASE_SENSOR_CORTES_TENSION);
            if ($mostrar_clase_compra_energia == true)
            {
                array_push($clases_sensor, CLASE_SENSOR_COMPRA_ENERGIA);
            }
            array_push($clases_sensor, CLASE_SENSOR_GAS);
            array_push($clases_sensor, CLASE_SENSOR_AGUA);
            array_push($clases_sensor, CLASE_SENSOR_GENERICA);
            return ($clases_sensor);
        }


        static function dame_descripcion_clase_sensor($clase_sensor)
        {
            switch ($clase_sensor)
            {
                case CLASE_NINGUNA:
                {
                    $descripcion_clase_sensor = "Ninguna";
                    break;
                }
                case CLASE_TODAS:
                {
                    $descripcion_clase_sensor = "Todas";
                    break;
                }
                case CLASE_SENSOR_TEMPERATURA:
                {
                    $descripcion_clase_sensor = "Temperatura";
                    break;
                }
                case CLASE_SENSOR_HUMEDAD:
                {
                    $descripcion_clase_sensor = "Humedad";
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR:
                {
                    $descripcion_clase_sensor = "Luz interior";
                    break;
                }
                case CLASE_SENSOR_VIENTO:
                {
                    $descripcion_clase_sensor = "Viento";
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $descripcion_clase_sensor = "Energía activa";
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $descripcion_clase_sensor = "Energía reactiva";
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $descripcion_clase_sensor = "Cortes de tensión";
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $descripcion_clase_sensor = "Compra de energía";
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $descripcion_clase_sensor = "Gas";
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $descripcion_clase_sensor = "Agua";
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    $descripcion_clase_sensor = "Genérica";
                    break;
                }
                default:
                {
                    $descripcion_clase_sensor = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_clase_sensor));
        }


        static function dame_caracteristicas_clase_sensor($clase_sensor)
        {
            $caracteristicas_clase_sensor = array();

            // Clase de sensor
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_TEMPERATURA:
                {
                    $caracteristicas_clase_sensor["icono"] = "temperatura";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_TEMPERATURA);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array();
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array(
                        CAMPO_GRADOS_HORA_CALEFACCION,
                        CAMPO_GRADOS_HORA_REFRIGERACION,
                        CAMPO_GRADOS_DIA_CALEFACCION,
                        CAMPO_GRADOS_DIA_REFRIGERACION);
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = NULL;
                    break;
                }
                case CLASE_SENSOR_HUMEDAD:
                {
                    $caracteristicas_clase_sensor["icono"] = "agua";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_HUMEDAD);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array();
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = NULL;
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR:
                {
                    $caracteristicas_clase_sensor["icono"] = "vela";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 2;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_ILUMINACION, CAMPO_LUZ_ARTIFICIAL);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array();
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = NULL;
                    break;
                }
                case CLASE_SENSOR_VIENTO:
                {
                    $caracteristicas_clase_sensor["icono"] = "viento";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 2;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_VELOCIDAD, CAMPO_DIRECCION);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array();
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = NULL;
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $caracteristicas_clase_sensor["icono"] = "electrico";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_ABSOLUTO);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_INCREMENTO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array(CAMPO_INCREMENTO_POTENCIA);
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
                    $caracteristicas_clase_sensor["valores_clase"] = true;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = true;
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $caracteristicas_clase_sensor["icono"] = "electrico";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_ABSOLUTO);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_INCREMENTO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array(CAMPO_INCREMENTO_POTENCIA);
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
                    $caracteristicas_clase_sensor["valores_clase"] = true;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = true;
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $caracteristicas_clase_sensor["icono"] = "electrico";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_CORTES);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = false;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = false;
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $caracteristicas_clase_sensor["icono"] = "electrico";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_CONSUMO_ESTIMADO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = true;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = true;
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $caracteristicas_clase_sensor["icono"] = "gas";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_ABSOLUTO);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_INCREMENTO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
                    $caracteristicas_clase_sensor["valores_clase"] = true;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = true;
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $caracteristicas_clase_sensor["icono"] = "agua";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_ABSOLUTO);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_INCREMENTO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = true;
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    $caracteristicas_clase_sensor["icono"] = "Sensor";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_INCREMENTAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 1;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array(CAMPO_VALOR);
                    $caracteristicas_clase_sensor["campos_incrementos"] = array(CAMPO_INCREMENTO);
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = true;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = true;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = false;
                    break;
                }
                // Ninguna
                case CLASE_NINGUNA:
                {
                    $caracteristicas_clase_sensor["icono"] = "";
                    $caracteristicas_clase_sensor["tipo"] = TIPO_CLASE_SENSOR_PUNTUAL;
                    $caracteristicas_clase_sensor["numero_valores"] = 0;
                    $caracteristicas_clase_sensor["campos_puntuales"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos"] = array();
                    $caracteristicas_clase_sensor["campos_puntuales_calculados"] = array();
                    $caracteristicas_clase_sensor["campos_incrementos_calculados"] = array();
                    $caracteristicas_clase_sensor["formato_valores_reducido"] = false;
                    $caracteristicas_clase_sensor["procesado_valores"] = false;
                    $caracteristicas_clase_sensor["granularidad_cuartohoraria"] = false;
                    $caracteristicas_clase_sensor["valores_clase"] = false;
                    $caracteristicas_clase_sensor["incremento_horario_cadena_ultimos_valores"] = NULL;
                    break;
                }
                default:
                {
                    throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
                }
            }
            return ($caracteristicas_clase_sensor);
        }


        static function dame_tipos_sensor()
        {
            $tipos_sensor = array();
            $numero_dispositivos = dame_numero_dispositivos();
            if ($numero_dispositivos > 0)
            {
                array_push($tipos_sensor, TIPO_SENSOR_REAL);
                array_push($tipos_sensor, TIPO_SENSOR_VIRTUAL);
            }
            array_push($tipos_sensor, TIPO_SENSOR_PROCESADO);
            array_push($tipos_sensor, TIPO_SENSOR_EXTERNO);
            return ($tipos_sensor);
        }


        static function dame_descripcion_tipo_sensor($tipo_sensor)
        {
            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_REAL:
                {
                    $descripcion_tipo_sensor = "Real";
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $descripcion_tipo_sensor = "Virtual";
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $descripcion_tipo_sensor = "Procesado";
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $descripcion_tipo_sensor = "Externo";
                    break;
                }
                default:
                {
                    $descripcion_tipo_sensor = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_sensor));
        }


        static function dame_clases_interfaz_sensor()
        {
            $clases_interfaz_sensor = array();
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_IEC102_SERIE);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_MODBUS_SERIE);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_MODBUS_IP);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS);
            array_push($clases_interfaz_sensor, CLASE_INTERFAZ_SENSOR_VALORES_FIJOS);
            return ($clases_interfaz_sensor);
        }


        static function dame_descripcion_clase_interfaz_sensor($clase_interfaz_sensor)
        {
            switch ($clase_interfaz_sensor)
            {
                case CLASE_INTERFAZ_SENSOR_ASINCRONO_SERIE:
                {
                    $descripcion_clase_interfaz_sensor = "Asíncrono serie";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_HTTP_ABBODINCEM:
                {
                    $descripcion_clase_interfaz_sensor = "Abbodincem";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_IEC102_SERIE:
                {
                    $descripcion_clase_interfaz_sensor = "IEC 102 serie";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_MODBUS_SERIE:
                {
                    $descripcion_clase_interfaz_sensor = "ModBus serie";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_MODBUS_IP:
                {
                    $descripcion_clase_interfaz_sensor = "ModBus IP";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_VALORES_ALEATORIOS:
                {
                    $descripcion_clase_interfaz_sensor = "Valores aleatorios";
                    break;
                }
                case CLASE_INTERFAZ_SENSOR_VALORES_FIJOS:
                {
                    $descripcion_clase_interfaz_sensor = "Valores fijos";
                    break;
                }
                default:
                {
                    $descripcion_clase_interfaz_sensor = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_clase_interfaz_sensor));
        }


        //
        // Funciones que devuelven si hay que mostrar los parametros especificados
        // (para los detalles de las tablas de sensores y de acciones de usuario)
        //


        static function dame_mostrar_calibracion($tipo)
        {
            $mostrar_calibracion = false;
            if ($tipo != TIPO_SENSOR_VIRTUAL)
            {
                $mostrar_calibracion = true;
            }
            return ($mostrar_calibracion);
        }


        static function dame_mostrar_tipo_valores($tipo)
        {
            $mostrar_tipo_valores = false;
            switch ($tipo)
            {
                case TIPO_SENSOR_PROCESADO:
                case TIPO_SENSOR_EXTERNO:
                {
                    $mostrar_tipo_valores = true;
                    break;
                }
            }
            return ($mostrar_tipo_valores);
        }


        static function dame_mostrar_cambio_valores_puntuales($clase, $tipo_valores)
        {
            $mostrar_cambio_valores_puntuales = false;
            switch ($clase)
            {
                case CLASE_SENSOR_GENERICA:
                {
                    switch ($tipo_valores)
                    {
                        case TIPO_VALORES_SENSOR_PUNTUALES:
                        {
                            $mostrar_cambio_valores_puntuales = true;
                            break;
                        }
                    }
                    break;
                }
            }
            return ($mostrar_cambio_valores_puntuales);
        }


        static function dame_mostrar_incrementos_tiempo_real_horarios($tipo, $clase)
        {
            $mostrar_incrementos_tiempo_real_horarios = false;
            switch ($tipo)
            {
                case TIPO_SENSOR_REAL:
                case TIPO_SENSOR_VIRTUAL:
                case TIPO_SENSOR_EXTERNO:
                {
                    switch ($clase)
                    {
                        case CLASE_SENSOR_GENERICA:
                        {
                            $mostrar_incrementos_tiempo_real_horarios = true;
                            break;
                        }
                    }
                    break;
                }
            }
            return ($mostrar_incrementos_tiempo_real_horarios);
        }


        static function dame_mostrar_incrementos_negativos_validos($clase)
        {
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
            $mostrar_incrementos_negativos_validos =
                (count($caracteristicas_clase_sensor["campos_incrementos"]) > 0) &&
                ($caracteristicas_clase_sensor["procesado_valores"] == true);
            return ($mostrar_incrementos_negativos_validos);
        }


        static function dame_mostrar_granularidad_cuartohoraria($clase)
        {
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
            $mostrar_granularidad_cuartohoraria = ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true);
            return ($mostrar_granularidad_cuartohoraria);
        }


        static function dame_mostrar_guardar_valores_base_datos($tipo)
        {
            $mostrar_guardar_valores_base_datos = false;
            if ($tipo != TIPO_SENSOR_PROCESADO)
            {
                $mostrar_guardar_valores_base_datos = true;
            }
            return ($mostrar_guardar_valores_base_datos);
        }


        static function dame_mostrar_notificar_todos_eventos($tipo, $clase)
        {
            $mostrar_notificar_todos_eventos = false;
            switch ($tipo)
            {
                case TIPO_SENSOR_PROCESADO:
                case TIPO_SENSOR_EXTERNO:
                {
                    $mostrar_notificar_todos_eventos = true;
                    break;
                }
                case TIPO_SENSOR_REAL:
                case TIPO_SENSOR_VIRTUAL:
                {
                    $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
                    if ($caracteristicas_clase_sensor["procesado_valores"] == true)
                    {
                        $mostrar_notificar_todos_eventos = true;
                    }
                    break;
                }
            }
            return ($mostrar_notificar_todos_eventos);
        }


        static function dame_mostrar_frecuencia_muestreo($tipo)
        {
            $mostrar_frecuencia_muestreo = false;
            switch ($tipo)
            {
                case TIPO_SENSOR_REAL:
                case TIPO_SENSOR_VIRTUAL:
                {
                    $mostrar_frecuencia_muestreo = true;
                    break;
                }
            }
            return ($mostrar_frecuencia_muestreo);
        }


        static function dame_mostrar_frecuencia_envio($tipo)
        {
            $mostrar_frecuencia_envio = false;
            switch ($tipo)
            {
                case TIPO_SENSOR_REAL:
                case TIPO_SENSOR_VIRTUAL:
                case TIPO_SENSOR_EXTERNO:
                {
                    $mostrar_frecuencia_envio = true;
                    break;
                }
            }
            return ($mostrar_frecuencia_envio);
        }


        //
        // Funciones para mostrar los detalles de la tabla (clase de sensor)
        //


        function dame_info_clase_sensor_energia_activa_detalles_tabla($fila)
        {
            $bd_red = BaseDatosRed::dame_base_datos();
            $info = "";

            // Parámetros de clase
            $parametros_clase_energia_activa = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);

            // Selección de país
            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                case PAIS_ESPANYA:
                {
                    // Tarifa eléctrica
                    $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, $parametros_clase_energia_activa);
                    if ($id_tarifa == ID_NINGUNO)
                    {
                        $nombre_tarifa_electrica = $this->idiomas->_("Ninguna");
                        $contrato_tarifa_electrica = CONTRATO_TARIFA_ELECTRICA_NINGUNO;
                    }
                    else
                    {
                        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
                        $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];
                        $contrato_tarifa_electrica = $fila_tarifa_electrica["contrato"];
                    }

                    // Grupo de tarifas eléctricas
                    $id_grupo_tarifas_electricas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                    if ($id_grupo_tarifas_electricas != ID_NINGUNO)
                    {
                        $nombre_grupo_tarifas_electricas = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA, $id_grupo_tarifas_electricas);
                    }

                    // Recuperación de información de últimos costes calculados en tarifas eléctricas 'indexadas'
                    // (si la tarifa actual del sensor es 'indexada')
                    switch ($contrato_tarifa_electrica)
                    {
                        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
                        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
                        case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
                        {
                            // Se muestran los últimos costes calculados del sensor (cuartos de hora y horas)
                            $ultimo_coste_calculado_horas = NULL;
                            $ultimo_coste_calculado_cuartoshora = NULL;
                            $resultado_ultimo_coste_calculado_sensor_horas = dame_cadenas_ultimo_coste_calculado_sensor($fila["nombre"], GRANULARIDAD_HORARIA);
                            if ($resultado_ultimo_coste_calculado_sensor_horas !== NULL)
                            {
                                $cadena_hora_ultimo_coste_calculado_horas_local = $resultado_ultimo_coste_calculado_sensor_horas["cadena_hora_coste_local"];
                                $cadena_ultimo_coste_calculado_horas = $resultado_ultimo_coste_calculado_sensor_horas["cadena_coste"];
                                $ultimo_coste_calculado_horas = $cadena_ultimo_coste_calculado_horas." (".$cadena_hora_ultimo_coste_calculado_horas_local.")";
                            }
                            $resultado_ultimo_coste_calculado_sensor_cuartoshora = dame_cadenas_ultimo_coste_calculado_sensor($fila["nombre"], GRANULARIDAD_CUARTOHORARIA);
                            if ($resultado_ultimo_coste_calculado_sensor_cuartoshora !== NULL)
                            {
                                $cadena_hora_ultimo_coste_calculado_cuartoshora_local = $resultado_ultimo_coste_calculado_sensor_cuartoshora["cadena_hora_coste_local"];
                                $cadena_ultimo_coste_calculado_cuartoshora = $resultado_ultimo_coste_calculado_sensor_cuartoshora["cadena_coste"];
                                $ultimo_coste_calculado_cuartoshora = $cadena_ultimo_coste_calculado_cuartoshora." (".$cadena_hora_ultimo_coste_calculado_cuartoshora_local.")";
                            }

                            if ($ultimo_coste_calculado_horas !== NULL)
                            {
                                $info .= "<i class='icon-info-sign color-azul'></i> ";
                                $info .= $this->idiomas->_("Último coste calculado")." (".$this->idiomas->_("horario")."): ".$ultimo_coste_calculado_horas."<br/>";
                            }
                            if ($ultimo_coste_calculado_cuartoshora !== NULL)
                            {
                                $info .= "<i class='icon-info-sign color-azul'></i> ";
                                $info .= $this->idiomas->_("Último coste calculado")." (".$this->idiomas->_("cuartohorario")."): ".$ultimo_coste_calculado_cuartoshora."<br/>";
                            }
                            if (($ultimo_coste_calculado_horas !== NULL) || ($ultimo_coste_calculado_cuartoshora !== NULL))
                            {
                                $info .= "<br/>";
                            }
                            break;
                        }
                    }

                    // Sensor asociado de compra de energía (si lo hay)
                    $consulta_sensor_compra_energia = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_compra_energia = $bd_red->ejecuta_consulta($consulta_sensor_compra_energia);
                    if ($res_sensor_compra_energia == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_compra_energia."'");
                    }
                    if ($res_sensor_compra_energia->dame_numero_filas() > 0)
                    {
                        $hay_sensor_compra_energia = true;
                        $fila_sensor_compra_energia = $res_sensor_compra_energia->dame_siguiente_fila();
                        $nombre_sensor_compra_energia = $fila_sensor_compra_energia;
                    }
                    else
                    {
                        $hay_sensor_compra_energia = false;
                    }

                    // Sólo si no hay sensor de compra de energía
                    if ($hay_sensor_compra_energia == false)
                    {
                        // Tarifa eléctrica y grupo de tarifas eléctricas
                        if ($id_grupo_tarifas_electricas != ID_NINGUNO)
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Grupo de tarifas eléctricas").": ".htmlspecialchars($nombre_grupo_tarifas_electricas, ENT_QUOTES)."<br/>";
                        }
                        else
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Tarifa eléctrica")." (".$this->idiomas->_("sin grupo")."): ".htmlspecialchars($nombre_tarifa_electrica, ENT_QUOTES)."<br/>";
                        }

                        // Parámetros de facturas
                        $parametros_clase_energia_activa = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
                        $cups = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];
                        $error_maximo_validacion_facturas_energia = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA];
                        $error_maximo_validacion_facturas_potencia = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA];
                        $error_maximo_validacion_facturas_otros_conceptos_coste_total = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL];
                        $tipo_fichero_validacion_facturas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS];
                        $prefijo_fichero_validacion_facturas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS];

                        if ($cups != "")
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("CUPS").": ".htmlspecialchars($cups, ENT_QUOTES)."<br/>";
                        }
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Errores máximos en validación de facturas y cierres").":";
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Energía").": ".$error_maximo_validacion_facturas_energia." "."%"."</li>";
                        if ($error_maximo_validacion_facturas_potencia != "")
                        {
                            $info .= "<li>".$this->idiomas->_("Potencia").": ".$error_maximo_validacion_facturas_potencia." "."%"."</li>";
                        }
                        if ($error_maximo_validacion_facturas_otros_conceptos_coste_total != "")
                        {
                            $info .= "<li>".$this->idiomas->_("Otros conceptos y coste total").": ".$error_maximo_validacion_facturas_otros_conceptos_coste_total." "."%"."</li>";
                        }
                        $info .= "</ul>";
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Tipo de fichero de validación automática de facturas y cierres").": ".
                            dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya($tipo_fichero_validacion_facturas)."<br/>";
                        if ($prefijo_fichero_validacion_facturas != "")
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Prefijo de fichero de validación de facturas y cierres").": ".$prefijo_fichero_validacion_facturas."<br/>";
                        }
                        $info .= "<br/>";
                    }

                    // Sensor asociado de energía reactiva y de corte de tensión (si los hay)
                    // Sensor padre de compra de energía (si lo hay)
                    $anyadir_salto_linea = false;
                    $consulta_sensor_energia_reactiva = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
                    if ($res_sensor_energia_reactiva == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_energia_reactiva."'");
                    }
                    $consulta_sensor_cortes_tension = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
                    if ($res_sensor_cortes_tension == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_cortes_tension."'");
                    }
                    $nombre_sensor_padre_compra_energia = NULL;
                    $consulta_sensores_compra_energia = "
                        SELECT
                            nombre,
                            parametros_clase
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    $res_sensores_compra_energia = $bd_red->ejecuta_consulta($consulta_sensores_compra_energia);
                    while ($fila_sensor_compra_energia = $res_sensores_compra_energia->dame_siguiente_fila())
                    {
                        $nombre_sensor_compra_energia = $fila_sensor_compra_energia["nombre"];
                        $cadena_parametros_clase_compra_energia = $fila_sensor_compra_energia["parametros_clase"];
                        $parametros_clase_compra_energia = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_compra_energia);
                        $cadena_ids_sensores_hijos_compra_energia = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                        $ids_sensores_hijos_compra_energia = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos_compra_energia);
                        if (in_array($fila["id"], $ids_sensores_hijos_compra_energia) == true)
                        {
                            $nombre_sensor_padre_compra_energia = $nombre_sensor_compra_energia;
                            break;
                        }
                    }
                    if ($res_sensor_energia_reactiva->dame_numero_filas() > 0)
                    {
                        $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
                        $nombre_sensor_energia_reactiva = $fila_sensor_energia_reactiva["nombre"];
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de energía reactiva").": ".htmlspecialchars($nombre_sensor_energia_reactiva, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($res_sensor_cortes_tension->dame_numero_filas() > 0)
                    {
                        $fila_sensor_cortes_tension = $res_sensor_cortes_tension->dame_siguiente_fila();
                        $nombre_sensor_cortes_tension = $fila_sensor_cortes_tension["nombre"];
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de corte de tensión").": ".htmlspecialchars($nombre_sensor_cortes_tension, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($nombre_sensor_padre_compra_energia !== NULL)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor padre de compra de energía").": ".htmlspecialchars($nombre_sensor_padre_compra_energia, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($anyadir_salto_linea == true)
                    {
                        $info .= "<br/>";
                    }

                    // Sensor asociado de compra de energía (si lo hay)
                    if ($hay_sensor_compra_energia == true)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de compra de energía").": ".htmlspecialchars($nombre_sensor_compra_energia, ENT_QUOTES)."<br/>";
                        $info .= "<br/>";
                    }
                    break;
                }
								case PAIS_PORTUGAL:
                {
                    // Tarifa eléctrica
                    $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, $parametros_clase_energia_activa);
                    if ($id_tarifa == ID_NINGUNO)
                    {
                        $nombre_tarifa_electrica = $this->idiomas->_("Ninguna");
                        $contrato_tarifa_electrica = CONTRATO_TARIFA_ELECTRICA_NINGUNO;
                    }
                    else
                    {
                        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa);
                        $nombre_tarifa_electrica = $fila_tarifa_electrica["nombre"];
                        $contrato_tarifa_electrica = $fila_tarifa_electrica["contrato"];
                    }

                    // Grupo de tarifas eléctricas
                    $id_grupo_tarifas_electricas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                    if ($id_grupo_tarifas_electricas != ID_NINGUNO)
                    {
                        $nombre_grupo_tarifas_electricas = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL, $id_grupo_tarifas_electricas);
                    }

                    // Sensor asociado de compra de energía (si lo hay)
                    $consulta_sensor_compra_energia = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_compra_energia = $bd_red->ejecuta_consulta($consulta_sensor_compra_energia);
                    if ($res_sensor_compra_energia == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_compra_energia."'");
                    }
                    if ($res_sensor_compra_energia->dame_numero_filas() > 0)
                    {
                        $hay_sensor_compra_energia = true;
                        $fila_sensor_compra_energia = $res_sensor_compra_energia->dame_siguiente_fila();
                        $nombre_sensor_compra_energia = $fila_sensor_compra_energia;
                    }
                    else
                    {
                        $hay_sensor_compra_energia = false;
                    }

                    // Sólo si no hay sensor de compra de energía
                    if ($hay_sensor_compra_energia == false)
                    {
                        // Tarifa eléctrica y grupo de tarifas eléctricas
                        if ($id_grupo_tarifas_electricas != ID_NINGUNO)
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Grupo de tarifas eléctricas").": ".htmlspecialchars($nombre_grupo_tarifas_electricas, ENT_QUOTES)."<br/>";
                        }
                        else
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("Tarifa eléctrica")." (".$this->idiomas->_("sin grupo")."): ".htmlspecialchars($nombre_tarifa_electrica, ENT_QUOTES)."<br/>";
                        }

                        // Parámetros de facturas
                        $parametros_clase_energia_activa = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
                        $cups = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

                        if ($cups != "")
                        {
                            $info .= "<i class='icon-info-sign color-azul'></i> ".
                                $this->idiomas->_("CUPS").": ".htmlspecialchars($cups, ENT_QUOTES)."<br/>";
                        }
                        $info .= "</ul>";
                        $info .= "<br/>";
                    }

                    // Sensor asociado de energía reactiva y de corte de tensión (si los hay)
                    // Sensor padre de compra de energía (si lo hay)
                    $anyadir_salto_linea = false;
                    $consulta_sensor_energia_reactiva = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
                    if ($res_sensor_energia_reactiva == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_energia_reactiva."'");
                    }
                    $consulta_sensor_cortes_tension = "
                        SELECT nombre
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($fila["id"])."')";
                    $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
                    if ($res_sensor_cortes_tension == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensor_cortes_tension."'");
                    }
                    $nombre_sensor_padre_compra_energia = NULL;
                    $consulta_sensores_compra_energia = "
                        SELECT
                            nombre,
                            parametros_clase
                        FROM sensores
                        WHERE
                            (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    $res_sensores_compra_energia = $bd_red->ejecuta_consulta($consulta_sensores_compra_energia);
                    while ($fila_sensor_compra_energia = $res_sensores_compra_energia->dame_siguiente_fila())
                    {
                        $nombre_sensor_compra_energia = $fila_sensor_compra_energia["nombre"];
                        $cadena_parametros_clase_compra_energia = $fila_sensor_compra_energia["parametros_clase"];
                        $parametros_clase_compra_energia = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_compra_energia);
                        $cadena_ids_sensores_hijos_compra_energia = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                        $ids_sensores_hijos_compra_energia = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos_compra_energia);
                        if (in_array($fila["id"], $ids_sensores_hijos_compra_energia) == true)
                        {
                            $nombre_sensor_padre_compra_energia = $nombre_sensor_compra_energia;
                            break;
                        }
                    }
                    if ($res_sensor_energia_reactiva->dame_numero_filas() > 0)
                    {
                        $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
                        $nombre_sensor_energia_reactiva = $fila_sensor_energia_reactiva["nombre"];
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de energía reactiva").": ".htmlspecialchars($nombre_sensor_energia_reactiva, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($res_sensor_cortes_tension->dame_numero_filas() > 0)
                    {
                        $fila_sensor_cortes_tension = $res_sensor_cortes_tension->dame_siguiente_fila();
                        $nombre_sensor_cortes_tension = $fila_sensor_cortes_tension["nombre"];
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de corte de tensión").": ".htmlspecialchars($nombre_sensor_cortes_tension, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($nombre_sensor_padre_compra_energia !== NULL)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor padre de compra de energía").": ".htmlspecialchars($nombre_sensor_padre_compra_energia, ENT_QUOTES)."<br/>";
                        $anyadir_salto_linea = true;
                    }
                    if ($anyadir_salto_linea == true)
                    {
                        $info .= "<br/>";
                    }

                    // Sensor asociado de compra de energía (si lo hay)
                    if ($hay_sensor_compra_energia == true)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de compra de energía").": ".htmlspecialchars($nombre_sensor_compra_energia, ENT_QUOTES)."<br/>";
                        $info .= "<br/>";
                    }
                    break;
                }
                case PAIS_NINGUNO:
                {
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas eléctricas desconocido: '".$pais_tarifas_electricas."'");
                }
            }

            return ($info);
        }


        function dame_info_clase_sensor_energia_reactiva_detalles_tabla($fila)
        {
            $info = "";

            // Sensor asociado de energía activa
            $parametros_clase_energia_reactiva = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
            $id_sensor_energia_activa = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
            if ($id_sensor_energia_activa == ID_NINGUNO)
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $this->idiomas->_("Sensor de energía activa").": ".$this->idiomas->_("Ninguno")."<br/>";
            }
            else
            {
                $nombre_sensor_energia_activa = dame_nombre_sensor($id_sensor_energia_activa);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Sensor de energía activa").": ".htmlspecialchars($nombre_sensor_energia_activa, ENT_QUOTES)."<br/>";
            }
            $tipo_energia_reactiva_sensor = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA];

            $tipo_energia_reactiva_sensor_descripcion= NodoSensor::dame_descripcion_tipo_energia_reactiva($tipo_energia_reactiva_sensor);
            if ($tipo_energia_reactiva_sensor == ID_NINGUNO)
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $this->idiomas->_("Tipo de energía reactiva").": ".$this->idiomas->_("Ninguno")."<br/>";
            }
            else
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Tipo de energía reactiva").": ".htmlspecialchars($tipo_energia_reactiva_sensor_descripcion, ENT_QUOTES)."<br/>";
            }
            $info .= "<br/>";

            return ($info);
        }


        function dame_info_clase_sensor_cortes_tension_detalles_tabla($fila)
        {
            $info = "";

            // Sensor asociado de energía activa
            $parametros_clase_cortes_tension = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
            $id_sensor_energia_activa = $parametros_clase_cortes_tension[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
            if ($id_sensor_energia_activa == ID_NINGUNO)
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $this->idiomas->_("Sensor de energía activa").": ".$this->idiomas->_("Ninguno")."<br/>";
            }
            else
            {
                $nombre_sensor_energia_activa = dame_nombre_sensor($id_sensor_energia_activa);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Sensor de energía activa").": ".htmlspecialchars($nombre_sensor_energia_activa, ENT_QUOTES)."<br/>";
            }
            $info .= "<br/>";

            return ($info);
        }


        function dame_info_clase_sensor_compra_energia_detalles_tabla($fila)
        {
            $info = "";

            // Parámetros de clase
            $parametros_clase_compra_energia = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);

            // Sensores hijos
            $cadena_ids_sensores_hijos = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
            $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
            $nombres_sensores_hijos = dame_nombres_sensores($ids_sensores_hijos);
            sort($nombres_sensores_hijos);
            $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Sensores hijos").": ";
            $info .= "<ul>";
            foreach ($nombres_sensores_hijos as $nombre_sensor_hijo)
            {
                $info .= "<li>".htmlspecialchars($nombre_sensor_hijo, ENT_QUOTES)."</li>";
            }
            $info .= "</ul>";

            // Sensor asociado
            $id_sensor_asociado = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
            $nombre_sensor_asociado = dame_nombre_sensor($id_sensor_asociado);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Sensor asociado").": ".htmlspecialchars($nombre_sensor_asociado, ENT_QUOTES)."<br/>";
            $info .= "<br/>";

            return ($info);
        }


        function dame_info_clase_sensor_gas_detalles_tabla($fila)
        {
            $info = "";

            // Parámetros de clase
            $parametros_clase_gas = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);

            // Selección de país
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    // Tarifa de gas
                    $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_GAS, $parametros_clase_gas);
                    $nombre_tarifa_gas = dame_nombre_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);

                    // Grupo de tarifas de gas
                    $id_grupo_tarifas_gas = $parametros_clase_gas[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                    $nombre_grupo_tarifas_gas = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_GAS_ESPANYA, $id_grupo_tarifas_gas);

                    // Tarifa de gas y grupo de tarifas de gas
                    if ($id_grupo_tarifas_gas != ID_NINGUNO)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Grupo de tarifas de gas").": ".htmlspecialchars($nombre_grupo_tarifas_gas, ENT_QUOTES)."<br/>";
                    }
                    else
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Tarifa de gas")." (".$this->idiomas->_("sin grupo")."): ".htmlspecialchars($nombre_tarifa_gas, ENT_QUOTES)."<br/>";
                    }

                    // Parámetros de clase
                    $parametros_clase_gas = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
                    $cups = $parametros_clase_gas[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];

                    // CUPS
                    if ($cups != "")
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("CUPS").": ".htmlspecialchars($cups, ENT_QUOTES)."<br/>";
                    }
                    $info .= "<br/>";
                    break;
                }
                case PAIS_NINGUNO:
                {
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas desconocido: '".$pais_tarifas_gas."'");
                }
            }

            return ($info);
        }


        function dame_info_clase_sensor_agua_detalles_tabla($fila)
        {
            $info = "";

            // Parámetros de clase
            $parametros_clase_agua = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);

            // Selección de país
            $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
            switch ($pais_tarifas_agua)
            {
                case PAIS_ESPANYA:
                {
                    // Tarifa de agua
                    $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_AGUA, $parametros_clase_agua);
                    $nombre_tarifa_agua = dame_nombre_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);

                    // Grupo de tarifas de agua
                    $id_grupo_tarifas_agua = $parametros_clase_agua[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                    $nombre_grupo_tarifas_agua = dame_nombre_grupo_tarifas(TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA, $id_grupo_tarifas_agua);

                    // Tarifa de agua y grupo de tarifas de agua
                    if ($id_grupo_tarifas_agua != ID_NINGUNO)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Grupo de tarifas de agua").": ".htmlspecialchars($nombre_grupo_tarifas_agua, ENT_QUOTES)."<br/>";
                    }
                    else
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Tarifa de agua")." (".$this->idiomas->_("sin grupo")."): ".htmlspecialchars($nombre_tarifa_agua, ENT_QUOTES)."<br/>";
                    }

                    // Parámetros de clase
                    $parametros_clase_agua = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_clase']);
                    $cups = $parametros_clase_agua[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS];

                    // CUPS
                    if ($cups != "")
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("CUPS").": ".htmlspecialchars($cups, ENT_QUOTES)."<br/>";
                    }
                    $info .= "<br/>";
                    break;
                }
                case PAIS_NINGUNO:
                {
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de agua desconocido: '".$pais_tarifas_agua."'");
                }
            }

            return ($info);
        }


        function dame_info_clase_sensor_generica_detalles_tabla($fila)
        {
            $info = "";

            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            if ($administracion_sensores == true)
            {
                $cadena_parametros_clase_generica = $fila["parametros_clase"];
                $parametros_clase_generica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_generica);
                $nombre_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA];
                $unidad_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA];
                $icono = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO];
                $colores_mapa_calor_valor = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR];
                $colores_mapa_calor_incremento = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO];
                $mostrar_incrementos_calculados = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS];

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Clase genérica").":"."<br/>";
                $info .= "<ul>";
                if ($nombre_medida != "")
                {
                    $info .= "<li>".$this->idiomas->_("Nombre de medida").": ".$nombre_medida."</li>";
                }
                if ($unidad_medida != "")
                {
                    $info .= "<li>".$this->idiomas->_("Unidad de medida").": ".$unidad_medida."</li>";
                }
                $info .= "<li>".$this->idiomas->_("Icono de mapa").": ".dame_descripcion_icono_mapa($icono)."</li>";
                $info .= "<li>".$this->idiomas->_("Colores de mapa de calor de valores").": ".dame_descripcion_colores_mapa_calor($colores_mapa_calor_valor)."</li>";
                $info .= "<li>".$this->idiomas->_("Colores de mapa de calor de incrementos de valores").": ".dame_descripcion_colores_mapa_calor($colores_mapa_calor_incremento)."</li>";
                $info .= "<li>".$this->idiomas->_("Mostrar incrementos calculados").": ".dame_descripcion_valores_si_no($mostrar_incrementos_calculados)."</li>";
                $info .= "</ul>";
                $info .= "<br/>";
            }

            return ($info);
        }


        //
        // Funciones para mostrar los detalles de la tabla (tipo de sensor)
        //


        function dame_info_tipo_sensor_real_detalles_tabla($fila)
        {
            $bd_red = BaseDatosRed::dame_base_datos();
            $info = "";

            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            if ($administracion_sensores == true)
            {
                $parametros_sensor_real = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
                $id_axon = $parametros_sensor_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON];
                $clase_interfaz = $parametros_sensor_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_sensor_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_sensor_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_OPCIONES_INTERFAZ];
                $opciones_interfaz = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $opciones_interfaz);

                $consulta_axon = "
                    SELECT nombre
                    FROM axones
                    WHERE
                        id = '".$bd_red->_($id_axon)."'";
                $res_axon = $bd_red->ejecuta_consulta($consulta_axon);
                if ($res_axon == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_axon."'");
                }
                if ($res_axon->dame_numero_filas() > 0)
                {
                    $fila_axon = $res_axon->dame_siguiente_fila();
                    $nombre_axon = htmlspecialchars($fila_axon["nombre"], ENT_QUOTES);
                }
                else
                {
                    $nombre_axon = $this->idiomas->_("Desconocido");
                }

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Axón").": ".$nombre_axon."<br/>";

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Clase de interfaz").": ".NodoSensor::dame_descripcion_clase_interfaz_sensor($clase_interfaz)."<br/>";
                $info .= "<ul>";
                if ($ubicacion_interfaz != "")
                {
                    $info .= "<li>".$this->idiomas->_("Ubicación de interfaz").": ".dame_descripcion_parametros_ubicacion_interfaz_sensor(
                        $clase_interfaz,
                        $ubicacion_interfaz,
                        TIPO_DESCRIPCION_HTML)."</li>";
                }
                if ($opciones_interfaz != "")
                {
                    $info .= "<li>".$this->idiomas->_("Opciones de interfaz").": ".dame_descripcion_parametros_opciones_interfaz_sensor(
                        $clase_interfaz,
                        $opciones_interfaz,
                        TIPO_DESCRIPCION_HTML)."</li>";
                }
                $info .= "</ul>";
            }

            return ($info);
        }


        function dame_info_tipo_sensor_virtual_detalles_tabla($fila)
        {
            $info = "";

            // Parámetros de sensor virtual
            $parametros_sensor_virtual = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
            $clase_virtual = $parametros_sensor_virtual[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
            $descripcion_clase_virtual = NodoSensor::dame_descripcion_clase_sensor_virtual($clase_virtual);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Clase de sensor virtual").": ".$descripcion_clase_virtual."<br/>";

            return ($info);
        }


        function dame_info_tipo_sensor_externo_detalles_tabla($fila)
        {
            $info = "";

            // Parámetros de sensor externo
            $parametros_sensor_externo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);

            // Identificador del sensor externo sólo para administradores
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            if ($administracion_sensores == true)
            {
                $id_externo = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador externo").": ".$id_externo."<br/>";
            }

            // Clase de sensor externo
            $clase_externo = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
            $descripcion_clase_externo = NodoSensor::dame_descripcion_clase_sensor_externo($clase_externo);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Clase de sensor externo").": ".$descripcion_clase_externo."<br/>";

            // Parámetros del sensor externo sólo para administradores
            if ($administracion_sensores == true)
            {
                $info .= "<ul>";
                $opciones_generales = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                $opciones_valores = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                switch ($clase_externo)
                {
                    case CLASE_SENSOR_EXTERNO_NINGUNA:
                    {
                        break;
                    }
                    default:
                    {
                        $info .= "<li>".$this->idiomas->_("Opciones generales").": ".dame_descripcion_parametros_opciones_generales_sensor_externo(
                            $clase_externo,
                            $opciones_generales,
                            TIPO_DESCRIPCION_HTML)."</li>";
                        $info .= "<li>".$this->idiomas->_("Opciones de valores").": ".dame_descripcion_parametros_opciones_valores_sensor_externo(
                            $clase_externo,
                            $opciones_valores,
                            TIPO_DESCRIPCION_HTML)."</li>";
                    }
                }
                $info .= "</ul>";
            }

            return ($info);
        }


        function dame_info_tipo_sensor_procesado_detalles_tabla($fila)
        {
            $info = "";

            // Párametros de sensor de procesado
            $parametros_sensor_procesado = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
            $clase_procesado = $parametros_sensor_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO];

            $descripcion_clase_procesado = NodoSensor::dame_descripcion_clase_sensor_procesado($clase_procesado);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Clase de sensor de procesado").": ".$descripcion_clase_procesado."<br/>";
            switch ($clase_procesado)
            {
                case CLASE_SENSOR_PROCESADO_FUNCION_VALORES:
                {
                    $info .= "<ul>";
                    $funcion_valores_horaria_sensor_procesado = $parametros_sensor_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_HORARIA];
                    $misma_funcion_valores_cuartohoraria_sensor_procesado = $parametros_sensor_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_MISMA_FUNCION_VALORES_CUARTOHORARIA];
                    $funcion_valores_cuartohoraria_sensor_procesado = $parametros_sensor_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_CUARTOHORARIA];
                    if ($funcion_valores_horaria_sensor_procesado == "")
                    {
                        $funcion_valores_horaria_sensor_procesado = $this->idiomas->_("ND");
                    }
                    $info .= "<li>".$this->idiomas->_("Función de valores").": ".htmlspecialchars($funcion_valores_horaria_sensor_procesado, ENT_QUOTES)."</li>";
                    if ($misma_funcion_valores_cuartohoraria_sensor_procesado == VALOR_NO)
                    {
                        if ($funcion_valores_cuartohoraria_sensor_procesado == "")
                        {
                            $funcion_valores_cuartohoraria_sensor_procesado = $this->idiomas->_("ND");
                        }
                        $info .= "<li>".$this->idiomas->_("Función de valores cuartohoraria").": ".
                            htmlspecialchars($funcion_valores_cuartohoraria_sensor_procesado, ENT_QUOTES)."</li>";
                    }
                    $info .= "</ul>";
                    break;
                }
            }

            return ($info);
        }


        function dame_info_ultimo_error_valores_tiempo_real_tipo_sensor_externo_detalles_tabla($fila)
        {
            $info = "";

            // Clase de sensor externo
            $parametros_sensor_externo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
            $clase_externo = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];

            // Último error de valores en tiempo real
            $ultimo_error_valores_tiempo_real = json_decode($fila['ultimo_error_valores_tiempo_real_json'], true);

            // Título de error
            $info .= "<i class='icon-flag color-gris-claro'></i> ";
            $info .= $this->idiomas->_("Error en la recuperación de valores").":";
            $info .= "<ul>";

            // Zona horaria
            $zona_horaria = dame_zona_horaria_local();

            // Fecha (en la que ha ocurrido el error)
            $cadena_fecha_hora_base_datos_utc = $ultimo_error_valores_tiempo_real["fecha"];
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $info .= "<li>".$this->idiomas->_("Fecha").": ".$cadena_fecha_hora_local_local."</li>";

            // Error y descripción del error según la clase de sensor externo
            switch ($clase_externo)
            {
                case CLASE_SENSOR_EXTERNO_FICHEROS_CSV:
                {
                    $nombre_fichero = $ultimo_error_valores_tiempo_real["nombre_fichero"];
                    $info .= "<li>".$this->idiomas->_("Nombre de fichero").": ".$nombre_fichero."</li>";
                    $descripcion_error = dame_descripcion_error_valores_fichero_csv($ultimo_error_valores_tiempo_real["error"]);
                    $cadena_parametros_error = modifica_cadena_parametros_error_valores_fichero_csv(
                        $ultimo_error_valores_tiempo_real["error"],
                        $ultimo_error_valores_tiempo_real["cadena_parametros_error"]);
                    break;
                }
                case CLASE_SENSOR_EXTERNO_HTTP_EMIOS:
                {
                    $descripcion_error = dame_descripcion_error_recuperacion_valores_http_emios($ultimo_error_valores_tiempo_real["error"]);
                    $cadena_parametros_error = $ultimo_error_valores_tiempo_real["cadena_parametros_error"];
                    break;
                }
                case CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO:
                {
                    $descripcion_error = dame_descripcion_error_recuperacion_valores_http_xml_powerstudio($ultimo_error_valores_tiempo_real["error"]);
                    $cadena_parametros_error = $ultimo_error_valores_tiempo_real["cadena_parametros_error"];
                    break;
                }
                case CLASE_SENSOR_EXTERNO_MODBUS_IP:
                {
                    $descripcion_error = dame_descripcion_error_recuperacion_valores_modbus_ip($ultimo_error_valores_tiempo_real["error"]);
                    $cadena_parametros_error = $ultimo_error_valores_tiempo_real["cadena_parametros_error"];
                    break;
                }
                case CLASE_SENSOR_EXTERNO_API:
                    {
                        $descripcion_error = dame_descripcion_error_recuperacion_valores_api($ultimo_error_valores_tiempo_real["error"]);
                        $cadena_parametros_error = $ultimo_error_valores_tiempo_real["cadena_parametros_error"];
                        break;
                    }
                default:
                {
                    throw new Exception("Clase de sensor externo incorrecta: '".$clase_externo."'");
                }
            }
            $info .= "<li>".$descripcion_error;
            if ($cadena_parametros_error != "")
            {
                $info .= " (".$cadena_parametros_error.")";
            }
            $info .= "</li>";
            $info .= "</ul>";

            return ($info);
        }


        function dame_info_ultimo_error_valores_tipo_sensor_procesado_detalles_tabla($fila, $granularidad)
        {
            $info = "";

            switch ($granularidad)
            {
                case GRANULARIDAD_CUARTOHORARIA:
                {
                    $cadena_ultimo_error_valores_json = $fila['ultimo_error_valores_cuartohorarios_json'];
                    $descripcion_granularidad_valores = $this->idiomas->_("cuartohorarios");
                    break;
                }
                case GRANULARIDAD_HORARIA:
                {
                    $cadena_ultimo_error_valores_json = $fila['ultimo_error_valores_horarios_json'];
                    $descripcion_granularidad_valores = $this->idiomas->_("horarios");
                    break;
                }
                default:
                {
                    throw new Exception("Granularidad incorrecta: '".$granularidad."'");
                }
            }

            // Último error de valores
            if ($cadena_ultimo_error_valores_json != "")
            {
                $ultimo_error_valores = json_decode($cadena_ultimo_error_valores_json, true);

                // Título de error
                $info .= "<i class='icon-flag color-gris-claro'></i> ";
                $info .= $this->idiomas->_("Error en el cálculo de valores")." (".$descripcion_granularidad_valores."):";
                $info .= "<ul>";

                // Zona horaria
                $zona_horaria = dame_zona_horaria_local();

                // Fecha (en la que ha ocurrido el error)
                $cadena_fecha_hora_base_datos_utc = $ultimo_error_valores["fecha"];
                $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $info .= "<li>".$this->idiomas->_("Fecha").": ".$cadena_fecha_hora_local_local."</li>";

                // Error y descripción del error de procesado
                $descripcion_error = dame_descripcion_error_calculo_valores_sensor_procesado($ultimo_error_valores["error"]);
                $cadena_parametros_error = modifica_cadena_parametros_error_calculo_valores_sensor_procesado(
                    $ultimo_error_valores["error"],
                    $ultimo_error_valores["cadena_parametros_error"]);
                $info .= "<li>".$descripcion_error;
                if ($cadena_parametros_error != "")
                {
                    $info .= " (".$cadena_parametros_error.")";
                }
                $info .= "</li>";
                $info .= "</ul>";
            }

            return ($info);
        }


        function dame_info_ultimo_error_valores_clase_sensor_detalles_tabla($fila, $granularidad, $valores_clase)
        {
            $info = "";

            switch ($granularidad)
            {
                case GRANULARIDAD_CUARTOHORARIA:
                {
                    $cadena_ultimo_error_valores_clase_json = $fila['ultimo_error_valores_clase_cuartohorarios_json'];
                    $descripcion_granularidad_valores = $this->idiomas->_("cuartohorarios");
                    break;
                }
                case GRANULARIDAD_HORARIA:
                {
                    $cadena_ultimo_error_valores_clase_json = $fila['ultimo_error_valores_clase_horarios_json'];
                    $descripcion_granularidad_valores = $this->idiomas->_("horarios");
                    break;
                }
                default:
                {
                    throw new Exception("Granularidad incorrecta: '".$granularidad."'");
                }
            }

            // Último error de valores
            if ($cadena_ultimo_error_valores_clase_json != "")
            {
                $ultimo_error_valores_clase = json_decode($cadena_ultimo_error_valores_clase_json, true);

                // Título de error
                $info .= "<i class='icon-flag color-gris-claro'></i> ";
                if ($valores_clase == true)
                {
                    $info .= $this->idiomas->_("Error en el cálculo de valores de clase");
                }
                else
                {
                    $info .= $this->idiomas->_("Error en el cálculo de valores");
                }
                $info .= " (".$descripcion_granularidad_valores."):";
                $info .= "<ul>";

                // Zona horaria
                $zona_horaria = dame_zona_horaria_local();

                // Fecha (en la que ha ocurrido el error)
                $cadena_fecha_hora_base_datos_utc = $ultimo_error_valores_clase["fecha"];
                $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $info .= "<li>".$this->idiomas->_("Fecha").": ".$cadena_fecha_hora_local_local."</li>";

                // Error y descripción del error de procesado
                $descripcion_error = dame_descripcion_error_calculo_valores_clase_sensor($ultimo_error_valores_clase["error"]);
                $cadena_parametros_error = modifica_cadena_parametros_error_calculo_valores_clase_sensor(
                    $ultimo_error_valores_clase["error"],
                    $ultimo_error_valores_clase["cadena_parametros_error"]);
                $info .= "<li>".$descripcion_error;
                if ($cadena_parametros_error != "")
                {
                    $info .= " (".$cadena_parametros_error.")";
                }
                $info .= "</li>";
                $info .= "</ul>";
            }

            return ($info);
        }


        //
        // Funciones para mostrar los detalles de la tabla (padres de sensor)
        //


        function dame_info_padres_sensor_detalles_tabla()
        {
            $bd_red = BaseDatosRed::dame_base_datos();
            $info = "";

            $ids_sensores_padres = array();
            $consulta_hijos_sensores = "
                SELECT sensor_padre
                FROM hijos_sensores
                WHERE
                    sensor_hijo = ".$bd_red->_($this->id);
            $res_hijos_sensores = $bd_red->ejecuta_consulta($consulta_hijos_sensores);
            if ($res_hijos_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_hijos_sensores."'");
            }
            while ($fila_hijo_sensor = $res_hijos_sensores->dame_siguiente_fila())
            {
                $id_sensor_padre = $fila_hijo_sensor["sensor_padre"];
                array_push($ids_sensores_padres, $id_sensor_padre);
            }

            if (count($ids_sensores_padres) > 0)
            {
                $nombres_sensores_padres = dame_nombres_sensores($ids_sensores_padres);
                sort($nombres_sensores_padres);

                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Sensores padres").":"."<br/>";
                $info .= "<ul>";
                foreach ($nombres_sensores_padres as $nombre_sensor_padre)
                {
                    $info .= "<li>".htmlspecialchars($nombre_sensor_padre, ENT_QUOTES)."</li>";
                }
                $info .= "</ul>";
            }
            return ($info);
        }
	}
?>
