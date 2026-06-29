<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/util_informes_analisis.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_informes_comparacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/HistoricoEvento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_informes_eventos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


	class ModuloSensores extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_SENSORES, NOMBRE_MODULO_SENSORES);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_SENSORES_PRINCIPAL,
                SECCION_SENSORES_EVENTOS,
                SECCION_SENSORES_INFORMACION,
                SECCION_SENSORES_ANALISIS,
                SECCION_SENSORES_COMPARACION,
                SECCION_SENSORES_ESTADISTICA,
                SECCION_SENSORES_MAPA
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_SENSORES_PRINCIPAL:
                {
                    $descripcion = "Principal";
                    break;
                }
                case SECCION_SENSORES_EVENTOS:
                {
                    $descripcion = "Eventos";
                    break;
                }
                case SECCION_SENSORES_INFORMACION:
                {
                    $descripcion = "Información";
                    break;
                }
                case SECCION_SENSORES_ANALISIS:
                {
                    $descripcion = "Análisis";
                    break;
                }
                case SECCION_SENSORES_COMPARACION:
                {
                    $descripcion = "Comparación";
                    break;
                }
                case SECCION_SENSORES_ESTADISTICA:
                {
                    $descripcion = "Estadística";
                    break;
                }

                case SECCION_SENSORES_MAPA:
                {
                    $descripcion = "Mapa";
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


		function dame_contenido_seccion($seccion, $parametros_extra)
		{
            // Módulo
            $html = "<div id='modulo' name='".MODULO_SENSORES."' hidden></div>";

            // Se añade la tabla de selección de localización actual y ratio
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $mostrar_seleccion_ratio = false;
                $seleccion_ratio_visible = false;
                switch ($seccion)
                {
                    case SECCION_SENSORES_PRINCIPAL:
                    case SECCION_SENSORES_INFORMACION:
                    {
                        $mostrar_seleccion_ratio = true;
                        $seleccion_ratio_visible = false;
                        break;
                    }
                    case SECCION_SENSORES_COMPARACION:
                    case SECCION_SENSORES_ESTADISTICA:
                    case SECCION_SENSORES_ANALISIS:
                    case SECCION_SENSORES_MAPA:
                    {
                        $mostrar_seleccion_ratio = true;
                        $seleccion_ratio_visible = true;
                        break;
                    }
                }
                $contenido_oculto = false;
                if (array_key_exists("seleccion_localizacion_actual_desplegada", $parametros_extra) == true)
                {
                    $contenido_oculto = ($parametros_extra["seleccion_localizacion_actual_desplegada"] == VALOR_NO);
                }
                $html .= dame_tabla_seleccion_localizacion_actual_ratio(
                    $mostrar_seleccion_ratio,
                    $seleccion_ratio_visible,
                    $contenido_oculto);
            }

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_SENSORES_PRINCIPAL:
                {
                    $usuario_administrador = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR);
                    $administracion_sensores = NodoSensor::dame_administracion_sensores();
                    $administracion_comentarios_sensores = NodoSensor::dame_administracion_comentarios_sensores();
                    $exportacion_sensores = NodoSensor::dame_exportacion_sensores();
                    $envio_valores_manuales_sensores = NodoSensor::dame_envio_valores_manuales_sensores();
                    $mostrar_herramientas_sensores = ($usuario_administrador == true) ||
                        ($administracion_sensores == true) ||
                        ($administracion_comentarios_sensores == true) ||
                        ($exportacion_sensores == true) ||
                        ($envio_valores_manuales_sensores == true);
                    if ($mostrar_herramientas_sensores == true)
                    {
                        $html .= $this->dame_herramientas_principal();
                    }
                    $html .= $this->dame_principal();
                    break;
                }
                case SECCION_SENSORES_EVENTOS:
                {
                    $html .= $this->dame_eventos();
                    break;
                }
                case SECCION_SENSORES_INFORMACION:
                {
                    $html .= $this->dame_informacion();
                    break;
                }
                case SECCION_SENSORES_COMPARACION:
                {
                    $html .= $this->dame_comparacion();
                    break;
                }
                case SECCION_SENSORES_ESTADISTICA:
                {
                    $html .= $this->dame_estadistica();
                    break;
                }
                case SECCION_SENSORES_ANALISIS:
                {
                    $html .= $this->dame_analisis();
                    break;
                }
                case SECCION_SENSORES_MAPA:
                {
                    $html .= $this->dame_mapa();
                    break;
                }
                default:
                {
                    $res = "ERROR";
                    $msg = $this->idiomas->_("Sección desconocida");
                    break;
                }
            }

            print(json_encode(array(
                "res" => $res,
                "msg" => $msg,
                "html" => $html))
            );
		}


        //
		// Funciones para obtener el contenido de las secciones
		//


        function dame_herramientas_principal()
		{
			// Se recuperan los controles a mostrar
            $boton_anyadir_comentarios_sensores = "<br/><button id='boton_anyadir_comentarios_sensores' class='btn-mini btn btn-success boton_mostrar_ventana_anyadir_comentarios' origen_comentarios='".ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES."'>".$this->idiomas->_("Añadir comentarios")."</button><br/><br/>";
			$boton_recargar_configuraciones_sensores = "<br/><button id='boton_recargar_configuraciones_sensores' class='btn-mini btn btn-success boton_sensores_envia_accion_herramientas_sensores'>".$this->idiomas->_("Recargar configuraciones")."</button><br/><br/>";
            $boton_importar_valores_sensor = "<br/><button id='boton_importar_valores_sensor' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_importacion_valores_sensor'>".$this->idiomas->_("Importar valores")."</button><br/><br/>";
            $boton_exportar_valores_sensor = "<br/><button id='boton_exportar_valores_sensor' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_exportacion_valores_sensor'>".$this->idiomas->_("Exportar valores")."</button><br/><br/>";
            $boton_borrar_valores_sensor = "<br/><button id='boton_borrar_valores_sensor' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_borrado_valores_sensor'>".$this->idiomas->_("Borrar valores")."</button><br/><br/>";
            $boton_recalcular_valores_clase_sensor = "<br/><button id='boton_recalcular_valores_clase_sensor' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor'>".$this->idiomas->_("Recalcular valores de clase")."</button><br/><br/>";
            $boton_enviar_valores_manuales_sensor = "<br/><button id='boton_enviar_valores_manuales_sensor' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_envio_valores_manuales_sensor'>".$this->idiomas->_("Enviar valores manuales")."</button><br/><br/>";
            $boton_asignar_localizacion = "<br/><button id='boton_asignar_localizacion' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_asignacion_localizacion'>".$this->idiomas->_("Asignar localización")."</button><br/><br/>";
            $boton_asignar_grupo = "<br/><button id='boton_asignar_grupo' class='btn-mini btn btn-success boton_sensores_mostrar_ventana_asignacion_grupo'>".$this->idiomas->_("Asignar grupo")."</button><br/><br/>";

            // Controles de localización visibles
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();

            // Perfil del usuario
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $administracion_sensores = NodoSensor::dame_administracion_sensores();
                    if ($administracion_sensores == true)
                    {
                        $botones = array(
                            $boton_anyadir_comentarios_sensores,
                            $boton_importar_valores_sensor,
                            $boton_exportar_valores_sensor,
                            $boton_borrar_valores_sensor,
                            $boton_recalcular_valores_clase_sensor,
                            $boton_enviar_valores_manuales_sensor);
                        if ($mostrar_controles_localizaciones == true)
                        {
                            array_push($botones, $boton_asignar_localizacion);
                        }
                        array_push($botones, $boton_asignar_grupo);
                    }
                    else
                    {
                        $botones = array();
                        $administracion_comentarios_sensores = NodoSensor::dame_administracion_comentarios_sensores();
                        $exportacion_sensores = NodoSensor::dame_exportacion_sensores();
                        $envio_valores_manuales_sensores = NodoSensor::dame_envio_valores_manuales_sensores();
                        if ($administracion_comentarios_sensores == true)
                        {
                            array_push($botones, $boton_anyadir_comentarios_sensores);
                        }
                        if ($exportacion_sensores == true)
                        {
                            array_push($botones, $boton_exportar_valores_sensor);
                        }
                        if ($envio_valores_manuales_sensores == true)
                        {
                            array_push($botones, $boton_enviar_valores_manuales_sensor);
                        }
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $botones = array(
                        $boton_anyadir_comentarios_sensores,
                        $boton_recargar_configuraciones_sensores,
                        $boton_importar_valores_sensor,
                        $boton_exportar_valores_sensor,
                        $boton_borrar_valores_sensor,
                        $boton_recalcular_valores_clase_sensor,
                        $boton_enviar_valores_manuales_sensor);
                    if ($mostrar_controles_localizaciones == true)
                    {
                        array_push($botones, $boton_asignar_localizacion);
                    }
                    array_push($botones, $boton_asignar_grupo);
                    break;
                }
                default:
                {
                    throw new Exception("Perfil de usuario desconocido: '".$_SESSION["perfil"]."'");
                }
            }

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-herramientas-sensores",
                $this->idiomas->_("Herramientas de sensores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_SENSORES
            );
			$tabla->anyade_fila("botones-herramientas-sensores", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_principal()
        {
            $contenido = "";
			$contenido .= $this->dame_sensores_grupos_operaciones_datos();

			return ($contenido);
		}


        function dame_sensores_grupos_operaciones_datos()
		{
            $administracion_sensores = NodoSensor::dame_administracion_sensores();

            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-principal-sensores'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-sensores'>".$this->idiomas->_("Sensores")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-grupos-sensores'>".$this->idiomas->_("Grupos")."</a></li>";
            if ($administracion_sensores == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-operaciones-datos-sensores-sensores'>".$this->idiomas->_("Operaciones de datos")."</a></li>";
            }
            $contenido .= "
                    </ul>
                    <div id='tabs-sensores-grupos-sensores' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active pestanya-principal-sensores' id='tab-sensores'>";
            $contenido .= $this->dame_tabla_filtro_sensores_tabla();
            $contenido .= "
                            <div id='tabla".TIPO_NODO_SENSOR."'>".
                                dame_tabla_nodos(TIPO_NODO_SENSOR)."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane pestanya-principal-sensores' id='tab-grupos-sensores'>";
            $contenido .= $this->dame_tabla_filtro_grupos_tabla();
            $contenido .= "
                            <div id='tabla".TIPO_NODO_GRUPO_SENSORES."'>".
                                dame_tabla_nodos(TIPO_NODO_GRUPO_SENSORES)."
                            </div>
                        </div>";

            if ($administracion_sensores == true)
            {
                $contenido .= "
                        <div class='tab-pane pestanya-principal-sensores' id='tab-operaciones-datos-sensores-sensores'>";

                $contenido .= "
                    <span class='boton-contenido-seccion'>
                        <button class='btn-mini btn btn-success boton_actualizar_tabla_operaciones_datos_sensores'>
                            <i class='icon-refresh color-blanco'></i>
                        </button>
                    </span>";
                $contenido .= dame_tabla_operaciones_datos_sensores_procesado(MODULO_SENSORES);
                $contenido .= dame_operaciones_datos_sensores_pendientes_procesado(MODULO_SENSORES);
                $contenido .= "
                        </div>";
            }

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_eventos()
		{
            $contenido = $this->dame_eventos_historico_informes();
			return ($contenido);
		}


        function dame_eventos_historico_informes()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-eventos-sensores'>".$this->idiomas->_("Eventos")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-historico-eventos-sensores'>".$this->idiomas->_("Histórico")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-activaciones-eventos-sensores'>".$this->idiomas->_("Activaciones")."</a></li>
                    </ul>
                    <div id='tabs-eventos-historico-activaciones-sensores' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-eventos-sensores'>";
            $contenido .= $this->dame_tabla_filtro_eventos_tabla();
            $contenido .= "
                            <div id='tablaEventos'>".
                                Evento::dame_tabla_eventos(
                                    "",
                                    CLASE_TODAS,
                                    ALARMA_EVENTO_TODOS,
                                    ACTIVACION_EVENTO_TODOS,
                                    false)."
                            </div>";
            $contenido .= "
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-historico-eventos-sensores'>";
            $contenido .= $this->dame_tabla_filtro_historico_eventos();
            $contenido .= $this->dame_tabla_historico_eventos();
            $contenido .= "
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-activaciones-eventos-sensores'>";
            $contenido .= $this->dame_activaciones_eventos();
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_tabla_historico_eventos()
		{
            $limite_elementos_tabla_superado = false;
            $contenido = "<div id='tablaHistoricoEventos'>".
                HistoricoEvento::dame_tabla_historico_eventos(
                    "",
                    CLASE_TODAS,
                    NULL,
                    NULL,
                    TIPO_FECHA_HISTORICO_EVENTOS_VALORES,
                    $limite_elementos_tabla_superado).
                "</div>";
			return ($contenido);
		}


        function dame_activaciones_eventos()
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_activaciones_eventos";

			$controles_filtro_eventos_activaciones_eventos = dame_controles_filtro_eventos_activaciones_eventos($sufijo_controles);
            $control_lista_doble_eventos = "<div id='lista_doble_eventos_sensores_activaciones_eventos'>";
            $control_lista_doble_eventos .= dame_control_lista_doble_eventos(
                $sufijo_controles,
                CLASE_NINGUNA,
                ORIGEN_EVENTO_TODOS,
                ID_TODOS,
                GRANULARIDAD_TODAS,
                MAX_EVENTOS_SELECCIONADOS_LISTA_EVENTOS_ACTIVACIONES_EVENTOS);
            $control_lista_doble_eventos .= "</div>";

            $control_lista_campos_sensor = dame_control_lista_campos_sensor_activaciones_eventos(
                $sufijo_controles,
                CLASE_NINGUNA,
                ID_NINGUNO,
                GRANULARIDAD_TODAS,
                CAMPO_NINGUNO);
            $opciones = array($control_lista_campos_sensor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $numero_informes_automaticos = dame_numero_informes_automaticos();
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_ACTIVACIONES_EVENTOS),
                $opciones,
                $botones_extra);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-activaciones-eventos",
                $this->idiomas->_("Informe de activaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Filtro de eventos")));
            $params_contenido_filtro_eventos = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_FILTRO_EVENTOS_ACTIVACIONES_EVENTOS)
            );
            $tabla->anyade_fila("", $controles_filtro_eventos_activaciones_eventos, $params_contenido_filtro_eventos);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Eventos")));
            $params_contenido_eventos = array(
                "clase_contenido" => "lista-eventos"
            );
            $tabla->anyade_contenido("", $control_lista_doble_eventos, $params_contenido_eventos);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ACTIVACIONES_EVENTOS),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_ACTIVACIONES_EVENTOS
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_activaciones_eventos(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion()
		{
            // Se recuperan las clases de sensor del usuario actual
            $clases_sensor = dame_clases_sensor_usuario_actual(true);
            if (count($clases_sensor) == 0)
            {
                // Sección vacía con mensaje de información
                $tabla = new TablaDatos(
                    "tabla-informacion-sensores",
                    $this->idiomas->_("Información"),
                    TIPO_TABLA_DATOS_CONTENEDOR
                );

                $mensaje_aviso = "
                    <div class='mensaje-seccion-vacia'>
                        <i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("No hay sensores disponibles")."
                    </div>";
                $tabla->anyade_fila("informacion-sensores", array($mensaje_aviso));

                $contenido = "
                    <div class='contenedor-seccion-vacia'>";
                $contenido .= $tabla->dame_tabla();
                $contenido .= "
                    </div>";
                return ($contenido);
            }

            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
				<div id='tabs' class='tabbable'>
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-informacion-sensores'>";

            // Nota: Hay clases que sólo se muestran si la "funcionalidad" está disponible en las tarifas del país correspondientes
            $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_pais_medicion(MEDICION_ELECTRICIDAD);
            $mostrar_informacion_compra_energia = ($caracteristicas_tarifas_electricas["compra_energia"] == true);

            // Se añaden las pestañas de las clases
            $primera_pestanya_informacion = true;
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_TEMPERATURA, $clases_sensor, "temperatura", $this->idiomas->_("Temperatura"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_HUMEDAD, $clases_sensor, "humedad", $this->idiomas->_("Humedad"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_LUZ_INTERIOR, $clases_sensor, "luz-interior", $this->idiomas->_("Luz interior"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_VIENTO, $clases_sensor, "viento", $this->idiomas->_("Viento"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_ENERGIA_ACTIVA, $clases_sensor, "energia-activa", $this->idiomas->_("Energía activa"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_ENERGIA_REACTIVA, $clases_sensor, "energia-reactiva", $this->idiomas->_("Energía reactiva"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_CORTES_TENSION, $clases_sensor, "cortes-tension", $this->idiomas->_("Cortes de tensión"));
            if ($mostrar_informacion_compra_energia == true)
            {
                $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_COMPRA_ENERGIA, $clases_sensor, "compra-energia", $this->idiomas->_("Compra de energía"));
            }
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_GAS, $clases_sensor, "gas", $this->idiomas->_("Gas"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_AGUA, $clases_sensor, "agua", $this->idiomas->_("Agua"));
            $this->anyade_pestanya_informacion_clase_sensor($contenido, $primera_pestanya_informacion, CLASE_SENSOR_GENERICA, $clases_sensor, "generica", $this->idiomas->_("Genérica"));

            $contenido .= "
					</ul>
                    <div id='tabs-informacion-sensores' class='tab-content'>";

            // Se añaden los contenidos de las pestañas de las clases
            $primer_contenido_pestanya_informacion = true;
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_TEMPERATURA, $clases_sensor, "temperatura", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_HUMEDAD, $clases_sensor, "humedad", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_LUZ_INTERIOR, $clases_sensor, "luz-interior", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_VIENTO, $clases_sensor, "viento", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_ENERGIA_ACTIVA, $clases_sensor, "energia-activa", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_ENERGIA_REACTIVA, $clases_sensor, "energia-reactiva", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_CORTES_TENSION, $clases_sensor, "cortes-tension", $numero_informes_automaticos);
            if ($mostrar_informacion_compra_energia == true)
            {
                $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_COMPRA_ENERGIA, $clases_sensor, "compra-energia", $numero_informes_automaticos);
            }
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_GAS, $clases_sensor, "gas", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_AGUA, $clases_sensor, "agua", $numero_informes_automaticos);
            $this->anyade_contenido_pestanya_informacion_clase_sensor($contenido, $primer_contenido_pestanya_informacion, CLASE_SENSOR_GENERICA, $clases_sensor, "generica", $numero_informes_automaticos);

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function anyade_pestanya_informacion_clase_sensor(&$contenido, &$primera_pestanya_informacion, $clase_sensor, $clases_sensor, $id_pestanya, $titulo_pestanya)
        {
            if (in_array($clase_sensor, $clases_sensor) == false)
            {
                return;
            }

            if ($primera_pestanya_informacion == true)
            {
                $contenido .= "<li class='active'>";
                $primera_pestanya_informacion = false;
            }
            else
            {
                $contenido .= "<li>";
            }
            $contenido .= "
                <a data-toggle='tab' class='titulo-pestanya' href='#tab-".$id_pestanya."'>".$titulo_pestanya."</a></li>";
        }


        function anyade_contenido_pestanya_informacion_clase_sensor(&$contenido, &$primer_contenido_pestanya_informacion, $clase_sensor, $clases_sensor, $id_pestanya, $numero_informes_automaticos)
        {
            if (in_array($clase_sensor, $clases_sensor) == false)
            {
                return;
            }

            if ($primer_contenido_pestanya_informacion == true)
            {
                $contenido .= "<div class='tab-pane active pestanya-informacion-sensores' ";
                $primer_contenido_pestanya_informacion = false;
            }
            else
            {
                $contenido .= "<div class='tab-pane pestanya-informacion-sensores' ";
            }
            $contenido .= "id='tab-".$id_pestanya."'>";
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_TEMPERATURA:
                {
                    $contenido .= $this->dame_informacion_temperatura($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR:
                {
                    $contenido .= $this->dame_informacion_luz_interior($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_HUMEDAD:
                {
                    $contenido .= $this->dame_informacion_humedad($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_VIENTO:
                {
                    $contenido .= $this->dame_informacion_viento($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $contenido .= $this->dame_informacion_energia_activa($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $contenido .= $this->dame_informacion_energia_reactiva($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $contenido .= $this->dame_informacion_cortes_tension($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA:
                {
                    $contenido .= $this->dame_informacion_compra_energia($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $contenido .= $this->dame_informacion_gas($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $contenido .= $this->dame_informacion_agua($numero_informes_automaticos);
                    break;
                }
                case CLASE_SENSOR_GENERICA:
                {
                    $contenido .= $this->dame_informacion_generica($numero_informes_automaticos);
                    break;
                }
            }
            $contenido .= "
				</div>";
        }


        function dame_informacion_temperatura($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_temperatura";

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_parametros_extra(
                $sufijo_controles,
                CLASE_SENSOR_TEMPERATURA,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_TEMPERATURA,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
			$control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_TEMPERATURA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_temperatura = "<i id='boton_sensores_ayuda_informe_temperatura_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_temperatura);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-temperatura",
                $this->idiomas->_("Información de temperatura"),
               TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_TEMPERATURA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_TEMPERATURA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_TEMPERATURA, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_humedad($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_humedad";

			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_HUMEDAD,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_HUMEDAD,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
			$control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
				$control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico("sensores_informacion_humedad", true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_HUMEDAD),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_humedad = "<i id='boton_sensores_ayuda_informe_humedad_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_humedad);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-humedad",
                $this->idiomas->_("Información de humedad"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_HUMEDAD),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_HUMEDAD
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_HUMEDAD, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_luz_interior($numero_informes_automaticos)
		{
			// Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_luz_interior";

			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_LUZ_INTERIOR,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_LUZ_INTERIOR,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
			$control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
				$control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_LUZ_INTERIOR),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_luz_interior = "<i id='boton_sensores_ayuda_informe_luz_interior_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_luz_interior);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-luz-interior",
                $this->idiomas->_("Información de luz interior"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_LUZ_INTERIOR),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_LUZ_INTERIOR
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_LUZ_INTERIOR, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_viento($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_viento";

			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_VIENTO,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_VIENTO,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
			$control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
				$control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_VIENTO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_viento = "<i id='boton_sensores_ayuda_informe_viento_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_viento);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-viento",
                $this->idiomas->_("Información de viento"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_VIENTO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_VIENTO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_VIENTO, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_energia_activa($numero_informes_automaticos)
		{
            return ($this->dame_informacion_energia(CLASE_SENSOR_ENERGIA_ACTIVA, $numero_informes_automaticos));
        }


        function dame_informacion_energia_reactiva($numero_informes_automaticos)
		{
            return ($this->dame_informacion_energia(CLASE_SENSOR_ENERGIA_REACTIVA, $numero_informes_automaticos));
        }


        function dame_informacion_energia($clase_sensor, $numero_informes_automaticos)
		{
            // Sufijo de controles y títulos de tipo de energía
            $sufijo_tipo_energia = NULL;
            $titulo_informacion_energia = NULL;
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $sufijo_tipo_energia = "activa";
                    $titulo_informacion_energia = $this->idiomas->_("Información de energía activa");
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $sufijo_tipo_energia = "reactiva";
                    $titulo_informacion_energia = $this->idiomas->_("Información de energía reactiva");
                    break;
                }
            }

            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_energia_".$sufijo_tipo_energia;

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_parametros_extra(
                $sufijo_controles,
                $clase_sensor,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                $clase_sensor,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_ENERGIA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_energia = "<i id='boton_sensores_ayuda_informe_energia_".$sufijo_tipo_energia."_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_energia);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-energia-".$sufijo_tipo_energia,
                $titulo_informacion_energia,
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_ENERGIA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_ENERGIA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion($clase_sensor, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_cortes_tension($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_cortes_tension";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_CORTES_TENSION,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_CORTES_TENSION,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_CORTES_TENSION),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_cortes_tension = "<i id='boton_sensores_ayuda_informe_cortes_tension_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_cortes_tension);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-informacion-cortes-tension",
                $this->idiomas->_("Información de cortes de tensión"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_CORTES_TENSION),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_CORTES_TENSION
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_CORTES_TENSION, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_compra_energia($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_compra_energia";

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_tipo_agrupacion_valores_parametros_extra(
                $sufijo_controles,
                CLASE_SENSOR_COMPRA_ENERGIA,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_COMPRA_ENERGIA,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_COMPRA_ENERGIA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_compra_energia = "<i id='boton_sensores_ayuda_informe_compra_energia_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_compra_energia);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-compra-energia",
                $this->idiomas->_("Información de compra de energía"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_COMPRA_ENERGIA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_COMPRA_ENERGIA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_COMPRA_ENERGIA, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_gas($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_gas";

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_tipo_agrupacion_valores_parametros_extra(
                $sufijo_controles,
                CLASE_SENSOR_GAS,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_GAS,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SENSORES_INFORMACION_GAS, MEDICION_GAS);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_GAS),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_gas = "<i id='boton_sensores_ayuda_informe_gas_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_gas);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-gas",
                $this->idiomas->_("Información de gas"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_GAS),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_GAS
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_GAS, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_agua($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_agua";

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_tipo_agrupacion_valores_parametros_extra(
                $sufijo_controles,
                CLASE_SENSOR_AGUA,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_AGUA,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_GAS),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_gas = "<i id='boton_sensores_ayuda_informe_gas_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_gas);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-agua",
                $this->idiomas->_("Información de agua"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_AGUA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_AGUA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_AGUA, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion_generica($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_informacion_generica";

            $controles_listas_sensores_campos = dame_controles_listas_sensores_campos_tipo_agrupacion_valores_parametros_extra(
                $sufijo_controles,
                CLASE_SENSOR_GENERICA,
                true,
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_SENSOR_GENERICA,
                $this->idiomas->_("Intervalo de valores"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_SIN_OPCIONES_EXTRA);
            $control_lista_tipos_mapa_calor = dame_control_lista_tipos_mapa_calor_informacion($sufijo_controles);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INFORMACION_GENERICA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informacion_generica = "<i id='boton_sensores_ayuda_informe_generica_informacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informacion_generica);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-generico",
                $this->idiomas->_("Información de datos genéricos"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_campos, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_GENERICA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_GENERICA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_informacion(CLASE_SENSOR_GENERICA, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_analisis()
        {
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-analisis-horario'>".$this->idiomas->_("Análisis horario")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-analisis-diario'>".$this->idiomas->_("Análisis diario")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-analisis-comportamiento'>".$this->idiomas->_("Análisis de comportamiento")."</a></li>
                    </ul>
                    <div id='tabs-analisis-sensores' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-analisis-horario'>";
            $contenido .= $this->dame_analisis_horario($numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-analisis-diario'>";
            $contenido .= $this->dame_analisis_diario($numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-analisis-comportamiento'>";
            $contenido .= $this->dame_analisis_comportamiento($numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


		function dame_analisis_horario($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_analisis_horario";

            $controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, false, true);

            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_analisis_horario($sufijo_controles);
            $opciones = array(
                $control_lista_tipos_mapa_calor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_ANALISIS_HORARIO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-analisis-horario",
                $this->idiomas->_("Análisis horario"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_HORARIO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_ANALISIS_HORARIO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_analisis_horario(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


		function dame_analisis_diario($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_analisis_diario";

			$controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, false, true);

            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_analisis_diario($sufijo_controles);
            $opciones = array(
                $control_lista_tipos_mapa_calor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_ANALISIS_DIARIO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-analisis-diario",
                $this->idiomas->_("Análisis diario"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_DIARIO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_ANALISIS_DIARIO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_analisis_diario(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


		function dame_analisis_comportamiento($numero_informes_automaticos)
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_analisis_comportamiento";

			$controles_listas_clases_sensor_campos = dame_controles_listas_clases_sensor_campos_parametros_extra($sufijo_controles, false, true);

            $control_lista_doble_sensores = "<div id='lista_doble_sensores_".$sufijo_controles."'>";
            $control_lista_doble_sensores .= dame_control_lista_doble_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPORTAMIENTO,
                $this->idiomas->_("Sensores"));
            $control_lista_doble_sensores .= "</div>";
            $opciones = array();
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_ANALISIS_COMPORTAMIENTO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-analisis-comportamiento",
                $this->idiomas->_("Análisis de comportamiento"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_campos, $params_contenido_campo);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_COMPORTAMIENTO),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_ANALISIS_COMPORTAMIENTO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_analisis_comportamiento(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_comparacion()
		{
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
				<div id='tabs' class='tabbable'>";
			$contenido .= "
					<ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-comparacion-sensores'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-comparacion-periodos'>".$this->idiomas->_("Comparación de periodos")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-comparacion-perfil-horario'>".$this->idiomas->_("Comparación con perfil horario")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-comparacion-campos-iguales'>".$this->idiomas->_("Comparación de campos iguales")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-comparacion-campos-diferentes'>".$this->idiomas->_("Comparación de campos diferentes")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-analisis-comparativo'>".$this->idiomas->_("Análisis comparativo")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-valores-generales'>".$this->idiomas->_("Valores generales")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-incrementos-totales'>".$this->idiomas->_("Incrementos totales")."</a></li>
					</ul>
					<div id='tabs-comparacion-sensores' class='tab-content'>";

            $contenido .= "
						<div class='tab-pane active pestanya-comparacion-sensores' id='tab-comparacion-periodos'>";
            $contenido .= $this->dame_comparacion_periodos($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-comparacion-sensores' id='tab-comparacion-perfil-horario'>";
            $contenido .= $this->dame_comparacion_perfil_horario($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-comparacion-sensores' id='tab-comparacion-campos-iguales'>";
            $contenido .= $this->dame_comparacion_campos_iguales($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-comparacion-sensores' id='tab-comparacion-campos-diferentes'>";
            $contenido .= $this->dame_comparacion_campos_diferentes($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-analisis-comparativo' id='tab-analisis-comparativo'>";
            $contenido .= $this->dame_analisis_comparativo($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-comparacion-sensores' id='tab-valores-generales'>";
            $contenido .= $this->dame_valores_generales($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane pestanya-comparacion-sensores' id='tab-incrementos-totales'>";
            $contenido .= $this->dame_incrementos_totales($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_comparacion_periodos($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_comparacion_periodos";

			$controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, true, true);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_comparacion($sufijo_controles);
            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_comparacion_periodos($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$periodos = dame_filtro_periodos_informe($sufijo_controles,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_COMPARACION_PERIODOS),
                modifica_dias_duracion_periodos_defecto_informe(
                    PERIODO_DEFECTO_SENSORES_COMPARACION_PERIODOS,
                    DIAS_DURACION_DEFECTO_SENSORES_PERIODO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-comparacion-periodos",
                $this->idiomas->_("Comparación de periodos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_VALORES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_VALORES
            );
			$tabla->anyade_fila("periodos-sensores", $periodos, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_comparacion_periodos(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_comparacion_perfil_horario($numero_informes_automaticos)
		{
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_comparacion_perfil_horario";

			$controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, false, true);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_comparacion_perfil_horario($sufijo_controles);
            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_comparacion_perfil_horario($sufijo_controles);
            $control_fecha_inicio_perfil_horario = dame_control_fecha_inicio(
                "perfil_horario_".$sufijo_controles,
                $idiomas->_("Inicio de perfil horario"),
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_COMPARACION_PERFIL_HORARIO));
            $control_fecha_fin_perfil_horario = dame_control_fecha_fin(
                "perfil_horario_".$sufijo_controles,
                $idiomas->_("Fin de perfil horario"),
                NULL,
                "");
            $control_lista_tipos_perfil_horario = $this->dame_control_lista_tipos_perfil_horario_comparacion_perfil_horario($sufijo_controles);
            $control_agrupaciones_dias_semana_perfil_horario = dame_entrada_cadena(
                "agrupaciones_dias_semana_".$sufijo_controles,
                $this->idiomas->_("Agrupaciones de días de la semana"),
                "",
                TAMANYO_CONTROL_GRANDE,
                true);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor,
                $control_fecha_inicio_perfil_horario,
                $control_fecha_fin_perfil_horario,
                $control_lista_tipos_perfil_horario,
                $control_agrupaciones_dias_semana_perfil_horario);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_COMPARACION_PERFIL_HORARIO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-comparacion-perfil-horario",
                $this->idiomas->_("Comparación con perfil horario"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERFIL_HORARIO),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_COMPARACION_PERFIL_HORARIO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_comparacion_perfil_horario(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_comparacion_campos_iguales($numero_informes_automaticos)
		{
			// Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_comparacion_campos_iguales";

			$controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, true, true);
            $control_lista_doble_sensores = "<div id='lista_doble_sensores_".$sufijo_controles."'>";
            $control_lista_doble_sensores .= dame_control_lista_doble_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_COMPARACION_CAMPOS_IGUALES,
                $this->idiomas->_("Sensores"));
            $control_lista_doble_sensores .= "</div>";

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_comparacion($sufijo_controles);
            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_comparacion_campos_iguales($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_COMPARACION_CAMPOS_IGUALES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-comparacion-campos-iguales",
                $this->idiomas->_("Comparación de campos iguales"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor principal y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores secundarios")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_VALORES_IGUALES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_COMPARACION_VALORES_IGUALES
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_comparacion_campos_iguales(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_comparacion_campos_diferentes($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_comparacion_campos_diferentes";

			$controles_listas_clases_sensor_sensores_campos_1 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("1_".$sufijo_controles, true, true);
            $controles_listas_clases_sensor_sensores_campos_2 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("2_".$sufijo_controles, true, false);
            $controles_listas_clases_sensor_sensores_campos_3 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("3_".$sufijo_controles, true, false);
            $controles_listas_clases_sensor_sensores_campos_4 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("4_".$sufijo_controles, true, false);
            $controles_listas_clases_sensor_sensores_campos_5 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("5_".$sufijo_controles, true, false);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_comparacion_campos_diferentes($sufijo_controles);
            $control_lista_unificar_escalas = $this->dame_control_lista_unificar_escalas_comparacion_campos_diferentes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_unificar_escalas);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_COMPARACION_CAMPOS_DIFERENTES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-comparacion-campos-diferentes",
                $this->idiomas->_("Comparación de campos diferentes"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores y campos")));
            $params_contenido_campo_1 = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_campo_2_3_4 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_campo_5 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_1, $params_contenido_campo_1);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_2, $params_contenido_campo_2_3_4);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_3, $params_contenido_campo_2_3_4);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_4, $params_contenido_campo_2_3_4);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_5, $params_contenido_campo_5);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_VALORES_DIFERENTES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_COMPARACION_VALORES_DIFERENTES
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_comparacion_campos_diferentes(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_analisis_comparativo($numero_informes_automaticos)
		{
			// Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_analisis_comparativo";

            $controles_lista_clases_sensor_campos = dame_controles_listas_clases_sensor_campos_parametros_extra($sufijo_controles, false, true);
            $control_lista_doble_sensores = "<div id='lista_doble_sensores_'".$sufijo_controles.">";
            $control_lista_doble_sensores .= dame_control_lista_doble_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_ANALISIS_COMPARATIVO,
                $this->idiomas->_("Sensores agregados"));
            $control_lista_doble_sensores .= "</div>";
            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                true,
                $this->idiomas->_("Sensor destacado"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_comparacion($sufijo_controles);
            $control_lista_tipos_mapa_calor = $this->dame_control_lista_tipos_mapa_calor_comparacion_analisis_comparativo($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_tipos_mapa_calor);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_ANALISIS_COMPARATIVO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-analisis-comparativo",
                $this->idiomas->_("Análisis comparativo"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO)
            );
            $tabla->anyade_fila("", $controles_lista_clases_sensor_campos, $params_contenido_campo);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_1 = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSORES),
                "sin_borde_inferior" => true
            );
            $params_contenido_2 = array(
                "clase_dato" => "desplegable-simple-sin-margen-superior",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR),
            );
            $tabla->anyade_fila("", array($control_lista_doble_sensores), $params_contenido_1);
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_2);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ANALISIS_COMPARATIVO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_ANALISIS_COMPARATIVO
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_analisis_comparativo(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_valores_generales($numero_informes_automaticos)
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_valores_generales";

			$controles_lista_clases_sensor_campos_1 = dame_controles_listas_clases_sensor_campos_parametros_extra("1_".$sufijo_controles, true, true);
            $controles_lista_clases_sensor_campos_2 = dame_controles_listas_clases_sensor_campos_parametros_extra("2_".$sufijo_controles, true, false);
            $controles_lista_clases_sensor_campos_3 = dame_controles_listas_clases_sensor_campos_parametros_extra("3_".$sufijo_controles, true, false);

            $control_lista_doble_sensores = "<div id='lista_doble_sensores_".$sufijo_controles."'>";
            $control_lista_doble_sensores .= dame_control_lista_doble_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_VALORES_GENERALES,
                $this->idiomas->_("Sensores"));
            $control_lista_doble_sensores .= "</div>";

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_valores_generales($sufijo_controles);
            $control_lista_agregaciones = dame_control_lista_agregaciones($sufijo_controles, NULL, NULL);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_agregaciones);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_VALORES_GENERALES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-valores-generales",
                $this->idiomas->_("Valores generales"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Campos")));
            $params_contenido_clase_sensor_1 = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_clase_sensor_2 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_clase_sensor_3 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO)
            );
			$tabla->anyade_fila("", $controles_lista_clases_sensor_campos_1, $params_contenido_clase_sensor_1);
            $tabla->anyade_fila("", $controles_lista_clases_sensor_campos_2, $params_contenido_clase_sensor_2);
            $tabla->anyade_fila("", $controles_lista_clases_sensor_campos_3, $params_contenido_clase_sensor_3);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_VALORES_GENERALES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_VALORES_GENERALES
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_valores_generales(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

            return ($tabla->dame_tabla());
        }


        function dame_incrementos_totales($numero_informes_automaticos)
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_incrementos_totales";

            $controles_lista_clases_sensor_campos_1 = dame_controles_listas_clases_sensor_campos_incrementos_parametros_extra("1_".$sufijo_controles, true, true);
            $controles_lista_clases_sensor_campos_2 = dame_controles_listas_clases_sensor_campos_incrementos_parametros_extra("2_".$sufijo_controles, true, false);
            $controles_lista_clases_sensor_campos_3 = dame_controles_listas_clases_sensor_campos_incrementos_parametros_extra("3_".$sufijo_controles, true, false);

            $control_lista_doble_sensores = "<div id='lista_doble_sensores_".$sufijo_controles."'>";
            $control_lista_doble_sensores .= dame_control_lista_doble_sensores(
                $sufijo_controles,
                CLASE_NINGUNA,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_INCREMENTOS_TOTALES,
                $this->idiomas->_("Sensores"));
            $control_lista_doble_sensores .= "</div>";

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_incrementos_totales($sufijo_controles);
            $control_lista_agregaciones = dame_control_lista_agregaciones($sufijo_controles, NULL, NULL);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_agregaciones);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_INCREMENTOS_TOTALES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-incrementos-totales",
                $this->idiomas->_("Incrementos totales"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Campos")));
            $params_contenido_clase_sensor_1 = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_clase_sensor_2 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_clase_sensor_3 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_CAMPO)
            );
			$tabla->anyade_fila("", $controles_lista_clases_sensor_campos_1, $params_contenido_clase_sensor_1);
            $tabla->anyade_fila("", $controles_lista_clases_sensor_campos_2, $params_contenido_clase_sensor_2);
            $tabla->anyade_fila("", $controles_lista_clases_sensor_campos_3, $params_contenido_clase_sensor_3);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INCREMENTOS_TOTALES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INCREMENTOS_TOTALES
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_incrementos_totales(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

            return ($tabla->dame_tabla());
        }


        function dame_estadistica()
		{
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
				<div id='tabs' class='tabbable'>";
			$contenido .= "
					<ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-histograma'>".$this->idiomas->_("Histograma")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-correlacion'>".$this->idiomas->_("Correlación")."</a></li>
					</ul>
					<div id='tabs-estadistica-sensores' class='tab-content'>";

            $contenido .= "
						<div class='tab-pane active' id='tab-histograma'>";
            $contenido .= $this->dame_histograma($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane' id='tab-correlacion'>";
            $contenido .= $this->dame_correlacion($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_histograma($numero_informes_automaticos)
		{
			// Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_histograma";

			$controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, true, true);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_estadistica_histograma($sufijo_controles);
            $control_lista_detalle = $this->dame_control_lista_detalles_estadistica_histograma($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_detalle);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_HISTOGRAMA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-histograma",
                $this->idiomas->_("Histograma"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_campo = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_campo);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_HISTOGRAMA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_HISTOGRAMA
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_histograma(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_correlacion($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "sensores_correlacion";

			$controles_listas_clases_sensor_sensores_campos_independiente_1 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("independiente_1_".$sufijo_controles, false, true);
            $controles_listas_clases_sensor_sensores_campos_independiente_2 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("independiente_2_".$sufijo_controles, false, false);
            $controles_listas_clases_sensor_sensores_campos_independiente_3 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("independiente_3_".$sufijo_controles, false, false);
            $controles_listas_clases_sensor_sensores_campos_independiente_4 = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("independiente_4_".$sufijo_controles, false, false);
            $controles_listas_clases_sensor_sensores_campos_dependiente = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra("dependiente_".$sufijo_controles, false, true);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_estadistica_correlacion($sufijo_controles);
            $control_lista_funciones_correlacion = $this->dame_control_lista_funciones_correlacion($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_funciones_correlacion);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            if ((dame_modulo_disponible_sesion(MODULO_PROYECTOS) == true) && (Proyecto::dame_administracion_proyectos() == true))
            {
                $boton_añadir_linea_base = dame_boton_formulario($sufijo_controles."_mostrar_ventana_anyadir_linea_base", $this->idiomas->_("Añadir línea base"), false);
                array_push($botones_extra, $boton_añadir_linea_base);
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_CORRELACION),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_informe_correlacion = "<i id='boton_sensores_ayuda_informe_correlacion'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_informe_correlacion);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-sensores-correlacion",
                $this->idiomas->_("Correlación"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido_campo_independiente_1 = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_campo_independiente_2_3 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO),
                "sin_borde_inferior" => true
            );
            $params_contenido_campo_independiente_4 = array(
                "clase_dato" => "desplegable-simple-sin-etiqueta",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $params_contenido_campo_dependiente = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CLASE_SENSOR_CAMPO)
            );
            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores independientes y campos")));
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_independiente_1, $params_contenido_campo_independiente_1);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_independiente_2, $params_contenido_campo_independiente_2_3);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_independiente_3, $params_contenido_campo_independiente_2_3);
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_independiente_4, $params_contenido_campo_independiente_4);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor dependiente y campo")));
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos_dependiente, $params_contenido_campo_dependiente);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CORRELACION),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_CORRELACION
            );
			$tabla->anyade_fila("fechas-sensores", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_sensores_correlacion(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_mapa()
		{
			// Control del mapa
			$mapa = "<div class='mapa' id='mapa_seccion'></div>";

            // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => dame_botones_mapa()
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-mapa",
                $this->idiomas->_("Mapa de sensores"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            $contenido = "";
			$contenido .= $this->dame_tabla_filtro_sensores_mapa();
			$contenido .= $tabla->dame_tabla();

			return ($contenido);
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_sensores_tabla()
		{
			// Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "sensores_filtro_sensores_tabla";

            // Tipo, clase, grupo y estado de sensor
            $control_lista_tipos = dame_control_lista_tipos_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_TIPOS_TODOS,
                $this->idiomas->_("Tipo"));
            $control_lista_clases = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase"));
            $control_lista_grupos = dame_control_lista_grupos_nodos($id_controles, TIPO_NODO_SENSOR, $this->idiomas->_("Grupo"));
            $control_lista_estados = dame_control_lista_estados_nodo($id_controles, TIPO_NODO_SENSOR, $this->idiomas->_("Estado"));
            array_push($controles, $control_lista_tipos);
            array_push($controles, $control_lista_clases);
            array_push($controles, $control_lista_grupos);
            array_push($controles, $control_lista_estados);

			$filtro_sensores = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-filtro-sensores-tabla",
                $this->idiomas->_("Filtro de sensores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_SENSORES_TABLA),
            );
			$tabla->anyade_fila("filtro-sensores-tabla", $filtro_sensores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_grupos_tabla()
		{
			// Se recuperan los controles a mostrar
			$filtro_sensores = dame_filtro_texto_clase_nodo("sensores_filtro_grupos_tabla", $this->idiomas->_("Nombre"), TIPO_NODO_SENSOR);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-filtro-grupos-tabla",
                $this->idiomas->_("Filtro de grupos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_GRUPOS_SENSORES_TABLA)
            );
			$tabla->anyade_fila("filtro-grupos-tabla", $filtro_sensores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_sensores_mapa()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "sensores_filtro_sensores_mapa";

            // Tipo, clase, grupo y estado de sensor
            $control_lista_tipos = dame_control_lista_tipos_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_TIPOS_TODOS,
                $this->idiomas->_("Tipo"));
            $control_lista_clases = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase"));
            $control_lista_grupos = dame_control_lista_grupos_nodos($id_controles, TIPO_NODO_SENSOR, $this->idiomas->_("Grupo"));
            $control_lista_estados = dame_control_lista_estados_nodo($id_controles, TIPO_NODO_SENSOR, $this->idiomas->_("Estado"));
            array_push($controles, $control_lista_tipos);
            array_push($controles, $control_lista_clases);
            array_push($controles, $control_lista_grupos);
            array_push($controles, $control_lista_estados);

			$filtro_sensores = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-filtro-sensores-mapa",
                $this->idiomas->_("Filtro de sensores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_SENSORES_MAPA),
            );
			$tabla->anyade_fila("filtro-sensores-mapa", $filtro_sensores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_eventos_tabla()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "sensores_filtro_eventos_tabla";

            // Clase de sensor, alarma y estado de eventos
            $control_lista_clases_sensor = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase de sensor"));
            $control_lista_alarmas = dame_control_lista_alarmas_evento($id_controles, $this->idiomas->_("Alarma"));
            $control_lista_activaciones = dame_control_lista_activaciones_evento($id_controles, $this->idiomas->_("Activado"));
            array_push($controles, $control_lista_clases_sensor);
            array_push($controles, $control_lista_alarmas);
            array_push($controles, $control_lista_activaciones);

			$filtro_eventos = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-filtro-eventos-tabla",
                $this->idiomas->_("Filtro de eventos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_EVENTOS_TABLA)
            );
			$tabla->anyade_fila("filtro-eventos-tabla", $filtro_eventos, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_historico_eventos()
		{
            // Se recuperan los controles a mostrar
			$filtro_historico_eventos = $this->dame_filtro_historico_eventos();

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-sensores-filtro-historico-eventos",
                $this->idiomas->_("Filtro de histórico de eventos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-filtro-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_HISTORICO_EVENTOS),
                "numero_columnas" => NUMEROS_COLUMNAS_FILTRO_HISTORICO_EVENTOS
            );
			$tabla->anyade_fila("filtro-historico-eventos", $filtro_historico_eventos, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_filtro_historico_eventos()
        {
            $idiomas = new Idiomas();

            $id_controles = "sensores_filtro_historico_eventos";
            $filtro = "<div id='etiqueta_filtro_".$id_controles."'>".$this->idiomas->_("Sensor y evento").": "."</div>";
            $filtro .= "<input type='text' class='filtro-texto' id='filtro_".$id_controles."'>";
            $control_lista_clases = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_SENSOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase"));
            $control_fecha_inicio = dame_control_fecha_inicio(
                $id_controles,
                $idiomas->_("Inicio"),
                "00:00",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SENSORES_HISTORICO_EVENTOS));
            $control_fecha_fin = dame_control_fecha_fin(
                $id_controles,
                $idiomas->_("Fin"),
                "23:59",
                "");
            $control_lista_tipos_fecha = $this->dame_control_lista_tipos_fecha_historico_eventos($id_controles);
            $boton = dame_boton_formulario($id_controles, $this->idiomas->_("Filtrar"));

            $controles = array(
                $filtro,
                $control_lista_clases,
                $control_fecha_inicio,
                $control_fecha_fin,
                $control_lista_tipos_fecha,
                $boton
            );
            return ($controles);
        }


        function dame_control_lista_tipos_fecha_historico_eventos($id_controles)
        {
            $control_lista_tipos_fecha = dame_control_lista_valores(
                $id_controles,
                "tipo_fecha",
                $this->idiomas->_("Fecha"),
                array(
                    array(TIPO_FECHA_HISTORICO_EVENTOS_EVENTO, $this->idiomas->_("Evento")),
                    array(TIPO_FECHA_HISTORICO_EVENTOS_VALORES, $this->idiomas->_("Valores"))),
                TIPO_FECHA_HISTORICO_EVENTOS_VALORES,
                "filtro-desplegable");
            return ($control_lista_tipos_fecha);
        }


        function dame_control_lista_tipos_mapa_calor_analisis_horario($id_controles)
        {
            $control_lista_tipos_mapa_calor_analisis_horario = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                    array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_analisis_horario);
        }


        function dame_control_lista_tipos_mapa_calor_analisis_diario($id_controles)
        {
            $control_lista_tipos_mapa_calor_analisis_diario = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                    array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_analisis_diario);
        }


        function dame_control_lista_intervalos_valores_comparacion($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo(
                CLASE_NINGUNA,
                CAMPO_NINGUNO,
                INTERVALO_VALORES_HORA,
                OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_SIN_OPCIONES_EXTRA);
            $control_lista_intervalos_valores_comparacion = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_comparacion);
        }


        function dame_control_lista_intervalos_valores_comparacion_perfil_horario($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_comparacion_perfil_horario(INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_comparacion = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_comparacion);
        }


        function dame_control_lista_intervalos_valores_comparacion_campos_diferentes($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_comparacion_campos_diferentes(INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_comparacion = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_comparacion);
        }


        function dame_control_lista_unificar_escalas_comparacion_campos_diferentes($id_controles)
        {
            $lista_unificar_escalas = dame_lista_valores_si_no(VALOR_SI);
            $control_lista_unificar_escalas_comparacion = dame_control_lista(
                $id_controles,
                "unificar_escalas",
                $this->idiomas->_("Unificar escalas"),
                $lista_unificar_escalas,
                "filtro-desplegable");
            return ($control_lista_unificar_escalas_comparacion);
        }


        function dame_control_lista_intervalos_valores_valores_generales($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo(
                CLASE_NINGUNA,
                CAMPO_NINGUNO,
                INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_valores_generales = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_valores_generales);
        }


        function dame_control_lista_intervalos_valores_incrementos_totales($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo(
                CLASE_NINGUNA,
                CAMPO_NINGUNO,
                INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_incrementos_totales = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_incrementos_totales);
        }


        function dame_control_lista_intervalos_valores_estadistica_histograma($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_histograma_clase_sensor_campo(
                CLASE_NINGUNA,
                CAMPO_NINGUNO,
                INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_estadistica_histograma = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_estadistica_histograma);
        }


        function dame_control_lista_detalles_estadistica_histograma($id_controles)
        {
            $control_lista_detalles_estadistica_histograma = dame_control_lista_valores(
                $id_controles,
                "detalle",
                $this->idiomas->_("Detalle"),
                array(
                    array(DETALLE_MINIMO, $this->idiomas->_("Mínimo")),
                    array(DETALLE_MEDIO, $this->idiomas->_("Medio")),
                    array(DETALLE_MAXIMO, $this->idiomas->_("Máximo"))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_detalles_estadistica_histograma);
        }


        function dame_control_lista_intervalos_valores_estadistica_correlacion($id_controles)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informe_correlacion(INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores_estadistica_correlacion = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores_estadistica_correlacion);
        }


        function dame_control_lista_funciones_correlacion($id_controles)
        {
            // Función de correlación
            $control_lista_funciones_correlacion = dame_control_lista_valores(
                $id_controles,
                "funcion_correlacion",
                $this->idiomas->_("Función de correlación"),
                array(
                    array(FUNCION_CORRELACION_AUTOMATICA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_AUTOMATICA)),
                    array(FUNCION_CORRELACION_LINEAL, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_LINEAL)),
                    array(FUNCION_CORRELACION_POLINOMIO_GRADO_2, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_POLINOMIO_GRADO_2)),
                    array(FUNCION_CORRELACION_LOGARITMICA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_LOGARITMICA)),
                    array(FUNCION_CORRELACION_RAIZ_CUADRADA, dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_RAIZ_CUADRADA))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_funciones_correlacion);
        }


        function dame_control_lista_tipos_mapa_calor_comparacion_periodos($id_controles)
        {
            $control_lista_tipos_mapa_calor_comparacion_periodos = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_NINGUNO, $this->idiomas->_("Ninguno")),
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_comparacion_periodos);
        }


        function dame_control_lista_tipos_mapa_calor_comparacion_perfil_horario($id_controles)
        {
            $control_lista_tipos_mapa_calor_comparacion_periodos = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_NINGUNO, $this->idiomas->_("Ninguno")),
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_comparacion_periodos);
        }


        function dame_control_lista_tipos_mapa_calor_comparacion_campos_iguales($id_controles)
        {
            $control_lista_tipos_mapa_calor_comparacion_campos_iguales = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_NINGUNO, $this->idiomas->_("Ninguno")),
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                    array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_comparacion_campos_iguales);
        }


        function dame_control_lista_tipos_mapa_calor_comparacion_analisis_comparativo($id_controles)
        {
            $control_lista_tipos_mapa_calor_comparacion_analisis_comparativo = dame_control_lista_valores(
                $id_controles,
                "tipo_mapa_calor",
                $this->idiomas->_("Tipo de mapa de calor"),
                array(
                    array(TIPO_MAPA_CALOR_NINGUNO, $this->idiomas->_("Ninguno")),
                    array(TIPO_MAPA_CALOR_DIARIO, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_DIARIO)),
                    array(TIPO_MAPA_CALOR_SEMANAL, dame_descripcion_tipo_mapa_calor(TIPO_MAPA_CALOR_SEMANAL))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_mapa_calor_comparacion_analisis_comparativo);
        }


        function dame_control_lista_tipos_perfil_horario_comparacion_perfil_horario($id_controles)
        {
            $control_lista_tipos_perfil_horario_comparacion_perfil_horario = dame_control_lista_valores(
                $id_controles,
                "tipo_perfil_horario",
                $this->idiomas->_("Tipo de perfil horario"),
                array(
                    array(TIPO_PERFIL_HORARIO_SEMANAL, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_SEMANAL)),
                    array(TIPO_PERFIL_HORARIO_DIARIO, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_DIARIO)),
                    array(TIPO_PERFIL_HORARIO_CONFIGURABLE, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_CONFIGURABLE))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_perfil_horario_comparacion_perfil_horario);
        }
	}
?>
