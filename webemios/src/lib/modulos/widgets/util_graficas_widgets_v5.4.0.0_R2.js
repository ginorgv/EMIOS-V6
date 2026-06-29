//
// Funciones para el dibujado de gráficas de widgets
//


// Muestra una gráfica temporal de líneas de valores de widgets
function muestra_grafica_temporal_lineas_valores_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    valores, bandas_valores, intervalo_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    color_ticks_eje_y,
    transparencia_fondo,
    tooltip_personalizado) {
    // Valores mínimo y máximo del eje y
    var min_y = valor_minimo;
    if (ajustar_valor_minimo == true) {
        min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    if (mostrar_etiquetas == true) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, [min_y], [max_y], 0)[0];
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        series: [],
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickOptions: {}
            },
            yaxis: {
                max: max_y,
                min: min_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0,
                animation: {
                    speed: 1000
                }
            },
            showLine: (mostrar_lineas_valores == true),
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: ((mostrar_lineas_valores == false) || (mostrar_indicadores_valores == true)),
            breakOnNull: true
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha mínima es diferente de la fecha máxima
    // (si no no se muestran los puntos de la gráfica)
    if (ajustar_fechas == true) {
        if ((fecha_minima != null) && (fecha_maxima != null)) {
            if (fecha_minima.getTime() != fecha_maxima.getTime()) {
                ajusta_fecha_maxima_intervalo_valores_grafica(fecha_maxima, intervalo_valores);
                parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_minima.getTime();
                parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_maxima.getTime();
            }
        }
    };

    // Intervalo mínimo de fechas y formato de fecha del eje X
    establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores);

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    if ((mostrar_lineas_valores == true) && (tipo_lineas_valores == TIPO_LINEAS_VALORES_CUADRADAS)) {
        valores[0] = dame_valores_lineas_cuadradas(valores[0]);
    }

    // Tooltip
    // - Personalizado: Se establece el tooltip especificado en los parámetros (en las series de valores)
    // - Formato de tooltip con etiquetas
    if (tooltip_personalizado == true) {
        var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
            cadena_tooltip = plot.series[seriesIndex].data[pointIndex][2];
            cadena_tooltip = unescapeHtml(cadena_tooltip);
            return (cadena_tooltip);
        };
        parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
    }
    else {
        // Formato de tooltip
        var formato_tooltip = "%2$s";
        if (unidad_medida != "") {
            formato_tooltip += " " + unidad_medida;
        }
        formato_tooltip += " (%1$s)";
        parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;

        // Nombres de series en los tooltips (sólo si hay más de 1 serie)
        var parametros_series_anyadidos = false;
        if (valores.length > 1) {
            for (var i = 0; i < valores.length; i++) {
                var parametros_serie = {
                    label: etiquetas[i]
                };
                parametros_grafica_jqplot["series"].push(parametros_serie);
            }
            parametros_series_anyadidos = true;
            var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
                var cadena_tooltip_modificada = "[" + plot.series[seriesIndex]["label"] + "]</br>" + cadena_tooltip;
                return (cadena_tooltip_modificada);
            };
            parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
        }
    }

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Bandas de valores
    if ((mostrar_lineas_valores == true) && (bandas_valores != null)) {
        for (var i = 0; i < bandas_valores.length; i++) {
            if (parametros_series_anyadidos == true) {
                parametros_grafica_jqplot["series"][i]["rendererOptions"] = {
                    bandData: bandas_valores[i],
                    smooth: false
                };
                parametros_grafica_jqplot["series"][i]["breakOnNull"] = true;
            }
            else {
                var parametros_banda_valores = {
                    rendererOptions: {
                        bandData: bandas_valores[i],
                        smooth: false
                    },
                    breakOnNull: true
                };
                parametros_grafica_jqplot["series"].push(parametros_banda_valores);
            }
        }
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden las líneas de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        true,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de barras de valores
function muestra_grafica_temporal_barras_valores_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    valores, intervalo_valores, numero_huecos_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    apilar_valores, mostrar_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICAS_BARRAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var altura_anyadida_valor_barras = null;
    if (mostrar_valores == true) {
        altura_anyadida_valor_barras = ALTURA_VALOR_BARRAS_VALORES;
    }
    else {
        altura_anyadida_valor_barras = 0;
    }
    if (mostrar_etiquetas == true) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    else {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, [], [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }

    // Segundos del intervalo de valores
    var segundos_intervalo_valores = dame_segundos_intervalo_valores(intervalo_valores);

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [[[0, 0]]];
    var parametros_grafica_vacia_jqplot = {
        animate: false,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                },
                minTickInterval: segundos_intervalo_valores
            },
            yaxis: {
                min: 0,
                max: max_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        }
    };

    // Nota: Se dibuja primero la gráfica vacía para poder recuperar la anchura en pixels de la gráfica para poder calcular la anchura correcta de las barras
    $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

    // Anchura de las barras de la gráfica
    var numero_valores_grafica = 0;
    if (apilar_valores == true) {
        numero_valores_grafica = valores[0].length;
    }
    else {
        for (var i = 0; i < valores.length; i++) {
            numero_valores_grafica += valores[i].length;
        }
    }
    numero_valores_grafica += numero_huecos_valores;
    var anchura_grafica_valores = $('#' + div_grafica + ' .jqplot-event-canvas').width();
    var anchura_barras_valores = Math.floor((anchura_grafica_valores / numero_valores_grafica) * PORCENTAJE_SEPARACION_BARRAS_GRAFICAS_TEMPORALES);
    if (anchura_barras_valores < 1) {
        anchura_barras_valores = 1;
    }

    // Se borra la gráfica auxiliar
    $('#' + div_grafica).html("");

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        stackSeries: apilar_valores,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
            },
            yaxis: {
                min: 0,
                max: max_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            rendererOptions: {
                shadowOffset: 0,
                barWidth: anchura_barras_valores,
                animation: {
                    speed: 1000
                }
            },
            pointLabels: {
                show: mostrar_valores
            }
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha mínima es diferente de la fecha máxima
    // (si no no se muestran los puntos de la gráfica)
    if (ajustar_fechas == true) {
        if ((fecha_minima != null) && (fecha_maxima != null)) {
            if (fecha_minima.getTime() != fecha_maxima.getTime()) {
                parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_minima.getTime();
                parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_maxima.getTime();
            }
        }
    };

    // Intervalo mínimo de fechas y formato de fecha del eje X
    establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores);

    // Función de formateo de tooltip
    // - Se añade la etiqueta de las series
    // - Se añade la fecha (ya formateada en los valores de las series)
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        cadena_tooltip = "";
        if ((etiquetas != null) && (etiquetas.length > 1)) {
            cadena_tooltip += "[" + etiquetas[seriesIndex] + "]" + "</br>";
        }
        cadena_tooltip += formatea_numero(plot.data[seriesIndex][pointIndex][1], numero_decimales_valores);
        if (unidad_medida != "") {
            cadena_tooltip += " " + unidad_medida;
        }
        cadena_tooltip += " (" + plot.data[seriesIndex][pointIndex][2] + ")";
        cadena_tooltip = unescapeHtml(cadena_tooltip);
        return (cadena_tooltip);
    };
    parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        true,
        0);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);

    // Se ordenan las etiquetas (con valores apilados se muestran al revés)
    if ((etiquetas != null) && (apilar_valores == true)) {
        ordena_etiquetas_grafica("#" + div_grafica);
        grafica.target.bind('jqplotZoom', function(ev, gridpos, datapos, plot, cursor) {
            var id_div_grafica = plot.targetId;
            ordena_etiquetas_grafica(id_div_grafica);
        });
        grafica.target.bind('jqplotResetZoom', function(ev, plot, cursor) {
            var id_div_grafica = plot.targetId;
            ordena_etiquetas_grafica(id_div_grafica);
        });
    }
}


