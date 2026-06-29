/*
 * Funciones de gráficas
 *
 */


// Muestra una gráfica temporal de líneas de valores
function muestra_grafica_temporal_lineas_valores(
    div_grafica,
    altura_grafica,
    titulo_grafica,
    etiquetas,
    valores, bandas_valores, intervalo_valores,
    ticks_eje_y,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    tooltip_personalizado,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Flag para mostrar las etiquetas fuera de la gráfica
    var etiquetas_fuera_grafica = (etiquetas != null) && (etiquetas.length > NUMERO_MAXIMO_ETIQUETAS_DENTRO_GRAFICAS);

    // Valores mínimo y máximo del eje y
    var min_y = valor_minimo;
    if (ajustar_valor_minimo == true) {
        min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }

    // Nota: En ocasiones al cambiar la altura, puede cambiar la anchura
    // (por eso se guarda la anchura original y a jqplot se le envían la anchura y la altura correctas)

    // Si hay altura de gráfica
    var anchura_grafica = $("#" + div_grafica).width();
    if (altura_grafica != null) {
        // Se le suma la altura del título y de las fechas de las gráficas (las etiquetas no incrementan la altura de las gráficas)
        var tamanyo_letra_pixeles = $.getDefaultPx("#body");
        altura_grafica += tamanyo_letra_pixeles * (ALTURA_TITULO_GRAFICAS + ALTURA_FECHA_TICKS_EJE_X_GRAFICAS);
        $("#" + div_grafica).height(altura_grafica);
    }
    else {
        // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
        if (etiquetas_fuera_grafica == false) {
            max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [min_y], [max_y], 0, true)[0];
        }
        else {
            max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, [], [min_y], [max_y], 0, true)[0];
        }
    }
    altura_grafica = $("#" + div_grafica).height();

    // Si hay etiquetas fuera de gráfica
    if (etiquetas_fuera_grafica == true) {
        // Parámetros de la gráfica de jqplot (vacía)
        var valores_grafica_vacia = [];
        for (var i = 0; i < etiquetas.length; i++) {
            valores_grafica_vacia.push([0, 0]);
        }
        var parametros_grafica_vacia_jqplot = {
            target_width: anchura_grafica,
            target_height: altura_grafica,
            title: titulo_grafica,
            animate: mostrar_animacion,
            seriesColors: colores_graficas_jqplot,
            series: [],
            axes: {
                xaxis: {
                    renderer: $.jqplot.DateAxisRenderer,
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                    tickOptions: {
                        angle: -30,
                        fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                    }
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
            legend: {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas
            }
        };

        // Nota: Se dibuja primero la gráfica vacía para poder calcular
        // la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica
        $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

        // Se aumenta la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica (si es necesario)
        altura_grafica = ajusta_altura_grafica_etiquetas_fuera_grafica(div_grafica, altura_grafica, etiquetas_fuera_grafica);

        // Se borra la gráfica auxiliar
        $('#' + div_grafica).html("");
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        series: [],
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0
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
        },
        captureRightClick: true
    };

    // Ticks del eje x
    if (ticks_eje_y != null) {
        parametros_grafica_jqplot["axes"]["yaxis"]["ticks"] = ticks_eje_y;
    }

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

    // Tooltip (esquina superior derecha)
    // - Nota: El tooltip personalizado no se muestra en el cursor porque pueden ser varias líneas y no habría sitio
    //   (debe ser como mucho una línea por 'valor')
    parametros_grafica_jqplot["cursor"]["showTooltip"] = true;
    parametros_grafica_jqplot["cursor"]["tooltipLocation"] = 'ne';
    parametros_grafica_jqplot["cursor"]["showVerticalLine"] = true;
    parametros_grafica_jqplot["cursor"]["showTooltipDataPosition"] = true;
    var cadena_tooltip = "";
    if (etiquetas != null) {
        cadena_tooltip += "[%1$s] ";
    }
    cadena_tooltip += '%3$s (%2$s)';
    parametros_grafica_jqplot["cursor"]["tooltipFormatString"] = cadena_tooltip;
    parametros_grafica_jqplot["cursor"]["intersectionThreshold"] = 8;

    // Nota: 'bringSeriesToFront: true' (en 'highlighter') no funciona (en ocasiones desaparecen las gráficas)

    // Nombres de series
    var parametros_series_anyadidos = false;
    if (etiquetas != null) {
        for (var i = 0; i < valores.length; i++) {
            var parametros_serie = {
                label: etiquetas[i]
            };
            parametros_grafica_jqplot["series"].push(parametros_serie);
        }
        parametros_series_anyadidos = true;
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
        if (etiquetas != null) {
            if (valores.length > 1) {
                var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
                    var cadena_tooltip_modificada = "[" + plot.series[seriesIndex]["label"] + "]</br>" + cadena_tooltip;
                    return (cadena_tooltip_modificada);
                };
                parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
            }
        }
    }

    // Etiquetas
    if (etiquetas != null) {
        if (etiquetas_fuera_grafica == false) {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'nw',
                labels: etiquetas
            };
        }
        else {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas
            };
        }
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

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        ajustar_fechas,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    parametros_grafica_globales["valores_extra"] = tooltip_personalizado;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica puntual de líneas de valores
function muestra_grafica_puntual_lineas_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores, bandas_valores,
    ticks_eje_x, unidad_ticks_eje_x, rotar_ticks_eje_x,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valores mínimo y máximo del eje y
    var porcentaje_margen_graficas = PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES;
    if (bandas_valores != null) {
        porcentaje_margen_graficas = PORCENTAJE_MARGEN_GRAFICAS_LINEAS_BANDAS_VALORES;
    }
    var min_y = valor_minimo;
    if (ajustar_valor_minimo == true) {
        min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, porcentaje_margen_graficas);
    }
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, porcentaje_margen_graficas);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [min_y], [max_y], 0, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Ángulo de inclinación de ticks del eje X
    // (si no se inclinan y el texto del tick es largo, el texto se "sale" de la gráfica)
    var angulo_ticks_eje_x = null;
    if (rotar_ticks_eje_x == true) {
        angulo_ticks_eje_x = -30;
    }
    else {
        angulo_ticks_eje_x = 0;
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: angulo_ticks_eje_x,
                    formatString: '%d',
                    fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                }
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
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        series: [],
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0
            },
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: true,
            breakOnNull: true
        },
        legend: {
            show: false
        }
    };

    // Ticks del eje x
    if (ticks_eje_x != null) {
        parametros_grafica_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;
    }

    // Formato de tooltip
    var formato_tooltip = "%1$s";
    if (unidad_ticks_eje_x != "") {
        formato_tooltip += " " + unidad_ticks_eje_x;
    }
    formato_tooltip += " - %2$s";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }
    parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;

    // Si hay textos en los ticks del eje x, se muestra el texto en lugar del índice
    // (p.e. días de la semana en el informe de análisis diario)
    if ((ticks_eje_x != null) && (ticks_eje_x.length > 0) && (ticks_eje_x[0].length == 2)) {
        var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
            var partes_cadena_tooltip = cadena_tooltip.split(" - ");
            var valor = partes_cadena_tooltip[1];
            var cadena_tooltip_modificada = plot.options.axes.xaxis.ticks[pointIndex][1] + " - " + valor;
            return (cadena_tooltip_modificada);
        };
        parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
    }

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: true,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Bandas de valores
    if (bandas_valores != null) {
        for (var i = 0; i < bandas_valores.length; i++) {
            var parametros_banda_valores = {
                rendererOptions: {
                    bandData: bandas_valores[i],
                    smooth: false
                }
            };
            parametros_grafica_jqplot["series"].push(parametros_banda_valores);
        }
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["ticks_eje_x"] = ticks_eje_x;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de barras de valores
function muestra_grafica_temporal_barras_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores, intervalo_valores, numero_huecos_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    apilar_valores, mostrar_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Flag para mostrar las etiquetas fuera de la gráfica
    var etiquetas_fuera_grafica = (etiquetas != null) && (etiquetas.length > NUMERO_MAXIMO_ETIQUETAS_DENTRO_GRAFICAS);

    // Se crea una variable ""LOCAL"" para poder personalizar colores
    // y que no se cambie en todas las gráficas

    colores_grafica = colores_graficas_jqplot.slice();

    if (colores_graficas_jqplot == COLORES_GRAFICAS_JQPLOT_EJENER){
        colores_grafica = cambia_colores_periodos_grafica(etiquetas);
    }
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICAS_BARRAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_anyadida_valor_barras = null;
    if (mostrar_valores == true) {
        altura_anyadida_valor_barras = ALTURA_VALOR_BARRAS_VALORES;
    }
    else {
        altura_anyadida_valor_barras = 0;
    }
    if (etiquetas_fuera_grafica == false) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    else {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, [], [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    var altura_grafica = $("#" + div_grafica).height();

    // Segundos del intervalo de valores
    var segundos_intervalo_valores = dame_segundos_intervalo_valores(intervalo_valores);

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [[[0, 0]]];
    var parametros_grafica_vacia_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        animate: false,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
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
            },
            legend: {
                show: false
            }
        }
    };

    // Etiquetas
    if (etiquetas_fuera_grafica == true) {
        parametros_grafica_vacia_jqplot["legend"] = {
            show: true,
            location: 'ne',
            placement: 'outsideGrid',
            labels: etiquetas
        };
    }

    // Nota: Se dibuja primero la gráfica vacía para poder recuperar la anchura en pixels de la gráfica
    // para poder calcular la anchura correcta de las barras y la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica
    $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

    // Se aumenta la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica (si es necesario)
    altura_grafica = ajusta_altura_grafica_etiquetas_fuera_grafica(div_grafica, altura_grafica, etiquetas_fuera_grafica);

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
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_grafica,
        stackSeries: apilar_valores,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
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
                barWidth: anchura_barras_valores
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
        if (etiquetas_fuera_grafica == false) {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'nw',
                labels: etiquetas
            };
        }
        else {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas
            };
        }
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
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

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

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


