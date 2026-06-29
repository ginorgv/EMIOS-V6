/*
 * Dibujado de widgets
 *
 */


// Dibuja el título y el contenido de un widget según el tipo
function dibuja_widget_tipo(
    id_pestanya,
    id_widget,
    nombre,
    tipo,
    parametros_tipo,
    datos,
    numero_columnas_fila_widget,
    numero_columnas_widget) {
    // Se actualiza el título del widget
    $("#texto_titulo_widget__" + id_widget).html(escapeHtml(nombre));

    // Nota: Algunas veces después de la visualización de los widgets, el contenido se queda con una anchura mayor que el widget,
    // hay que comprobar si es así y si ocurre establecer la anchura correcta en el contenido del widget
    var anchura_widget = $("#widget_" + id_widget).width();
    var anchura_contenido_widget = $("#contenido_widget_" + id_widget).width();
    if (anchura_contenido_widget != anchura_widget) {
        $("#contenido_widget_" + id_widget).width(anchura_widget);
    }

    // Comprobación de error
    var datos_widget = jQuery.parseJSON(datos);
    var hay_error = (datos_widget.res == "ERROR");
    if (hay_error == true) {
        establece_contenido_widget_error(id_widget, datos_widget);
        return;
    }

    // Se dibuja el contenido del widget
    switch (tipo) {
        // Widgets "generales" (sin módulo asociado)
        case TIPO_WIDGET_IMAGEN: {
            dibuja_widget_tipo_imagen(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        // Widgets del módulo Localizaciones
        case TIPO_WIDGET_VALOR_RATIO: {
            dibuja_widget_tipo_valor_ratio(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        // Widgets del módulo Sensores
        case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
        case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR: {
            dibuja_widget_tipo_valor_digital_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
        case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR: {
            dibuja_widget_tipo_valor_analogico_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_SENSOR: {
            dibuja_widget_tipo_grafica_valores_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_MAPA_CALOR_SENSOR: {
            dibuja_widget_tipo_mapa_calor_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR: {
            dibuja_widget_tipo_grafica_comparacion_periodos_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR: {
            dibuja_widget_tipo_evolucion_valores_comparacion_periodos_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES: {
            dibuja_widget_tipo_grafica_comparacion_campos_iguales_sensores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES: {
            dibuja_widget_tipo_grafica_comparacion_campos_diferentes_sensores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES: {
            dibuja_widget_tipo_grafica_valores_generales_sensores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES: {
            dibuja_widget_tipo_valor_agregado_valores_generales_sensores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES: {
            dibuja_widget_tipo_grafica_incrementos_totales_sensores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        // Widgets del módulo Actuadores
        case TIPO_WIDGET_INFORMACION_ACTUADOR:
        case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES: {
            dibuja_widget_tipo_informacion_actuador_grupo_actuadores(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        // Widgets del módulo Smartmeter
        case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR: {
            dibuja_widget_tipo_grafica_consumos_costes_tramos_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_COSTE_FACTURA_SENSOR: {
            dibuja_widget_tipo_coste_factura_sensor(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        // Widgets del módulo Proyectos
        case TIPO_WIDGET_SIMULADOR_LINEA_BASE: {
            dibuja_widget_tipo_simulador_linea_base(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
        case TIPO_WIDGET_INFORMACION_PROYECTO: {
            dibuja_widget_tipo_informacion_proyecto(
                id_pestanya,
                id_widget,
                nombre,
                tipo,
                parametros_tipo,
                datos,
                numero_columnas_fila_widget,
                numero_columnas_widget);
            break;
        }
    }
}


//
// Funciones auxiliares
//


// Dibuja el icono del widget
function dibuja_icono_widget(
    id_widget,
    icono,
    color_icono,
    transparencia_icono) {
    if (icono == ID_NINGUNO) {
        return;
    }

    // Se dibuja el icono del widget
    var sufijo_icono = null;
    switch (color_icono) {
        case COLOR_ICONO_WIDGET_BLANCO: {
            sufijo_icono = "blanco";
            break;
        }
        case COLOR_ICONO_WIDGET_NEGRO: {
            sufijo_icono = "negro";
            break;
        }
    }
    $("#icono_widget_" + id_widget).css('background-image', 'url("./rsc/imagenes/widget-' + icono + "-" + sufijo_icono + '.png")');
    $("#icono_widget_" + id_widget).css('background-repeat', 'no-repeat');
    $("#icono_widget_" + id_widget).css('background-size', 'contain');
    $("#icono_widget_" + id_widget).css('background-position', 'right');
    $("#icono_widget_" + id_widget).css('opacity', (1 - transparencia_icono));
}


// Establece el contenido del widget con error
function establece_contenido_widget_error(id_widget, datos_widget) {
    // Datos del widget
    var mensaje_error_widget = datos_widget.msg;

    // Parámetros de apariencia de widgets
    var parametros_apariencia_widgets = datos_widget.parametros_apariencia_widgets;
    var estilo_fuente = parametros_apariencia_widgets.estilo_fuente;
    var modificar_colores_titulo = parametros_apariencia_widgets.modificar_colores_titulo;
    var color_fondo_titulo = parametros_apariencia_widgets.color_fondo_titulo;
    var transparencia_fondo_titulo = parametros_apariencia_widgets.transparencia_fondo_titulo;
    var modificar_colores = parametros_apariencia_widgets.modificar_colores;
    var color = parametros_apariencia_widgets.color;
    var color_fondo = parametros_apariencia_widgets.color_fondo;
    var transparencia_fondo = parametros_apariencia_widgets.transparencia_fondo;

    // Colores de fondo del widget
    if (modificar_colores_titulo == VALOR_NO) {
        color_fondo_titulo = color_tema_oscuro;
    }
    if (modificar_colores == VALOR_NO) {
        color = COLOR_NEGRO;
        color_fondo = color_tema_fondo;
    }

    var html_icono_mensaje = "<i class='icon-warning-sign color-rojo'></i>";
    var mensaje = TLNT.Idiomas._("Error");
    var color_fondo_mensaje = COLOR_FONDO_WIDGET_ERROR;
    var html_elementos_adicionales = null;
    if ((mensaje_error_widget != null) && (mensaje_error_widget != "")) {
        html_elementos_adicionales = "<div class='mensaje-error-widget'>" + "(" + mensaje_error_widget.toLowerCase() + ")" + "</div>";
    }
    establece_contenido_widget_mensaje(
        id_widget,
        estilo_fuente,
        color_fondo_titulo,
        transparencia_fondo_titulo,
        modificar_colores,
        color,
        color_fondo,
        transparencia_fondo,
        html_icono_mensaje,
        mensaje,
        color_fondo_mensaje,
        html_elementos_adicionales);
}


// Establece el contenido del widget sin datos
function establece_contenido_widget_sin_datos(
    id_widget,
    estilo_fuente,
    color_fondo_titulo,
    transparencia_fondo_titulo,
    modificar_colores,
    color,
    color_fondo,
    transparencia_fondo,
    html_elementos_adicionales) {
    var html_icono_mensaje = "<i class='icon-info-sign color-azul'></i>";
    var mensaje = TLNT.Idiomas._("Sin datos");
    var color_fondo_mensaje = COLOR_FONDO_WIDGET_SIN_DATOS;
    establece_contenido_widget_mensaje(
        id_widget,
        estilo_fuente,
        color_fondo_titulo,
        transparencia_fondo_titulo,
        modificar_colores,
        color,
        color_fondo,
        transparencia_fondo,
        html_icono_mensaje,
        mensaje,
        color_fondo_mensaje,
        html_elementos_adicionales);
}


// Establece el contenido del widget con un mensaje
function establece_contenido_widget_mensaje(
    id_widget,
    estilo_fuente,
    color_fondo_titulo,
    transparencia_fondo_titulo,
    modificar_colores,
    color,
    color_fondo,
    transparencia_fondo,
    html_icono_mensaje,
    mensaje,
    color_fondo_mensaje,
    html_elementos_adicionales) {
    // Contenido del widget
    var html_contenido_widget = "";
    html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
    html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
    html_contenido_widget += "        <div id='texto-mensaje-widget__" + id_widget + "' class='texto-mensaje-widgets'>";
    html_contenido_widget += html_icono_mensaje + " " + mensaje;
    html_contenido_widget += "        </div>";
    if (html_elementos_adicionales != null) {
        html_contenido_widget += html_elementos_adicionales;
    }
    html_contenido_widget += "    </div>";
    html_contenido_widget += "</div>";
    html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

    // Se actualiza el contenido del widget
    $("#contenido_widget_" + id_widget).html(html_contenido_widget);

    // Colores del widget
    if (modificar_colores == VALOR_NO) {
        color_fondo = color_fondo_mensaje;
    }
    $("#texto-mensaje-widget__" + id_widget).css('color', color);

    // Estilo de fuente
    if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
        $("#texto-mensaje-widget__" + id_widget).css('font-weight', 'bold');
    }

    // Se establece el color de fondo del widget
    establece_color_fondo_widget(
        id_widget,
        color_fondo_titulo,
        transparencia_fondo_titulo,
        color_fondo,
        transparencia_fondo);
}


// Establece el color de fondo del widget (y del título si el fondo es transparente)
function establece_color_fondo_widget(
    id_widget,
    color_fondo_titulo,
    transparencia_fondo_titulo,
    color_fondo,
    transparencia_fondo) {
    if (transparencia_fondo_titulo != 0) {
        color_fondo_titulo = convierte_color_hexadecimal_rgb(color_fondo_titulo, transparencia_fondo_titulo);
    }
    if (transparencia_fondo != 0) {
        color_fondo = convierte_color_hexadecimal_rgb(color_fondo, transparencia_fondo);
    }
    $("#widget_" + id_widget).css('background-color', color_fondo);
    if (transparencia_fondo_titulo != 1) {
        $("#titulo_widget_" + id_widget).css('background-color', color_fondo_titulo);
    }
}


//
// Dibujado de widgets "generales" (sin módulo asociado)
//


// Dibuja un widget de tipo 'Imagen'
function dibuja_widget_tipo_imagen(
    id_pestanya,
    id_widget,
    nombre_widget,
    tipo,
    parametros_tipo,
    datos,
    numero_columnas_fila_widget,
    numero_columnas_widget) {
    // Parámetros de tipo
    var altura_maxima = parametros_tipo["altura_maxima"];
    if (altura_maxima == "") {
        altura_maxima = ALTURA_MAXIMA_IMAGEN_WIDGET;
    }

    // Datos del widget
    var datos_widget = jQuery.parseJSON(datos);

    // Parámetros de apariencia de widgets
    var parametros_apariencia_widgets = datos_widget.parametros_apariencia_widgets;
    var modificar_colores_titulo = parametros_apariencia_widgets.modificar_colores_titulo;
    var color_fondo_titulo = parametros_apariencia_widgets.color_fondo_titulo;
    var transparencia_fondo_titulo = parametros_apariencia_widgets.transparencia_fondo_titulo;
    var modificar_colores = parametros_apariencia_widgets.modificar_colores;
    var color_fondo = parametros_apariencia_widgets.color_fondo;
    var transparencia_fondo = parametros_apariencia_widgets.transparencia_fondo;

    // Colores de fondo del widget
    if (modificar_colores_titulo == VALOR_NO) {
        color_fondo_titulo = color_tema_oscuro;
    }
    if (modificar_colores == VALOR_NO) {
        color_fondo = color_tema_fondo;
    }

    // Se carga la imagen en el servidor
    var id_origen = [
        id_pestanya,
        id_widget].join(SEPARADOR_PARAMETROS_SIMPLES);
    var res_carga_imagen = carga_imagen_base_datos(ORIGEN_IMAGEN_WIDGET_IMAGEN, id_origen, null);
    var imagen_cargada_correcta = res_carga_imagen.imagen_cargada_correcta;
    if (imagen_cargada_correcta == false) {
        var datos_widget = jQuery.parseJSON(datos);
        var hay_error = (datos_widget.res == "ERROR");
        if (hay_error == true) {
            datos_widget.msg = TLNT.Idiomas._("Ha ocurrido un error al cargar la imagen");
            establece_contenido_widget_error(id_widget, datos_widget);
            return;
        }
    }
    var ruta_fichero_imagen = res_carga_imagen.ruta_fichero_imagen;

    // Contenido del widget (valor y fecha del ratio)
    var html_contenido_widget = "";
    html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
    html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
    html_contenido_widget += "        <div class='contenedor-imagen-widget'>";
    html_contenido_widget += "            <a style='text-decoration: none;'>";
    html_contenido_widget += "                <img class='imagen-informe' style='max-height: " + altura_maxima + "px;' src='" + ruta_fichero_imagen + "'>";
    html_contenido_widget += "            </a>";
    html_contenido_widget += "        </div>";
    html_contenido_widget += "    </div>";
    html_contenido_widget += "</div>";

    // Se actualiza el contenido del widget
    $("#contenido_widget_" + id_widget).html(html_contenido_widget);

    // Se establece el color de fondo del widget
    establece_color_fondo_widget(
        id_widget,
        color_fondo_titulo,
        transparencia_fondo_titulo,
        color_fondo,
        transparencia_fondo);
}