// Muestra una gráfica puntual de barras de valores de widgets
function muestra_grafica_puntual_barras_valores_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    valores,
    ticks_eje_x, valores_ticks_eje_x_sombreado_grafica,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    apilar_valores, mostrar_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICAS_BARRAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var altura_anyadida_valor_barras = null;
    if (mostrar_valores == true) {
        altura_anyadida_valor_barras = ALTURA_VALOR_BARRAS_VALORES;
    }
    else {
        altura_anyadida_valor_barras = 0;
    }
    if (mostrar_etiquetas == true) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, [0], [max_y], altura_anyadida_valor_barras)[0];
    }
    else {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, [], [0], [max_y], altura_anyadida_valor_barras)[0];
    }

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [[[0, 0]]];
    var parametros_grafica_vacia_jqplot = {
        animate: false,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
                showTicks: false,
                tickOptions: {
                    formatString: '%d',
                    fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                }
            },
            yaxis: {
                min: 0,
                max: max_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        }
    };

    // Ticks del eje x
    if (ticks_eje_x != null) {
        parametros_grafica_vacia_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;
    }

    // Nota: Se dibuja primero la gráfica vacía para poder recuperar la anchura en pixels de la gráfica para poder calcular la anchura correcta de las barras
    $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

    // Anchura de las barras de la gráfica
    var anchura_grafica_valores = $('#' + div_grafica + ' .jqplot-event-canvas').width();
    var numero_ticks_eje_x = null;
    if (ticks_eje_x != null) {
        numero_ticks_eje_x = ticks_eje_x.length;
    } else {
        numero_ticks_eje_x = 1;
    }
    var numero_valores_tick_eje_x = null;
    if (etiquetas != null) {
        if (apilar_valores == true) {
            numero_valores_tick_eje_x = 1;
        }
        else {
            numero_valores_tick_eje_x = etiquetas.length;
        }
    } else {
        numero_valores_tick_eje_x = 1;
    }
    var anchura_barras_valores = Math.floor((anchura_grafica_valores / (numero_ticks_eje_x * numero_valores_tick_eje_x)) * PORCENTAJE_SEPARACION_BARRAS_GRAFICAS_PUNTUALES);
    var separacion_barras_valores = Math.floor((anchura_grafica_valores - (anchura_barras_valores * numero_ticks_eje_x * numero_valores_tick_eje_x)) /
        (numero_ticks_eje_x * (numero_valores_tick_eje_x + 1)));
    if (separacion_barras_valores == 0) {
        anchura_barras_valores -= 1;
        separacion_barras_valores = 1;
    }

    // Se borra la gráfica auxiliar
    $('#' + div_grafica).html("");

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        stackSeries: apilar_valores,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
                showTicks: false,
                tickOptions: {
                    formatString: '%d',
                    fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                }
            },
            yaxis: {
                min: 0,
                max: max_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false
        },
        highlighter: {
            show: true
        },
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            rendererOptions: {
                shadowOffset: 0,
                barWidth: anchura_barras_valores,
                barPadding: separacion_barras_valores,
                animation: {
                    speed: 1000
                }
            },
            pointLabels: {
                show: mostrar_valores
            }
        },
        legend: {
            show: false
        }
    };

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Formato de tooltip
    var formato_tooltip = "[%s]</br>" + "%'." + numero_decimales_valores + "f";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }

    // Función de formateo de tooltip
    // - Se añade la etiqueta
    // - Si hay más de un valor en los datos del punto (de la gráfica), el valor es el segundo (el primero es el índice del eje X)
    // - Si hay más de 1 tick en el eje X (si hay sólo uno es el título) y hay más de 1 valor en el punto, se añade el texto del tick del eje X
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        cadena_tooltip = "";
        if ((etiquetas != null) && (etiquetas.length > 1)) {
            cadena_tooltip += "[" + etiquetas[seriesIndex] + "]" + "</br>";
        }
        var datos_grafica_punto = plot.data[seriesIndex][pointIndex];
        if (datos_grafica_punto.length == undefined) {
            cadena_tooltip += formatea_numero(plot.data[seriesIndex][pointIndex], numero_decimales_valores);
        }
        else {
            cadena_tooltip += formatea_numero(plot.data[seriesIndex][pointIndex][1], numero_decimales_valores);
        }
        if (unidad_medida != "") {
            cadena_tooltip += " " + unidad_medida;
        }
        if ((ticks_eje_x.length > 1) && (datos_grafica_punto.length != undefined)) {
            cadena_tooltip += " (" + ticks_eje_x[plot.data[seriesIndex][pointIndex][0] - 1].toLowerCase() + ")";
        }
        cadena_tooltip = unescapeHtml(cadena_tooltip);
        return (cadena_tooltip);
    };
    parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;

    // Ticks del eje x
    if (ticks_eje_x != null) {
        parametros_grafica_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;
    }

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Si hay sombreado de gráfica
    if (valores_ticks_eje_x_sombreado_grafica != null) {
        // Para el dibujado de líneas en la gráfica
        parametros_grafica_jqplot["canvasOverlay"] = {
            show: true,
            objects: []
        };

        // Rectángulos de sombreado de gráfica
        for (var i = 0; i < valores_ticks_eje_x_sombreado_grafica.length; i++) {
            var rectangulo_sombreado_grafica = {
                rectangle: {
                    xmin: valores_ticks_eje_x_sombreado_grafica[i] - 0.5,
                    xmax: valores_ticks_eje_x_sombreado_grafica[i] + 0.5,
                    color: "rgba(150, 150, 150, 0.20)",
                    shadow: false
                }
            };
            parametros_grafica_jqplot["canvasOverlay"]["objects"].push(rectangulo_sombreado_grafica);
        }
    }

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["ticks_eje_x"] = ticks_eje_x;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);

    // Se ordenan las etiquetas (con valores apilados se muestran al revés)
    if ((etiquetas != null) && (apilar_valores == true)) {
        ordena_etiquetas_grafica("#" + div_grafica);
        grafica.target.bind('jqplotZoom', function(ev, gridpos, datapos, plot, cursor) {
            var id_div_grafica = plot.targetId;
            ordena_etiquetas_grafica(id_div_grafica);
        });
        grafica.target.bind('jqplotResetZoom', function(ev, plot, cursor) {
            var id_div_grafica = plot.targetId;
            ordena_etiquetas_grafica(id_div_grafica);
        });
    }
}