// Muestra una gráfica puntual de barras de valores
function muestra_grafica_puntual_barras_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    ticks_eje_x, valores_ticks_eje_x_sombreado_grafica,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    apilar_valores, mostrar_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Flag para mostrar las etiquetas fuera de la gráfica
    var etiquetas_fuera_grafica = (etiquetas != null) && (etiquetas.length > NUMERO_MAXIMO_ETIQUETAS_DENTRO_GRAFICAS);

    valores_grafica_puntual_barras_valores = valores.slice();
    etiquetas_grafica_puntual_barras_valores = etiquetas.slice();

    // Se generan al revés en el PHP por lo que se invierten aquí
    if ((div_grafica == 'grafica-consumos-totales-comparacion-periodos') || (div_grafica == 'grafica-costes-totales-comparacion-periodos') || (div_grafica == 'grafica-precios-medios-comparacion-periodos')) {
        console.log('Entra en la 1a condicion');
        etiquetas_grafica_puntual_barras_valores.reverse();
    }
    console.log('despues del if '+etiquetas_grafica_puntual_barras_valores);

    // Se crea una variable ""LOCAL"" para poder personalizar colores
    // y que no se cambie en todas las gráficas
    colores_grafica = colores_graficas_jqplot.slice();
    if (~div_grafica.indexOf('comparacion-periodos')){
        colores_grafica = colores_grafica.slice(0,2);
        colores_grafica.reverse();
    }

    if (colores_graficas_jqplot == COLORES_GRAFICAS_JQPLOT_EJENER){
        colores_grafica = cambia_colores_periodos_grafica(etiquetas_grafica_puntual_barras_valores);
    }

    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICAS_BARRAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_anyadida_valor_barras = null;
    if (mostrar_valores == true) {
        altura_anyadida_valor_barras = ALTURA_VALOR_BARRAS_VALORES;
    }
    else {
        altura_anyadida_valor_barras = 0;
    }
    if (etiquetas_fuera_grafica == false) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas_grafica_puntual_barras_valores, [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    else {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, [], [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [];
    for (var i = 0; i < etiquetas_grafica_puntual_barras_valores.length; i++) {
        valores_grafica_vacia.push([0, 0]);
    }
    var parametros_grafica_vacia_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
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
            },
            legend: {
                show: false
            }
        }
    };

    // Ticks del eje x
    if (ticks_eje_x != null) {
        parametros_grafica_vacia_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;
    }

    // Etiquetas
    if (etiquetas_fuera_grafica == true) {
        parametros_grafica_vacia_jqplot["legend"] = {
            show: true,
            location: 'ne',
            placement: 'outsideGrid',
            labels: etiquetas_grafica_puntual_barras_valores
        };
    }

    // Nota: Se dibuja primero la gráfica vacía para poder calcular la anchura correcta de las barras y
    // la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica
    $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

    // Se aumenta la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica (si es necesario)
    altura_grafica = ajusta_altura_grafica_etiquetas_fuera_grafica(div_grafica, altura_grafica, etiquetas_fuera_grafica);

    // Anchura de las barras de la gráfica
    var anchura_grafica_valores = $('#' + div_grafica + ' .jqplot-event-canvas').width();
    var numero_ticks_eje_x = null;
    if (ticks_eje_x != null) {
        numero_ticks_eje_x = ticks_eje_x.length;
    } else {
        numero_ticks_eje_x = 1;
    }
    var numero_valores_tick_eje_x = null;
    if (etiquetas_grafica_puntual_barras_valores != null) {
        if (apilar_valores == true) {
            numero_valores_tick_eje_x = 1;
        }
        else {
            numero_valores_tick_eje_x = etiquetas_grafica_puntual_barras_valores.length;
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
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_grafica,
        stackSeries: apilar_valores,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
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
                barPadding: separacion_barras_valores
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
        if ((etiquetas_grafica_puntual_barras_valores != null) && (etiquetas_grafica_puntual_barras_valores.length > 1)) {
            cadena_tooltip += "[" + etiquetas_grafica_puntual_barras_valores[seriesIndex] + "]" + "</br>";
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
    if (etiquetas_grafica_puntual_barras_valores != null) {
        if (etiquetas_fuera_grafica == false) {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'nw',
                labels: etiquetas_grafica_puntual_barras_valores
            };
        }
        else {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas_grafica_puntual_barras_valores
            };
        }
    }

    // Si hay sombreado de gráfica
    if (valores_ticks_eje_x_sombreado_grafica != null) {
        // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
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

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores_grafica_puntual_barras_valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["ticks_eje_x"] = ticks_eje_x;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);

    // Se ordenan las etiquetas (con valores apilados se muestran al revés)
    if ((etiquetas_grafica_puntual_barras_valores != null) && (apilar_valores == true)) {
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


// Muestra una gráfica puntual de barras de etiquetas y valores
function muestra_grafica_puntual_barras_etiquetas_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    ticks_eje_x,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida, apilar_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICAS_BARRAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_anyadida_etiqueta_barras = ALTURA_VALOR_BARRAS_VALORES;
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, [], [0], [max_y], altura_anyadida_etiqueta_barras, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Tamaño de letra en pixeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    var tamanyo_fuente_ticks_eje_x = tamanyo_letra_pixeles * 0.75;
    var cadena_tamanyo_fuente_ticks_eje_x = tamanyo_fuente_ticks_eje_x.toString() + "pt";

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        stackSeries: apilar_valores,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
                tickOptions: {
                    formatString: '%d',
                    fontSize: cadena_tamanyo_fuente_ticks_eje_x
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
            show: true,
            tooltipAxes: 'y',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            rendererOptions: {
                shadowOffset: 0,
                varyBarColor: true
            }
        }
    };

    // Formato de tooltip
    var formato_tooltip = "%1$s";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }
    parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;

    // Ticks del eje x
    if (ticks_eje_x != null) {
        parametros_grafica_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;
    }

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["series"] = [{
            pointLabels: {
                show: true,
                labels: etiquetas
            }
        }];
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    parametros_grafica_globales["etiquetas"] = etiquetas;
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


// Muestra una gráfica de tarta de valores
function muestra_grafica_tarta_valores(
    div_grafica,
    titulo_grafica,
    titulo_eje_x,
    etiquetas,
    valores,
    numero_decimales_valores, unidad_medida,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Tamaño de letra en píxeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");

    // Flag para mostrar las etiquetas fuera de la gráfica
    var etiquetas_fuera_grafica = (etiquetas != null) && (etiquetas.length > NUMERO_MAXIMO_ETIQUETAS_DENTRO_GRAFICAS);

    // Ajuste de altura para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_anyadida_etiquetas_valores = 0;
    if (etiquetas_fuera_grafica == false) {
        if ((etiquetas != null) && (etiquetas.length > MAX_ETIQUETAS_GRAFICAS_VALORES)) {
            altura_anyadida_etiquetas_valores = (etiquetas.length - MAX_ETIQUETAS_GRAFICAS_VALORES) * ALTURA_ETIQUETA_GRAFICAS_VALORES;
        }
    }
    var altura_grafica = ALTURA_GRAFICAS_VALORES + (altura_anyadida_etiquetas_valores * tamanyo_letra_pixeles) + (tamanyo_letra_pixeles * 1.2);
    altura_grafica += (tamanyo_letra_pixeles * ALTURA_TITULO_GRAFICAS);
    $("#" + div_grafica).height(altura_grafica);

    // Si hay etiquetas fuera de gráfica
    if (etiquetas_fuera_grafica == true) {
        // Parámetros de la gráfica de jqplot (vacía)
        var valores_grafica_vacia = [];
        for (var i = 0; i < etiquetas.length; i++) {
            valores_grafica_vacia.push(["", 0]);
        }
        var parametros_grafica_vacia_jqplot = {
            target_width: anchura_grafica,
            target_height: altura_grafica,
            title: titulo_grafica,
            animate: mostrar_animacion,
            seriesColors: colores_graficas_jqplot,
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: [titulo_eje_x],
                    tickOptions: {
                        fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                    }
                }
            },
            legend: {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas
            }
        };

        // Nota: Se dibuja primero la gráfica vacía para poder calcular
        // la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica
        $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

        // Se aumenta la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica (si es necesario)
        altura_grafica = ajusta_altura_grafica_etiquetas_fuera_grafica(div_grafica, altura_grafica, etiquetas_fuera_grafica);

        // Se borra la gráfica auxiliar
        $('#' + div_grafica).html("");
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: [titulo_eje_x],
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
                padding: tamanyo_letra_pixeles,
                showDataLabels: true,
                dataLabelFormatString: "%.2f %",
                dataLabelThreshold: PORCENTAJE_MINIMO_VALOR_VISIBLE_TARTA_VALORES,
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
            cadena_tooltip = "[" + etiquetas[pointIndex] + "]" + "</br>";
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
        if (etiquetas_fuera_grafica == false) {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'nw',
                labels: etiquetas
            };
        }
        else {
            parametros_grafica_jqplot["legend"] = {
                show: true,
                location: 'ne',
                placement: 'outsideGrid',
                labels: etiquetas
            };
        }
    }

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_tarta_valores"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas y puntos de valores
function muestra_grafica_temporal_lineas_puntos_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores, intervalo_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    mostrar_indicadores_valores,
    tooltip_personalizado,
    mostrar_animacion,
    anyadir_menu_contextual) {
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
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [min_y], [max_y], 0, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        series: [
            {},
            {
                showLine: false,
                showMarker: true,
                rendererOptions: {
                    smooth: true
                },
                markerOptions: {
                    style: 'filledSquare',
                    size: 8
                }
            }
        ],
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0
            },
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: mostrar_indicadores_valores,
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

    // Formato de tooltip
    if (tooltip_personalizado == false) {
        var formato_tooltip = "%2$s";
        if (unidad_medida != "") {
            formato_tooltip += " " + unidad_medida;
        }
        formato_tooltip += " (%1$s)";
        parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;
    }
    else {
        var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
            cadena_tooltip = plot.series[seriesIndex].data[pointIndex][2];
            cadena_tooltip = unescapeHtml(cadena_tooltip);
            return (cadena_tooltip);
        };
        parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
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

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        ajustar_fechas,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica puntual de línea de valores con puntos independientes
function muestra_grafica_lineal_lineas_valores_puntos(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    tick_minimo_eje_x, tick_maximo_eje_x, intervalo_ticks_eje_x, unidad_ticks_eje_x,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    puntos_grafica,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valor mínimo y máximo del eje y
    var min_y = valor_minimo;
    if (ajustar_valor_minimo == true) {
        min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [min_y], [max_y], 0, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                min: tick_minimo_eje_x,
                max: tick_maximo_eje_x,
                renderer: $.jqplot.LinearAxisRenderer,
                tickOptions: {
                    formatString: '%d'
                }
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
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        series: [
            {
                rendererOptions: {
                    shadowOffset: 0
                },
                showMarker: false
            }
        ],
        legend: {
            show: false
        }
    };

    // Intervalo de 'ticks' del eje X
    if (intervalo_ticks_eje_x != null) {
        parametros_grafica_jqplot["axes"]["xaxis"]["tickInterval"] = intervalo_ticks_eje_x;
    }

    // Formato de tooltip
    var formato_tooltip = "%1$s";
    if (unidad_ticks_eje_x != "") {
        formato_tooltip += " " + unidad_ticks_eje_x;
    }
    formato_tooltip += " - %2$s";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }
    parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;

    // Etiquetas
    if (etiquetas != null) {
        parametros_grafica_jqplot["legend"] = {
            show: true,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Se añaden los puntos a las series de la gráfica y a los parámetros
    var series_grafica = [];
    series_grafica.push(valores);
    for (var i = 0; i < puntos_grafica.length; i++) {
        series_grafica.push(
            [[puntos_grafica[i]["x"], puntos_grafica[i]["y"], puntos_grafica[i]["nombre"]]]);
        var formato_tooltip = "%1$s";
        if (unidad_ticks_eje_x != "") {
            formato_tooltip += " " + unidad_ticks_eje_x;
        }
        formato_tooltip += " - %2$s";
        if (unidad_medida != "") {
            formato_tooltip += " " + unidad_medida;
        }
        formato_tooltip += " [%3$s]";
        parametros_grafica_jqplot["series"].push(
            {
                highlighter: {
                    tooltipAxes: 'both',
                    yvalues: 2,
                    formatString: formato_tooltip
                },
                showLine: false,
                markerOptions: {
                    shadowOffset: 0,
                    style: dame_estilo_punto_grafica(puntos_grafica[i]["icono"])
                }
            }
        );
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, series_grafica, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica de curva de Lorenz
function muestra_grafica_curva_lorenz(
    div_grafica,
    titulo_grafica,
    etiqueta_eje_x,
    etiqueta_eje_y,
    valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Altura de la gráfica
    var altura_grafica = ALTURA_GRAFICAS_CURVA_LORENZ;
    $("#" + div_grafica).height(altura_grafica);

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                max: 100,
                pad: 0,
                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    fontSize: dame_cadena_tamanyo_letra_texto_ticks_eje_x()
                },
                label: etiqueta_eje_x
            },
            yaxis: {
                max: 100,
                pad: 0,
                tickOptions: {
                    formatString: '%.2f'
                },
                labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
                label: etiqueta_eje_y
            }
        },
        series: [{
            rendererOptions: {
                smooth: true
            }
        }],
        cursor: {
            show: true,
            zoom: true,
            showTooltip: false
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            formatString: "%1$s - %2$s"
        },
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0
            },
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: true
        },
        legend: {
            show: false
        },
        canvasOverlay: {
            show: true,
            objects: [{
                line: {
                    start: [0, 0],
                    stop: [100, 100],
                    lineWidth: 3,
                    color: 'rgb(170, 170, 170)',
                    shadow: false,
                    showTooltip: false
                }
            }]
        }
    };

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, [valores], parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 2;
    parametros_grafica_globales["numero_decimales_valores"] = 2;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica de pareto
