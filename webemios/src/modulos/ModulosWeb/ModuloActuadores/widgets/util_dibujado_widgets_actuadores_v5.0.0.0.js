/*
 * Dibujado de widgets
 *
 */


// Dibuja un widget de tipo 'Información de actuador' o 'Información de grupo de actuadores'
function dibuja_widget_tipo_informacion_actuador_grupo_actuadores(
    id_pestanya,
    id_widget,
    nombre_widget,
    tipo,
    parametros_tipo,
    datos,
    numero_columnas_fila_widget,
    numero_columnas_widget) {
    // Datos del widget
    var datos_widget = jQuery.parseJSON(datos);
    var html_boton_envio_accion = datos_widget.html_boton_envio_accion;
    var icono = datos_widget.icono;

    // Parámetros de apariencia de widgets
    var parametros_apariencia_widgets = datos_widget.parametros_apariencia_widgets;
    var estilo_fuente = parametros_apariencia_widgets.estilo_fuente;
    var mostrar_fechas = parametros_apariencia_widgets.mostrar_fechas;
    var mostrar_botones = parametros_apariencia_widgets.mostrar_botones;
    var modificar_colores_titulo = parametros_apariencia_widgets.modificar_colores_titulo;
    var color_fondo_titulo = parametros_apariencia_widgets.color_fondo_titulo;
    var transparencia_fondo_titulo = parametros_apariencia_widgets.transparencia_fondo_titulo;
    var modificar_colores = parametros_apariencia_widgets.modificar_colores;
    var color = parametros_apariencia_widgets.color;
    var color_fondo = parametros_apariencia_widgets.color_fondo;
    var transparencia_fondo = parametros_apariencia_widgets.transparencia_fondo;
    var color_icono = parametros_apariencia_widgets.color_icono;
    var transparencia_icono = parametros_apariencia_widgets.transparencia_icono;

    // Colores de fondo del widget
    if (modificar_colores_titulo == VALOR_NO) {
        color_fondo_titulo = color_tema_oscuro;
    }
    if (modificar_colores == VALOR_NO) {
        color = COLOR_NEGRO;
        color_fondo = color_tema_fondo;
    }

    // Comprobación de widget sin datos
    var sin_datos = datos_widget.sin_ultima_accion;
    if (sin_datos == true) {
        var html_elementos_adicionales = null;
        if ((mostrar_botones == VALOR_SI) && (html_boton_envio_accion != "")) {
            html_elementos_adicionales = html_boton_envio_accion;
        }
        establece_contenido_widget_sin_datos(
            id_widget,
            estilo_fuente,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            modificar_colores,
            color,
            color_fondo,
            transparencia_fondo,
            html_elementos_adicionales);
    }
    else {
        // Datos del widget
        var imagen_ultima_accion = datos_widget.imagen_ultima_accion;
        var nombre_estado_ultima_accion = datos_widget.nombre_estado_ultima_accion;
        var cadena_hora_ultima_accion = datos_widget.cadena_hora_ultima_accion;
        var icono_estado_ultima_ejecucion_accion = datos_widget.icono_estado_ultima_ejecucion_accion;
        var clase_tamanyo_fuente_imagen_accion_widget = datos_widget.clase_tamanyo_fuente_imagen_accion_widget;
        var clase_tamanyo_fuente_nombre_estado_widget = datos_widget.clase_tamanyo_fuente_nombre_estado_widget;

        // Contenido del widget (imagen y nombre de estado de última acción)
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='imagen-accion-widget-informacion-actuador__" + id_widget + "' class='" + clase_tamanyo_fuente_imagen_accion_widget + "'>";
        html_contenido_widget += imagen_ultima_accion;
        html_contenido_widget += "        </div>";
        if (nombre_estado_ultima_accion != "") {
            html_contenido_widget += "    <div id='texto-nombre-estado-widget-informacion-actuador__" + id_widget + "' class='texto-nombre-estado-widget-informacion-actuador " + clase_tamanyo_fuente_nombre_estado_widget + "'>";
            html_contenido_widget += nombre_estado_ultima_accion;
            html_contenido_widget += "    </div>";
        }
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='fecha-hora-widget-informacion-actuador__" + id_widget + "' class='fecha-hora-widget-informacion-actuador'>";
            html_contenido_widget += cadena_hora_ultima_accion;
            if (icono_estado_ultima_ejecucion_accion != "") {
                html_contenido_widget += " [" + icono_estado_ultima_ejecucion_accion + "]";
            }
            html_contenido_widget += "    </div>";
        }
        var boton_envio_accion_mostrado = false;
        if ((mostrar_botones == VALOR_SI) && (html_boton_envio_accion != "")) {
            html_contenido_widget += html_boton_envio_accion;
            boton_envio_accion_mostrado = true;
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Se añade un margen inferior si no se muestran las fechas o el botón de envío de accion
        if ((mostrar_fechas == VALOR_NO) && (boton_envio_accion_mostrado == false)) {
            if (nombre_estado_ultima_accion == "") {
                $("#imagen-accion-widget-informacion-actuador__" + id_widget).css('padding-bottom', '0.1em');
            }
            else {
                $("#texto-nombre-estado-widget-informacion-actuador__" + id_widget).css('padding-bottom', '0.2em');
            }
        }

        // Color y fuente del widget
        $("#texto-nombre-estado-widget-informacion-actuador__" + id_widget).css('color', color);
        $("#fecha-hora-widget-informacion-actuador__" + id_widget).css('color', color);
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#texto-nombre-estado-widget-informacion-actuador__" + id_widget).css('font-weight', "bold");
        }

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }

    // Icono del widget
    dibuja_icono_widget(
        id_widget,
        icono,
        color_icono,
        transparencia_icono);
}