// Muestra una gráfica de tarta de valores de widgets
function muestra_grafica_tarta_valores_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    valores,
    numero_decimales_valores, unidad_medida,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Tamaño de letra en píxeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");

    // Ajuste de altura para que se muestren todas las etiquetas correctamente en las gráficas
    var altura_anyadida_etiquetas_valores = 0;
    if (mostrar_etiquetas == true) {
        if ((etiquetas != null) && (etiquetas.length > MAX_ETIQUETAS_GRAFICAS_VALORES)) {
            altura_anyadida_etiquetas_valores = (etiquetas.length - MAX_ETIQUETAS_GRAFICAS_VALORES) * ALTURA_ETIQUETA_GRAFICAS_VALORES;
        }
    }
    var altura_grafica_inicial = $('#' + div_grafica).height();
    var altura_grafica = altura_grafica_inicial + (altura_anyadida_etiquetas_valores * tamanyo_letra_pixeles);

    // Altura
    if ($("#" + div_grafica).height() < altura_grafica) {
        $("#" + div_grafica).height(altura_grafica);
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: [""],
                showTicks: false,
                tickOptions: {
                    fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                }
            }
        },
        highlighter: {
            show: true,
            useAxesFormatters: false
        },
        seriesDefaults: {
            renderer: $.jqplot.PieRenderer,
            trendline: {
                show: false
            },
            shadow: false,
            rendererOptions: {
                shadowOffset: 0,
                padding: 8,
                showDataLabels: true,
                dataLabelFormatString: "%.2f %",
                sliceMargin: 2
            }
        },
        legend: {
            show: false
        }
    };

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Formato de tooltip:
    // - Nombres de series en los tooltips
    // - Valor y unidad de medida
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        cadena_tooltip = "";
        if ((etiquetas != null) && (etiquetas.length > 1)) {
            cadena_tooltip += "[" + etiquetas[pointIndex] + "]" + "</br>";
        }
        cadena_tooltip += formatea_numero(plot.data[seriesIndex][pointIndex][1], numero_decimales_valores);
        if (unidad_medida != "") {
            cadena_tooltip += " " + unidad_medida;
        }
        cadena_tooltip = unescapeHtml(cadena_tooltip);
        return (cadena_tooltip);
    };
    parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_tarta_valores"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de valores Sí/No de widgets
