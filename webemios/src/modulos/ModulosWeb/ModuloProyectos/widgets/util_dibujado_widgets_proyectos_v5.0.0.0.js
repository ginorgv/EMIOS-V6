/*
 * Dibujado de widgets
 *
 */


// Dibuja un widget de tipo 'Simulador de línea base'
function dibuja_widget_tipo_simulador_linea_base(
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
    var transparencia_fondo_graficas = parametros_apariencia_widgets.transparencia_fondo_graficas;

    // Colores de fondo del widget
    if (modificar_colores_titulo == VALOR_NO) {
        color_fondo_titulo = color_tema_oscuro;
    }
    if (modificar_colores == VALOR_NO) {
        color = COLOR_NEGRO;
        color_fondo = color_tema_fondo;
    }

    // Comprobación de widget sin datos
    var sin_datos = (datos_widget.hay_datos == false);
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
        var fecha_hora_inicio_consulta = datos_widget.fecha_hora_inicio_consulta;
        var fecha_hora_fin_consulta = datos_widget.fecha_hora_fin_consulta;
        var grafica_valores = datos_widget.grafica_valores;
        var bandas_valores = datos_widget.bandas_valores;
        var min_valor = datos_widget.min_valor;
        var max_valor = datos_widget.max_valor;
        var etiquetas_valores = datos_widget.etiquetas_valores;
        var numero_decimales_valores = datos_widget.numero_decimales_valores;
        var unidad_medida = datos_widget.unidad_medida;
        var intervalo_valores = datos_widget.intervalo_valores;

        // Parámetros extra para el dibujado de widget
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_simulador_linea_base__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_widget_clase_altura_contenido_widget > 2) {
            mostrar_etiquetas = false;
        }
        else {
            mostrar_etiquetas = true;
        }
        var numero_maximo_valores_grafica_indicador_visible = null;
        switch (numero_columnas_widget_clase_altura_contenido_widget) {
            case 1: {
                numero_maximo_valores_grafica_indicador_visible = NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE_WIDGETS_GRAFICA_GRANDE;
                break;
            }
            case 2: {
                numero_maximo_valores_grafica_indicador_visible = NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE_WIDGETS_GRAFICA_MEDIANA;
                break;
            }
            default: {
                numero_maximo_valores_grafica_indicador_visible = NUMERO_MAXIMO_VALORES_GRAFICA_INDICADOR_VISIBLE_WIDGETS_GRAFICA_PEQUENYA;
                break;
            }
        }
        var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
        var mostrar_indicadores_valores = (numero_valores_grafica_valores <= numero_maximo_valores_grafica_indicador_visible);

        // Contenido del widget (gráfica y fechas)
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='" + div_grafica + "' class='grafica-widgets-graficas " + clase_altura_grafica_widgets + "'></div>";
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='fechas-horas-widget-grafica__" + id_widget + "' class='fechas-horas-widgets-graficas'>";
            html_contenido_widget += html_cadena_horas_valores;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color de las fechas
        $("#fechas-horas-widget-grafica__" + id_widget).css('color', color);

        // Se añade un margen inferior si no se muestran las fechas
        if (mostrar_fechas == VALOR_NO) {
            $("#" + div_grafica).css('padding-bottom', '0.5em');
        }

        // Fecha inicial y final (para los ejes x de las gráficas)
        var fecha_hora_inicio_consulta = new $.jsDate(fecha_hora_inicio_consulta);
        var fecha_hora_fin_consulta = new $.jsDate(fecha_hora_fin_consulta);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Se dibuja la gráfica
        muestra_grafica_temporal_lineas_valores_widgets(
            nombre_widget,
            div_grafica,
            etiquetas_valores, mostrar_etiquetas,
            grafica_valores, bandas_valores, intervalo_valores,
            fecha_hora_inicio_consulta, fecha_hora_fin_consulta, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            null,
            true,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            color_ticks_eje_y,
            transparencia_fondo_graficas,
            false);

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Información de proyecto'
function dibuja_widget_tipo_informacion_proyecto(
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
    var estado_avance_proyecto = datos_widget.estado_avance_proyecto;
    var estado_proyecto = datos_widget.estado_proyecto;
    var cadena_hora_inicio = datos_widget.cadena_hora_inicio;
    var cadena_hora_ultimo_calculo_avance = datos_widget.cadena_hora_ultimo_calculo_avance;
    var cadena_hora_fin_valores_avance = datos_widget.cadena_hora_fin_valores_avance;
    var descripcion_avance_proyecto = datos_widget.descripcion_avance_proyecto;
    var descripcion_estado_proyecto = datos_widget.descripcion_estado_proyecto;
    var clase_tamanyo_fuente_avance_proyecto_widget = datos_widget.clase_tamanyo_fuente_avance_proyecto_widget;
    var clase_tamanyo_fuente_estado_proyecto_widget = datos_widget.clase_tamanyo_fuente_estado_proyecto_widget;
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

    // Contenido del widget (avance y estado del proyecto y fecha de últimos valores de avance)
    var sin_datos = null;
    var html_contenido_widget = "";
    switch (estado_proyecto) {
        case ESTADO_PROYECTO_NINGUNO:
        case ESTADO_PROYECTO_SIN_LINEA_BASE:
        case ESTADO_PROYECTO_ERROR:
        case ESTADO_PROYECTO_PENDIENTE: {
            sin_datos = true;
            var html_estado_proyecto = null;
            switch (estado_proyecto) {
                case ESTADO_PROYECTO_NINGUNO: {
                    html_estado_proyecto = "<i class='icon-info-sign color-azul'></i> " + TLNT.Idiomas._("Sin datos");
                    break;
                }
                case ESTADO_PROYECTO_SIN_LINEA_BASE: {
                    html_estado_proyecto = "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Sin línea base");
                    break;
                }
                case ESTADO_PROYECTO_ERROR: {
                    html_estado_proyecto = "<i class='icon-warning-sign color-rojo'></i> " + TLNT.Idiomas._("Error");
                    break;
                }
                case ESTADO_PROYECTO_PENDIENTE: {
                    html_estado_proyecto = "<i class='icon-time color-gris'></i> " + TLNT.Idiomas._("Pendiente");
                    break;
                }
            }
            html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
            html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
            html_contenido_widget += "        <div id='texto-sin-datos-widget-informacion-proyecto__" + id_widget + "' class='texto-sin-datos-widget-informacion-proyecto'>";
            html_contenido_widget += html_estado_proyecto;
            html_contenido_widget += "        </div>";
            if ((mostrar_fechas == VALOR_SI) && (cadena_hora_ultimo_calculo_avance != null)) {
                html_contenido_widget += "    <div id='fecha-hora-widget-informacion-proyecto__" + id_widget + "' class='fecha-hora-widget-informacion-proyecto'>";
                html_contenido_widget += "(" + cadena_hora_ultimo_calculo_avance + ")";
                html_contenido_widget += "    </div>";
            }
            html_contenido_widget += "    </div>";
            html_contenido_widget += "</div>";
            break;
        }
        case ESTADO_PROYECTO_ACTIVO:
        case ESTADO_PROYECTO_FINALIZADO: {
            sin_datos = false;
            html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
            html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
            html_contenido_widget += "        <div class='avance-proyecto-widget-informacion-proyecto'>";
            html_contenido_widget += "            <div id='texto-avance-proyecto-widget-informacion-proyecto__" + id_widget + "' class='texto-avance-proyecto-widget-informacion-proyecto " + clase_tamanyo_fuente_avance_proyecto_widget + "'>";
            html_contenido_widget += descripcion_avance_proyecto;
            html_contenido_widget += "            </div>";
            html_contenido_widget += "        </div>";
            html_contenido_widget += "        <div class='estado-proyecto-widget-informacion-proyecto'>";
            html_contenido_widget += "            <div id='texto-estado-proyecto-widget-informacion-proyecto__" + id_widget + "' class='texto-estado-proyecto-widget-informacion-proyecto " + clase_tamanyo_fuente_estado_proyecto_widget + "'>";
            html_contenido_widget += descripcion_estado_proyecto;
            html_contenido_widget += "            </div>";
            html_contenido_widget += "        </div>";
            if ((mostrar_fechas == VALOR_SI) && (cadena_hora_fin_valores_avance != null)) {
                html_contenido_widget += "    <div id='fecha-hora-widget-informacion-proyecto__" + id_widget + "' class='fecha-hora-widget-informacion-proyecto'>";
                html_contenido_widget += "(" + cadena_hora_inicio + "), (" + cadena_hora_fin_valores_avance + ")";
                html_contenido_widget += "    </div>";
            }
            html_contenido_widget += "    </div>";
            html_contenido_widget += "</div>";
            break;
        }
    }
    html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

    // Se actualiza el contenido del widget
    $("#contenido_widget_" + id_widget).html(html_contenido_widget);

    // Se añade un margen inferior si no se muestran las fechas
    if ((mostrar_fechas == VALOR_NO) && (sin_datos == false)) {
        $("#texto-estado-proyecto-widget-informacion-proyecto__" + id_widget).css('padding-bottom', '0.2em');
    }

    // Color y fuente del widget
    $("#texto-sin-datos-widget-informacion-proyecto__" + id_widget).css('color', color);
    $("#texto-avance-proyecto-widget-informacion-proyecto__" + id_widget).css('color', color);
    $("#texto-estado-proyecto-widget-informacion-proyecto__" + id_widget).css('color', color);
    $("#fecha-hora-widget-informacion-proyecto__" + id_widget).css('color', color);
    if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
        $("#texto-sin-datos-widget-informacion-proyecto__" + id_widget).css('font-weight', "bold");
        $("#texto-avance-proyecto-widget-informacion-proyecto__" + id_widget).css('font-weight', "bold");
    }

    // Color de fondo del widget (se modifica el título del widget si es transparente)
    if (modificar_colores == VALOR_NO) {
        switch (estado_avance_proyecto) {
            case ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO: {
                color_fondo = COLOR_AZUL_WIDGET_PROYECTO;
                break;
            }
            case ESTADO_AVANCE_PROYECTO_POSITIVO: {
                color_fondo = COLOR_VERDE_WIDGET_PROYECTO;
                break;
            }
            case ESTADO_AVANCE_PROYECTO_NEGATIVO: {
                color_fondo = COLOR_ROJO_WIDGET_PROYECTO;
                break;
            }
        }
    }

    // Se establece el color de fondo del widget
    establece_color_fondo_widget(
        id_widget,
        color_fondo_titulo,
        transparencia_fondo_titulo,
        color_fondo,
        transparencia_fondo);

    // Icono del widget
    dibuja_icono_widget(
        id_widget,
        icono,
        color_icono,
        transparencia_icono);
}