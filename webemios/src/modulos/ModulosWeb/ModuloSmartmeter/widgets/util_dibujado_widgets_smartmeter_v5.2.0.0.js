/*
 * Dibujado de widgets
 *
 */


// Dibuja un widget de tipo 'Gráfica de consumos y costes por tramo de un sensor'
function dibuja_widget_tipo_grafica_consumos_costes_tramos_sensor(
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
        var nombres_tramos = datos_widget.nombres_tramos;
        var min_hora_grafica = datos_widget.min_hora_grafica;
        var max_hora_grafica = datos_widget.max_hora_grafica;
        var grafica_consumos_tramos_horarios = datos_widget.grafica_consumos_tramos_horarios;
        var grafica_costes_tramos_horarios = datos_widget.grafica_costes_tramos_horarios;
        var numero_huecos_datos_consumos_costes_tramos_horarios = datos_widget.numero_huecos_datos_consumos_costes_tramos_horarios;
        var max_consumo_hora = datos_widget.max_consumo_hora;
        var max_coste_hora = datos_widget.max_coste_hora;
        var min_fecha_grafica = datos_widget.min_fecha_grafica;
        var max_fecha_grafica = datos_widget.max_fecha_grafica;
        var grafica_consumos_tramos_diarios = datos_widget.grafica_consumos_tramos_diarios;
        var grafica_costes_tramos_diarios = datos_widget.grafica_costes_tramos_diarios;
        var max_consumo_dia = datos_widget.max_consumo_dia;
        var max_coste_dia = datos_widget.max_coste_dia;
        var grafica_medias_consumos_tramos_dias_semana = datos_widget.grafica_medias_consumos_tramos_dias_semana;
        var grafica_medias_costes_tramos_dias_semana = datos_widget.grafica_medias_costes_tramos_dias_semana;
        var max_media_consumo_dia_semana = datos_widget.max_media_consumo_dia_semana;
        var max_media_coste_dia_semana = datos_widget.max_media_coste_dia_semana;
        var unidad_medida_consumo = datos_widget.unidad_medida_consumo;
        var unidad_medida_coste = datos_widget.unidad_medida_coste;

        // Parámetros extra para el dibujado de widget
        var valor = datos_widget.valor;
        var agrupacion_valores = datos_widget.agrupacion_valores;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Se recupera la gráfica y el valor máximo correspondiente
        var grafica_consumos_costes_tramos = null;
        var numero_huecos_consumos_costes_tramos = null;
        var max_consumo_coste_tramos = null;
        var unidad_medida_consumo_coste = null;
        switch (valor) {
            case VALOR_CONSUMO: {
                switch (agrupacion_valores) {
                    case AGRUPACION_VALORES_HORA: {
                        grafica_consumos_costes_tramos = grafica_consumos_tramos_horarios;
                        numero_huecos_consumos_costes_tramos = numero_huecos_datos_consumos_costes_tramos_horarios;
                        max_consumo_coste_tramos = max_consumo_hora;
                        break;
                    }
                    case AGRUPACION_VALORES_FECHA: {
                        grafica_consumos_costes_tramos = grafica_consumos_tramos_diarios;
                        numero_huecos_consumos_costes_tramos = 0;
                        max_consumo_coste_tramos = max_consumo_dia;
                        break;
                    }
                    case AGRUPACION_VALORES_DIA_SEMANA: {
                        grafica_consumos_costes_tramos = grafica_medias_consumos_tramos_dias_semana;
                        numero_huecos_consumos_costes_tramos = 0;
                        max_consumo_coste_tramos = max_media_consumo_dia_semana;
                        break;
                    }
                }
                unidad_medida_consumo_coste = unidad_medida_consumo;
                break;
            }
            case VALOR_COSTE: {
                switch (agrupacion_valores) {
                    case AGRUPACION_VALORES_HORA: {
                        grafica_consumos_costes_tramos = grafica_costes_tramos_horarios;
                        numero_huecos_consumos_costes_tramos = numero_huecos_datos_consumos_costes_tramos_horarios;
                        max_consumo_coste_tramos = max_coste_hora;
                        break;
                    }
                    case AGRUPACION_VALORES_FECHA: {
                        grafica_consumos_costes_tramos = grafica_costes_tramos_diarios;
                        numero_huecos_consumos_costes_tramos = 0;
                        max_consumo_coste_tramos = max_coste_dia;
                        break;
                    }
                    case AGRUPACION_VALORES_DIA_SEMANA: {
                        grafica_consumos_costes_tramos = grafica_medias_costes_tramos_dias_semana;
                        numero_huecos_consumos_costes_tramos = 0;
                        max_consumo_coste_tramos = max_media_coste_dia_semana;
                        break;
                    }
                }
                unidad_medida_consumo_coste = unidad_medida_coste;
                break;
            }
        }

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_consumos_costes_tramos_sensor__" + id_widget;

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

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Se dibuja la gráfica correspondiente
        switch (agrupacion_valores) {
            case AGRUPACION_VALORES_HORA: {
                // Hora inicial y final (para los ejes x de las gráficas)
                var min_hora_grafica = new $.jsDate(min_hora_grafica);
                var max_hora_grafica = new $.jsDate(max_hora_grafica);

                // Gráfica de barras de consumos y costes por hora
                muestra_grafica_temporal_barras_valores_widgets(
                    nombre_widget,
                    div_grafica,
                    nombres_tramos, mostrar_etiquetas,
                    grafica_consumos_costes_tramos, INTERVALO_VALORES_HORA, numero_huecos_consumos_costes_tramos,
                    min_hora_grafica, max_hora_grafica, true,
                    max_consumo_coste_tramos, true,
                    2, unidad_medida_consumo_coste,
                    true, false,
                    color_ticks_eje_y,
                    transparencia_fondo_graficas);
                break;
            }
            case AGRUPACION_VALORES_FECHA: {
                // Fecha inicial y final (para los ejes x de las gráficas)
                var min_fecha_grafica = new $.jsDate(min_fecha_grafica);
                var max_fecha_grafica = new $.jsDate(max_fecha_grafica);

                // Gráfica de barras de consumos y costes por fecha
                muestra_grafica_temporal_barras_valores_widgets(
                    nombre_widget,
                    div_grafica,
                    nombres_tramos, mostrar_etiquetas,
                    grafica_consumos_costes_tramos, INTERVALO_VALORES_DIA, numero_huecos_consumos_costes_tramos,
                    min_fecha_grafica, max_fecha_grafica, true,
                    max_consumo_coste_tramos, true,
                    2, unidad_medida_consumo_coste,
                    true, false,
                    color_ticks_eje_y,
                    transparencia_fondo_graficas);
                break;
            }
            case AGRUPACION_VALORES_DIA_SEMANA: {
                // Ticks de los días de la semana
                var nombres_dias_semana = dame_nombres_dias_semana();

                // Gráfica de barras de consumos y costes por día de la semana
                muestra_grafica_puntual_barras_valores_widgets(
                    nombre_widget,
                    div_grafica,
                    nombres_tramos, mostrar_etiquetas,
                    grafica_consumos_costes_tramos,
                    nombres_dias_semana, [6, 7],
                    max_consumo_coste_tramos, true,
                    2, unidad_medida_consumo_coste,
                    true, false,
                    color_ticks_eje_y,
                    transparencia_fondo_graficas);
                break;
            }
        }

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Coste de factura de un sensor'
function dibuja_widget_tipo_coste_factura_sensor(
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
    var sin_datos = false;
    var html_icono_mensaje = null;
    var mensaje = null;
    var color_fondo_mensaje = COLOR_FONDO_WIDGET_SIN_DATOS;
    if (sin_datos == false) {
        var tarifa_asignada = datos_widget.tarifa_asignada;
        if (tarifa_asignada == false) {
            sin_datos = true;
            html_icono_mensaje = "<i class='icon-warning-sign color-rojo'></i>";
            mensaje = TLNT.Idiomas._("Sin tarifa eléctrica asignada");
        }
    }
    if (sin_datos == false) {
        var hay_datos = datos_widget.hay_datos;
        if (hay_datos == false) {
            sin_datos = true;
            html_icono_mensaje = "<i class='icon-info-sign color-azul'></i>";"";
            mensaje = TLNT.Idiomas._("Sin datos");
        }
    }
    if (sin_datos == true) {
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
            null);
    }
    else {
        // Datos del widget
        var html_cadena_coste = datos_widget.html_cadena_coste;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;
        var colores_fondo = datos_widget.colores_fondo;
        var indice_color_fondo = datos_widget.indice_color_fondo;

        // Contenido del widget
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='coste_widget-coste-factura-sensor__" + id_widget + "' class='valor-unidad-widget-valor-digital-sensor'>";
        html_contenido_widget += html_cadena_coste;
        html_contenido_widget += "        </div>";
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='horas-widget-coste-factura-sensor__" + id_widget + "' class='fecha-hora-widget-valor-digital-sensor'>";
            html_contenido_widget += html_cadena_horas_valores;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color y fuente del widget
        $("#coste_widget-coste-factura-sensor__" + id_widget).css('color', color);
        $("#horas-widget-coste-factura-sensor__" + id_widget).css('color', color);
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#coste_widget-coste-factura-sensor__" + id_widget).css('font-weight', "bold");
        }

        // Color de fondo del widget
        if (indice_color_fondo != ID_NINGUNO) {
            color_fondo = colores_fondo[indice_color_fondo];
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