function muestra_grafica_temporal_valores_si_no_widgets(
    nombre_widget,
    div_grafica,
    valores, intervalo_valores,
    fecha_inicial, fecha_final,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Valores de ticks del eje y
    var valor_tick_no = -0.20;
    var valor_tick_si = 1.20;

    // Parámetros de la gráfica jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickOptions: {}
            },
            yaxis: {
                ticks: [
                    [valor_tick_no, TLNT.Idiomas._("No")],
                    [valor_tick_si, TLNT.Idiomas._("Sí")]]
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            useYTickMarks: true,
            tooltipAxes: 'both',
            formatString: '%2$s (%1$s)'
        },
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0,
                animation: {
                    speed: 1000
                }
            },
            showLine: (mostrar_lineas_valores == true),
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: ((mostrar_lineas_valores == false) || (mostrar_indicadores_valores == true)),
            breakOnNull: true
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha inicial es diferente de la fecha final
    // (si no no se muestran los puntos de la gráfica)
    if (fecha_inicial.getTime() != fecha_final.getTime()) {
        parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_inicial.getTime();
        parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_final.getTime();
    }

    // Intervalo mínimo de fechas y formato de fecha del eje X
    establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores);

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    if ((mostrar_lineas_valores == true) && (tipo_lineas_valores == TIPO_LINEAS_VALORES_CUADRADAS)) {
        valores[0] = dame_valores_lineas_cuadradas(valores[0]);
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_inicial,
        fecha_final,
        true,
        0);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = 2;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores de horas de widgets
