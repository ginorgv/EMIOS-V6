<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/InformeAutomatico.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    class ModuloPersonal extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_PERSONAL, NOMBRE_MODULO_PERSONAL);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_PERSONAL_PLANTILLAS_INFORMES,
                SECCION_PERSONAL_INFORMES_AUTOMATICOS,
                SECCION_PERSONAL_WIDGETS
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_PERSONAL_PLANTILLAS_INFORMES:
                {
                    $descripcion = "Plantillas de informes";
                    break;
                }
                case SECCION_PERSONAL_INFORMES_AUTOMATICOS:
                {
                    $descripcion = "Informes automáticos";
                    break;
                }
                case SECCION_PERSONAL_WIDGETS:
                {
                    $descripcion = "Widgets";
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
            $html = "<div id='modulo' name='".MODULO_PERSONAL."' hidden></div>";

            // Se añade la tabla de selección de localización actual (sin seleccion de ratio)
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $mostrar_seleccion_ratio = false;
                $seleccion_ratio_visible = false;
                $contenido_oculto = true;
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
                case SECCION_PERSONAL_PLANTILLAS_INFORMES:
                {
                    $html .= $this->dame_plantillas_informes();
                    break;
                }
                case SECCION_PERSONAL_INFORMES_AUTOMATICOS:
                {
                    $html .= $this->dame_informes_automaticos();
                    break;
                }
                case SECCION_PERSONAL_WIDGETS:
                {
                    $html .= $this->dame_pestanyas_widgets();
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


        function dame_pestanyas_widgets()
		{
            $contenido = dame_pestanyas_widgets_modulo(MODULO_PERSONAL, NULL);
            return ($contenido);
		}


        function dame_plantillas_informes()
		{
            $contenido = $this->dame_plantillas_informes_informes();
			return ($contenido);
		}


        function dame_plantillas_informes_informes()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-plantillas-informes-personal'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-plantillas-informes-personal'>".$this->idiomas->_("Plantillas de informes")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-informe-plantilla-informe-personal'>".$this->idiomas->_("Informe")."</a></li>
                    </ul>
                    <div id='tab-plantillas-informes-informe-personal' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active pestanya-plantillas-informes-personal' id='tab-plantillas-informes-personal'>";
            $contenido .= $this->dame_tabla_filtro_plantillas_informes_tabla();
            $contenido .= "
                            <div id='tablaPlantillasInformes'>".
                                PlantillaInforme::dame_tabla_plantillas_informes()."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane pestanya-plantillas-informes-personal' id='tab-informe-plantilla-informe-personal'>";
            $contenido .= $this->dame_informe_plantilla_informe();
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


        function dame_informe_plantilla_informe($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "personal_informe_plantilla_informe";

            $control_lista_plantillas_informes = dame_control_lista_plantillas_informes($sufijo_controles, OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_SIN_OPCIONES_EXTRA);

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
                PERIODO_DEFECTO_PERSONAL_INFORME_PLANTILLA_INFORME,
                array(),
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $parametros_pie_pagina = dame_controles_parametros_pie_pagina($sufijo_controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-personal-informe-plantilla-informe",
                $this->idiomas->_("Informe de plantilla de informe"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                array()
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Plantillas de informes")));
            $params_contenido_plantillas_informes = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_PLANTILLA_INFORME)
            );
            $tabla->anyade_fila("", array($control_lista_plantillas_informes), $params_contenido_plantillas_informes);

            $tabla->anyade_cabecera("cabecera-parametros-plantillas-informes", array($this->idiomas->_("Parámetros")));
            $tabla->anyade_fila("parametros-plantillas-informes", "");

            $params_fila_oculta_elementos_desplegables = array("oculta" => true);

            $tabla->anyade_cabecera_elementos_desplegables(
                "cabecera-subtitulos-portadas-plantillas-informes",
                array($this->idiomas->_("Subtítulos de portadas")),
                array("subtitulos-portadas-plantillas-informes"),
                false);
            $tabla->anyade_fila("subtitulos-portadas-plantillas-informes", "", $params_fila_oculta_elementos_desplegables);

            $tabla->anyade_cabecera_elementos_desplegables(
                "cabecera-titulos-plantillas-informes",
                array($this->idiomas->_("Títulos")),
                array("titulos-plantillas-informes"),
                false);
            $tabla->anyade_fila("titulos-plantillas-informes", "", $params_fila_oculta_elementos_desplegables);

            $tabla->anyade_cabecera_elementos_desplegables(
                "cabecera-textos-plantillas-informes",
                array($this->idiomas->_("Textos")),
                array("textos-plantillas-informes"),
                false);
            $tabla->anyade_fila("textos-plantillas-informes", "", $params_fila_oculta_elementos_desplegables);

            $tabla->anyade_cabecera_elementos_desplegables(
                "cabecera-imagenes-plantillas-informes",
                array($this->idiomas->_("Imágenes")),
                array("imagenes-plantillas-informes"),
                false);
            $tabla->anyade_fila("imagenes-plantillas-informes", "", $params_fila_oculta_elementos_desplegables);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_INFORME_PLANTILLA_INFORME),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_INFORME_PLANTILLA_INFORME
            );
			$tabla->anyade_fila("fechas-personal", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                "personal_informe_plantilla_informe",
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion_fechas_personal_informe_plantilla_informe",
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion_fechas_personal_informe_plantilla_informe",
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);
            anyade_controles_parametros_pie_pagina_tabla_informe(
                "personal_informe_plantilla_informe",
                $tabla,
                $this->idiomas->_("Pie de página"),
                $parametros_pie_pagina);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_graficas = array(
                "clase_contenido" => "informe"
            );
            $informe = "
                <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-personal-informe-plantilla-informe'>
                    <i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("No hay datos")."
                </div>
                <div id='informe-personal-informe-plantilla-informe' hidden>
                </div>";
            $tabla->anyade_contenido("", $informe, $params_contenido_graficas);

			return ($tabla->dame_tabla());
        }


        function dame_informes_automaticos()
		{
            $contenido = dame_tabla_filtro_informes_automaticos_tabla();
            $contenido .= "
                <div id='tablaInformesAutomaticos'>".
                    InformeAutomatico::dame_tabla_informes_automaticos()."
                </div>";
            return ($contenido);
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        // Devuelve la tabla que contiene el filtro para la tabla de plantillas de informes
        function dame_tabla_filtro_plantillas_informes_tabla()
        {
            // Se recuperan los controles a mostrar
            $id_controles = "personal_filtro_plantillas_informes_tabla";
            $filtro_plantillas_informes = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), array());

			// Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-personal-filtro-plantillas-informes-tabla",
                $this->idiomas->_("Filtro de plantillas de informes"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_PLANTILLAS_INFORMES),
            );
            $tabla->anyade_fila("filtro-plantillas-informes-tabla", $filtro_plantillas_informes, $params_fila);

            return ($tabla->dame_tabla());
        }
	}
?>