function muestra_grafica_pareto(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    numero_valor_destacado_pareto, indice_color_barra_valor_destacado_pareto,
    mostrar_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICA_PARETO);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_anyadida_valor_barras = null;
    if (mostrar_valores == true) {
        altura_anyadida_valor_barras = ALTURA_VALOR_BARRAS_VALORES;
    }
    else {
        altura_anyadida_valor_barras = 0;
    }
    if ((numero_valor_destacado_pareto == null) || (etiquetas == null)) {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, null, [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    else {
        max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, [etiquetas[0]], [0], [max_y], altura_anyadida_valor_barras, false)[0];
    }
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [[[0, 0]]];
    var parametros_grafica_vacia_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
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
    var ticks_eje_x = [];
    for (var i = 0; i < etiquetas.length; i++) {
        ticks_eje_x.push(i + 1);
    }
    parametros_grafica_vacia_jqplot["axes"]["xaxis"]["ticks"] = ticks_eje_x;

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
    var numero_valores_tick_eje_x = 1;
    var anchura_barras_valores = Math.floor((anchura_grafica_valores / (numero_ticks_eje_x * numero_valores_tick_eje_x)) * PORCENTAJE_SEPARACION_BARRAS_GRAFICAS_PUNTUALES);
    var separacion_barras_valores = Math.floor((anchura_grafica_valores - (anchura_barras_valores * numero_ticks_eje_x * numero_valores_tick_eje_x)) /
        (numero_ticks_eje_x * (numero_valores_tick_eje_x + 1)));
    if (separacion_barras_valores == 0) {
        anchura_barras_valores -= 1;
        separacion_barras_valores = 1;
    }

    // Se borra la gráfica auxiliar
    $('#' + div_grafica).html("");

    // Colores (todos del mismo color) y el destacado del color indicado
    var colores_grafica_pareto = [];
    for (var i = 0; i < etiquetas.length; i++) {
        colores_grafica_pareto.push(COLOR_VALOR_GRAFICA_PARETO);
    }
    if (numero_valor_destacado_pareto != null) {
        colores_grafica_pareto[numero_valor_destacado_pareto] = colores_graficas_jqplot[indice_color_barra_valor_destacado_pareto];
    }

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_grafica_pareto,
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
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
                varyBarColor: true
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

    // Función de formateo de tooltip:
    // - Se añade la etiqueta
    // - Si hay más de un valor en los datos del punto (de la gráfica), el valor es el segundo (el primero es el índice del eje X)
    // - Si hay más de 1 tick en el eje X (si hay sólo uno es el título) y hay más de 1 valor en el punto, se añade el texto del tick del eje X
    var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
        cadena_tooltip = "";
        if (etiquetas != null) {
            cadena_tooltip += "[" + etiquetas[pointIndex] + "]" + "</br>";
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
    if ((numero_valor_destacado_pareto != null) && (etiquetas != null)) {
        parametros_grafica_jqplot["legend"] = {
            show: true,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, [valores], parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["ticks_eje_x"] = ticks_eje_x;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 0;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de valores Sí/No
function muestra_grafica_temporal_valores_si_no(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores, intervalo_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    lineas_grafica,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    tooltip_personalizado,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Tamaño de letra en pixeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");

    // Se le suma la altura del título y de las fechas de la graficas
    var anchura_grafica = $("#" + div_grafica).width();
    var altura_grafica = ALTURA_GRAFICAS_VALORES_SI_NO;
    altura_grafica += tamanyo_letra_pixeles * (ALTURA_TITULO_GRAFICAS + ALTURA_FECHA_TICKS_EJE_X_GRAFICAS);
    $("#" + div_grafica).height(altura_grafica);
    altura_grafica = $("#" + div_grafica).height();

    // Valores de ticks del eje y
    var valor_tick_no = -0.20;
    var valor_tick_si = 1.20;

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        series: [],
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
            yAxisFormatString: "%'.2f"
        },
        seriesDefaults: {
            rendererOptions: {
                shadowOffset: 0
            },
            showLine: (mostrar_lineas_valores == true),
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: ((mostrar_lineas_valores == false) || (mostrar_indicadores_valores == true)),
            breakOnNull: true
        },
        captureRightClick: true
    };

    // Nombres de series
    if (etiquetas != null) {
        for (var i = 0; i < valores.length; i++) {
            var parametros_serie = {
                label: etiquetas[i]
            };
            parametros_grafica_jqplot["series"].push(parametros_serie);
        }
    }

    // Tooltip
    if (tooltip_personalizado == false) {
        parametros_grafica_jqplot["highlighter"]["useYTickMarks"] = true;
        parametros_grafica_jqplot["highlighter"]["tooltipAxes"] = 'both';
        parametros_grafica_jqplot["highlighter"]["formatString"] = '%2$s (%1$s)';
    }
    else {
        var funcion_formateo_cadena_tooltip = function(cadena_tooltip, seriesIndex, pointIndex, plot) {
            cadena_tooltip = plot.series[seriesIndex].data[pointIndex][2];
            cadena_tooltip = unescapeHtml(cadena_tooltip);
            return (cadena_tooltip);
        };
        parametros_grafica_jqplot["highlighter"]["tooltipContentEditor"] = funcion_formateo_cadena_tooltip;
    }

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    if ((mostrar_lineas_valores == true) && (tipo_lineas_valores == TIPO_LINEAS_VALORES_CUADRADAS)) {
        valores[0] = dame_valores_lineas_cuadradas(valores[0]);
    }

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

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        ajustar_fechas,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = 2;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores de horas
function muestra_grafica_temporal_lineas_valores_horarios(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    fecha_minima, fecha_maxima, intervalo_etiquetas_fechas,
    lineas_grafica,
    mostrar_indicadores_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Mostrado de etiquetas
    var mostrar_etiquetas = true;
    if (etiquetas == null) {
        mostrar_etiquetas = false;
    }

    // Se le suma la altura del título
    var anchura_grafica = $("#" + div_grafica).width();
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    var altura_grafica = ALTURA_GRAFICAS_VALORES;
    altura_grafica += tamanyo_letra_pixeles * ALTURA_TITULO_GRAFICAS;
    $("#" + div_grafica).height(altura_grafica);
    altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                min: fecha_minima.getTime(),
                max: fecha_maxima.getTime(),
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickInterval: intervalo_etiquetas_fechas,
                tickOptions: {
                    formatString: formato_fecha_local_jqplot,
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
                shadowOffset: 0
            },
            markerOptions: {
                shadowOffset: 0
            },
            showMarker: mostrar_indicadores_valores
        },
        legend: {
            show: mostrar_etiquetas,
            location: 'nw',
            labels: etiquetas
        }
    };

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    var milisegundos_desplazamiento_rectangulos_fin_semana = (86400 / 2) * 1000 * (-1);
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        true,
        milisegundos_desplazamiento_rectangulos_fin_semana);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["valores_horarios"] = true;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica de un histograma de valores
function muestra_grafica_histograma_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    tick_minimo_eje_x, tick_maximo_eje_x,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valor máximo del eje y
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, 0, PORCENTAJE_MARGEN_GRAFICA_HISTOGRAMA_VALORES);
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [0], [max_y], 0, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot (vacía)
    var valores_grafica_vacia = [[[0, 0]]];
    var parametros_grafica_vacia_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        animate: false,
        axes: {
            xaxis: {
                renderer: $.jqplot.LinearAxisRenderer,
                min: tick_minimo_eje_x,
                max: tick_maximo_eje_x,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_x(value, numero_decimales_valores, ""));
                    }
                }
            },
            yaxis: {
                max: max_y,
                min: 0
            }
        }
    };

    // Nota: Se dibuja primero la gráfica vacía para poder recuperar la anchura en pixels de la gráfica para poder calcular la anchura correcta de las barras
    $.jqplot(div_grafica, valores_grafica_vacia, parametros_grafica_vacia_jqplot);

    // Anchura de las barras de la gráfica
    var numero_valores_histograma = valores[0].length;
    var anchura_grafica_histograma = $('#' + div_grafica+ ' .jqplot-event-canvas').width();
    var anchura_barras_histograma = Math.floor((anchura_grafica_histograma / numero_valores_histograma) * PORCENTAJE_SEPARACION_BARRAS_HISTOGRAMA);

    // Se borra la gráfica auxiliar
    $('#' + div_grafica).html("");

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.LinearAxisRenderer,
                min: tick_minimo_eje_x,
                max: tick_maximo_eje_x,
                tickOptions: {
                    formatter: function(format, value) {
                        return (formatea_tick_eje_x(value, numero_decimales_valores, ""));
                    }
                }
            },
            yaxis: {
                max: max_y,
                min: 0,
                tickOptions: {
                    formatString: '%.2f'
                }
            }
        },
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            rendererOptions: {
                shadowOffset: 0,
                barWidth: anchura_barras_histograma
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
            tooltipAxes: 'y',
            yvalues: 2,
            formatString: "%2$s"
        },
        legend: {
            show: false
        }
    };

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

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 2;
    parametros_grafica_globales["numero_decimales_valores"] = 2;
    parametros_grafica_globales["valores_extra"] = true;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica de correlación de valores