function muestra_grafica_temporal_lineas_valores_horarios_widgets(
    nombre_widget,
    div_grafica,
    etiquetas,
    valores,
    fecha_inicial,
    fecha_final,
    mostrar_indicadores_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Parámetros de la gráfica jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    formatString: formato_fecha_local_jqplot,
                    angle: -30,
                    fontSize: '7pt'
                },
                showTicks: false
            },
            yaxis: {
                min: MIN_HORA_DIA_GRAFICA,
                max: MAX_HORA_DIA_GRAFICA,
                renderer: $.jqplot.DateAxisRenderer,
                tickOptions: {
                    formatString: "%H:%M"
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            formatString: '%2$s (%1$s)'
        },
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0,
                animation: {
                    speed: 1000
                }
            },
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: mostrar_indicadores_valores
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha inicial es diferente de la fecha final
    // (si no no se muestran los puntos de la gráfica)
    if (fecha_inicial.getTime() != fecha_final.getTime()) {
        parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_inicial.getTime();
        parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_final.getTime();
    }

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: true,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden las líneas de los fines de semana a la gráfica
    var milisegundos_desplazamiento_rectangulos_fin_semana = (86400 / 2) * 1000 * (-1);
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_inicial,
        fecha_final,
        true,
        milisegundos_desplazamiento_rectangulos_fin_semana);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["valores_horarios"] = true;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores con fechas diferentes de widgets
