/*
 * Dibujado de widgets
 *
 */


// Dibuja un widget de tipo 'Valor digital de un sensor'
function dibuja_widget_tipo_valor_digital_sensor(
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
    var html_boton_envio_valores_manuales = datos_widget.html_boton_envio_valores_manuales;
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
    var sin_datos = datos_widget.sin_valores;
    if (sin_datos == true) {
        var html_elementos_adicionales = null;
        if ((mostrar_botones == VALOR_SI) && (html_boton_envio_valores_manuales != "")) {
            html_elementos_adicionales = html_boton_envio_valores_manuales;
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
        var html_cadena_valores = datos_widget.html_cadena_valores;
        var html_cadena_hora_valores = datos_widget.html_cadena_hora_valores;
        var timeout_envio_activado = datos_widget.timeout_envio_activado;
        var hay_eventos_alarma_activados = datos_widget.hay_eventos_alarma_activados;
        var colores_fondo = datos_widget.colores_fondo;
        var indice_color_fondo = datos_widget.indice_color_fondo;

        // Iconos del sensor (después de la fecha de valores)
        var html_iconos_sensor = "";
        if (timeout_envio_activado == true) {
            html_iconos_sensor += "<i class='icon-bell-alt color-rojo'></i>";
        }
        if (hay_eventos_alarma_activados == true) {
            if (html_iconos_sensor != "") {
                html_iconos_sensor += " ";
            }
            html_iconos_sensor += "<i class='icon-warning-sign color-rojo'></i>";
        }

        // Contenido del widget (valores, fecha e iconos del sensor)
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='valor-unidad-widget-valor-digital-sensor__" + id_widget + "' class='valor-unidad-widget-valor-digital-sensor'>";
        html_contenido_widget += html_cadena_valores;
        html_contenido_widget += "        </div>";
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='fecha-hora-widget-valor-digital-sensor__" + id_widget + "' class='fecha-hora-widget-valor-digital-sensor'>";
            html_contenido_widget += html_cadena_hora_valores;
            if (html_iconos_sensor != "") {
                html_contenido_widget += " [" + html_iconos_sensor + "]";
            }
            html_contenido_widget += "    </div>";
        }
        if ((mostrar_botones == VALOR_SI) && (html_boton_envio_valores_manuales != "")) {
            html_contenido_widget += html_boton_envio_valores_manuales;
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


// Dibuja un widget de tipo 'Valor analógico de un sensor'
function dibuja_widget_tipo_valor_analogico_sensor(
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
        var tipo_grafico = datos_widget.tipo_grafico;
        var valor_sensor = datos_widget.valor_sensor;
        var cadena_valor_sensor = datos_widget.cadena_valor_sensor;
        var unidad_sensor = datos_widget.unidad_sensor;
        var cadena_hora_valor = datos_widget.cadena_hora_valor;
        var valor_minimo_indicador = parseInt(datos_widget.valor_minimo_indicador);
        var valor_maximo_indicador = parseInt(datos_widget.valor_maximo_indicador);
        var colores_fondo = datos_widget.colores_fondo;
        var valor_limite_colores_fondo_1 = parseFloat(datos_widget.valor_limite_colores_fondo_1);
        var valor_limite_colores_fondo_2 = parseFloat(datos_widget.valor_limite_colores_fondo_2);
        var valor_digital = datos_widget.valor_digital;
        var timeout_envio_activado = datos_widget.timeout_envio_activado;
        var hay_eventos_alarma_activados = datos_widget.hay_eventos_alarma_activados;
        var indice_color_fondo = datos_widget.indice_color_fondo;

        // Iconos del sensor (después de la fecha de valores)
        var html_iconos_sensor = "";
        if (timeout_envio_activado == true) {
            html_iconos_sensor += "<i class='icon-bell-alt color-rojo'></i>";
        }
        if (hay_eventos_alarma_activados == true) {
            if (html_iconos_sensor != "") {
                html_iconos_sensor += " ";
            }
            html_iconos_sensor += "<i class='icon-warning-sign color-rojo'></i>";
        }

        // Color de fondo del widget
        switch (tipo_grafico) {
            case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ: {
                if (indice_color_fondo != ID_NINGUNO) {
                    color_fondo = colores_fondo[indice_color_fondo];
                }
            }
        }

        // Elemento (div) para la gráfica de valor analógico
        var div_grafica = "indicador_widget_valor_analogico_sensor__" + id_widget;

        // Nota: Altura dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_indicador_widget_reloj_sin_valor_digital = "altura-indicador-widget-reloj-sin-valor-digital-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var clase_altura_indicador_widget_reloj_con_valor_digital = "altura-indicador-widget-reloj-con-valor-digital-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var clase_altura_indicador_widget_circular = "altura-indicador-widget-circular-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;

        // Se rellena el contenido del widget
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";

        // Contenido del gráfico del widget
        switch (tipo_grafico) {
            case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ: {
                switch (valor_digital) {
                    case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO: {
                        html_contenido_widget += "<div id='" + div_grafica + "' class='indicador-widget-valor-analogico-sensor-reloj-sin-valor-digital " +
                            clase_altura_indicador_widget_reloj_sin_valor_digital + "'></div>";
                        break;
                    }
                    case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO: {
                        var anchura_widget = $("#contenido_widget_" + id_widget).width();
                        html_contenido_widget += "<div id='" + div_grafica + "' class='indicador-widget-valor-analogico-sensor-reloj-con-valor-digital " +
                            clase_altura_indicador_widget_reloj_con_valor_digital + "'></div>";
                        html_contenido_widget += "<div id='valor-unidad-widget-valor-analogico-sensor-reloj__" + id_widget + "' class='valor-unidad-widget-valor-analogico-sensor-reloj' style='max-width: " + anchura_widget + "px'>" + cadena_valor_sensor;
                        if (unidad_sensor != "") {
                            html_contenido_widget += " " + "<span class='texto-pequenyo-widget-valor-digital-sensor'>" + unidad_sensor + "</span>";
                        }
                        html_contenido_widget += "</div>";
                        break;
                    }
                }
                break;
            }
            case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR: {
                html_contenido_widget += "<div id='" + div_grafica + "' " + "class='indicador-widget-valor-analogico-sensor-circular " +
                    clase_altura_indicador_widget_circular + "'></div>";
                break;
            }
        }

        // Hora de últimos valores
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "<div id='fecha-hora-widget-valor-analogico-sensor__" + id_widget + "' class='fecha-hora-widget-valor-analogico-sensor'>" + cadena_hora_valor;
            if (html_iconos_sensor != "") {
                html_contenido_widget += " [" + html_iconos_sensor + "]";
            }
            html_contenido_widget += "</div>";
        }

        // Fin de contenido de widget e icono del widget
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Se añade un margen inferior si no se muestran las fechas
        if (mostrar_fechas == VALOR_NO) {
            switch (tipo_grafico) {
                case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ: {
                    switch (valor_digital) {
                        case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO: {
                            $("#indicador_widget_valor_analogico_sensor__" + id_widget).css('padding-bottom', '0.75em');
                            break;
                        }
                        case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO: {
                            $("#valor-unidad-widget-valor-analogico-sensor-reloj__" + id_widget).css('padding-bottom', '0.3em');
                            break;
                        }
                    }
                    break;
                }
                case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR: {
                    $("#indicador_widget_valor_analogico_sensor__" + id_widget).css('padding-bottom', '0.75em');
                    break;
                }
            }
        }

        // Color y fuente del widget
        if (modificar_colores == VALOR_SI) {
            $("#valor-unidad-widget-valor-analogico-sensor-reloj__" + id_widget).css('color', color);
            $("#fecha-hora-widget-valor-analogico-sensor__" + id_widget).css('color', color);
        }
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#valor-unidad-widget-valor-analogico-sensor-reloj__" + id_widget).css('font-weight', "bold");
        }

        // Valor del sensor dentro del rango de valores del indicador
        if (valor_sensor < valor_minimo_indicador) {
            valor_sensor = valor_minimo_indicador;
        }
        else {
            if (valor_sensor > valor_maximo_indicador) {
                valor_sensor = valor_maximo_indicador;
            }
        }

        // Se dibuja el gráfico del valor analógico
        switch (tipo_grafico) {
            case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_RELOJ: {
                muestra_grafico_valor_analogico_reloj_widgets(
                    div_grafica,
                    valor_sensor, unidad_sensor,
                    valor_minimo_indicador, valor_maximo_indicador,
                    [valor_limite_colores_fondo_1, valor_limite_colores_fondo_2], colores_fondo);
                break;
            }
            case TIPO_GRAFICO_WIDGET_VALOR_ANALOGICO_CIRCULAR: {
                var numero_columnas_widget_clase_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
                if (numero_columnas_widget_clase_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
                    numero_columnas_widget_clase_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
                }
                var factor_tamanyo_fuente = null;
                switch (parseInt(numero_columnas_widget_clase_contenido_widget)) {
                    case 5: {
                        factor_tamanyo_fuente = 2;
                        break;
                    }
                    case 4: {
                        factor_tamanyo_fuente = 2.5;
                        break;
                    }
                    case 3: {
                        factor_tamanyo_fuente = 3;
                        break;
                    }
                    case 2: {
                        factor_tamanyo_fuente = 4;
                        break;
                    }
                    case 1: {
                        factor_tamanyo_fuente = 5;
                        break;
                    }
                }
                muestra_grafico_valor_analogico_circular_widgets(
                    div_grafica,
                    valor_sensor, unidad_sensor,
                    valor_minimo_indicador, valor_maximo_indicador,
                    [valor_limite_colores_fondo_1, valor_limite_colores_fondo_2], colores_fondo,
                    factor_tamanyo_fuente, estilo_fuente, color,
                    false);
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

    // Icono del widget
    dibuja_icono_widget(
        id_widget,
        icono,
        color_icono,
        transparencia_icono);
}


// Dibuja un widget de tipo 'Gráfica de valores de un sensor'
function dibuja_widget_tipo_grafica_valores_sensor(
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
    var nombre_sensor = datos_widget.nombre_sensor;
    var clase_sensor = datos_widget.clase_sensor;
    var campo = datos_widget.campo;
    var intervalo_valores = datos_widget.intervalo_valores;
    var mostrar_lineas_valores = datos_widget.mostrar_lineas_valores;
    var tipo_lineas_valores = datos_widget.tipo_lineas_valores;
    var datos_grafica = datos_widget.datos_grafica;
    var numero_decimales = datos_widget.numero_decimales;
    var min_valor = datos_widget.min_valor;
    var max_valor = datos_widget.max_valor;
    var min_fecha = datos_widget.min_fecha;
    var max_fecha = datos_widget.max_fecha;
    var unidad_medida = datos_widget.unidad_medida;
    var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

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

    // Comprobación de datos disponibles (sólo hay una serie)
    var grafica_valores = datos_grafica;
    var numero_valores_grafica_valores = dame_numero_maximo_valores_series_grafica(grafica_valores);
    var sin_datos = (numero_valores_grafica_valores == 0);

    // Comprobación de widget sin datos
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
        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_valores_sensor__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
            mostrar_etiquetas = false;
        }
        else {
            mostrar_etiquetas = true;
        }
        var numero_maximo_valores_grafica_indicador_visible = null;
        switch (parseInt(numero_columnas_fila_widget)) {
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
        var min_fecha = new $.jsDate(min_fecha);
        var max_fecha = new $.jsDate(max_fecha);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Se dibujan las gráficas específicas del sensor (si no se dibuja la gráfica de valores genérica)
        var grafica_especifica_dibujada = false;
        switch (clase_sensor) {
            case CLASE_SENSOR_LUZ_INTERIOR: {
                switch (campo) {
                    case CAMPO_LUZ_ARTIFICIAL: {
                        // Tipo de líneas de valores
                        var tipo_lineas_valores = null;
                        switch (intervalo_valores) {
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS: {
                                tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                                break;
                            }
                            default: {
                                tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                                break;
                            }
                        }
                        $("#" + div_grafica).height(ALTURA_GRAFICAS_VALORES_SI_NO);
                        muestra_grafica_temporal_valores_si_no_widgets(
                            nombre_widget,
                            div_grafica,
                            grafica_valores, intervalo_valores,
                            min_fecha, max_fecha,
                            mostrar_lineas_valores,
                            tipo_lineas_valores,
                            mostrar_indicadores_valores,
                            color_ticks_eje_y,
                            transparencia_fondo_graficas);
                        grafica_especifica_dibujada = true;
                        break;
                    }
                }
                break;
            }
        }
        if (grafica_especifica_dibujada == false) {
            var etiquetas_valores = [nombre_sensor];
            var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
            muestra_grafica_temporal_lineas_valores_widgets(
                nombre_widget,
                div_grafica,
                etiquetas_valores, mostrar_etiquetas,
                grafica_valores, null, intervalo_valores,
                min_fecha, max_fecha, true,
                min_valor, true,
                max_valor, true,
                numero_decimales, unidad_medida,
                lineas_referencia,
                mostrar_lineas_valores,
                tipo_lineas_valores,
                mostrar_indicadores_valores,
                color_ticks_eje_y,
                transparencia_fondo_graficas,
                false);
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


// Dibuja un widget de tipo 'Mapa de calor de un sensor'
function dibuja_widget_tipo_mapa_calor_sensor(
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
    var tipo_mapa_calor = datos_widget.tipo_mapa_calor;
    var dias_mapa_calor = datos_widget.dias_mapa_calor;
    var datos_mapa_calor = datos_widget.datos_mapa_calor;
    var colores_mapa_calor = datos_widget.colores_mapa_calor;
    var descripcion_campo = datos_widget.descripcion_campo;
    var unidad_medida = datos_widget.unidad_medida;
    var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

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

    // Colores de fondo del widget
    if (modificar_colores_titulo == VALOR_NO) {
        color_fondo_titulo = color_tema_oscuro;
    }
    if (modificar_colores == VALOR_NO) {
        color = COLOR_NEGRO;
        color_fondo = color_tema_fondo;
    }

    // Comprobación de datos disponibles
    var sin_datos = (datos_mapa_calor.length == 0);

    // Comprobación de widget sin datos
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
        // Elemento para el mapa de calor de sensor
        var div_mapa_calor = "mapa_calor_widget_mapa_calor_sensor__" + id_widget;

        // Contenido del widget (gráfica y fechas)
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='" + div_mapa_calor + "' class='mapa-calor-widget-mapa-calor'></div>";
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='fechas-horas-widget-mapa-calor__" + id_widget + "' class='fechas-horas-widget-mapa-calor'>";
            html_contenido_widget += html_cadena_horas_valores;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color de las fechas
        $("#fechas-horas-widget-mapa-calor__" + id_widget).css('color', color);

        // Se añade un margen inferior si no se muestran las fechas
        if (mostrar_fechas == VALOR_NO) {
            $("#" + div_mapa_calor).css('padding-bottom', '0.5em');
        }

        // Se dibuja el mapa de calor
        var escala_colores_mapa_calor = dame_escala_colores_mapa_calor(colores_mapa_calor);
        muestra_grafico_mapa_calor_widgets(
            div_mapa_calor,
            tipo_mapa_calor,
            dias_mapa_calor,
            datos_mapa_calor,
            escala_colores_mapa_calor,
            color,
            true,
            true);

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Gráfica de comparación de periodos de un sensor'
function dibuja_widget_tipo_grafica_comparacion_periodos_sensor(
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
        var min_fecha = datos_widget.min_fecha;
        var max_fecha = datos_widget.max_fecha;
        var grafica_valores = datos_widget.grafica_valores;
        var min_valor = datos_widget.min_valor;
        var max_valor = datos_widget.max_valor;
        var etiquetas_valores = datos_widget.etiquetas_valores;
        var etiquetas_tooltips_valores = datos_widget.etiquetas_tooltips_valores;
        var tipo_lineas_valores = datos_widget.tipo_lineas_valores;
        var numero_decimales_valores = datos_widget.numero_decimales_valores;
        var unidad_medida = datos_widget.unidad_medida;

        // Parámetros 'extra' para el dibujado del widget
        var clase_sensor = datos_widget.clase_sensor;
        var campo = datos_widget.campo;
        var intervalo_valores = datos_widget.intervalo_valores;
        var mostrar_lineas_valores = datos_widget.mostrar_lineas_valores;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_comparacion_periodos_sensor__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
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
        var min_fecha = new $.jsDate(min_fecha);
        var max_fecha = new $.jsDate(max_fecha);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Gráfica de valores de comparación de periodos
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(clase_sensor, campo);
        muestra_grafica_temporal_lineas_valores_fechas_diferentes_widgets(
            nombre_widget,
            div_grafica,
            etiquetas_valores, mostrar_etiquetas,
            etiquetas_tooltips_valores,
            grafica_valores, intervalo_valores,
            min_fecha, max_fecha,
            min_valor, max_valor,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            tipo_lineas_valores,
            mostrar_indicadores_valores,
            color_ticks_eje_y,
            transparencia_fondo_graficas);

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Evolución de valores de comparación de periodos de un sensor'
function dibuja_widget_tipo_evolucion_valores_comparacion_periodos_sensor(
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
        var html_contenido_widget = "";
        var texto_diferencia_valores_totales_sin_unidad = datos_widget.texto_diferencia_valores_totales_sin_unidad;
        var unidad_medida = datos_widget.unidad_medida;
        var texto_porcentaje_diferencia_valores_totales = datos_widget.texto_porcentaje_diferencia_valores_totales;
        var texto_periodo = datos_widget.texto_periodo;
        var cadena_hora_inicio_posterior = datos_widget.cadena_hora_inicio_posterior;
        var cadena_hora_valor_final_posterior = datos_widget.cadena_hora_valor_final_posterior;
        var clase_tamanyo_fuente_evolucion_valores_widget = datos_widget.clase_tamanyo_fuente_evolucion_valores_widget;
        var clase_tamanyo_fuente_texto_periodo_widget = datos_widget.clase_tamanyo_fuente_texto_periodo_widget;
        var colores_fondo = datos_widget.colores_fondo;
        var indice_color_fondo = datos_widget.indice_color_fondo;

        // Contenido del widget (gráfica y fechas)
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget + "' class='evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor'>";
        html_contenido_widget += "            <div class='texto-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor " + clase_tamanyo_fuente_evolucion_valores_widget + "'>";
        html_contenido_widget += texto_diferencia_valores_totales_sin_unidad;
        if (unidad_medida != "") {
            html_contenido_widget += " " + "<span class='unidad-medida-widget-evolucion-valores-comparacion-periodos-sensor'>" + unidad_medida + "</span>";
        }
        if (texto_porcentaje_diferencia_valores_totales != "") {
            html_contenido_widget += " " + texto_porcentaje_diferencia_valores_totales;
        }
        html_contenido_widget += "            </div>";
        html_contenido_widget += "        </div>";
        if (mostrar_fechas == VALOR_SI) {
            if (texto_periodo != null) {
                html_contenido_widget += "<div id='texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget + "' class='texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor'>";
                html_contenido_widget += "    <div class='texto-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor " + clase_tamanyo_fuente_texto_periodo_widget + "'>";
                html_contenido_widget += texto_periodo;
                html_contenido_widget += "    </div>";
                html_contenido_widget += "</div>";
            }
            html_contenido_widget += "    <div id='fecha-hora-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget + "' class='fecha-hora-widget-evolucion-valores-comparacion-periodos-sensor'>";
            var cadena_horas_inicios_periodos = "(" + cadena_hora_inicio_posterior + "), (" + cadena_hora_valor_final_posterior + ")";
            html_contenido_widget += cadena_horas_inicios_periodos;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color y fuente del widget
        $("#evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget).css('color', color);
        $("#texto-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget).css('color', color);
        $("#fecha-hora-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget).css('color', color);
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor__" + id_widget).css('font-weight', "bold");
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


// Dibuja un widget de tipo 'Gráfica de comparación de campos iguales de sensores'
function dibuja_widget_tipo_grafica_comparacion_campos_iguales_sensores(
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
        var min_fecha = datos_widget.min_fecha;
        var max_fecha = datos_widget.max_fecha;
        var grafica_valores = datos_widget.grafica_valores;
        var min_valor = datos_widget.min_valor;
        var max_valor = datos_widget.max_valor;
        var etiquetas_valores = datos_widget.etiquetas_valores;
        var tipo_lineas_valores = datos_widget.tipo_lineas_valores;
        var numero_decimales_valores = datos_widget.numero_decimales_valores;
        var unidad_medida = datos_widget.unidad_medida;

        // Parámetros 'extra' para el dibujado del widget
        var intervalo_valores = datos_widget.intervalo_valores;
        var mostrar_lineas_valores = datos_widget.mostrar_lineas_valores;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_comparacion_campos_iguales_sensores__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
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

        // Contenido del widget (gráfica, fecha e iconos del sensor)
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
        var min_fecha = new $.jsDate(min_fecha);
        var max_fecha = new $.jsDate(max_fecha);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Gráfica de valores de comparación de campos iguales
        muestra_grafica_temporal_lineas_valores_widgets(
            nombre_widget,
            div_grafica,
            etiquetas_valores, mostrar_etiquetas,
            grafica_valores, null, intervalo_valores,
            min_fecha, max_fecha, true,
            min_valor, true,
            max_valor, true,
            numero_decimales_valores, unidad_medida,
            null,
            mostrar_lineas_valores,
            tipo_lineas_valores,
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


// Dibuja un widget de tipo 'Gráfica de comparación de campos diferentes de sensores'
function dibuja_widget_tipo_grafica_comparacion_campos_diferentes_sensores(
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
        var datos_widget = jQuery.parseJSON(datos);
        var min_fecha = datos_widget.min_fecha;
        var max_fecha = datos_widget.max_fecha;
        var grafica_valores = datos_widget.grafica_valores;
        var min_valores = datos_widget.min_valores;
        var max_valores = datos_widget.max_valores;
        var etiquetas_valores = datos_widget.etiquetas_valores;
        var etiquetas_valores_unidad = datos_widget.etiquetas_valores_unidad;
        var tipos_lineas_valores = datos_widget.tipos_lineas_valores;
        var numeros_decimales_valores = datos_widget.numeros_decimales_valores;
        var unidades_medida = datos_widget.unidades_medida;

        // Parámetros 'extra' para el dibujado del widget
        var intervalo_valores = datos_widget.intervalo_valores;
        var unificar_escalas = datos_widget.unificar_escalas;
        var mostrar_lineas_valores = datos_widget.mostrar_lineas_valores;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_comparacion_campos_diferentes_sensores__" + id_widget;

        // Etiquetas de los tooltips
        var etiquetas_tooltips = etiquetas_valores;

        // Nota: Altura y etiquetas dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
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

        // Contenido del widget (gráfica, fecha e iconos del sensor)
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
        var min_fecha = new $.jsDate(min_fecha);
        var max_fecha = new $.jsDate(max_fecha);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Gráfica de valores de comparación de campos iguales
        muestra_grafica_temporal_lineas_valores_ejes_diferentes_widgets(
            nombre_widget,
            div_grafica,
            etiquetas_valores_unidad, mostrar_etiquetas,
            etiquetas_tooltips,
            grafica_valores, intervalo_valores,
            unificar_escalas,
            min_fecha, max_fecha,
            min_valores, max_valores,
            numeros_decimales_valores, unidades_medida,
            mostrar_lineas_valores,
            tipos_lineas_valores,
            mostrar_indicadores_valores,
            color_ticks_eje_y,
            transparencia_fondo_graficas);

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Gráfica de valores generales de sensores'
function dibuja_widget_tipo_grafica_valores_generales_sensores(
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
        var min_fecha = datos_widget.min_fecha;
        var max_fecha = datos_widget.max_fecha;
        var campo_incremental = datos_widget.campo_incremental;
        var grafica_valores = datos_widget.grafica_valores;
        var min_valores = datos_widget.min_valores;
        var max_valores = datos_widget.max_valores;
        var etiquetas_valores = datos_widget.etiquetas_valores;
        var numero_decimales_valores = datos_widget.numero_decimales_valores;
        var unidad_medida = datos_widget.unidad_medida;

        // Parámetros 'extra' para el dibujado del widget
        var primera_clase_sensor = datos_widget.clases_sensor[0];
        var primer_campo = datos_widget.campos[0];
        var intervalo_valores = datos_widget.intervalo_valores;
        var agregacion = datos_widget.agregacion;
        var mostrar_lineas_valores = datos_widget.mostrar_lineas_valores;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_valores_generales_sensores__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula de widgets
        var numero_columnas_widget_clase_altura_contenido_widget = Math.ceil(numero_columnas_fila_widget / numero_columnas_widget);
        if (numero_columnas_widget_clase_altura_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS) {
            numero_columnas_widget_clase_altura_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
        }
        var clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_widget_clase_altura_contenido_widget;
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
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

        // Contenido del widget (gráfica, fecha e iconos del sensor)
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
        var min_fecha = new $.jsDate(min_fecha);
        var max_fecha = new $.jsDate(max_fecha);

        // Color de ticks del eje y
        var color_ticks_eje_y = null;
        if (modificar_colores == VALOR_SI) {
            color_ticks_eje_y = color;
        }

        // Flag de tooltips personalizados
        var tooltips_personalizados = (agregacion != AGREGACION_NINGUNA);

        // Gráfica de valores de valores generales
        var valor_minimo = null;
        var ajustar_valor_minimo = null;
        if (campo_incremental == true) {
            valor_minimo = 0;
            ajustar_valor_minimo = false;
        }
        else {
            valor_minimo = min_valores;
            ajustar_valor_minimo = true;
        }
        var lineas_referencia = dame_lineas_referencia_grafica_valores_clase_sensor_campo(primera_clase_sensor, primer_campo);
        muestra_grafica_temporal_lineas_valores_widgets(
            nombre_widget,
            div_grafica,
            etiquetas_valores, mostrar_etiquetas,
            grafica_valores, null, intervalo_valores,
            min_fecha, max_fecha, true,
            valor_minimo, ajustar_valor_minimo,
            max_valores, true,
            numero_decimales_valores, unidad_medida,
            lineas_referencia,
            mostrar_lineas_valores,
            TIPO_LINEAS_VALORES_ESTANDAR,
            mostrar_indicadores_valores,
            color_ticks_eje_y,
            transparencia_fondo_graficas,
            tooltips_personalizados);

        // Se establece el color de fondo del widget
        establece_color_fondo_widget(
            id_widget,
            color_fondo_titulo,
            transparencia_fondo_titulo,
            color_fondo,
            transparencia_fondo);
    }
}


// Dibuja un widget de tipo 'Valor agregado de valores generales de sensores'
function dibuja_widget_tipo_valor_agregado_valores_generales_sensores(
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
        var html_contenido_widget = "";
        var texto_valor_agregado_sin_unidad = datos_widget.texto_valor_agregado_sin_unidad;
        var unidad_medida = datos_widget.unidad_medida;
        var clase_tamanyo_fuente_valor_agregado_widget = datos_widget.clase_tamanyo_fuente_valor_agregado_widget;
        var colores_fondo = datos_widget.colores_fondo;
        var indice_color_fondo = datos_widget.indice_color_fondo;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Contenido del widget
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='valor-unidad-widget-valor-agregado-valores-generales-sensores__" + id_widget + "' class='valor-unidad-widget-valor-digital-sensor'>";
        html_contenido_widget += "            <div class='texto-grande-widget-valor-digital-sensor " + clase_tamanyo_fuente_valor_agregado_widget + "'>";
        html_contenido_widget += texto_valor_agregado_sin_unidad;
        if (unidad_medida != "") {
            html_contenido_widget += " " + "<span class='texto-pequenyo-widget-valor-digital-sensor'>" + unidad_medida + "</span>";
        }
        html_contenido_widget += "            </div>";
        html_contenido_widget += "        </div>";
        if (mostrar_fechas == VALOR_SI) {
            html_contenido_widget += "    <div id='fechas-horas-widget-valor-agregado-valores-generales-sensores__" + id_widget + "' class='fecha-hora-widget-valor-digital-sensor'>";
            html_contenido_widget += html_cadena_horas_valores;
            html_contenido_widget += "    </div>";
        }
        html_contenido_widget += "    </div>";
        html_contenido_widget += "</div>";
        html_contenido_widget += "<div id='icono_widget_" + id_widget + "' class='icono-widget'></div>";

        // Se actualiza el contenido del widget
        $("#contenido_widget_" + id_widget).html(html_contenido_widget);

        // Color y fuente del widget
        $("#valor-unidad-widget-valor-agregado-valores-generales-sensores__" + id_widget).css('color', color);
        $("#fechas-horas-widget-valor-agregado-valores-generales-sensores__" + id_widget).css('color', color);
        if (estilo_fuente == ESTILO_FUENTE_NEGRITA) {
            $("#valor-unidad-widget-valor-agregado-valores-generales-sensores__" + id_widget).css('font-weight', "bold");
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


// Dibuja un widget de tipo 'Gráfica de incrementos totales de sensores'
function dibuja_widget_tipo_grafica_incrementos_totales_sensores(
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
        var grafica_incrementos_totales = datos_widget.grafica_incrementos_totales;
        var grafica_porcentajes_incrementos = datos_widget.grafica_porcentajes_incrementos;
        var max_incrementos_totales = datos_widget.max_incrementos_totales;
        var etiquetas_incrementos = datos_widget.etiquetas_incrementos;
        var unidad_medida = datos_widget.unidad_medida;

        // Parámetros 'extra' para el dibujado del widget
        var tipo_grafica = datos_widget.tipo_grafica;
        var html_cadena_horas_valores = datos_widget.html_cadena_horas_valores;

        // Elemento para la gráfica de valores de sensor
        var div_grafica = "grafica_widget_grafica_incrementos_totales_sensores__" + id_widget;

        // Nota: Altura, etiquetas y mostrar indicadores de valores dependiente de la configuración de la cuadrícula de widgets
        var clase_altura_grafica_widgets = null;
        switch (tipo_grafica) {
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_BARRAS_VALORES: {
                clase_altura_grafica_widgets = "altura-grafica-widgets-columnas-" + numero_columnas_fila_widget;
                break;
            }
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_TARTA_VALORES: {
                clase_altura_grafica_widgets = "altura-grafica-tarta-widgets-columnas-" + numero_columnas_fila_widget;
                break;
            }
        }
        var mostrar_etiquetas = null;
        if (numero_columnas_fila_widget > 2) {
            mostrar_etiquetas = false;
        }
        else {
            mostrar_etiquetas = true;
        }

        // Contenido del widget (gráfica, fecha e iconos del sensor)
        var clase_grafica_widgets = null;
        switch (tipo_grafica) {
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_BARRAS_VALORES: {
                clase_grafica_widgets = "grafica-widgets-graficas";
                break;
            }
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_TARTA_VALORES: {
                clase_grafica_widgets = "grafica-widgets-graficas-tarta";
                break;
            }
        }
        var html_contenido_widget = "";
        html_contenido_widget += "<div class='contenido-widget-contenedor-centrado'>";
        html_contenido_widget += "    <div class='contenido-widget-contenido-centrado'>";
        html_contenido_widget += "        <div id='" + div_grafica + "' class='" + clase_grafica_widgets + " " + clase_altura_grafica_widgets + "'></div>";
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
        switch (tipo_grafica) {
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_BARRAS_VALORES: {
                muestra_grafica_puntual_barras_valores_widgets(
                    nombre_widget,
                    div_grafica,
                    etiquetas_incrementos, mostrar_etiquetas,
                    grafica_incrementos_totales,
                    [TLNT.Idiomas._("Sensores")], null,
                    max_incrementos_totales, true,
                    2, unidad_medida,
                    false, true,
                    color_ticks_eje_y,
                    transparencia_fondo_graficas);
                break;
            }
            case TIPO_GRAFICA_WIDGET_INCREMENTOS_TOTALES_SENSORES_TARTA_VALORES: {
                muestra_grafica_tarta_valores_widgets(
                    nombre_widget,
                    div_grafica,
                    etiquetas_incrementos, mostrar_etiquetas,
                    grafica_porcentajes_incrementos,
                    2, unidad_medida,
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