function muestra_grafica_correlacion_valores(
    div_grafica,
    titulo_grafica,
    etiquetas,
    valores,
    tick_minimo_eje_x, tick_maximo_eje_x, numero_decimales_ticks_eje_x, unidad_ticks_eje_x,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    mostrar_indicadores_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
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
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, [min_y], [max_y], 0, false)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                min: tick_minimo_eje_x,
                max: tick_maximo_eje_x,
                renderer: $.jqplot.LinearAxisRenderer,
                tickOptions: {
                    formatString: "%'." + numero_decimales_ticks_eje_x + "f"
                }
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
            showTooltip: false,
            tooltipLocation: 'sw'
        },
        highlighter: {
            show: true,
            tooltipAxes: 'both',
            yAxisFormatString: "%'." + numero_decimales_valores + "f"
        },
        series: [
            {
                highlighter: {
                    tooltipAxes: 'both',
                    yvalues: 2
                },
                rendererOptions: {
                    shadowOffset: 0
                },
                showLine: false,
                markerOptions: {
                    shadowOffset: 0,
                    style: 'square'
                }
            },
            {
                showMarker: mostrar_indicadores_valores
            }
        ],
        legend: {
            show: false
        }
    };

    // Formato de tooltip
    var formato_tooltip = "%1$s";
    if (unidad_ticks_eje_x != "") {
        formato_tooltip += " " + unidad_ticks_eje_x;
    }
    formato_tooltip += " - %2$s";
    if (unidad_medida != "") {
        formato_tooltip += " " + unidad_medida;
    }
    parametros_grafica_jqplot["highlighter"]["formatString"] = formato_tooltip;
    var formato_tooltip_puntos = formato_tooltip + " (%3$s)";
    parametros_grafica_jqplot["series"][0]["highlighter"]["formatString"] = formato_tooltip_puntos;

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

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = false;
    parametros_grafica_globales["numero_decimales_ticks_eje_x"] = 2;
    parametros_grafica_globales["numero_decimales_valores"] = 2;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores con fechas diferentes
