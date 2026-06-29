<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/Ratio.php');


	class ModuloLocalizaciones extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_LOCALIZACIONES, NOMBRE_MODULO_LOCALIZACIONES);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_LOCALIZACIONES_PRINCIPAL,
                SECCION_LOCALIZACIONES_INSTALACIONES,
                SECCION_LOCALIZACIONES_TOPOLOGIA,
                SECCION_LOCALIZACIONES_RATIOS,
                SECCION_LOCALIZACIONES_MAPA
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_LOCALIZACIONES_PRINCIPAL:
                {
                    $descripcion = "Principal";
                    break;
                }
                case SECCION_LOCALIZACIONES_INSTALACIONES:
                {
                    $descripcion = "Instalaciones";
                    break;
                }
                case SECCION_LOCALIZACIONES_TOPOLOGIA:
                {
                    $descripcion = "Topología";
                    break;
                }
                case SECCION_LOCALIZACIONES_RATIOS:
                {
                    $descripcion = "Ratios";
                    break;
                }
                case SECCION_LOCALIZACIONES_MAPA:
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
            $html = "<div id='modulo' name='".MODULO_LOCALIZACIONES."' hidden></div>";

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_LOCALIZACIONES_PRINCIPAL:
                {
                    $html .= $this->dame_principal();
                    break;
                }
                case SECCION_LOCALIZACIONES_INSTALACIONES:
                {
                    $html .= $this->dame_instalaciones();
                    break;
                }
                case SECCION_LOCALIZACIONES_TOPOLOGIA:
                {
                    $html .= $this->dame_topologia();
                    break;
                }
                case SECCION_LOCALIZACIONES_RATIOS:
                {
                    $html .= $this->dame_ratios();
                    break;
                }
                case SECCION_LOCALIZACIONES_MAPA:
                {
                    $html .= $this->dame_tabla_filtro_localizaciones_mapa();
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


        function dame_principal()
        {
            $contenido = "";
            $contenido .= $this->dame_tabla_filtro_localizaciones_tabla();
			$contenido .= $this->dame_localizaciones();

			return ($contenido);
		}


        function dame_localizaciones()
		{
            $contenido = "
                <div id='tablaLocalizaciones'>".
                    Localizacion::dame_tabla_localizaciones("", NULL)."
                </div>";
			return ($contenido);
        }


        function dame_instalaciones()
		{
            $contenido = $this->dame_instalaciones_informes();
			return ($contenido);
		}


        function dame_instalaciones_informes()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-instalaciones-informes-localizaciones'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-instalaciones'>".$this->idiomas->_("Instalaciones")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-mapa-instalaciones'>".$this->idiomas->_("Mapa de instalaciones")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-imagen-instalacion'>".$this->idiomas->_("Imagen de instalación")."</a></li>
                    </ul>
                    <div id='tabs-instalaciones-localizaciones' class='tab-content'>";

            // Localización seleccionada por defecto
            $id_localizacion_seleccionada = $_SESSION["id_localizacion"];
            switch ($id_localizacion_seleccionada)
            {
                case ID_NINGUNO:
                case ID_DESACTIVADO:
                {
                    $id_localizacion_seleccionada = ID_TODOS;
                    break;
                }
                case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                {
                    $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                    if (count($ids_localizaciones_seleccionadas) == 1)
                    {
                        $id_localizacion_seleccionada = $ids_localizaciones_seleccionadas[0];
                    }
                    else
                    {
                        $id_localizacion_seleccionada = ID_TODOS;
                    }
                    break;
                }
            }

			$contenido .= "
                        <div class='tab-pane active' id='tab-instalaciones'>";
            $contenido .= $this->dame_tabla_filtro_instalaciones_tabla($id_localizacion_seleccionada);
            $contenido .= "
                            <div id='tablaInstalaciones'>".
                                Instalacion::dame_tabla_instalaciones("", $id_localizacion_seleccionada, VALOR_SI)."
                            </div>
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-mapa-instalaciones'>";
            $contenido .= $this->dame_tabla_seleccion_localizacion_mapa_instalaciones();
            $contenido .= $this->dame_mapa_instalaciones();
            $contenido .= "
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-imagen-instalacion'>";
            $contenido .= $this->dame_tabla_seleccion_instalacion_imagen_instalacion();
            $contenido .= $this->dame_imagen_instalacion();
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


        function dame_mapa_instalaciones()
		{
            // Control del mapa (y parámetros ocultos)
			$mapa = "
                <div class='mapa' id='mapa-instalaciones'>
                    <div id='texto-mapa-instalaciones' class='texto-mapa-vacio'><i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("No hay localización seleccionada")."
                    </div>
                    <div id='mapa-mapa-instalaciones' class='mapa-vacio'></div>
                </div>";
            $mapa .= '
                <div id="parametros_mapa_instalaciones"
                    id_localizacion="'.ID_NINGUNO.'"
                    etiquetas="'.VALOR_SI.'"
                    hidden>
                </div>';

            // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => $this->dame_botones_mapa_instalaciones()
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-mapa-instalaciones",
                $this->idiomas->_("Mapa de instalaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            return ($tabla->dame_tabla());
        }


        function dame_botones_mapa_instalaciones()
        {
            $boton_actualizar_mapa = "<i id='boton_localizaciones_actualizar_mapa_instalaciones' class='icon-refresh color-blanco boton-tabla-datos'></i>";
            $boton_centrar_mapa = "<i id='boton_localizaciones_centrar_mapa_instalaciones' class='icon-screenshot color-blanco boton-tabla-datos'></i>";
            $boton_etiquetas_mapa = "<i id='boton_localizaciones_etiquetas_mapa_instalaciones' class='icon-tags color-blanco boton-tabla-datos'></i>";

            $botones = array(
                $boton_actualizar_mapa,
                $boton_centrar_mapa,
                $boton_etiquetas_mapa);
            return ($botones);
        }


        function dame_imagen_instalacion()
		{
            // Control de la imagen (mapa) (y parámetros ocultos)
			$mapa = "
                <div class='mapa' id='imagen_instalacion_localizaciones'>
                    <div id='texto-imagen-instalacion' class='texto-mapa-vacio'><i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("No hay instalación seleccionada")."
                    </div>
                    <div id='mapa-imagen-instalacion' class='mapa-vacio'></div>
                </div>";
            $mapa .= '
                <div id="parametros_imagen_instalacion"
                    id_instalacion="'.ID_NINGUNO.'"
                    etiquetas="'.VALOR_SI.'"
                    hidden>
                </div>';

            // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => $this->dame_botones_imagen_instalacion()
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-imagen-instalacion",
                $this->idiomas->_("Imagen de instalación"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            return ($tabla->dame_tabla());
        }


        function dame_botones_imagen_instalacion()
        {
            $boton_actualizar_mapa = "<i id='boton_localizaciones_actualizar_imagen_instalacion' class='icon-refresh color-blanco boton-tabla-datos'></i>";
            $boton_centrar_mapa = "<i id='boton_localizaciones_centrar_imagen_instalacion' class='icon-screenshot color-blanco boton-tabla-datos'></i>";
            $boton_etiquetas_mapa = "<i id='boton_localizaciones_etiquetas_imagen_instalacion' class='icon-tags color-blanco boton-tabla-datos'></i>";

            $botones = array(
                $boton_actualizar_mapa,
                $boton_centrar_mapa,
                $boton_etiquetas_mapa);
            return ($botones);
        }


        function dame_topologia()
		{
            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-topologia-localizaciones'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-topologia-localizacion'>".$this->idiomas->_("Topología de localización")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-topologia-instalacion'>".$this->idiomas->_("Topología de instalación")."</a></li>
                    </ul>
                    <div id='tabs-topologia-localizaciones' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-topologia-localizacion'>";
            $contenido .= $this->dame_tabla_seleccion_localizacion_topologia_localizacion();
            $contenido .= $this->dame_topologia_localizacion();
            $contenido .= "
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-topologia-instalacion'>";
            $contenido .= $this->dame_tabla_seleccion_instalacion_topologia_instalacion();
            $contenido .= $this->dame_topologia_instalacion();
            $contenido .= "
                        </div>";

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
        }


		function dame_topologia_localizacion()
		{
            // Se recuperan los controles a mostrar
			$topologia = "
                <div class='topologiaarbol' id='topologia-localizacion'>
                    <div id='texto-topologia-localizacion' class='texto-topologia-vacia'><i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("No hay localización seleccionada")."
                    </div>
                    <div id='grafico-topologia-localizacion' class='grafico-topologiaarbol'></div>
                </div>";

            // Se introduce la información en una tabla
            $boton_actualizar_topologia_red = "<i id='boton_localizaciones_actualizar_topologia_localizacion' class='icon-refresh color-blanco boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_topologia_red);

             // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => $opciones
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-topologia-localizacion",
                $this->idiomas->_("Topología de localización"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );
            $params_contenido = array("clase_contenido" => "contenedor-topologiaarbol");
			$tabla->anyade_contenido("", $topologia, $params_contenido);

            return ($tabla->dame_tabla());
		}


        function dame_topologia_instalacion()
		{
            // Se recuperan los controles a mostrar
			$topologia = "
                <div class='topologiaarbol' id='topologia-instalacion'>
                    <div id='texto-topologia-instalacion' class='texto-topologia-vacia'><i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("No hay instalación seleccionada")."
                    </div>
                    <div id='grafico-topologia-instalacion' class='grafico-topologiaarbol'></div>
                </div>";

            // Se introduce la información en una tabla
            $boton_actualizar_topologia_red = "<i id='boton_localizaciones_actualizar_topologia_instalacion' class='icon-refresh color-blanco boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_topologia_red);

             // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => $opciones
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-topologia-instalacion",
                $this->idiomas->_("Topología de instalación"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );
			$params_contenido = array("clase_contenido" => "contenedor-topologiaarbol");
			$tabla->anyade_contenido("", $topologia, $params_contenido);

            return ($tabla->dame_tabla());
		}


        function dame_ratios()
		{
            $contenido = $this->dame_tabla_filtro_ratios_tabla();
            $contenido .= "
                <div id='tablaRatios'>".
                    Ratio::dame_tabla_ratios("")."
                </div><br/>";
			return ($contenido);
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
                "tabla-localizaciones-mapa",
                $this->idiomas->_("Mapa de localizaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            return ($tabla->dame_tabla());
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_localizaciones_tabla()
        {
            // Se recuperan los controles a mostrar
			$filtro_localizaciones = dame_filtro_texto_controles_extra(
                "localizaciones_filtro_localizaciones_tabla",
                $this->idiomas->_("Nombre"),
                array());

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-localizaciones-tabla",
                $this->idiomas->_("Filtro de localizaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_LOCALIZACIONES_TABLA),
            );
            $tabla->anyade_fila("filtro-localizaciones-tabla", $filtro_localizaciones, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_filtro_instalaciones_tabla($id_localizacion_seleccionada)
        {
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "localizaciones_filtro_instalaciones_tabla";

            // Localización e incluir localizaciones hijas
            $control_lista_localizaciones = dame_control_lista_localizaciones(
                $id_controles,
                $idiomas->_("Localización"),
                $id_localizacion_seleccionada,
                OPCIONES_EXTRA_LISTA_LOCALIZACIONES_TODAS);
            $control_lista_incluir_localizaciones_descendientes = dame_control_lista_valores(
                $id_controles,
                "incluir_localizaciones_descendientes",
                $idiomas->_("Incluir localizaciones hijas"),
                array(
                    array(VALOR_SI, dame_descripcion_valores_si_no(VALOR_SI)),
                    array(VALOR_NO, dame_descripcion_valores_si_no(VALOR_NO))),
                VALOR_SI,
                "filtro-desplegable");
            array_push($controles, $control_lista_localizaciones);
            array_push($controles, $control_lista_incluir_localizaciones_descendientes);

            $filtro_instalaciones = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

			// Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-instalaciones-tabla",
                $this->idiomas->_("Filtro de instalaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_INSTALACIONES_TABLA)
            );
            $tabla->anyade_fila("filtro-instalaciones-tabla", $filtro_instalaciones, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_filtro_ratios_tabla()
        {
            // Se recuperan los controles a mostrar
			$filtro_ratios = dame_filtro_texto_controles_extra(
                "localizaciones_filtro_ratios_tabla",
                $this->idiomas->_("Nombre"),
                array());

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-ratios-tabla",
                $this->idiomas->_("Filtro de ratios"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_RATIOS_TABLA),
            );
            $tabla->anyade_fila("filtro-ratios-tabla", $filtro_ratios, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_seleccion_localizacion_mapa_instalaciones()
        {
            // Se recuperan los controles a mostrar
            $seleccion_localizacion = dame_seleccion_localizacion("localizaciones_seleccion_mapa_instalaciones");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-mapa-instalaciones",
                $this->idiomas->_("Selección de localización"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_SELECCION_MAPA_INSTALACIONES),
            );
            $tabla->anyade_fila("seleccion-mapa-instalaciones", $seleccion_localizacion, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_seleccion_instalacion_imagen_instalacion()
        {
            // Se recuperan los controles a mostrar
            $seleccion_instalacion = dame_seleccion_instalacion_localizacion(
                "localizaciones_seleccion_imagen_instalacion",
                OPCIONES_EXTRA_LISTA_INSTALACIONES_CON_IMAGEN_NINGUNA);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-imagen-instalacion",
                $this->idiomas->_("Selección de instalación"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_SELECCION_IMAGEN_INSTALACION),
            );
            $tabla->anyade_fila("seleccion-imagen-instalacion", $seleccion_instalacion, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_seleccion_localizacion_topologia_localizacion()
        {
            // Se recuperan los controles a mostrar
            $seleccion_localizacion = dame_seleccion_localizacion("localizaciones_seleccion_topologia_localizacion");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-topologia-localizacion",
                $this->idiomas->_("Selección de localización"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_SELECCION_TOPOLOGIA_LOCALIZACION),
            );
            $tabla->anyade_fila("seleccion-topologia-localizacion", $seleccion_localizacion, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_seleccion_instalacion_topologia_instalacion()
        {
            // Se recuperan los controles a mostrar
            $seleccion_instalacion = dame_seleccion_instalacion_localizacion(
                "localizaciones_seleccion_topologia_instalacion",
                OPCIONES_EXTRA_LISTA_INSTALACIONES_NINGUNA);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-topologia-instalacion",
                $this->idiomas->_("Selección de instalación"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_SELECCION_TOPOLOGIA_INSTALACION),
            );
            $tabla->anyade_fila("seleccion-topologia-instalacion", $seleccion_instalacion, $params_fila);

            return ($tabla->dame_tabla());
        }


        function dame_tabla_filtro_localizaciones_mapa()
        {
            // Se recuperan los controles a mostrar
			$filtro_localizaciones = dame_filtro_texto_controles_extra(
                "localizaciones_filtro_localizaciones_mapa",
                $this->idiomas->_("Nombre"),
                array());

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-localizaciones-filtro-localizaciones-mapa",
                $this->idiomas->_("Filtro de localizaciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_LOCALIZACIONES_MAPA),
            );
            $tabla->anyade_fila("filtro-localizaciones-mapa", $filtro_localizaciones, $params_fila);

            return ($tabla->dame_tabla());
        }
	}
?>