function muestra_grafica_temporal_lineas_valores_fechas_diferentes_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    etiquetas_tooltips,
    valores, intervalo_valores,
    fecha_minima, fecha_maxima,
    valor_minimo, valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Valores mínimo y máximo del eje y
    var min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICA_WIDGETS_GRAFICAS);
    var max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICA_WIDGETS_GRAFICAS);

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    if (mostrar_etiquetas == true) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, [min_y], [max_y], 0)[0];
    }
    // Se generan al revés en el PHP por lo que se invierten aquí
    valores.reverse();
    etiquetas.reverse();
    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickOptions: {}
            },
            yaxis: {
                min: min_y,
                max: max_y,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numero_decimales_valores, unidad_medida));
                    }
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        series: [
            {
                label: etiquetas_tooltips[1]
            },
            {
                label: etiquetas_tooltips[0],
                linePattern: 'dashed',
                lineWidth: 2
            }
            
        ],
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0,
                animation: {
                    speed: 1000
                }
            },
            showLine: (mostrar_lineas_valores == true),
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: ((mostrar_lineas_valores == false) || (mostrar_indicadores_valores == true)),
            breakOnNull: true
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha inicial es diferente de la fecha final
    // (si no no se muestran los puntos de la gráfica)
    if (fecha_minima.getTime() != fecha_maxima.getTime()) {
        parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_minima.getTime();
        parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_maxima.getTime();
    }

    // Intervalo mínimo de fechas y formato de fecha del eje X
    establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores);

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    if ((mostrar_lineas_valores == true) && (tipo_lineas_valores == TIPO_LINEAS_VALORES_CUADRADAS)) {
        for (var i = 0; i < valores.length; i++) {
            valores[i] = dame_valores_lineas_cuadradas(valores[i]);
        }
    }

    // Formato de tooltip
    var formato_tooltip = "%2$s";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }
    formato_tooltip += " (%3$s)";
    parametros_grafica_jqplot["highlighter"]["yvalues"] = 2;
    parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;

    // Nombres de series en los tooltips
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        var cadena_tooltip_modificada = "[" + plot.series[seriesIndex]["label"] + "]</br>" + cadena_tooltip;
        return (cadena_tooltip_modificada);
    };
    parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Estilo de líneas de la gráfica
    establece_estilo_lineas_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        true,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    parametros_grafica_globales["valores_extra"] = true;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores con ejes diferentes (máximo 5) de widgets