function muestra_grafica_temporal_lineas_valores_fechas_diferentes(
    div_grafica,
    titulo_grafica,
    etiquetas,
    etiquetas_tooltips,
    valores, intervalo_valores,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valor_minimo, ajustar_valor_minimo,
    valor_maximo, ajustar_valor_maximo,
    numero_decimales_valores, unidad_medida,
    lineas_grafica,
    mostrar_lineas_valores,
    tipo_lineas_valores,
    mostrar_indicadores_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valores mínimo y máximo del eje y
    var min_y = valor_minimo;
    if (ajustar_valor_minimo == true) {
        min_y = dame_valor_minimo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }
    var max_y = valor_maximo;
    if (ajustar_valor_maximo == true) {
        max_y = dame_valor_maximo_eje(valor_maximo, valor_minimo, PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
    }
    // Se generan al revés en el PHP por lo que se invierten aquí
    console.log(div_grafica);
    valores_grafica_temporal_lineas_valores_fechas_diferentes = valores.slice();
    etiquetas_grafica_temporal_lineas_valores_fechas_diferentes = etiquetas.slice();
    if ((div_grafica == 'grafica-consumos-comparacion-periodos') || (div_grafica == 'grafica-valores-comparacion-periodos')){
        valores_grafica_temporal_lineas_valores_fechas_diferentes = valores.reverse().slice();
        etiquetas_grafica_temporal_lineas_valores_fechas_diferentes = etiquetas.reverse().slice();
    } else if (div_grafica == 'grafica-costes-comparacion-periodos'){
        valores_grafica_temporal_lineas_valores_fechas_diferentes = valores.reverse().slice();
        //etiquetas_grafica_temporal_lineas_valores_fechas_diferentes = etiquetas.reverse().slice();
    } else if (div_grafica == 'grafica-consumos-periodos-analisis-consumo-estudio-general'){
        valores_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
        etiquetas_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
        console.log('Entra en la 3a condicion');
    } else if (div_grafica == 'grafica-costes-periodos-analisis-coste-estudio-general'){
        valores_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
        etiquetas_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
        console.log('Entra en la 3a condicion');
    } else {
        valores_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
        etiquetas_grafica_temporal_lineas_valores_fechas_diferentes.reverse();
    }

    // Ajuste de altura y de valor máximo para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    max_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas_grafica_temporal_lineas_valores_fechas_diferentes, [min_y], [max_y], 0, true)[0];
    var altura_grafica = $("#" + div_grafica).height();

    // Parámetros de la gráfica de jqplot
    // - En la segunda serie (periodo anterior) se dibujan líneas discontinuas
    var parametros_grafica_jqplot = {
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
                shadowOffset: 0
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

    // Estilo de puntos
    if ((mostrar_lineas_valores == false) && (mostrar_indicadores_valores == false)) {
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["size"] = 1;
        parametros_grafica_jqplot["seriesDefaults"]["markerOptions"]["style"] = "circle";
    }

    // Tipo de líneas de valores
    if ((mostrar_lineas_valores == true) && (tipo_lineas_valores == TIPO_LINEAS_VALORES_CUADRADAS)) {
        for (var i = 0; i < valores_grafica_temporal_lineas_valores_fechas_diferentes.length; i++) {
            valores_grafica_temporal_lineas_valores_fechas_diferentes[i] = dame_valores_lineas_cuadradas(valores_grafica_temporal_lineas_valores_fechas_diferentes[i]);
        }
    }

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

    // Tooltip (esquina superior derecha)
    parametros_grafica_jqplot["cursor"]["showTooltip"] = true;
    parametros_grafica_jqplot["cursor"]["tooltipLocation"] = 'ne';
    parametros_grafica_jqplot["cursor"]["showVerticalLine"] = true;
    parametros_grafica_jqplot["cursor"]["showTooltipDataPosition"] = true;
    parametros_grafica_jqplot["cursor"]["tooltipYvalues"] = 2;
    var cadena_tooltip = "";
    if (etiquetas_grafica_temporal_lineas_valores_fechas_diferentes != null) {
        cadena_tooltip += "[%1$s] ";
    }
    cadena_tooltip += '%3$s (%4$s)';
    parametros_grafica_jqplot["cursor"]["tooltipFormatString"] = cadena_tooltip;
    parametros_grafica_jqplot["cursor"]["intersectionThreshold"] = 8;

    // Nota: 'bringSeriesToFront: true' (en 'highlighter') no funciona (en ocasiones desaparecen las gráficas)

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
    if (etiquetas_grafica_temporal_lineas_valores_fechas_diferentes != null) {
        parametros_grafica_jqplot["legend"] = {
            show: true,
            location: 'nw',
            labels: etiquetas_grafica_temporal_lineas_valores_fechas_diferentes
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Estilo de líneas de la gráfica
    establece_estilo_lineas_grafica();

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        ajustar_fechas,
        0);

    // Se añaden las líneas a la gráfica
    anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores_grafica_temporal_lineas_valores_fechas_diferentes, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numero_decimales_valores"] = numero_decimales_valores;
    parametros_grafica_globales["valores_extra"] = true;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


// Muestra una gráfica temporal de líneas de valores con ejes diferentes (máximo 5)
function muestra_grafica_temporal_lineas_valores_ejes_diferentes(
    div_grafica,
    titulo_grafica,
    etiquetas,
    etiquetas_tooltips,
    valores, intervalo_valores,
    unificar_escalas,
    fecha_minima, fecha_maxima, ajustar_fechas,
    valores_minimos, ajustar_valores_minimos,
    valores_maximos, ajustar_valores_maximos,
    numeros_decimales_valores, unidades_medida,
    mostrar_lineas_valores,
    tipos_lineas_valores,
    mostrar_indicadores_valores,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Valores mínimos y máximos del eje y
    var mins_y = [];
    if (ajustar_valores_minimos == true) {
        for (var i = 0; i < valores_minimos.length; i++) {
            var min_y = dame_valor_minimo_eje(valores_maximos[i], valores_minimos[i], PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
            mins_y.push(min_y);
        }
    }
    else {
        mins_y = valores_minimos;
    }
    var maxs_y = [];
    if (ajustar_valores_maximos == true) {
        for (var i = 0; i < valores_maximos.length; i++) {
            var max_y = dame_valor_maximo_eje(valores_maximos[i], valores_minimos[i], PORCENTAJE_MARGEN_GRAFICAS_LINEAS_VALORES);
            maxs_y.push(max_y);
        }
    }
    else {
        maxs_y = valores_maximos;
    }

    // Ajuste de altura y de valores máximos para que se muestren todas las etiquetas correctamente en las gráficas
    var anchura_grafica = $("#" + div_grafica).width();
    maxs_y = ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, mins_y, maxs_y, 0, true);
    var altura_grafica = $("#" + div_grafica).height();

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
        target_width: anchura_grafica,
        target_height: altura_grafica,
        title: titulo_grafica,
        animate: mostrar_animacion,
        seriesColors: colores_graficas_jqplot,
        axes: {
            xaxis: {
                renderer: $.jqplot.DateAxisRenderer,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                    angle: -30,
                    fontSize: dame_cadena_tamanyo_letra_fecha_ticks_eje_x()
                }
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
            showTooltip: false
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
                shadowOffset: 0
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

    // Tooltip (esquina superior derecha)
    parametros_grafica_jqplot["cursor"]["showTooltip"] = true;
    parametros_grafica_jqplot["cursor"]["tooltipLocation"] = 'ne';
    parametros_grafica_jqplot["cursor"]["showVerticalLine"] = true;
    parametros_grafica_jqplot["cursor"]["showTooltipDataPosition"] = true;
    parametros_grafica_jqplot["cursor"]["tooltipYvalues"] = 2;
    var cadena_tooltip = "";
    if (etiquetas != null) {
        cadena_tooltip += "[%1$s] ";
    }
    cadena_tooltip += '%3$s (%2$s)';
    parametros_grafica_jqplot["cursor"]["tooltipFormatString"] = cadena_tooltip;
    parametros_grafica_jqplot["cursor"]["intersectionThreshold"] = 8;

    // Nota: 'bringSeriesToFront: true' (en 'highlighter') no funciona (en ocasiones desaparecen las gráficas)

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
            show: true,
            location: 'nw',
            labels: etiquetas
        };
    }

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Inicialización del 'overlay' para el dibujado de objetos sobre la gráfica
    parametros_grafica_jqplot["canvasOverlay"] = {
        show: true,
        objects: []
    };

    // Se añaden los rectángulos de los fines de semana a la gráfica
    anyade_rectangulos_sombreado_fin_semana_grafica(
        parametros_grafica_jqplot,
        fecha_minima,
        fecha_maxima,
        ajustar_fechas,
        0);

    // Se dibuja la gráfica
    elimina_grafica_global(div_grafica);
    var grafica = $.jqplot(div_grafica, valores, parametros_grafica_jqplot);
    guarda_grafica_global(div_grafica, grafica);
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = dame_info_menu_contextual_grafica(grafica);
        anyade_menu_contextual(div_grafica, info_menu_contextual, titulo_grafica);
    }

    // Parámetros de la gráfica globales (para la exportación de valores)
    var parametros_grafica_globales = [];
    parametros_grafica_globales["grafica_temporal"] = true;
    parametros_grafica_globales["numeros_decimales_valores"] = numeros_decimales_valores;
    guarda_parametros_grafica_global(div_grafica, parametros_grafica_globales);
}


//
// Funciones de cálculo de valores de los ejes de las gráficas
//


// Devuelve el valor máximo del eje (aplicando el porcentaje del margen y un redondeo)
function dame_valor_maximo_eje(valor_maximo, valor_minimo, porcentaje_margen) {
    if (valor_minimo == valor_maximo) {
        return (valor_maximo + 1);
    }

    var diferencia_valores_eje = valor_maximo - valor_minimo;
    var margen_eje = diferencia_valores_eje * (porcentaje_margen - 1);
    var valor_maximo_eje = valor_maximo + margen_eje;

    var rango_valores_eje = diferencia_valores_eje * porcentaje_margen;
    var redondeo = dame_redondeo_rango_valores_eje(rango_valores_eje);
    if (redondeo > 0) {
        valor_maximo_eje = Math.floor(valor_maximo_eje / redondeo + 1) * redondeo;
    }

    return (valor_maximo_eje);
}


// Devuelve el valor mínimo del eje (aplicando el porcentaje del margen y un redondeo)
function dame_valor_minimo_eje(valor_maximo, valor_minimo, porcentaje_margen) {
    if (valor_minimo == valor_maximo) {
        return (valor_minimo - 1);
    }

    var diferencia_valores_eje = valor_maximo - valor_minimo;
    var margen_eje = diferencia_valores_eje * (porcentaje_margen - 1);
    var valor_minimo_eje = valor_minimo - margen_eje;

    var rango_valores_eje = diferencia_valores_eje * porcentaje_margen;
    var redondeo = dame_redondeo_rango_valores_eje(rango_valores_eje);
    if (redondeo > 0) {
        valor_minimo_eje = Math.ceil(valor_minimo_eje / redondeo - 1) * redondeo;
    }

    return (valor_minimo_eje);
}


// Devuelve el redondeo a aplicar según el valor de valores de un eje
function dame_redondeo_rango_valores_eje(rango_valores_eje) {
    var redondeo = 0;
    if (rango_valores_eje < 0.1) {
        redondeo = 0.01;
    }
    else if (rango_valores_eje < 1) {
        redondeo = 0.05;
    }
    else if (rango_valores_eje < 5) {
        redondeo = 0.10;
    }
    else if (rango_valores_eje < 10) {
        redondeo = 1;
    }
    else if (rango_valores_eje < 20) {
        redondeo = 2;
    }
    else if (rango_valores_eje < 50) {
        redondeo = 5;
    }
    else if (rango_valores_eje < 250) {
        redondeo = 10;
    }
    else if (rango_valores_eje < 500) {
        redondeo = 25;
    }
    else if (rango_valores_eje < 1000) {
        redondeo = 50;
    }
    else if (rango_valores_eje < 5000) {
        redondeo = 100;
    }
    else if (rango_valores_eje < 1000000) {
        redondeo = 1000;
    }
    else {
        redondeo = 5000;
    }
    return (redondeo);
}


//
// Funciones auxiliares
//


// Ajusta la altura y los valores máximos de una gráfica para que se muestren todas las etiquetas (sin superponerse en los datos de la gráfica)
// y establece la altura de la gráfica
function ajusta_altura_valores_maximos_grafica_etiquetas(div_grafica, etiquetas, mins_y, maxs_y, altura_extra, fecha_ticks_eje_x) {
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    if (etiquetas != null) {
        // Ajuste de altura para que se muestren todas las etiquetas correctamente en las gráficas
        var altura_anyadida_etiquetas_valores = MARGEN_ETIQUETAS_GRAFICAS_VALORES + (etiquetas.length * ALTURA_ETIQUETA_GRAFICAS_VALORES);
        var altura_grafica = ALTURA_GRAFICAS_VALORES + (altura_anyadida_etiquetas_valores + altura_extra) * tamanyo_letra_pixeles;
        var porcentaje_etiquetas_altura_grafica = altura_grafica / ALTURA_GRAFICAS_VALORES;

        // Se le suma la altura del título
        altura_grafica += (tamanyo_letra_pixeles * ALTURA_TITULO_GRAFICAS);

        // Se ajusta la altura de las fechas en los ticks del eje X
        if (fecha_ticks_eje_x == true) {
            altura_grafica += (tamanyo_letra_pixeles * ALTURA_FECHA_TICKS_EJE_X_GRAFICAS);
        }
        else {
            altura_grafica += (tamanyo_letra_pixeles * ALTURA_TEXTO_TICKS_EJE_X_GRAFICAS);
        }

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
    else {
        // Se le suman la altura extra y la altura del título
        altura_grafica = ALTURA_GRAFICAS_VALORES + (altura_extra * tamanyo_letra_pixeles);
        altura_grafica += (tamanyo_letra_pixeles * ALTURA_TITULO_GRAFICAS);

        // Altura
        $("#" + div_grafica).height(altura_grafica);
    }

    // Se devuelven los valores máximos
    return (maxs_y);
}


// Aumenta la altura de la gráfica si se dibujan las etiquetas fuera de la gráfica (si es necesario)
function ajusta_altura_grafica_etiquetas_fuera_grafica(div_grafica, altura_grafica, etiquetas_fuera_grafica) {
    if (etiquetas_fuera_grafica == true) {
        var elemento_leyenda = $('#' + div_grafica + ' .jqplot-table-legend').first();
        var altura_leyenda = elemento_leyenda.outerHeight(true) + elemento_leyenda.position().top;
        if (altura_leyenda > altura_grafica) {
            var altura_grafica_modificada = altura_leyenda +
                $('#' + div_grafica + ' .jqplot-title').outerHeight(true) +
                $('#' + div_grafica + ' .jqplot-xaxis').outerHeight(true) +
                (elemento_leyenda.outerHeight(true) - elemento_leyenda.height());
            $("#" + div_grafica).height(altura_grafica_modificada);
            altura_grafica = $("#" + div_grafica).height();
        }
    }
    return (altura_grafica);
}


// Ordena alfabéticamente las etiquetas de una gráfica
function ordena_etiquetas_grafica(id_div_grafica) {
    // (http://stackoverflow.com/questions/15249128/jqplotjquery-sort-legend-labels)
    var rows = $(id_div_grafica + ' .jqplot-table-legend tr').get();
    rows.sort(function(a, b) {
        return $(a).children().last().text().localeCompare($(b).children().last().text());
    });

    $.each(rows, function(index, item) {
        $(id_div_grafica + ' .jqplot-table-legend tbody').append(item);
    });
}


// Devuelve el formato correspondiente de fecha de las gráficas
function dame_cadena_formato_fecha_grafica(formato_fecha) {
    var cadena_formato_fecha = "";
    switch (formato_fecha) {
        case FORMATO_FECHA_GRAFICA_FECHA: {
            cadena_formato_fecha = formato_fecha_local_jqplot;
            break;
        }
        case FORMATO_FECHA_GRAFICA_FECHA_HORA: {
            cadena_formato_fecha = formato_fecha_local_jqplot + ", %H:%M";
            break;
        }
        case FORMATO_FECHA_GRAFICA_FECHA_HORA_SEGUNDO: {
            cadena_formato_fecha = formato_fecha_local_jqplot + ", %H:%M:%S";
            break;
        }
    }
    return (cadena_formato_fecha);
}


// Devuelve el estilo correspondientes de un punto de las gráficas
function dame_estilo_punto_grafica(icono_punto) {
    var estilo_punto = "";
    switch (icono_punto) {
        case ICONO_PUNTO_GRAFICA_CUADRADO: {
            estilo_punto = "square";
            break;
        }
        case ICONO_PUNTO_GRAFICA_X: {
            estilo_punto = "x";
            break;
        }
    }
    return (estilo_punto);
}


// Establece los parámetros para el formato de los números
function establece_parametros_formato_numeros() {
    $.jqplot.sprintf.thousandsSeparator = separador_miles;
    $.jqplot.sprintf.decimalMark = punto_decimal;
}


// Establece el orden (z-indez) de dibujado de los elementos de las gráficas
function establece_orden_dibujado_elementos_grafica() {
    // (http://jsfiddle.net/Boro/XzdHt/)
    // - Nota: Se dibuja el 'overlayCanvas' después del 'series' porque si no los tooltips de las líneas verticales
    //   se muestran 'detrás' de los valores de la gráfica y no se leen correctamente
    $.jqplot.postDrawHooks.push(function() {
        $(".jqplot-series-canvas").css('z-index', '0');
        $(".jqplot-overlayCanvas-canvas").css('z-index', '1');
        $(".jqplot-table-legend").css('z-index', '2');
        $(".jqplot-event-canvas").css('z-index', '3');
        $(".jqplot-meterGauge-tick").css('z-index', '4');
        $(".jqplot-highlighter-tooltip").css('z-index', '5');
    });
}


// Establece el estilo de las líneas de las gráficas
function establece_estilo_lineas_grafica() {
    // Formato de líneas discontínuas
    // (http://www.jqplot.com/deploy/dist/examples/dashedLines.html)
    $.jqplot.config.dashLength = 4;
    $.jqplot.config.gapLength = 3;
}


// Añade los rectangulos de sombreado de fin de semana a la grafica
// (https://bitbucket.org/cleonello/jqplot/pull-requests/23/issue-523-adding-rectangles-to-canvas/diff)
function anyade_rectangulos_sombreado_fin_semana_grafica(
    parametros_grafica_jqplot,
    fecha_minima,
    fecha_maxima,
    fechas_ajustadas,
    milisegundos_desplazamiento_fin_semana) {
    // Nota: Si no hay fecha mínima o máxima, se establecen valores por defecto
    var fecha_inicio = null;
    if (fecha_minima == null) {
        fecha_minima = new Date(2010, 01, 01, 0, 0, 0, 0);
    }
    if (fecha_maxima == null) {
        fecha_maxima = new Date();
    }
    var fecha_inicio = new Date(fecha_minima.getTime());
    var fecha_fin = new Date(fecha_maxima.getTime());
    fecha_inicio.setHours(0, 0, 0, 0);
    fecha_fin.setHours(0, 0, 0, 0);
    if (fechas_ajustadas == false) {
        // Nota: Para pintar todos los fines de semana porque jqplot autoajusta los extremos de la gráfica para redondear los valores del eje x
        var margen_seguridad_dias = 31;
        fecha_inicio.setDate(fecha_inicio.getDate() - margen_seguridad_dias);
        fecha_fin.setDate(fecha_fin.getDate() + margen_seguridad_dias);
    }
    while (fecha_inicio <= fecha_fin) {
        if ((fecha_inicio.getDay() == 6) || (fecha_inicio.getDay() == 0)) {
            var fecha_siguiente_inicio = new Date(fecha_inicio);
            fecha_siguiente_inicio.setDate(fecha_siguiente_inicio.getDate() + 1);

            var milisegundos_fecha_inicio_linea = fecha_inicio.getTime();
            var milisegundos_fecha_fin_linea = fecha_siguiente_inicio.getTime() - 1;
            milisegundos_fecha_inicio_linea += milisegundos_desplazamiento_fin_semana;
            milisegundos_fecha_fin_linea += milisegundos_desplazamiento_fin_semana;

            var rectangulo_fin_semana = {
                rectangle: {
                    xmin: milisegundos_fecha_inicio_linea,
                    xmax: milisegundos_fecha_fin_linea,
                    color: "rgba(150, 150, 150, 0.20)",
                    shadow: false
                }
            };
            parametros_grafica_jqplot["canvasOverlay"]["objects"].push(rectangulo_fin_semana);
        }
        fecha_inicio.setDate(fecha_inicio.getDate() + 1);
    }
}


// Añade las líneas de la gráfica a los parámetros de la gráfica
function anyade_lineas_grafica(parametros_grafica_jqplot, lineas_grafica) {
    if (lineas_grafica == null) {
        return;
    }

    for (var i = 0; i < lineas_grafica.length; i++) {
        var mostrar_tooltip = null;
        var texto_tooltip = null;
        if ("texto_tooltip" in lineas_grafica[i]) {
            mostrar_tooltip = true;
            texto_tooltip = lineas_grafica[i]["texto_tooltip"];
        }
        else {
            mostrar_tooltip = false;
            texto_tooltip = "";
        }

        var parametros_linea_grafica = {};
        switch (lineas_grafica[i]["tipo"]) {
            case TIPO_LINEA_GRAFICA_VERTICAL_CONTINUA: {
                parametros_linea_grafica = {
                    x: lineas_grafica[i]["valor"],
                    yOffset: 0,
                    color: lineas_grafica[i]["color"],
                    lineWidth: 3,
                    shadow: false,
                    showTooltip: mostrar_tooltip,
                    tooltipFormatString: texto_tooltip
                };
                parametros_grafica_jqplot["canvasOverlay"]["objects"].push(
                    {
                        verticalLine: parametros_linea_grafica
                    }
                );
                break;
            }
            case TIPO_LINEA_GRAFICA_HORIZONTAL_CONTINUA: {
                parametros_linea_grafica = {
                    xOffset: 0,
                    y: lineas_grafica[i]["valor"],
                    color: lineas_grafica[i]["color"],
                    lineWidth: 3,
                    shadow: false,
                    showTooltip: mostrar_tooltip,
                    tooltipFormatString: texto_tooltip
                };
                parametros_grafica_jqplot["canvasOverlay"]["objects"].push(
                    {
                        horizontalLine: parametros_linea_grafica
                    }
                );
                break;
            }
            case TIPO_LINEA_GRAFICA_HORIZONTAL_DISCONTINUA: {
                parametros_linea_grafica = {
                    xOffset: 0,
                    y: lineas_grafica[i]["valor"],
                    color: lineas_grafica[i]["color"],
                    lineWidth: 3,
                    shadow: false,
                    showTooltip: mostrar_tooltip,
                    tooltipFormatString: texto_tooltip
                };
                parametros_grafica_jqplot["canvasOverlay"]["objects"].push(
                    {
                        dashedHorizontalLine: parametros_linea_grafica
                    }
                );
                break;
            }
        }
    }
}


// Modifica estilos de la gráfica
function modifica_estilos_grafica(
    parametros_grafica_jqplot,
    color_ticks_eje_y,
    transparencia_fondo) {
    // Color de ticks del eje Y
    if (color_ticks_eje_y != null) {
        // Eje Y
        if ("yaxis" in parametros_grafica_jqplot["axes"] == true) {
            if ("tickOptions" in parametros_grafica_jqplot["axes"]["yaxis"] == false) {
                parametros_grafica_jqplot["axes"]["yaxis"]["tickOptions"] = {};
            }
            parametros_grafica_jqplot["axes"]["yaxis"]["tickOptions"]["textColor"] = color_ticks_eje_y;
        }

        // Ejes Y secundarios (gráfica de campos diferentes)
        if ("y2axis" in parametros_grafica_jqplot["axes"] == true) {
            if ("tickOptions" in parametros_grafica_jqplot["axes"]["y2axis"] == false) {
                parametros_grafica_jqplot["axes"]["y2axis"]["tickOptions"] = {};
            }
            parametros_grafica_jqplot["axes"]["y2axis"]["tickOptions"]["textColor"] = color_ticks_eje_y;
        }
        if ("y3axis" in parametros_grafica_jqplot["axes"] == true) {
            if ("tickOptions" in parametros_grafica_jqplot["axes"]["y3axis"] == false) {
                parametros_grafica_jqplot["axes"]["y3axis"]["tickOptions"] = {};
            }
            parametros_grafica_jqplot["axes"]["y3axis"]["tickOptions"]["textColor"] = color_ticks_eje_y;
        }
        if ("y4axis" in parametros_grafica_jqplot["axes"] == true) {
            if ("tickOptions" in parametros_grafica_jqplot["axes"]["y4axis"] == false) {
                parametros_grafica_jqplot["axes"]["y4axis"]["tickOptions"] = {};
            }
            parametros_grafica_jqplot["axes"]["y4axis"]["tickOptions"]["textColor"] = color_ticks_eje_y;
        }
        if ("y5axis" in parametros_grafica_jqplot["axes"] == true) {
            if ("tickOptions" in parametros_grafica_jqplot["axes"]["y5axis"] == false) {
                parametros_grafica_jqplot["axes"]["y5axis"]["tickOptions"] = {};
            }
            parametros_grafica_jqplot["axes"]["y5axis"]["tickOptions"]["textColor"] = color_ticks_eje_y;
        }
    }

    // Transparencia de fondo
    if ((transparencia_fondo != null) && (transparencia_fondo != 0)) {
        var color_fondo = convierte_color_hexadecimal_rgb(COLOR_FONDO_GRAFICAS_TRANSPARENCIA, transparencia_fondo);
        parametros_grafica_jqplot["grid"] = {
            background: color_fondo
        };
    }
}


// Devuelve la cadena con el tamaño de letra para las fechas de los ticks del eje x
function dame_cadena_tamanyo_letra_fecha_ticks_eje_x() {
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    var tamanyo_fuente_ticks_eje_x = tamanyo_letra_pixeles * TAMANYO_FUENTES_GRAFICAS_FECHA_TICKS_EJE_X;
    var cadena_tamanyo_fuente_ticks_eje_x = tamanyo_fuente_ticks_eje_x.toString() + "pt";
    return (cadena_tamanyo_fuente_ticks_eje_x);
}


// Devuelve la cadena con el tamaño de letra para los textos de los ticks del eje x
function dame_cadena_tamanyo_letra_texto_ticks_eje_x() {
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    var tamanyo_fuente_ticks_eje_x = tamanyo_letra_pixeles * TAMANYO_FUENTES_GRAFICAS_TEXTO_TICKS_EJE_X;
    var cadena_tamanyo_fuente_ticks_eje_x = tamanyo_fuente_ticks_eje_x.toString() + "pt";
    return (cadena_tamanyo_fuente_ticks_eje_x);
}


// Vacía el tooltip de las gráficas
// Nota: Para evitar que se muestre el tooltip en la imagen (es un bug de jqplot)
function vacia_tooltip_graficas() {
    $(".jqplot-highlighter-tooltip").html("");
    $(".jqplot-canvasOverlay-tooltip").html("");
}


// Devuelve los valores para mostrar las líneas cuadradas
function dame_valores_lineas_cuadradas(valores) {
    var valores_lineas_cuadradas = [];
    if (valores.length > 0) {
        var valor_anterior = null;
        var valor_anterior = null;
        var tooltip_anterior = null;
        var timestamp_valor_bucle = null;
        var timestamp_valor_bucle_modificado = null;
        var valor_bucle = null;
        var tooltip_bucle = null;
        var tupla_valor_bucle = null;
        var tupla_valor_bucle_modificado = null;
        for (var i = 0; i < valores.length; i++) {
            tupla_valor_bucle = valores[i];
            timestamp_valor_bucle = tupla_valor_bucle[0];
            valor_bucle = tupla_valor_bucle[1];
            if (tupla_valor_bucle.length > 2) {
                tooltip_bucle = tupla_valor_bucle[2];
            }
            if (valor_anterior != null) {
                if (valor_anterior != valor_bucle) {
                    // Nota: Se resta 1 (ms) al timestamp del valor modificado porque en algunos navegadores (firefox)
                    // dibujaba los puntos con el mismo timestamp en diferente orden
                    timestamp_valor_bucle_modificado = timestamp_valor_bucle - 1;
                    tupla_valor_bucle_modificado = [timestamp_valor_bucle_modificado, valor_anterior];
                    if (tooltip_anterior != null) {
                        tupla_valor_bucle_modificado.push(tooltip_anterior);
                    }
                    valores_lineas_cuadradas.push(tupla_valor_bucle_modificado);
                }
            }
            valores_lineas_cuadradas.push(tupla_valor_bucle);
            valor_anterior = valor_bucle;
            tooltip_anterior = tooltip_bucle;
        }
    }
    return (valores_lineas_cuadradas);
}


// Formatea el 'tick' del eje y
function formatea_tick_eje_y(numero, numero_decimales_ticks_eje_y, unidad_ticks_eje_y) {
    // - Si no es un número (p.e. '24 H' en un valor en análisis horario, no se formatea para no modificar el texto)
    if ((PATRON_NUMERO_ENTERO.test(numero) == true) || (PATRON_NUMERO_REAL_NOTACION_EXPONENCIAL.test(numero) == true)) {
        numero = formatea_numero_limite_digitos(numero, numero_decimales_ticks_eje_y, true);
        var numero_maximo_caracteres_unidad_ticks_eje_y = NUMERO_MAXIMO_CARACTERES_UNIDAD_TICKS_EJE_Y - (numero_decimales_ticks_eje_y - 2);
        if (unidad_ticks_eje_y != "") {
            if (unidad_ticks_eje_y.length > numero_maximo_caracteres_unidad_ticks_eje_y) {
                numero += " " + unidad_ticks_eje_y.substring(0, numero_maximo_caracteres_unidad_ticks_eje_y / 2) + "...";
            }
            else {
                numero += " " + unidad_ticks_eje_y;
            }
        }
    }
    return (numero);
}


// Formatea el 'tick' del eje x
function formatea_tick_eje_x(numero, numero_decimales_ticks_eje_y, unidad_ticks_eje_y) {
    // - Si no es un número (p.e. '24 H' en un valor en análisis horario, no se formatea para no modificar el texto)
    if ((PATRON_NUMERO_ENTERO.test(numero) == true) || (PATRON_NUMERO_REAL_NOTACION_EXPONENCIAL.test(numero) == true)) {
        numero = formatea_numero_limite_digitos(numero, numero_decimales_ticks_eje_y, false);
        var numero_maximo_caracteres_unidad_ticks_eje_y = NUMERO_MAXIMO_CARACTERES_UNIDAD_TICKS_EJE_Y - (numero_decimales_ticks_eje_y - 2);
        if (unidad_ticks_eje_y != "") {
            if (unidad_ticks_eje_y.length > numero_maximo_caracteres_unidad_ticks_eje_y) {
                numero += " " + unidad_ticks_eje_y.substring(0, numero_maximo_caracteres_unidad_ticks_eje_y / 2) + "...";
            }
            else {
                numero += " " + unidad_ticks_eje_y;
            }
        }
    }
    return (numero);
}


// Establece el intervalo mínimo entre fechas en el eje X
function establece_intervalo_minimo_fechas_formato_fecha_eje_x(parametros_grafica_jqplot, intervalo_valores) {
    var segundos_intervalo_valores = null;
    var formato_fecha = null;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL:
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            segundos_intervalo_valores = 60;
            formato_fecha = FORMATO_FECHA_GRAFICA_FECHA_HORA_SEGUNDO;
            break;
        }
        case INTERVALO_VALORES_CUARTOHORA: {
            segundos_intervalo_valores = 900;
            formato_fecha = FORMATO_FECHA_GRAFICA_FECHA_HORA;
            break;
        }
        case INTERVALO_VALORES_HORA: {
            segundos_intervalo_valores = 3600;
            formato_fecha = FORMATO_FECHA_GRAFICA_FECHA_HORA;
            break;
        }
        case INTERVALO_VALORES_DIA:
        case INTERVALO_VALORES_SEMANA:
        case INTERVALO_VALORES_MES: {
            segundos_intervalo_valores = 24 * 3600;
            formato_fecha = FORMATO_FECHA_GRAFICA_FECHA;
            break;
        }
    }
    parametros_grafica_jqplot["axes"]["xaxis"]["minTickInterval"] = segundos_intervalo_valores;
    parametros_grafica_jqplot["axes"]["xaxis"]["tickOptions"]["formatString"] = dame_cadena_formato_fecha_grafica(formato_fecha);
}


// Devuelve los segundos del intervalo de valores
function dame_segundos_intervalo_valores(intervalo_valores) {
    var segundos_intervalo_valores = null;
    switch (intervalo_valores) {
        case INTERVALO_VALORES_TIEMPO_REAL:
        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
            segundos_intervalo_valores = 1;
            break;
        }
        case INTERVALO_VALORES_CUARTOHORA: {
            segundos_intervalo_valores = 900;
            break;
        }
        case INTERVALO_VALORES_HORA: {
            segundos_intervalo_valores = 3600;
            break;
        }
        case INTERVALO_VALORES_DIA: {
            segundos_intervalo_valores = 24 * 3600;
            break;
        }
        case INTERVALO_VALORES_SEMANA: {
            segundos_intervalo_valores = 7 * 24 * 3600;
            break;
        }
        case INTERVALO_VALORES_MES: {
            segundos_intervalo_valores = 30 * 24 * 3600;
            break;
        }
    }
    return (segundos_intervalo_valores);
}


