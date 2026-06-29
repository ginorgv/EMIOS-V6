<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_informes_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


	class ModuloProyectos extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_PROYECTOS, NOMBRE_MODULO_PROYECTOS);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_PROYECTOS_PRINCIPAL,
                SECCION_PROYECTOS_LINEAS_BASE,
                SECCION_PROYECTOS_INFORMACION
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_PROYECTOS_PRINCIPAL:
                {
                    $descripcion = "Principal";
                    break;
                }
                case SECCION_PROYECTOS_LINEAS_BASE:
                {
                    $descripcion = "Líneas base";
                    break;
                }
                case SECCION_PROYECTOS_INFORMACION:
                {
                    $descripcion = "Información";
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
            $html = "<div id='modulo' name='".MODULO_PROYECTOS."' hidden></div>";

            // Se añade la tabla de selección de localización actual y ratio
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
                case SECCION_PROYECTOS_PRINCIPAL:
                {
                    $html .= $this->dame_principal();
                    break;
                }
                case SECCION_PROYECTOS_LINEAS_BASE:
                {
                    $html .= $this->dame_lineas_base();
                    break;
                }
                case SECCION_PROYECTOS_INFORMACION:
                {
                    $html .= $this->dame_informacion();
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


        function dame_principal()
        {
            $contenido = "";
            $contenido .= $this->dame_tabla_filtro_proyectos_tabla();
			$contenido .= $this->dame_proyectos();

			return ($contenido);
		}


        function dame_proyectos()
		{
            $contenido = "
                <div id='tablaProyectos'>".
                    Proyecto::dame_tabla_proyectos("", INTERVALO_VALORES_TODOS, ESTADO_AVANCE_PROYECTO_TODOS, ESTADO_PROYECTO_TODOS)."
                </div>";
			return ($contenido);
        }


        function dame_lineas_base()
		{
            $contenido = $this->dame_lineas_base_informes();
			return ($contenido);
		}


        function dame_lineas_base_informes()
		{
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-lineas-base-proyectos'>".$this->idiomas->_("Líneas base")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-linea-base-proyectos'>".$this->idiomas->_("Simulador")."</a></li>
                    </ul>
                    <div id='tabs-lineas-base-proyectos' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-lineas-base-proyectos'>";
            $contenido .= $this->dame_tabla_filtro_lineas_base_tabla();
            $contenido .= "
                            <div id='tablaLineasBase'>".
                                LineaBase::dame_tabla_lineas_base("", TIPO_TODOS, INTERVALO_VALORES_TODOS)."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-simulador-linea-base-proyectos'>";
            $contenido .= $this->dame_simulador_linea_base($numero_informes_automaticos);
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_simulador_linea_base($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "proyectos_simulador_linea_base";

            $control_lista_lineas_base = dame_control_lista_lineas_base($sufijo_controles);

            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array($control_lista_comentarios);
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
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_PROYECTOS_SIMULADOR_LINEA_BASE),
                $opciones,
                $botones_extra);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-proyectos-simulador-linea-base",
                $this->idiomas->_("Simulador de línea base"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                array()
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Línea base")));
            $params_contenido_lineas_base = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_LINEA_BASE)
            );
            $tabla->anyade_fila("", array($control_lista_lineas_base), $params_contenido_lineas_base);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_LINEA_BASE),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_LINEA_BASE
            );
			$tabla->anyade_fila("fechas-proyectos", $fechas, $params_fila);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_proyectos_simulador_linea_base(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_informacion()
		{
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
				<div id='tabs' class='tabbable'>";
			$contenido .= "
					<ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-informacion-proyecto'>".$this->idiomas->_("Proyecto")."</a></li>
					</ul>
					<div id='tabs-informacion-proyectos' class='tab-content'>";

            $contenido .= "
						<div class='tab-pane active' id='tab-proyecto'>";
            $contenido .= $this->dame_informacion_proyecto($numero_informes_automaticos);
            $contenido .= "
						</div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_informacion_proyecto($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "proyectos_informacion_proyecto";

            $control_lista_proyectos = dame_control_lista_proyectos($sufijo_controles);

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
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_PROYECTOS_INFORMACION_PROYECTO),
                array(),
                $botones_extra);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-proyectos-informacion-proyecto",
                $this->idiomas->_("Información de proyecto"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                array()
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Proyecto")));
            $params_contenido_proyectos = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_PROYECTO)
            );
            $tabla->anyade_fila("", array($control_lista_proyectos), $params_contenido_proyectos);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORMACION_PROYECTO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_INFORMACION_PROYECTO
            );
			$tabla->anyade_fila("fecha-proyectos", $fechas, $params_fila);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_proyectos_informacion_proyecto(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        // Devuelve la tabla que contiene el filtro para la tabla de proyectos
        function dame_tabla_filtro_proyectos_tabla()
        {
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "proyectos_filtro_proyectos_tabla";

            // Intervalo de valores, avance y estado
            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_proyecto($id_controles, OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_TODOS);
            $control_lista_estados_avance = dame_control_lista_estados_avance_proyecto($id_controles, OPCIONES_EXTRA_LISTA_ESTADOS_AVANCE_PROYECTO_TODOS);
            $control_lista_estados = dame_control_lista_estados_proyecto($id_controles, OPCIONES_EXTRA_LISTA_ESTADOS_PROYECTO_TODOS);
            array_push($controles, $control_lista_intervalos_valores);
            array_push($controles, $control_lista_estados_avance);
            array_push($controles, $control_lista_estados);

            $filtro_lineas_base = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

			// Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-proyectos-filtro-proyectos-tabla",
                $this->idiomas->_("Filtro de proyectos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_PROYECTOS_TABLA)
            );
            $tabla->anyade_fila("filtro-proyectos-tabla", $filtro_lineas_base, $params_fila);

            return ($tabla->dame_tabla());
        }


        // Devuelve la tabla que contiene el filtro para la tabla de líneas base
        function dame_tabla_filtro_lineas_base_tabla()
        {
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "proyectos_filtro_lineas_base_tabla";

            // Tipo e intervalo de valores
            $control_lista_tipos = dame_control_lista_tipos_linea_base($id_controles, OPCIONES_EXTRA_LISTA_TIPOS_TODOS);
            $control_lista_intervalos_valores = dame_control_lista_intervalos_valores_linea_base($id_controles, TIPO_LINEA_BASE_FUNCIONAL, OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_TODOS);
            array_push($controles, $control_lista_tipos);
            array_push($controles, $control_lista_intervalos_valores);

            $filtro_lineas_base = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

			// Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-proyectos-filtro-lineas-base-tabla",
                $this->idiomas->_("Filtro de líneas base"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_LINEAS_BASE_TABLA)
            );
            $tabla->anyade_fila("filtro-lineas-base-tabla", $filtro_lineas_base, $params_fila);

            return ($tabla->dame_tabla());
        }
	}
?>