function muestra_grafica_temporal_lineas_valores_ejes_diferentes_widgets(
    nombre_widget,
    div_grafica,
    etiquetas, mostrar_etiquetas,
    etiquetas_tooltips,
    valores, intervalo_valores,
    unificar_escalas,
    fecha_minima, fecha_maxima,
    valores_minimos, valores_maximos,
    numeros_decimales_valores, unidades_medida,
    mostrar_lineas_valores,
    tipos_lineas_valores,
    mostrar_indicadores_valores,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Valores mínimos y máximos del eje y
    var mins_y = [];
    for (var i = 0; i < valores_minimos.length; i++) {
        var min_y = dame_valor_minimo_eje(valores_maximos[i], valores_minimos[i], PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
        mins_y.push(min_y);
    }
    var maxs_y = [];
    for (var i = 0; i < valores_maximos.length; i++) {
        var max_y = dame_valor_maximo_eje(valores_maximos[i], valores_minimos[i], PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
        maxs_y.push(max_y);
    }

    // Ajuste de altura y de valores máximos para que se muestren todas las etiquetas correctamente en las gráficas
    if (mostrar_etiquetas == true) {
        maxs_y = ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, mins_y, maxs_y, 0);
    }

    // Se añaden valores mínimo y máximo (que faltan hasta el número máximo de valores diferentes, aunque no se muestren es necesario)
    var numero_series_valores = valores.length;
    for (var i = numero_series_valores; i < NUMERO_LINEAS_VALORES_EJES_DIFERENTES; i++)
    {
        mins_y.push(0);
        maxs_y.push(0);
        etiquetas_tooltips.push("");
    }

    // Si las unidades de medida son las mismas, no se duplica el eje y
    // (los máximos y mínimos son el máximo de los máximos y el mínimo de los mínimos de los valores con la misma unidad de medida)
    var nombres_ejes_y = [
        "yaxis",
        "y2axis",
        "y3axis",
        "y4axis",
        "y5axis"
    ];
    if (unificar_escalas == VALOR_SI) {
        for (var i = 0; i < unidades_medida.length; i++) {
            var unidad_medida_i = unidades_medida[i];
            var nombre_eje_y_i = nombres_ejes_y[i];
            for (var j = i + 1; j < unidades_medida.length; j++) {
                var unidad_medida_j = unidades_medida[j];
                if (unidad_medida_j == unidad_medida_i) {
                    nombres_ejes_y[j] = nombre_eje_y_i;
                }
            }
        }
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        animate: true,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                showTicks: false,
                tickOptions: {}
            },
            yaxis: {
                min: mins_y[0],
                max: maxs_y[0],
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numeros_decimales_valores[0], unidades_medida[0]));
                    }
                }
            },
            y2axis: {
                min: mins_y[1],
                max: maxs_y[1],
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numeros_decimales_valores[1], unidades_medida[1]));
                    }
                }
            },
            y3axis: {
                min: mins_y[2],
                max: maxs_y[2],
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numeros_decimales_valores[2], unidades_medida[2]));
                    }
                }
            },
            y4axis: {
                min: mins_y[3],
                max: maxs_y[3],
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numeros_decimales_valores[3], unidades_medida[3]));
                    }
                }
            },
            y5axis: {
                min: mins_y[4],
                max: maxs_y[4],
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_y(value, numeros_decimales_valores[4], unidades_medida[4]));
                    }
                }
            }
        },
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both'
        },
        series: [
            {
                yaxis: nombres_ejes_y[0],
                highlighter: {
                    yAxisFormatString: "%'." + numeros_decimales_valores[0] + "f"
                },
                label: etiquetas_tooltips[0]
            },
            {
                yaxis: nombres_ejes_y[1],
                highlighter: {
                    yAxisFormatString: "%'." + numeros_decimales_valores[1] + "f"
                },
                label: etiquetas_tooltips[1]
            },
            {
                yaxis: nombres_ejes_y[2],
                highlighter: {
                    yAxisFormatString: "%'." + numeros_decimales_valores[2] + "f"
                },
                label: etiquetas_tooltips[2]
            },
            {
                yaxis: nombres_ejes_y[3],
                highlighter: {
                    yAxisFormatString: "%'." + numeros_decimales_valores[3] + "f"
                },
                label: etiquetas_tooltips[3]
            },
            {
                yaxis: nombres_ejes_y[4],
                highlighter: {
                    yAxisFormatString: "%'." + numeros_decimales_valores[4] + "f"
                },
                label: etiquetas_tooltips[4]
            }
        ],
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0,
                animation: {
                    speed: 1000
                }
            },
            showLine: (mostrar_lineas_valores == true),
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: ((mostrar_lineas_valores == false) || (mostrar_indicadores_valores == true)),
            breakOnNull: true
        },
        legend: {
            show: false
        }
    };

    // Se ajustan las fechas sólo si la fecha inicial es diferente de la fecha final
    // (si no no se muestran los puntos de la gráfica)
    if (fecha_minima.getTime() != fecha_maxima.getTime()) {
        parametros_grafica_jqplot["axes"]["xaxis"]["min"] = fecha_minima.getTime();
        parametros_grafica_jqplot["axes"]["xaxis"]["max"] = fecha_maxima.getTime();
    }

    // Intervalo mínimo de fechas y formato de fecha del eje X
    establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores);

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    for (var i = 0; i < numero_series_valores; i++) {
        if ((mostrar_lineas_valores == true) && (tipos_lineas_valores[i] == TIPO_LINEAS_VALORES_CUADRADAS)) {
            valores[i] = dame_valores_lineas_cuadradas(valores[i]);
        }
    }

    // Nombres de series en los tooltips
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        // Nota: Hay un posible error en jqplot y en la cadena de tooltip añade dos veces la unidad, hay que volver a crear la cadena entera aquí
        // ('0.00 arti... artificial light (18/01/2017, 07:00)')
        cadena_tooltip = "";
        cadena_tooltip += formatea_numero_limite_digitos(
            plot.series[seriesIndex]["data"][pointIndex][1],
            numeros_decimales_valores[seriesIndex],
            true);
        if (unidades_medida[seriesIndex] != "") {
            cadena_tooltip += " " + unidades_medida[seriesIndex];
        }
        cadena_tooltip += " (" + plot.series[seriesIndex]["data"][pointIndex][2] + ")";

        var cadena_tooltip_modificada = "[" + plot.series[seriesIndex]["label"] + "]</br>" + cadena_tooltip;
        return (cadena_tooltip_modificada);
    };
    parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Para el dibujado de líneas en la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        true,
        0);

    // Modifica estilos de la gráfica
    modifica_estilos_grafica(
        parametros_grafica_jqplot,
        color_ticks_eje_y,
        transparencia_fondo);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    var info_menu_contextual = dame_info_menu_contextual_graficas_widgets();
    anyade_menu_contextual(div_grafica, info_menu_contextual, nombre_widget);

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numeros_decimales_valores"] = numeros_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