// Nota: Dependiendo del intervalo de valores (como se ajusta a la primera hora del periodo, se modifica el periodo de fin)
function ajusta_fecha_maxima_intervalo_valores_grafica(fecha_maxima, intervalo_valores) {
    switch (intervalo_valores) {
        case INTERVALO_VALORES_DIA: {
            fecha_maxima.setHours(0, 0, 0);
            break;
        }
        case INTERVALO_VALORES_SEMANA: {
            fecha_maxima.setHours(0, 0, 0);
            var dia_semana_fecha_maxima = fecha_maxima.getDay() || 7;
            if (dia_semana_fecha_maxima !== 1) {
                fecha_maxima.setTime(fecha_maxima.getTime() - ((dia_semana_fecha_maxima - 1) * 24 * 3600 * 1000));
            }
            break;
        }
        case INTERVALO_VALORES_MES: {
            fecha_maxima.setDate(1);
            break;
        }
    }
}


//
// Funciones para obtener los valores de las gráficas
//


// Devuelve los nombres de las series de valores de la gráfica especificada
function dame_nombres_series_valores_grafica(div_grafica) {
    var grafica_jqplot = graficas_jqplot[div_grafica];

    // Se recuperan los parámetros de la gráfica
    var parametros_grafica_jqplot = parametros_graficas_jqplot[div_grafica];
    var grafica_tarta_valores = parametros_grafica_jqplot["grafica_tarta_valores"];

    // Si es gráfica de tarta de valores, solo hay una serie sin nombre
    var nombres_series = null;
    if ((grafica_tarta_valores != null) && (grafica_tarta_valores == true)) {
        nombres_series = [null];
    }
    else {
        // Si hay ticks en el eje X y solo hay un valor es una gráfica de barra de valores puntual con solo un valor por serie
        // (se considera como si sólo hubiera una serie sin nombre)
        var ticks_eje_x = parametros_grafica_jqplot["ticks_eje_x"];
        if ((ticks_eje_x != null) && (ticks_eje_x.length == 1)) {
            nombres_series = [null];
        }
        else {
            // Se recuperan los nombres de las series
            nombres_series = grafica_jqplot.legend.labels;
            if ((nombres_series == null) || (nombres_series.length == 0)) {
                nombres_series = [];
                for (var i = 0; i < grafica_jqplot.series.length; i++) {
                    nombres_series.push(null);
                }
            }
        }
    }
    return (nombres_series);
}


