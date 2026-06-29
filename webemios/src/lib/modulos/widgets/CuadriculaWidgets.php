<?php
    session_start();

	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');


    // Constantes

    // Indices de parámetros de apariencia de pestaña de widgets
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_IMAGEN_FONDO", 0);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NOMBRE_IMAGEN_FONDO", 1);
	define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_CABECERA", 2);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_HORA_CABECERA", 3);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_HORA_CABECERA", 4);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_FECHA_CABECERA", 5);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_FECHA_CABECERA", 6);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_TITULO_CABECERA", 7);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_TITULO_CABECERA", 8);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_PREFIJO_TITULO_CABECERA", 9);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_PREFIJO_TITULO_CABECERA", 10);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_SUFIJO_TITULO_CABECERA", 11);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_SUFIJO_TITULO_CABECERA", 12);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NUMERO_LINEAS_SEPARACION_CABECERA", 13);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MODIFICAR_COLOR_TITULO_FILAS_WIDGETS", 14);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_COLOR_TITULO_FILAS_WIDGETS", 15);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_MOSTRAR_PIE", 16);
    define("INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_NUMERO_LINEAS_SEPARACION_PIE", 17);

    // Indices de parámetros de apariencia de widgets
	define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_OPCIONES", 0);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_FECHAS", 1);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_BOTONES", 2);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_ESTILO_FUENTE", 3);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_BORDE", 4);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MOSTRAR_BORDE", 5);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_BORDE", 6);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_COLORES_TITULO", 7);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_TITULO", 8);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_FONDO_TITULO", 9);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO_TITULO", 10);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_MODIFICAR_COLORES", 11);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR", 12);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_FONDO", 13);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO", 14);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_COLOR_ICONO", 15);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_ICONO", 16);
    define("INDICE_PARAMETRO_APARIENCIA_WIDGETS_PESTANYA_WIDGETS_TRANSPARENCIA_FONDO_GRAFICAS", 17);

    // Indices de parámetros de opciones de pantalla completa
	define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MODIFICAR", 0);
    define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MOSTRAR_OPCIONES", 1);
    define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_ESTILO_FUENTE_TITULO", 2);
    define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_COLOR_PANTALLA_COMPLETA", 3);
    define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_COLOR_FONDO_PANTALLA_COMPLETA", 4);
    define("INDICE_PARAMETRO_OPCIONES_PANTALLA_COMPLETA_PESTANYA_WIDGETS_MOSTRAR_PIE_PAGINA", 5);


	// Clase que forma una cuadrícula de widgets
    class CuadriculaWidgets
	{
        // Miembros de CuadriculaWidgets
        public $id;
        public $ids_widgets;
        public $numeros_columnas_widgets;

        public $numeros_columnas_filas_widgets;
        public $titulos_filas_widgets;
        public $ajustar_altura_widgets;
        public $cadena_parametros_apariencia_pestanya;
        public $cadena_parametros_apariencia_widgets;
        public $cadena_parametros_opciones_pantalla_completa;
        public $parametros_apariencia_pestanya;
        public $parametros_apariencia_widgets;

        public $fila_widgets_actual;
        public $columna_fila_widgets_actual;

        public $datos_html;


        // Funciones de cuadrícula de widgets


        // Constructor
		function __construct($id, $params = array())
		{
            $this->id = $id;
            $this->ids_widgets = array();
            $this->numeros_columnas_widgets = array();
            $this->titulos_filas_widgets = array();
            $this->fila_widgets_actual = 0;
            $this->columna_fila_widgets_actual = 0;
            $this->datos_html = "";

            // Parámetros de la fila de la pestaña de widgets
            $this->nombre = $params["nombre"];
            $this->numeros_columnas_filas_widgets = explode(",", $params["numeros_columnas_filas_widgets"]);
            if ($params["titulos_filas_widgets"] != "")
            {
                $this->titulos_filas_widgets = explode("\n", $params["titulos_filas_widgets"]);
            }
            $this->ajustar_altura_widgets = $params["ajustar_altura_widgets"];
            $this->cadena_parametros_apariencia_pestanya = $params["parametros_apariencia_pestanya"];
            $this->cadena_parametros_apariencia_widgets = $params["parametros_apariencia_widgets"];
            $this->cadena_parametros_opciones_pantalla_completa = $params["parametros_opciones_pantalla_completa"];
            $this->parametros_apariencia_pestanya = dame_nombres_valores_parametros_apariencia_pestanya_pestanya_widgets($this->cadena_parametros_apariencia_pestanya);
            $this->parametros_apariencia_widgets = dame_nombres_valores_parametros_apariencia_widgets_pestanya_widgets($this->cadena_parametros_apariencia_widgets);

            // 'Colspan' total (mínimo común múltiplo del número de columnas de las filas de widgets)
            $this->colspan_total = 1;
            foreach ($this->numeros_columnas_filas_widgets AS $numero_columnas_fila_widgets)
            {
                $this->colspan_total = mcm($this->colspan_total, $numero_columnas_fila_widgets);
            }
        }


        // Añade un widget a la cuadrícula (el contenido del widget se generará en javascript, aquí se añade el "contenedor")
        function anyade_widget(
            $id,
            $nombre,
            $tipo,
            $cadena_numero_columnas)
        {
            // Número de columnas
            $parametros_numero_columnas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_numero_columnas);
            switch (count($parametros_numero_columnas))
            {
                case 1:
                {
                    $numero_columnas_vacias_izquierda_widget = 0;
                    $numero_columnas_contenido_widget = $parametros_numero_columnas[0];
                    $numero_columnas_vacias_derecha_widget = 0;
                    break;
                }
                case 3:
                {
                    $numero_columnas_vacias_izquierda_widget = $parametros_numero_columnas[0];
                    $numero_columnas_contenido_widget = $parametros_numero_columnas[1];
                    $numero_columnas_vacias_derecha_widget = $parametros_numero_columnas[2];
                    break;
                }
                default:
                {
                    throw new Exception("Número de parámetros de número de columnas incorrecto: '".$cadena_numero_columnas."'");
                }
            }

            // Opciones del widget
            if ($this->parametros_apariencia_widgets["mostrar_opciones"] == VALOR_SI)
            {
                // Botones visibles siempre
                $boton_actualizar_widget = "<div class='opciones-titulo-widget'>
                    <i id='actualiza_widget__".$id."' class='icon-refresh boton-tabla-datos boton_actualizar_widget'></i></div>";
                $opciones_widget = $boton_actualizar_widget;

                // Botones visibles sólo con administración de widgets
                $administracion_widgets = dame_administracion_widgets();
                if ($administracion_widgets == true)
                {
                    $boton_editar_widget = "<div class='opciones-titulo-widget'>
                        <i id='anyade_modifica_widget__".$id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' class='icon-pencil boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_widget'></i></div>";
                    $boton_duplicar_widget = "<div class='opciones-titulo-widget'>
                        <i id='anyade_modifica_widget__".$id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' class='icon-copy boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_widget'></i></div>";
                    $boton_eliminar_widget = "<div class='opciones-titulo-widget'>
                        <i id='elimina_widget__".$this->id."__".$id."__".$tipo."' nombre_widget='".$nombre."' class='icon-remove boton-tabla-datos boton_eliminar_widget'></i></div>";
                    $opciones_widget = $boton_eliminar_widget.$boton_duplicar_widget.$boton_editar_widget.$boton_actualizar_widget;
                }
            }

            // Se crea el html del widget
            if ($this->columna_fila_widgets_actual == 0)
            {
                $this->datos_html .= "<tr>";
            }

            // Cabecera de cuadrícula de widgets y título de fila de widget
            if ($this->columna_fila_widgets_actual == 0)
            {
                // Título de la fila de widgets
                if ($this->fila_widgets_actual + 1 <= count($this->titulos_filas_widgets))
                {
                    // Color de título de fila de widgets
                    if ($this->parametros_apariencia_pestanya["modificar_color_titulo_filas_widgets"] == VALOR_NO)
                    {
                        $color_titulo_fila_widgets .= COLOR_NEGRO;
                    }
                    else
                    {
                        $color_titulo_fila_widgets .= $this->parametros_apariencia_pestanya["color_titulo_filas_widgets"];
                    }

                    // Estilo del texto del título de la fila de widgets
                    $estilo_texto_titulo_fila_widgets = "style='";
                    $estilo_texto_titulo_fila_widgets .= "color: ".$color_titulo_fila_widgets.";";
                    if ($this->parametros_apariencia_widgets["estilo_fuente"] == ESTILO_FUENTE_NEGRITA)
                    {
                        $estilo_texto_titulo_fila_widgets .= "font-weight: bold;";
                    }
                    $estilo_texto_titulo_fila_widgets .= "'";

                    // Título de la fila de widgets
                    $titulo_fila_widgets = $this->titulos_filas_widgets[$this->fila_widgets_actual];
                    $this->datos_html .= "
                        <td class='titulo-fila-widgets elemento-no-seleccionable' colspan='".$this->colspan_total."' ".$estilo_texto_titulo_fila_widgets.">".
                            "<i class='icon-info-sign color-azul'></i> ".$titulo_fila_widgets.
                        "</td>";
                }

                // Inicio de fila
                $this->datos_html .= "<tr>";
            }

            // Altura y anchura del widget según el número de columnas de su fila y del propio widget
            array_push($this->numeros_columnas_widgets, $cadena_numero_columnas);
            $numero_widget = count($this->ids_widgets) + 1;
            $res_numeros_columnas = CuadriculaWidgets::dame_numeros_columnas_fila_widget_widget(
                $this->numeros_columnas_filas_widgets,
                $this->numeros_columnas_widgets,
                $numero_widget);
            $numero_columnas_fila_widget = $res_numeros_columnas["numero_columnas_fila_widget"];
            $numero_columnas_widget = $res_numeros_columnas["numero_columnas_widget"];

            // Número de columnas del contenido del widget y de los espacios vacíos a la izquierda y a la derecha del widget
            $numero_columnas_contenido_widget = $numero_columnas_widget;
            if (($numero_columnas_vacias_izquierda_widget > 0) && ($numero_columnas_contenido_widget > 0))
            {
                $numero_columnas_contenido_widget -= $numero_columnas_vacias_izquierda_widget;
            }
            $colspan_vacio_izquierda_widget_widget = ($this->colspan_total / $numero_columnas_fila_widget) * $numero_columnas_vacias_izquierda_widget;
            if (($numero_columnas_vacias_derecha_widget > 0) && ($numero_columnas_contenido_widget > 0))
            {
                $numero_columnas_contenido_widget -= $numero_columnas_vacias_derecha_widget;
            }
            $colspan_vacio_derecha_widget_widget = ($this->colspan_total / $numero_columnas_fila_widget) * $numero_columnas_vacias_derecha_widget;
            $colspan_widget = ($this->colspan_total / $numero_columnas_fila_widget) * $numero_columnas_contenido_widget;

            // Estilo del widget
            $estilo_widget = "";
            $borde_widgets_visible = false;
            if ($this->parametros_apariencia_widgets["modificar_borde"] == VALOR_NO)
            {
                $estilo_widget .= "style='border: 1px solid ".$_SESSION["colores"]["color_tema_oscuro"].";'";
                $borde_widgets_visible = true;
            }
            else
            {
                if ($this->parametros_apariencia_widgets["mostrar_borde"] == VALOR_SI)
                {
                    $estilo_widget .= "style='border: 1px solid ".$this->parametros_apariencia_widgets["color_borde"].";'";
                    $borde_widgets_visible = true;
                }
            }

            // Estilo de título de widgets
            $estilo_titulo_widget = "style='";
            if (($borde_widgets_visible == false) ||
                ($this->parametros_apariencia_widgets["modificar_borde"] == true) ||
                ($this->parametros_apariencia_widgets["modificar_colores_titulo"] == VALOR_SI))
            {
                // Nota: Sin este estilo, el 'relleno' del color de fondo no se ajusta correctamente en los bordes superiores
                $estilo_titulo_widget .= "border-radius: 3px 3px 0px 0px;";
            }
            if ($this->parametros_apariencia_widgets["modificar_colores_titulo"] == VALOR_NO)
            {
                $estilo_titulo_widget .= "color: ".COLOR_BLANCO.";";
            }
            else
            {
                $estilo_titulo_widget .= "color: ".$this->parametros_apariencia_widgets["color_titulo"].";";
            }
            $estilo_titulo_widget .= "'";

            // Clase y estilo del texto del título del widget
            $clase_texto_titulo_widget = "texto-titulo-widget";
            $estilo_texto_titulo_widget = "style='";
            if ($this->parametros_apariencia_widgets["estilo_fuente"] == ESTILO_FUENTE_NEGRITA)
            {
                $estilo_texto_titulo_widget .= "font-weight: bold;";
            }
            if ($this->parametros_apariencia_widgets["mostrar_opciones"] == VALOR_SI)
            {
                $clase_texto_titulo_widget = "texto-titulo-widget-con-opciones";
            }
            else
            {
                $clase_texto_titulo_widget = "texto-titulo-widget-sin-opciones";

                // Nota: Si no se muestran las opciones de los widgets y el color de fondo del título es igual al color de fondo del widget (contenido)
                // se ajusta el margen superior del texto del título
                $estilo_texto_titulo_widget .= "text-align: center;";
                if (($this->parametros_apariencia_widgets["modificar_colores"] == VALOR_SI) &&
                    (($this->parametros_apariencia_widgets["color_fondo_titulo"] == $this->parametros_apariencia_widgets["color_fondo"]) && ($this->parametros_apariencia_widgets["transparencia_fondo_titulo"] == $this->parametros_apariencia_widgets["transparencia_fondo"])))
                {
                    $estilo_texto_titulo_widget .= "padding-top: 0.5em;";
                }
                else
                {
                    $estilo_texto_titulo_widget .= "padding-top: 0.3em;";
                }
            }
            $estilo_texto_titulo_widget .= "'";

            // Clase del contenido del widget
            $clase_contenido_widget = "contenido-widget";
            if ($this->ajustar_altura_widgets == VALOR_NO)
            {
                $numero_columnas_widget_clase_altura_contenido_widget = ceil($numero_columnas_fila_widget / $numero_columnas_widget);
                if ($numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS)
                {
                    $numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
                }
                $clase_altura_contenido_widget = "altura-contenido-widget-columnas-".$numero_columnas_widget_clase_altura_contenido_widget;
                $clase_contenido_widget .= " ".$clase_altura_contenido_widget;
            }

            // Columnas vacías a la izquierda del widget
            if ($numero_columnas_vacias_izquierda_widget > 0)
            {
                $this->datos_html .= "<td colspan='".$colspan_vacio_izquierda_widget_widget."'></td>";
            }

            // Se añade el contenedor del widget
            $this->datos_html .= "
                <td class='contenedor-widget elemento-no-seleccionable' colspan='".$colspan_widget."'>
                    <div id='widget_".$id."' class='widget' ".$estilo_widget.">
                        <div id='titulo_widget_".$id."' class='titulo-widget' ".$estilo_titulo_widget.">
                            <div id='texto_titulo_widget__".$id."' class='".$clase_texto_titulo_widget."' ".$estilo_texto_titulo_widget."'>".htmlspecialchars($nombre, ENT_QUOTES)."</div>";
            if ($this->parametros_apariencia_widgets["mostrar_opciones"] == VALOR_SI)
            {
                $this->datos_html .= "
                            <div class='contenedor-opciones-titulo-widget'>".$opciones_widget."</div>";
            }
            $this->datos_html .= "
                        </div>
                        <div id='contenido_widget_".$id."' class='".$clase_contenido_widget."'></div>
                    </div>
                </td>";

            // Columnas vacías a la derecha del widget
            if ($numero_columnas_vacias_derecha_widget > 0)
            {
                $this->datos_html .= "<td colspan='".$colspan_vacio_derecha_widget_widget."'></td>";
            }

            $this->columna_fila_widgets_actual += $numero_columnas_widget;
            if ($this->columna_fila_widgets_actual >= $numero_columnas_fila_widget)
            {
                $this->datos_html .= "</tr>";
                $this->fila_widgets_actual += 1;
                $this->columna_fila_widgets_actual = 0;
            }

            // Se añaden los parámetros (no visibles) en un 'div' oculto
            $this->datos_html .= '
                <div id="parametros_widget__'.$id.'"
                    numero_widget="'.$numero_widget.'"
                    hidden>
                </div>';

            array_push($this->ids_widgets, $id);
        }


        // Devuelve los identificadores de los widgets de la cuadrícula
        function dame_ids_widgets()
        {
            return ($this->ids_widgets);
        }


        // Devuelve los números de columnas de los widgets de la cuadrícula
        function dame_numeros_columnas_widgets()
        {
            return ($this->numeros_columnas_widgets);
        }


        // Devuelve el contenido de la cuadrícula (html)
		function dame_cuadricula()
		{
            // Cabecera
            $cabecera = "";
            if ($this->parametros_apariencia_pestanya["mostrar_cabecera"] == VALOR_SI)
            {
                $cabecera .= "
                    <td class='cabecera-pestanya-widgets' colspan='".$this->colspan_total."'>";
                if ($this->parametros_apariencia_pestanya["mostrar_hora_cabecera"] == VALOR_SI)
                {
                    $cabecera .= "
                        <div class='hora-cabecera-pestanya-widgets' style='color: ".$this->parametros_apariencia_pestanya["color_hora_cabecera"]."'>".
                            "ND"."
                        </div>";
                }
                if ($this->parametros_apariencia_pestanya["mostrar_fecha_cabecera"] == VALOR_SI)
                {
                    $cabecera .= "
                        <div class='fecha-cabecera-pestanya-widgets' style='color: ".$this->parametros_apariencia_pestanya["color_fecha_cabecera"]."'>".
                            "ND"."
                        </div>";
                }
                if ($this->parametros_apariencia_pestanya["mostrar_titulo_cabecera"] == VALOR_SI)
                {
                    $cabecera .= "
                        <div class='titulo-cabecera-pestanya-widgets'>";
                    if ($this->parametros_apariencia_pestanya["prefijo_titulo_cabecera"] != "")
                    {
                        $cabecera .= "
                            <span style='color: ".$this->parametros_apariencia_pestanya["color_prefijo_titulo_cabecera"]."'>".
                                htmlspecialchars($this->parametros_apariencia_pestanya["prefijo_titulo_cabecera"], ENT_QUOTES)." "."
                            </span>";
                    }
                    $cabecera .= "
                            <span style='color: ".$this->parametros_apariencia_pestanya["color_titulo_cabecera"]."'>".
                                htmlspecialchars($this->nombre, ENT_QUOTES)." "."
                            </span>";
                    if ($this->parametros_apariencia_pestanya["prefijo_titulo_cabecera"] != "")
                    {
                        $cabecera .= "
                            <span style='color: ".$this->parametros_apariencia_pestanya["color_sufijo_titulo_cabecera"]."'>".
                                htmlspecialchars($this->parametros_apariencia_pestanya["sufijo_titulo_cabecera"], ENT_QUOTES)." "."
                            </span>";
                    }
                    $cabecera .= "
                        </div>";

                    $cabecera .= "
                        <div style='padding-bottom:".$this->parametros_apariencia_pestanya["numero_lineas_separacion_cabecera"]."em'></div>";
                }
                $cabecera .= "
                    </td>";

                // Inicio de fila
                $cabecera .= "<tr>";
            }

            // Pie de cuadrícula de widgets
            $pie = "";
            if ($this->parametros_apariencia_pestanya["mostrar_pie"] == VALOR_SI)
            {
                $pie .= "<tr>";
                $pie .= "
                    <td>
                        <div style='padding-bottom:".$this->parametros_apariencia_pestanya["numero_lineas_separacion_pie"]."em'></div>
                    </td>";
            }

            // Inicio de tabla (contiene la cabecera)
            $inicio_tabla = "
                <table class='tabla-cuadricula-widgets'>
                    <thead>
                        <th colspan='".$this->colspan_total."'></th>
                    </thead>
                    <tbody>";
            $inicio_tabla .= $cabecera;

            // Fin de tabla (contiene el pie)
            $fin_tabla = "";
            if ($this->columna_fila_widgets_actual > 0)
            {
                $fin_tabla .= "</tr>";
            }
            $fin_tabla .= $pie;
            $fin_tabla .= "
                    </tbody>
                </table>";

            // Contenido de la cuadrícula de widgets
			$contenido = "
                <div class='contenido-cuadricula-widgets'>";
            $contenido .= $inicio_tabla;
            $contenido .= $this->datos_html;
            $contenido .= $fin_tabla;
            $contenido .= "
                </div>";

            // Se añaden los parámetros (no visibles) en un 'div' oculto
            $contenido .= '
                <div id="parametros_cuadricula_widgets__'.$this->id.'"
                    ids_widgets="'.implode(",", $this->ids_widgets).'"
                    numeros_columnas_filas_widgets="'.implode(",", $this->numeros_columnas_filas_widgets).'"
                    ajustar_altura_widgets="'.$this->ajustar_altura_widgets.'"
                    cadena_parametros_apariencia_pestanya="'.$this->cadena_parametros_apariencia_pestanya.'"
                    cadena_parametros_apariencia_widgets="'.$this->cadena_parametros_apariencia_widgets.'"
                    cadena_parametros_opciones_pantalla_completa="'.$this->cadena_parametros_opciones_pantalla_completa.'"
                    hidden>
                </div>';

			return ($contenido);
		}


        // Devuelve los números de columnas de la fila del widget y del widget
        static function dame_numeros_columnas_fila_widget_widget(
            $numeros_columnas_filas_widgets,
            $numeros_columnas_widgets,
            $numero_widget)
        {
            $numero_widget_actual = 1;
            $numero_fila_widgets_actual = 1;
            $numero_columnas_fila_widgets_actual = CuadriculaWidgets::dame_numero_columnas_fila_widgets(
                $numeros_columnas_filas_widgets,
                $numero_fila_widgets_actual);
            $numero_columnas_libres_fila_widgets_actual = $numero_columnas_fila_widgets_actual;
            for ($i = 0; $i < count($numeros_columnas_widgets); $i++)
            {
                // Número de columnas del widget actual (total)
                $cadena_numero_columnas_widget_actual = $numeros_columnas_widgets[$i];
                $parametros_numero_columnas_widget_actual = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_numero_columnas_widget_actual);
                switch (count($parametros_numero_columnas_widget_actual))
                {
                    case 1:
                    {
                        $numero_columnas_vacias_izquierda_widget_actual = 0;
                        $numero_columnas_contenido_widget_actual = $parametros_numero_columnas_widget_actual[0];
                        $numero_columnas_vacias_derecha_widget_actual = 0;
                        break;
                    }
                    case 3:
                    {
                        $numero_columnas_vacias_izquierda_widget_actual = $parametros_numero_columnas_widget_actual[0];
                        $numero_columnas_contenido_widget_actual = $parametros_numero_columnas_widget_actual[1];
                        $numero_columnas_vacias_derecha_widget_actual = $parametros_numero_columnas_widget_actual[2];
                        break;
                    }
                    default:
                    {
                        throw new Exception("Número de parámetros de número de columnas incorrecto: '".$parametros_numero_columnas_widget_actual."'");
                    }
                }
                $numero_columnas_widget_actual =
                    $numero_columnas_vacias_izquierda_widget_actual +
                    $numero_columnas_contenido_widget_actual +
                    $numero_columnas_vacias_derecha_widget_actual;

                if ($numero_columnas_widget_actual > $numero_columnas_libres_fila_widgets_actual)
                {
                    $numero_columnas_widget_actual = $numero_columnas_libres_fila_widgets_actual;
                }
                $numero_columnas_libres_fila_widgets_actual -= $numero_columnas_widget_actual;
                if ($numero_widget_actual == $numero_widget)
                {
                    break;
                }
                if ($numero_columnas_libres_fila_widgets_actual == 0)
                {
                    $numero_fila_widgets_actual += 1;
                    $numero_columnas_fila_widgets_actual = CuadriculaWidgets::dame_numero_columnas_fila_widgets(
                        $numeros_columnas_filas_widgets,
                        $numero_fila_widgets_actual);
                    $numero_columnas_libres_fila_widgets_actual = $numero_columnas_fila_widgets_actual;
                }
                $numero_widget_actual += 1;
            }

            $res_numeros_columnas = array(
                "numero_columnas_fila_widget" => $numero_columnas_fila_widgets_actual,
                "numero_columnas_widget" => $numero_columnas_widget_actual);
            return ($res_numeros_columnas);
        }


        // Devuelve el número de columna de la fila especificada
        static function dame_numero_columnas_fila_widgets($numeros_columnas_filas_widgets, $numero_fila_widgets)
        {
            if (count($numeros_columnas_filas_widgets) < $numero_fila_widgets)
            {
                $numero_columnas_fila = $numeros_columnas_filas_widgets[count($numeros_columnas_filas_widgets) - 1];
            }
            else
            {
                $numero_columnas_fila = $numeros_columnas_filas_widgets[$numero_fila_widgets - 1];
            }
            return ($numero_columnas_fila);
        }
    }
?>
