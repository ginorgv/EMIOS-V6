<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/Programacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/HistoricoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


	class ModuloActuadores extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_ACTUADORES, NOMBRE_MODULO_ACTUADORES);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_ACTUADORES_PRINCIPAL,
                SECCION_ACTUADORES_REGLAS,
                SECCION_ACTUADORES_PROGRAMACIONES,
                SECCION_ACTUADORES_INFORMACION,
                SECCION_ACTUADORES_MAPA
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_ACTUADORES_PRINCIPAL:
                {
                    $descripcion = "Principal";
                    break;
                }
                case SECCION_ACTUADORES_PROGRAMACIONES:
                {
                    $descripcion = "Programaciones";
                    break;
                }
                case SECCION_ACTUADORES_REGLAS:
                {
                    $descripcion = "Reglas";
                    break;
                }
                case SECCION_ACTUADORES_INFORMACION:
                {
                    $descripcion = "Información";
                    break;
                }
                case SECCION_ACTUADORES_MAPA:
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
            $html = "<div id='modulo' name='".MODULO_ACTUADORES."' hidden></div>";

            // Se añade la tabla de selección de localización actual (sin seleccion de ratio)
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $mostrar_seleccion_ratio = false;
                $seleccion_ratio_visible = false;
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
                case SECCION_ACTUADORES_PRINCIPAL:
                {
                    $usuario_administrador = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR);
                    $administracion_actuadores = NodoActuador::dame_administracion_actuadores();
                    $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
                    $mostrar_herramientas_actuadores = ($usuario_administrador == true) ||
                        ($administracion_actuadores == true) ||
                        ($envio_acciones_actuadores == true);
                    if ($mostrar_herramientas_actuadores == true)
                    {
                        $html .= $this->dame_herramientas_principal();
                    }
                    $html .= $this->dame_principal();
                    break;
                }
                case SECCION_ACTUADORES_REGLAS:
                {
                    if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
                    {
                        $html .= $this->dame_herramientas_reglas();
                    }
                    $html .= $this->dame_reglas();
                    break;
                }
                case SECCION_ACTUADORES_PROGRAMACIONES:
                {
                    $html .= $this->dame_programaciones();
                    break;
                }
                case SECCION_ACTUADORES_INFORMACION:
                {
                    $html .= $this->dame_informacion();
                    break;
                }
                case SECCION_ACTUADORES_MAPA:
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
            $boton_anyadir_comentarios_actuadores = "<br/><button id='boton_anyadir_comentarios_actuadores' class='btn-mini btn btn-success boton_mostrar_ventana_anyadir_comentarios' origen_comentarios='".ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES."'>".$this->idiomas->_("Añadir comentarios")."</button><br/><br/>";
            $boton_enviar_accion = "<br/><button id='boton_enviar_accion' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_envio_accion'>".$this->idiomas->_("Enviar acción")."</button><br/><br/>";
			$boton_borrar_acciones_enviadas = "<br/><button id='boton_borrar_acciones_enviadas' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_borrado_acciones_enviadas'>".$this->idiomas->_("Borrar acciones enviadas")."</button><br/><br/>";
            $boton_asignar_localizacion = "<br/><button id='boton_asignar_localizacion' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_asignacion_localizacion'>".$this->idiomas->_("Asignar localización")."</button><br/><br/>";
            $boton_asignar_grupo = "<br/><button id='boton_asignar_grupo' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_asignacion_grupo'>".$this->idiomas->_("Asignar grupo")."</button><br/><br/>";

            // Flag para mostrar la localización
            $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);

            // Perfil del usuario
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $administracion_actuadores = NodoActuador::dame_administracion_actuadores();
                    if ($administracion_actuadores == true)
                    {
                        $botones = array(
                            $boton_anyadir_comentarios_actuadores,
                            $boton_enviar_accion,
                            $boton_borrar_acciones_enviadas);
                        if ($mostrar_localizacion == true)
                        {
                            array_push($botones, $boton_asignar_localizacion);
                        }
                        array_push($botones, $boton_asignar_grupo);
                    }
                    else
                    {
                        $botones = array();
                        $administracion_comentarios_actuadores = NodoActuador::dame_administracion_comentarios_actuadores();
                        $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
                        if ($administracion_comentarios_actuadores == true)
                        {
                            array_push($botones, $boton_anyadir_comentarios_actuadores);
                        }
                        if ($envio_acciones_actuadores == true)
                        {
                            array_push($botones, $boton_enviar_accion);
                        }
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $botones = array(
                        $boton_anyadir_comentarios_actuadores,
                        $boton_enviar_accion,
                        $boton_borrar_acciones_enviadas);
                    if ($mostrar_localizacion == true)
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
                "tabla-actuadores-herramientas-actuadores",
                $this->idiomas->_("Herramientas de actuadores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_ACTUADORES
            );
			$tabla->anyade_fila("botones-herramientas-actuadores", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_principal()
		{
			$contenido = "";
			$contenido .= $this->dame_actuadores_grupos();

			return ($contenido);
		}


        function dame_actuadores_grupos()
		{
			$contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-principal-actuadores'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-actuadores'>".$this->idiomas->_("Actuadores")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-grupos-actuadores'>".$this->idiomas->_("Grupos")."</a></li>
                    </ul>
                    <div id='tabs-actuadores-grupos' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-actuadores'>";
            $contenido .= $this->dame_tabla_filtro_actuadores_tabla();
            $contenido .= "
                            <div id='tabla".TIPO_NODO_ACTUADOR."'>".
                                dame_tabla_nodos(TIPO_NODO_ACTUADOR)."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-grupos-actuadores'>";
            $contenido .= $this->dame_tabla_filtro_grupos_tabla();
            $contenido .= "
                            <div id='tabla".TIPO_NODO_GRUPO_ACTUADORES."'>".
                                dame_tabla_nodos(TIPO_NODO_GRUPO_ACTUADORES)."
                            </div>
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


        function dame_herramientas_reglas()
		{
			// Se recuperan los controles a mostrar
			$boton_recargar_configuraciones_reglas = "<br/><button id='boton_recargar_configuraciones_reglas' class='btn-mini btn btn-success boton_actuadores_envia_accion_herramientas_reglas'>".$this->idiomas->_("Recargar configuraciones")."</button><br/><br/>";
            $botones = array(
                $boton_recargar_configuraciones_reglas);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-herramientas-reglas",
                $this->idiomas->_("Herramientas de reglas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_REGLAS
            );
			$tabla->anyade_fila("botones-herramientas-reglas", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


		function dame_reglas()
		{
            $contenido = $this->dame_reglas_historico();
			return ($contenido);
		}


        function dame_reglas_historico()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-reglas-actuadores'>".$this->idiomas->_("Reglas")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-historico-reglas-actuadores'>".$this->idiomas->_("Histórico")."</a></li>
                    </ul>
                    <div id='tabs-reglas-historico-actuadores' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-reglas-actuadores'>";
            $contenido .= $this->dame_tabla_filtro_reglas_tabla();
            $contenido .= "
                            <div id='tablaReglas'>".
                                Regla::dame_tabla_reglas(
                                    "",
                                    HABILITACION_REGLA_TODAS,
                                    ACTIVACION_REGLA_TODAS,
                                    false)."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-historico-reglas-actuadores'>";
            $contenido .= $this->dame_tabla_filtro_historico_reglas();
            $contenido .= $this->dame_tabla_historico_reglas();
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_tabla_historico_reglas()
		{
            $contenido = "<div id='tablaHistoricoReglas'>".HistoricoRegla::dame_tabla_historico_reglas("", NULL, NULL)."</div>";
			return ($contenido);
		}


        function dame_programaciones()
		{
            $contenido .= "
                <div id='datos-programaciones-actuadores'>";
            $contenido .= $this->dame_tabla_filtro_programaciones_tabla();
            $contenido .= "
                    <div id='tablaProgramaciones'>".
                        Programacion::dame_tabla_programaciones("", CLASE_TODAS)."
                    </div>
                </div>";

			return ($contenido);
		}


        function dame_informacion()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-acciones-enviadas'>".$this->idiomas->_("Acciones enviadas")."</a></li>
                    </ul>
                    <div id='tabs-informacion-actuadores' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-acciones-enviadas'>";
            $contenido .= $this->dame_informacion_acciones_enviadas();
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_informacion_acciones_enviadas()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "actuadores_informacion_acciones_enviadas";

			$controles_filtro_acciones_acciones_enviadas = dame_controles_filtro_acciones_acciones_enviadas($sufijo_controles);
            $controles_listas_clases_sensor_sensores_campos = dame_controles_listas_clases_sensor_sensores_campos_parametros_extra($sufijo_controles, true, true);

            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_informacion(
                $sufijo_controles,
                CLASE_NINGUNA,
                $this->idiomas->_("Intervalo de sensor"),
                OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO);
			$control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_comentarios);
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
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-informacion-acciones-enviadas",
                $this->idiomas->_("Información de acciones enviadas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Filtro de acciones")));
            $params_contenido_actuador = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_FILTRO_ACCIONES_ACCIONES_ENVIADAS)
            );
            $tabla->anyade_fila("", $controles_filtro_acciones_acciones_enviadas, $params_contenido_actuador);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y campo")));
            $params_contenido_sensor = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_ACCIONES_ENVIADAS)
            );
            $tabla->anyade_fila("", $controles_listas_clases_sensor_sensores_campos, $params_contenido_sensor);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ACCIONES_ENVIADAS),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_ACCIONES_ENVIADAS
            );
			$tabla->anyade_fila("fechas-actuadores", $fechas, $params_fila);

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
            $informe = dame_html_informe_tipo_actuadores_informacion_acciones_enviadas(TIPO_INFORME_WEB_EMIOS);
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
                "tabla-actuadores-mapa",
                $this->idiomas->_("Mapa de actuadores"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            $contenido = "";
			$contenido .= $this->dame_tabla_filtro_actuadores_mapa();
			$contenido .= $tabla->dame_tabla();

			return ($contenido);
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_actuadores_tabla()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "actuadores_filtro_actuadores_tabla";

            // Tipo, clase, grupo y estado de sensor
            $control_lista_tipos = dame_control_lista_tipos_nodo(
                $id_controles,
                TIPO_NODO_ACTUADOR,
                OPCIONES_EXTRA_LISTA_TIPOS_TODOS,
                $this->idiomas->_("Tipo"));
            $control_lista_clases = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_ACTUADOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase"));
            $control_lista_grupos = dame_control_lista_grupos_nodos($id_controles, TIPO_NODO_ACTUADOR, $this->idiomas->_("Grupo"));
            $control_lista_estados = dame_control_lista_estados_nodo($id_controles, TIPO_NODO_ACTUADOR, $this->idiomas->_("Estado"));
            array_push($controles, $control_lista_tipos);
            array_push($controles, $control_lista_clases);
            array_push($controles, $control_lista_grupos);
            array_push($controles, $control_lista_estados);

			$filtro_sensores = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-actuadores-tabla",
                $this->idiomas->_("Filtro de actuadores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ACTUADORES_TABLA),
            );
			$tabla->anyade_fila("filtro-actuadores-tabla", $filtro_sensores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_grupos_tabla()
		{
            // Se recuperan los controles a mostrar
			$filtro_actuadores = dame_filtro_texto_clase_nodo("actuadores_filtro_grupos_tabla", $this->idiomas->_("Nombre"), TIPO_NODO_ACTUADOR);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-grupos-tabla",
                $this->idiomas->_("Filtro de grupos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_GRUPOS_ACTUADORES_TABLA)
            );
			$tabla->anyade_fila("filtro-actuadores-tabla", $filtro_actuadores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_actuadores_mapa()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "actuadores_filtro_actuadores_mapa";

            // Tipo, clase, grupo y estado de sensor
            $control_lista_tipos = dame_control_lista_tipos_nodo(
                $id_controles,
                TIPO_NODO_ACTUADOR,
                OPCIONES_EXTRA_LISTA_TIPOS_TODOS,
                $this->idiomas->_("Tipo"));
            $control_lista_clases = dame_control_lista_clases_nodo(
                $id_controles,
                TIPO_NODO_ACTUADOR,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS,
                $this->idiomas->_("Clase"));
            $control_lista_grupos = dame_control_lista_grupos_nodos($id_controles, TIPO_NODO_ACTUADOR, $this->idiomas->_("Grupo"));
            $control_lista_estados = dame_control_lista_estados_nodo($id_controles, TIPO_NODO_ACTUADOR, $this->idiomas->_("Estado"));
            array_push($controles, $control_lista_tipos);
            array_push($controles, $control_lista_clases);
            array_push($controles, $control_lista_grupos);
            array_push($controles, $control_lista_estados);

			$filtro_sensores = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-actuadores-mapa",
                $this->idiomas->_("Filtro de actuadores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ACTUADORES_MAPA),
            );
			$tabla->anyade_fila("filtro-actuadores-mapa", $filtro_sensores, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_reglas_tabla()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "actuadores_filtro_reglas_tabla";

            // Habilitación y estado de reglas
            $control_lista_habilitaciones = dame_control_lista_habilitaciones_regla($id_controles, $this->idiomas->_("Habilitada"));
            $control_lista_activaciones = dame_control_lista_activaciones_regla($id_controles, $this->idiomas->_("Activada"));
            array_push($controles, $control_lista_habilitaciones);
            array_push($controles, $control_lista_activaciones);

			$filtro_reglas = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-reglas-tabla",
                $this->idiomas->_("Filtro de reglas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_REGLAS_TABLA)
            );
			$tabla->anyade_fila("filtro-reglas-tabla", $filtro_reglas, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_historico_reglas()
		{
            // Se recuperan los controles a mostrar
			$filtro_texto = dame_filtro_texto_fechas(
                "actuadores_filtro_historico_reglas",
                "00:00",
                "23:59",
                $this->idiomas->_("Regla"),
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_ACTUADORES_HISTORICO_REGLAS),
                "");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-historico-reglas",
                $this->idiomas->_("Filtro de histórico de reglas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_HISTORICO_REGLAS)
            );
			$tabla->anyade_fila("filtro-historico-reglas", $filtro_texto, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_programaciones_tabla()
		{
            // Se recuperan los controles a mostrar
			$filtro_programaciones = dame_filtro_texto_clase_nodo("actuadores_filtro_programaciones_tabla", $this->idiomas->_("Nombre"), TIPO_NODO_ACTUADOR);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-actuadores-filtro-programaciones-tabla",
                $this->idiomas->_("Filtro de programaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_PROGRAMACIONES_TABLA)
            );
			$tabla->anyade_fila("filtro-programaciones-tabla", $filtro_programaciones, $params_fila);

			return ($tabla->dame_tabla());
		}
	}
?>