//
// Funciones auxiliares para el dibujado de gráficas
//


// Ajusta la altura y los valores máximos de una gráfica para que se muestren todas las etiquetas (sin superponerse en los datos de la gráfica)
// y establece la altura de la gráfica
function ajusta_altura_valores_maximos_grafica_etiquetas_widgets(div_grafica, etiquetas, mins_y, maxs_y, altura_extra) {
    if (etiquetas != null) {
        // Tamaño de letra en pixeles
        var tamanyo_letra_pixeles = $.getDefaultPx("#body");

        // Ajuste de altura para que se muestren todas las etiquetas correctamente en las gráficas
        var altura_grafica_inicial = $('#' + div_grafica).height();
        var altura_anyadida_etiquetas_valores = 0;
        if (etiquetas.length > 0) {
            altura_anyadida_etiquetas_valores = MARGEN_ETIQUETAS_GRAFICAS_VALORES + (etiquetas.length * ALTURA_ETIQUETA_GRAFICAS_VALORES);
        }
        var altura_grafica = altura_grafica_inicial + (altura_anyadida_etiquetas_valores + altura_extra) * tamanyo_letra_pixeles;
        var porcentaje_etiquetas_altura_grafica = altura_grafica / altura_grafica_inicial;

        // Altura
        $("#" + div_grafica).height(altura_grafica);

        // Se aumenta el rango de valores para dejar espacio para las etiquetas
        for (var i = 0; i < maxs_y.length; i++) {
            var rango_valores_y = maxs_y[i] - mins_y[i];
            rango_valores_y *= porcentaje_etiquetas_altura_grafica;
            maxs_y[i] = mins_y[i] + rango_valores_y;
            var redondeo = dame_redondeo_rango_valores_eje(rango_valores_y);
            if (redondeo > 0) {
                maxs_y[i] = mins_y[i] + Math.ceil(rango_valores_y / redondeo) * redondeo;
            }
        }
    }

    // Se devuelven los valores máximos
    return (maxs_y);
}


// Devuelve información del menú contextual de las gráficas de los widgets
function dame_info_menu_contextual_graficas_widgets() {
    var exportacion_valores = exportacion_valores_sensores;
    var opciones_menu_contextual_graficas = [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN];
    if (exportacion_valores == true) {
        opciones_menu_contextual_graficas.push(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES);
    }
    var info_menu_contextual_graficas = {
        "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT_WIDGET,
        "opciones": opciones_menu_contextual_graficas};
    return (info_menu_contextual_graficas);
}
