<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/util_informacion_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/util_plantillas_informes_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/InformesFichero/util_informacion_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/InformesFichero/util_lineas_base_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/util_analisis_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/util_comparacion_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/util_estadistica_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/util_eventos_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/util_informacion_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/util_compra_energia_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/util_consumos_costes_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/util_facturas_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/util_informes_personalizados_informes_automaticos.php');


    // Constantes

    // Indices de parámetros de periodicidad
    define("INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_DIA_GENERACION_INFORME", 0);
    define("INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_NUMERO_DIAS_RETRASO_GENERACION_INFORME", 1);

    // Indices de parámetros de periodos de tiempo
    define("INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_TIPO_SELECCION_PERIODO_TIEMPO", 0);
    define("INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_PERIODO_TIEMPO", 1);
    define("INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_INICIAR_COMIENZO_PERIODO_TIEMPO", 2);


	// Clase que representa un informe automático
	class InformeAutomatico
	{
        // Funciones estáticas de informe automático


        // Devuelve la cabecera para la tabla de informes automáticos
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Periodicidad"),
                $idiomas->_("Último envío"),
			));
        }


        // Devuelve la consulta para la tabla de informes automáticos
        static function dame_consulta_informes_automaticos($filtro)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM informes_automaticos
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de informes automáticos
        static function dame_tabla_informes_automaticos($filtro)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $boton_actualizar_tabla_informes_automaticos = "<i id='actualiza_informes_automaticos' class='icon-refresh color-blanco boton_actualizar_tabla_informes_automaticos boton-tabla-datos'></i>";
            $boton_ayuda_tabla_informes_automaticos = "<i id='ayuda_informes_automaticos' class='icon-question-sign color-blanco boton_personal_ayuda_tabla_informes_automaticos boton-tabla-datos'></i>";
            $opciones = array(
                $boton_actualizar_tabla_informes_automaticos,
                $boton_ayuda_tabla_informes_automaticos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_INFORMES_AUTOMATICOS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_INFORMES_AUTOMATICOS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-informes-automaticos",
                $idiomas->_("Informes automáticos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = InformeAutomatico::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las programaciones a la tabla y el pie de tabla
            $consulta = InformeAutomatico::dame_consulta_informes_automaticos($filtro);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            $numero_informes_automaticos = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $informe_automatico = new InformeAutomatico($fila);
                $params_fila = array(
                    "opciones" => $informe_automatico->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosInformeAutomatico__".$fila['id'],
                    $informe_automatico->dame_datos_tabla(),
                    $params_fila
                );
                $numero_informes_automaticos += 1;
            }
            $texto_pie = $idiomas->_("Informes automáticos").": ".$numero_informes_automaticos;
            $numero_maximo_informes_automaticos = dame_numero_maximo_informes_automaticos();
            if ($numero_maximo_informes_automaticos > 0)
            {
                $texto_pie .= " (".$idiomas->_("máximo").": ".$numero_maximo_informes_automaticos.")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
        }


        // Devuelve la descripción de la periodicidad del informe automático
        static function dame_descripcion_periodicidad($periodicidad)
        {
            switch ($periodicidad)
            {
                case PERIODICIDAD_INFORME_AUTOMATICO_DIARIA:
                {
                    $descripcion = "Diaria";
                    break;
                }
                case PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL:
                {
                    $descripcion = "Semanal";
                    break;
                }
                case PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL:
                {
                    $descripcion = "Mensual";
                    break;
                }
                case PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA:
                {
                    $descripcion = "Personalizada";
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


        // Devuelve la descripción del tipo de selección de periodo de tiempo del informe automático
        static function dame_descripcion_tipo_seleccion_periodo_tiempo($tipo_seleccion_periodo_tiempo)
        {
            switch ($tipo_seleccion_periodo_tiempo)
            {
                case TIPO_SELECCION_PERIODO_TIEMPO_AUTOMATICO:
                {
                    $descripcion = "Automático";
                    break;
                }
                case TIPO_SELECCION_PERIODO_TIEMPO_CONFIGURABLE:
                {
                    $descripcion = "Configurable";
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


        // Devuelve la descripción del tipo del informe automático
        static function dame_descripcion_tipo($tipo)
        {
            switch ($tipo)
            {
                // Informes automáticos del módulo Personal
                case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
                {
                    $descripcion = "Plantilla de informe";
                    break;
                }
                // Informes automáticos del módulo Sensores
                case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $descripcion = "Activaciones de eventos";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
                {
                    $descripcion = "Temperatura";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
                {
                    $descripcion = "Humedad";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
                {
                    $descripcion = "Luz interior";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
                {
                    $descripcion = "Viento";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
                {
                    $descripcion = "Energía activa";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
                {
                    $descripcion = "Energía reactiva";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
                {
                    $descripcion = "Cortes de tensión (Sensores)";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
                {
                    $descripcion = "Compra de energía";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                {
                    $descripcion = "Gas";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
                {
                    $descripcion = "Agua";
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
                {
                    $descripcion = "Genérica";
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
                {
                    $descripcion = "Análisis horario";
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
                {
                    $descripcion = "Análisis diario";
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $descripcion = "Análisis de comportamiento";
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
                {
                    $descripcion = "Comparación de periodos";
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                {
                    $descripcion = "Comparación con perfil horario";
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                {
                    $descripcion = "Comparación de campos iguales";
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                {
                    $descripcion = "Comparación de campos diferentes";
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                {
                    $descripcion = "Análisis comparativo";
                    break;
                }
                case TIPO_INFORME_SENSORES_VALORES_GENERALES:
                {
                    $descripcion = "Valores generales";
                    break;
                }
                case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $descripcion = "Incrementos totales";
                    break;
                }
                case TIPO_INFORME_SENSORES_HISTOGRAMA:
                {
                    $descripcion = "Histograma";
                    break;
                }
                case TIPO_INFORME_SENSORES_CORRELACION:
                {
                    $descripcion = "Correlación";
                    break;
                }
                // Informes automáticos del módulo Actuadores
                case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $descripcion = "Acciones enviadas";
                    break;
                }
                // Informes automáticos del módulo Smartmeter
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                {
                    $descripcion = "Consumos y costes generales";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                {
                    $descripcion = "Consumos y costes totales";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
                {
                    $descripcion = "Consumos y costes por tramo";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
                {
                    $descripcion = "Excesos de potencia";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
                {
                    $descripcion = "Excesos de energía reactiva";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
                {
                    $descripcion = "Cortes de tensión (SmartMeter)";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                {
                    $descripcion = "Excesos de caudal";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                {
                    $descripcion = "Comparación de periodos";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                {
                    $descripcion = "Desvíos de compra de energía";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                {
                    $descripcion = "Desvíos ponderados de compra de energía";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $descripcion = "Simulación de factura";
                    break;
                }
                case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
                {
                    $descripcion = "Estudio general";
                    break;
                }
                // Informes automáticos del módulo Proyectos
                case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $descripcion = "Simulación de línea base";
                    break;
                }
                case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $descripcion = "Información de proyecto";
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


        // Miembros de informe automáticos


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
            $descripcion_tipo = $icono_dato_erroneo;
            $descripcion_periodicidad = $icono_dato_erroneo;
            $descripcion_ultimo_envio = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Descripciones
                $descripcion_tipo = InformeAutomatico::dame_descripcion_tipo($this->params["tipo"]);
                $descripcion_periodicidad = InformeAutomatico::dame_descripcion_periodicidad($this->params["periodicidad"]);
                $parametros_periodicidad = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["parametros_periodicidad"]);
                $dia_generacion = $parametros_periodicidad[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_DIA_GENERACION_INFORME];
                switch ($this->params["periodicidad"])
                {
                    case PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL:
                    {
                        $descripcion_periodicidad .= " (".strtolower(dame_nombre_dia_semana($dia_generacion)).")";
                        break;
                    }
                    case PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL:
                    {
                        $descripcion_periodicidad .= " (".$dia_generacion.")";
                        break;
                    }
                }
                $descripcion_ultimo_envio = "";
                $ultimo_envio_correcto = $this->params["ultimo_envio_correcto"];
                if ($ultimo_envio_correcto !== NULL)
                {
                    if ($this->params["ultimo_envio_correcto"] == VALOR_SI)
                    {
                        $descripcion_ultimo_envio .= "<i class='icon-thumbs-up-alt color-verde'></i> ";
                    }
                    else
                    {
                        $descripcion_ultimo_envio .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                    }

                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_ultimo_envio_local_utc = convierte_formato_fecha($this->params["hora_ultimo_envio"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_ultimo_envio_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimo_envio_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                    $descripcion_ultimo_envio .= "(".$cadena_hora_ultimo_envio_local_local.")";
                }
                else
                {
                    $descripcion_ultimo_envio = $this->idiomas->_("ND");
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
                $descripcion_tipo,
                $descripcion_periodicidad,
                $descripcion_ultimo_envio
			));
        }


        function dame_opciones_tabla()
		{
            $id = $this->id;
            $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);

            if (InformeAutomatico::dame_administracion_informes_automaticos() == true)
            {
                $editar = "<i id='modifica_informe_automatico__".$id."' ".
                    "class='icon-pencil color-gris boton_mostrar_ventana_modificar_informe_automatico boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_informe_automatico__".$id."' nombre_informe_automatico='".$nombre."' ".
                    "class='icon-remove color-gris boton_eliminar_informe_automatico boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }
            else
            {
                $opciones = array();
            }
			return ($opciones);
		}


        function dame_detalles_tabla()
		{
			$info = "";

            // Identificador
            $mostrar_identificador = false;
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    if ($_SESSION["utilizada_contrasenya_admin_superadmin"] == true)
                    {
                        $mostrar_identificador = true;
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $mostrar_identificador = true;
                    break;
                }
            }
            if ($mostrar_identificador == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Se obtiene el html de los parámetros específicos según el tipo de informe automático
            $tipo = $this->params["tipo"];
            $parametros_tipo = $this->params["parametros_tipo"];
            $parametros_tipo_json = $this->params["parametros_tipo_json"];
            switch ($tipo)
            {
                // Informes automáticos del módulo Personal
                case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_personal_informe_plantilla_informe($this->id, $parametros_tipo, $parametros_tipo_json);
                    break;
                }
                // Informes automáticos del módulo Sensores
                case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_activaciones_eventos($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_temperatura($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_humedad($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_luz_interior($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_viento($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_energia($tipo, $parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_cortes_tension($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_compra_energia($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_gas($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_agua($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_informacion_generica($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_analisis_horario($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_analisis_diario($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_analisis_comportamiento($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_comparacion_periodos($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_comparacion_perfil_horario($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_comparacion_campos_iguales($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_comparacion_campos_diferentes($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_analisis_comparativo($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_VALORES_GENERALES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_valores_generales($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_incrementos_totales($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_HISTOGRAMA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_histograma($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SENSORES_CORRELACION:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_sensores_correlacion($parametros_tipo);
                    break;
                }
                // Informes automáticos del módulo Actuadores
                case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_actuadores_informacion_acciones_enviadas($parametros_tipo);
                    break;
                }
                // Informes automáticos del módulo Smartmeter
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_generales($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_totales($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_tramos($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_potencia($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_energia_reactiva($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_cortes_tension($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_caudal($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_comparacion_periodos($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_desvios_compra_energia($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_desvios_ponderados_compra_energia($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_simulador_factura($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_smartmeter_estudio_general($parametros_tipo, $parametros_tipo_json);
                    break;
                }
                // Informes automáticos del módulo Proyectos
                case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_proyectos_simulador_linea_base($parametros_tipo);
                    break;
                }
                case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $info .= dame_html_parametros_tipo_informe_automatico_proyectos_informacion_proyecto($parametros_tipo);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de informe automático desconocido: '".$tipo."'");
                }
            }
            $info .= "<br/>";

            // Número de días de retraso
            $parametros_periodicidad = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["parametros_periodicidad"]);
            $numero_dias_retraso_generacion = $parametros_periodicidad[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_NUMERO_DIAS_RETRASO_GENERACION_INFORME];
            if ($numero_dias_retraso_generacion > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Número de días de retraso").": ".$numero_dias_retraso_generacion."<br/>";
            }

            // Parámetros de periodo de tiempo
            $parametros_periodo_tiempo = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["parametros_periodo_tiempo"]);
            $tipo_seleccion_periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_TIPO_SELECCION_PERIODO_TIEMPO];
            $periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_PERIODO_TIEMPO];
            $iniciar_comienzo_periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_INICIAR_COMIENZO_PERIODO_TIEMPO];
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            $info .= $this->idiomas->_("Parámetros de periodo de tiempo").":";
            $lista_parametros_periodo_tiempo = "<ul>";
            $lista_parametros_periodo_tiempo .= "<li>".$this->idiomas->_("Tipo de selección de periodo de tiempo").": ".
                InformeAutomatico::dame_descripcion_tipo_seleccion_periodo_tiempo($tipo_seleccion_periodo_tiempo)."</li>";
            switch ($tipo_seleccion_periodo_tiempo)
            {
                case TIPO_SELECCION_PERIODO_TIEMPO_CONFIGURABLE:
                {
                    $lista_parametros_periodo_tiempo .= "<li>".$this->idiomas->_("Periodo de tiempo").": ".
                        dame_descripcion_periodo_tiempo($periodo_tiempo)."</li>";
                    switch ($periodo_tiempo)
                    {
                        case PERIODO_TIEMPO_DIA:
                        {
                            break;
                        }
                        default:
                        {
                            $lista_parametros_periodo_tiempo .= "<li>".$this->idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": ".
                                dame_descripcion_valores_si_no($iniciar_comienzo_periodo_tiempo)."</li>";
                            break;
                        }
                    }
                }
            }
            $lista_parametros_periodo_tiempo .= "</ul>";
            $info .= $lista_parametros_periodo_tiempo;
            $info .= "<br/>";

            // Número de horas de desplazamiento
            $numero_horas_desplazamiento = $this->params["numero_horas_desplazamiento"];
            if ($numero_horas_desplazamiento != 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Número de horas de desplazamiento").": ".$numero_horas_desplazamiento."<br/>";
                $info .= "<br/>";
            }

            // Estado de error
            if ($this->params["ultimo_envio_correcto"] && $this->params["ultimo_envio_correcto"] != VALOR_SI){
                $info .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $this->idiomas->_("Último informe incorrecto")."<br/>";
                if ($this->params["ultimo_envio_correcto"] == VALOR_FALLO_GENERACION)
                {
                    $info .= "<ul><li>" . $this->idiomas->_("Fallo en la generación del informe");
                } elseif ($this->params["ultimo_envio_correcto"] == VALOR_FALLO_ENVIO) {
                    $info .= "<ul><li>" . $this->idiomas->_("Al menos uno de los destinarios no ha recibido el informe");
                } else {
                    $info .= "<ul><li>" . $this->idiomas->_("Error desconocido");
                }

                $info .= "</li></ul><br/>";

            }

            // Direcciones e-mail de destino
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            $info .= $this->idiomas->_("Direcciones e-mail de destino").":";
            $lista_direcciones_email_destino = "<ul>";
            $direcciones_email_destino = explode(";", $this->params["direcciones_email_destino"]);
            foreach ($direcciones_email_destino AS $direccion_email_destino)
            {
                $lista_direcciones_email_destino .= "<li>".htmlspecialchars($direccion_email_destino, ENT_QUOTES)."</li>";
            }
            $lista_direcciones_email_destino .= "</ul>";
            $info .= $lista_direcciones_email_destino;

			return ($info);
		}


        // Devuelve si el usuario actual tiene permiso de administración de informes automáticos
        function dame_administracion_informes_automaticos()
        {
            $administracion_informes_automaticos = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_personal"]["administracion_informes_automaticos"] == VALOR_SI);
            return ($administracion_informes_automaticos);
        }
    }
?>
