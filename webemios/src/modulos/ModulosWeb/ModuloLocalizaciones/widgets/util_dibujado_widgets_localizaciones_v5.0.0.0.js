/*
 * Dibujado de widgets
 *
 */


// Dibuja un widget de tipo 'Valor de un ratio'
function dibuja_widget_tipo_valor_ratio(
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
    var icono = datos_widget.icono;

    // Parámetros de apariencia de widgets
    var parametros_apariencia_widgets = datos_widget.parametros_apariencia_widgets;
    var estilo_fuente = parametros_apariencia_widgets.estilo_fuente;
    var mostrar_fechas = parametros_apariencia_widgets.mostrar_fechas;
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
    var sin_datos = datos_widget.sin_valores;
    if (sin_datos == true) {
        establece_contenido_widget_sin_datos(
            id_widget,
            estilo_fuente,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            modificar_colores,
            color,
            color_fondo,
            transparencia_fondo,
            null);
    }
    else {
        // Datos del widget
        var html_cadena_valores = datos_widget.html_cadena_valores;
        var html_cadena_hora_valores = datos_widget.html_cadena_hora_valores;

        // Contenido del widget (valor y fecha del ratio)
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='valor-unidad-widget-valor-digital-sensor__" + id_widget + "' class='valor-unidad-widget-valor-digital-sensor'>";
        html_contenido_widget += html_cadena_valores;
        html_contenido_widget += "        </div>";
        if ((mostrar_fechas == VALOR_SI) && (html_cadena_hora_valores != null)) {
            html_contenido_widget += "    <div id='fecha-hora-widget-valor-digital-sensor__" + id_widget + "' class='fecha-hora-widget-valor-digital-sensor'>";
            html_contenido_widget += html_cadena_hora_valores;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color y fuente del widget
        $("#valor-unidad-widget-valor-digital-sensor__" + id_widget).css('color', color);
        $("#fecha-hora-widget-valor-digital-sensor__" + id_widget).css('color', color);
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#valor-unidad-widget-valor-digital-sensor__" + id_widget).css('font-weight', "bold");
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