// Devuelve las filas de valores de la gráfica y serie especificadas
function dame_filas_valores_serie_valores_grafica(div_grafica, indice_serie) {
    var grafica_jqplot = graficas_jqplot[div_grafica];

    // Se recuperan los parámetros de la gráfica
    var parametros_grafica_jqplot = parametros_graficas_jqplot[div_grafica];
    var grafica_tarta_valores = parametros_grafica_jqplot["grafica_tarta_valores"];
    var grafica_temporal = parametros_grafica_jqplot["grafica_temporal"];
    var numero_decimales_valores = parametros_grafica_jqplot["numero_decimales_valores"];
    var valores_extra = parametros_grafica_jqplot["valores_extra"];

    // Valores de las serie de valores
    var valores_serie_valores_jqplot = grafica_jqplot.series[indice_serie].data;
    var filas_valores_serie_valores_jqplot = [];

    // Si es una tarta de valores, en la serie de valores están el nombre y el valor correspondiente
    if ((grafica_tarta_valores != null) && (grafica_tarta_valores == true)) {
        // Cabecera
        var fila_cabecera_serie_valores_jqplot = [
            TLNT.Idiomas._('Nombre'),
            TLNT.Idiomas._('Valor')];
        filas_valores_serie_valores_jqplot.push(fila_cabecera_serie_valores_jqplot);

        // Filas de valores
        for (var i = 0; i < valores_serie_valores_jqplot.length; i++) {
            var fila_valores_serie_valores_jqplot = [];

            fila_valores_serie_valores_jqplot.push(valores_serie_valores_jqplot[i][0]);

            var valor = valores_serie_valores_jqplot[i][1];
            var valor_formateado = formatea_numero(parseFloat(valor), numero_decimales_valores);
            fila_valores_serie_valores_jqplot.push(valor_formateado);

            filas_valores_serie_valores_jqplot.push(fila_valores_serie_valores_jqplot);
        }
    }
    else {
        // Se recorren los valores de la serie y se formatean si es necesario
        if (grafica_temporal == true) {
            // Cabecera
            var fila_cabecera_serie_valores_jqplot = [
                TLNT.Idiomas._('Timestamp (UTC)'),
                TLNT.Idiomas._('Fecha y hora (local)'),
                TLNT.Idiomas._('Valor')];
            if ((valores_extra != null) && (valores_extra == true)) {
                if (valores_serie_valores_jqplot.length > 0) {
                    for (var j = 2; j < valores_serie_valores_jqplot[0].length; j++) {
                        var numero_valor_extra = j - 1;
                        fila_cabecera_serie_valores_jqplot.push(TLNT.Idiomas._('Extra') + " " + numero_valor_extra);
                    }
                }
            }
            filas_valores_serie_valores_jqplot.push(fila_cabecera_serie_valores_jqplot);

            // Filas de valores
            for (var i = 0; i < valores_serie_valores_jqplot.length; i++) {
                var fila_valores_serie_valores_jqplot = [];

                // Se añade el timestamp (UTC) y la fecha y hora (local)
                var timestamp_utc = valores_serie_valores_jqplot[i][0];
                fila_valores_serie_valores_jqplot.push(timestamp_utc);
                var fecha_local = new Date(timestamp_utc);
                var cadena_fecha_local = convierte_fecha_a_cadena(fecha_local, formato_fecha_local_jquery_ui);
                cadena_fecha_local += ", " + dame_cadena_hora(fecha_local);
                fila_valores_serie_valores_jqplot.push(cadena_fecha_local);

                // Si son valores horarios, se añade sólo la hora del valor (no se añade la fecha)
                var valores_horarios = parametros_grafica_jqplot["valores_horarios"];
                if ((valores_horarios != null) && (valores_horarios == true)) {
                    var timestamp_utc = valores_serie_valores_jqplot[i][1];
                    var fecha_local = new Date(timestamp_utc);
                    var cadena_valor_horario = anyade_cero_hora_minuto_segundo(fecha_local.getHours()) + ":" +
                        anyade_cero_hora_minuto_segundo(fecha_local.getMinutes()) + ":" +
                        anyade_cero_hora_minuto_segundo(fecha_local.getSeconds());
                    fila_valores_serie_valores_jqplot.push(cadena_valor_horario);
                }
                else {
                    // Valor formateado con el número de decimales correspondiente
                    if (numero_decimales_valores == null) {
                        var numeros_decimales_valores = parametros_grafica_jqplot["numeros_decimales_valores"];
                        numero_decimales_valores = numeros_decimales_valores[indice_serie];
                    }
                    var valor = valores_serie_valores_jqplot[i][1];
                    if (valor == null) {
                        continue;
                    }
                    if ((valores_serie_valores_jqplot[i].length == 3) && (valores_serie_valores_jqplot[i][2] == null)) {
                        continue;
                    }
                    var valor_formateado = formatea_numero(parseFloat(valor), numero_decimales_valores);
                    fila_valores_serie_valores_jqplot.push(valor_formateado);
                }

                // Valores extra (sin formatear, suelen ser cadenas para tooltips)
                if ((valores_extra != null) && (valores_extra == true)) {
                    for (var j = 2; j < valores_serie_valores_jqplot[i].length; j++) {
                        var valor = valores_serie_valores_jqplot[i][j];
                        valor = unescapeHtml(valor);
                        fila_valores_serie_valores_jqplot.push(valor);
                    }
                }

                // Se añade la fila de valores
                filas_valores_serie_valores_jqplot.push(fila_valores_serie_valores_jqplot);
            }
        }
        else {
            // Si hay ticks en el eje X y solo hay un valor es una gráfica de barra de valores puntual con solo un valor por serie
            // (se considera como si sólo hubiera una serie sin nombre)
            var ticks_eje_x = parametros_grafica_jqplot["ticks_eje_x"];
            if ((ticks_eje_x != null) && (ticks_eje_x.length == 1)) {
                // Cabecera
                var fila_cabecera_serie_valores_jqplot = [
                    TLNT.Idiomas._('Nombre'),
                    TLNT.Idiomas._('Valor')];
                filas_valores_serie_valores_jqplot.push(fila_cabecera_serie_valores_jqplot);

                // Filas de valores
                var nombres_series = grafica_jqplot.legend.labels;
                for (var i = 0; i < nombres_series.length; i++) {
                    var fila_valores_serie_valores_jqplot = [];

                    var valores_serie_valores_jqplot = grafica_jqplot.series[i].data;
                    fila_valores_serie_valores_jqplot.push(nombres_series[i]);

                    var valor = valores_serie_valores_jqplot[0][1];
                    var valor_formateado = formatea_numero(parseFloat(valor), numero_decimales_valores);
                    fila_valores_serie_valores_jqplot.push(valor_formateado);

                    filas_valores_serie_valores_jqplot.push(fila_valores_serie_valores_jqplot);
                }
            }
            else {
                // Cabecera
                var fila_cabecera_serie_valores_jqplot = [
                    TLNT.Idiomas._('Valor (eje X)'),
                    TLNT.Idiomas._('Valor (eje Y)')];
                if (valores_serie_valores_jqplot.length > 0) {
                    for (var j = 2; j < valores_serie_valores_jqplot[0].length; j++) {
                        var numero_valor_extra = j - 1;
                        fila_cabecera_serie_valores_jqplot.push(TLNT.Idiomas._('Extra') + " " + numero_valor_extra);
                    }
                }
                var etiquetas = parametros_grafica_jqplot["etiquetas"];
                if (etiquetas != null) {
                    fila_cabecera_serie_valores_jqplot.push(TLNT.Idiomas._('Etiqueta'));
                }
                filas_valores_serie_valores_jqplot.push(fila_cabecera_serie_valores_jqplot);

                // Filas de valores
                for (var i = 0; i < valores_serie_valores_jqplot.length; i++) {
                    var fila_valores_serie_valores_jqplot = [];

                    // Si hay ticks en el eje X, se añade el nombre del tick correspondiente
                    // Si no hay ticks en el eje X se formatea con el número de decimales correspondiente
                    var numero_decimales_ticks_eje_x = parametros_grafica_jqplot["numero_decimales_ticks_eje_x"];
                    var valor_tick_eje_x = valores_serie_valores_jqplot[i][0];
                    if (ticks_eje_x != null) {
                        var nombre_tick_eje_x = dame_nombre_tick(ticks_eje_x, valor_tick_eje_x);
                        fila_valores_serie_valores_jqplot.push(nombre_tick_eje_x);
                    } else {
                        var valor_tick_eje_x_formateado = formatea_numero(parseFloat(valor_tick_eje_x), numero_decimales_ticks_eje_x);
                        fila_valores_serie_valores_jqplot.push(valor_tick_eje_x_formateado);
                    }

                    // Valor formateado con el número de decimales correspondiente
                    var valor = valores_serie_valores_jqplot[i][1];
                    var valor_formateado = formatea_numero(parseFloat(valor), numero_decimales_valores);
                    fila_valores_serie_valores_jqplot.push(valor_formateado);

                    // Valores extra (sin formatear, suelen ser cadenas para tooltips)
                    if ((valores_extra != null) && (valores_extra == true)) {
                        for (var j = 2; j < valores_serie_valores_jqplot[i].length; j++) {
                            var valor = valores_serie_valores_jqplot[i][j];
                            fila_valores_serie_valores_jqplot.push(valor);
                        }
                    }

                    // Etiquetas (para las gráficas de barras de valores con etiquetas)
                    if (etiquetas != null) {
                        var etiqueta = etiquetas[i];
                        fila_valores_serie_valores_jqplot.push(etiqueta);
                    }

                    // Se añade la fila de valores
                    filas_valores_serie_valores_jqplot.push(fila_valores_serie_valores_jqplot);
                }
            }
        }
    }
    return (filas_valores_serie_valores_jqplot);
}


// Devuelve el nombre del tick de la gráfica
function dame_nombre_tick(ticks, valor_tick) {
    var nombre_tick = valor_tick;
    if (ticks.length == 1) {
        nombre_tick = ticks[0];
    }
    else {
        for (var i = 0; i < ticks.length; i++) {
            if ($.isArray(ticks[i]) == false) {
                if (i == valor_tick) {
                    nombre_tick = ticks[i];
                    break;
                }
            } else {
                if (ticks[i][0] == valor_tick) {
                    nombre_tick = ticks[i][1];
                    break;
                }
            }
        }
    }
    return (nombre_tick);
}


// Añade un cero inicial a la hora, minuto o segundo
function anyade_cero_hora_minuto_segundo(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return (i);
}


//
// Funciones auxiliares de gráficas
//


// Devuelve el número máximo de valores de las series de una gráfica
function dame_numero_maximo_valores_series_grafica(grafica) {
    var numero_maximo_valores_grafica = 0;
    for (var i = 0; i < grafica.length; i++) {
        if (grafica[i].length > numero_maximo_valores_grafica) {
            numero_maximo_valores_grafica = grafica[i].length;
        }
    }
    return (numero_maximo_valores_grafica);
}


// Devuelve si hay valores en alguna serie de la gráfica
function dame_hay_valores_alguna_serie_grafica(grafica) {
    if (grafica.length == 0) {
        return (false);
    }

    var hay_valores = false;
    for (var i = 0; i < grafica.length; i++) {
        if (grafica[i].length > 0) {
            hay_valores = true;
        }
    }
    return (hay_valores);
}


// Devuelve si hay valores en todas las series de la gráfica
function dame_hay_valores_todas_series_grafica(grafica) {
    if (grafica.length == 0) {
        return (false);
    }

    var hay_valores = true;
    for (var i = 0; i < grafica.length; i++) {
        if (grafica[i].length == 0) {
            hay_valores = false;
        }
    }
    return (hay_valores);
}


// Guarda la gráfica en la variable global
function guarda_grafica_global(div_grafica, grafica) {
    graficas_jqplot[div_grafica] = grafica;
}


// Elimina la gráfica global
function elimina_grafica_global(div_grafica) {
    if (div_grafica in graficas_jqplot) {
        var grafica = graficas_jqplot[div_grafica];
        grafica.destroy();
        grafica = null;
        delete graficas_jqplot[div_grafica];
    }
}


// Elimina todas las gráficas globales
function elimina_graficas_globales() {
    for (var div_grafica in graficas_jqplot) {
        var grafica = graficas_jqplot[div_grafica];
        grafica.destroy();
        grafica = null;
        delete graficas_jqplot[div_grafica];
    }
    graficas_jqplot = [];
}


// Guarda los parámetros en la variable global
function guarda_parametros_grafica_global(div_grafica, parametros_grafica) {
    var parametros_grafica_anterior = parametros_graficas_jqplot[div_grafica];
    if (typeof parametros_grafica_anterior !== "undefined") {
        parametros_grafica_anterior = null;
    }
    parametros_graficas_jqplot[div_grafica] = parametros_grafica;
}


// Elimina todos los parámetros de gráficas globales
function elimina_parametros_graficas_globales() {
    for (var parametro_grafica in parametros_graficas_jqplot) {
        parametro_grafica = null;
    }
    parametros_graficas_jqplot = [];
}


// Devuelve información del menú contextual de las gráfica especificada
function dame_info_menu_contextual_grafica(grafica) {
    // Se calculan las opciones del menú contextual
    var exportacion_valores = exportacion_valores_sensores;
    var administracion_comentarios = false;
    if (grafica_con_comentarios == true) {
        switch (modulo_informe_dibujado) {
            case MODULO_SENSORES:
            case MODULO_SMARTMETER:
            case MODULO_PROYECTOS: {
                administracion_comentarios = administracion_comentarios_sensores;
                break;
            }
            case MODULO_ACTUADORES: {
                administracion_comentarios = administracion_comentarios_actuadores;
                break;
            }
        }
    }
    var opciones_menu_contextual_grafica = [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN];
    if (exportacion_valores == true) {
        opciones_menu_contextual_grafica.push(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES);
    }
    if (administracion_comentarios == true) {
        switch (tipo_informe_dibujado) {
            case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
            case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
            case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
            case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
            case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
            case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
                opciones_menu_contextual_grafica.push(OPCION_MENU_CONTEXTUAL_ANYADIR_COMENTARIO);
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
                opciones_menu_contextual_grafica.push(OPCION_MENU_CONTEXTUAL_ANYADIR_COMENTARIOS);
                break;
            }
        }
    }
    var info_menu_contextual_grafica = {
        "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_JQPLOT,
        "opciones": opciones_menu_contextual_grafica};

    // Se añaden los eventos necesarios para poder realizar las acciones de los menús contextuales
    var establecer_eventos_deteccion_fecha_boton_derecho = false;
    if (administracion_comentarios == true) {
        establecer_eventos_deteccion_fecha_boton_derecho = true;
    }
    if (establecer_eventos_deteccion_fecha_boton_derecho == true) {
        $(grafica.targetId).mousedown(function(e) {
            if (e.button == 2) {
                info_grafica_boton_derecho_recuperada = false;
                fecha_hora_grafica_boton_derecho = null;
                indice_serie_grafica_boton_derecho = null;
                nombre_serie_grafica_boton_derecho = null;
                nombre_primera_serie_grafica_boton_derecho = null;
            }
            return (true);
        });
        grafica.target.bind('jqplotDataRightClick', function(ev, seriesIndex, pointIndex, data) {
            var date = new Date(data[0]);
            info_grafica_boton_derecho_recuperada = true;
            fecha_hora_grafica_boton_derecho = date;
            indice_serie_grafica_boton_derecho = seriesIndex;
        });
        // Nota: Este evento salta dos veces
        grafica.target.bind('jqplotRightClick', function(ev, gridpos, datapos, neighbor, plot) {
            if (info_grafica_boton_derecho_recuperada == false) {
                info_grafica_boton_derecho_recuperada = true;
                var date = new Date(datapos.xaxis);
                fecha_hora_grafica_boton_derecho = date;
            }
            else {
                if (indice_serie_grafica_boton_derecho != null) {
                    nombre_serie_grafica_boton_derecho = plot.series[indice_serie_grafica_boton_derecho]["label"];
                }
            }
            nombre_primera_serie_grafica_boton_derecho = plot.series[0]["label"];
        });
    }

    // Se devuelve la información del menú contextual
    return (info_menu_contextual_grafica);
}

function cambia_colores_periodos_grafica(etiquetas_grafica) {
     // Se crea una variable ""LOCAL"" para poder personalizar colores
    // y que no se cambie en todas las gráficas
    var colores_grafica_let = colores_grafica;
    // Se utiliza alternativa a includes porque
    // no existe en ES5 js 2015
    if (~etiquetas_grafica.indexOf('P1')){
        colores_grafica_let[etiquetas_grafica.indexOf('P1')] = COLORES_GRAFICAS_JQPLOT_EJENER[0];
    }
    if (~etiquetas_grafica.indexOf('P2')){
        colores_grafica_let[etiquetas_grafica.indexOf('P2')] = COLORES_GRAFICAS_JQPLOT_EJENER[1];
    }
    if (~etiquetas_grafica.indexOf('P3')){
        colores_grafica_let[etiquetas_grafica.indexOf('P3')] = COLORES_GRAFICAS_JQPLOT_EJENER[2];
    }
    if (~etiquetas_grafica.indexOf('P4')){
        colores_grafica_let[etiquetas_grafica.indexOf('P4')] = COLORES_GRAFICAS_JQPLOT_EJENER[3];
    }
    if (~etiquetas_grafica.indexOf('P5')){
        colores_grafica_let[etiquetas_grafica.indexOf('P5')] = COLORES_GRAFICAS_JQPLOT_EJENER[4];
    }
    if (~etiquetas_grafica.indexOf('P6')){
        colores_grafica_let[etiquetas_grafica.indexOf('P6')] = COLORES_GRAFICAS_JQPLOT_EJENER[5];
    }
    return (colores_grafica_let);
}