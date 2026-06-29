/*
 * Funciones para mostrar gráficos de widgets
 *
 */


// Muestra un gráfico de mapa de calor de widgets
function muestra_grafico_mapa_calor_widgets(
    div_grafico,
    tipo_mapa_calor,
    periodos_mapa_calor,
    datos_mapa_calor,
    escala_colores_mapa_calor,
    color_etiquetas_mapa_calor,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Título
    heatmapTitleText = "";

    // Periodos
    switch (tipo_mapa_calor) {
        case TIPO_MAPA_CALOR_DIARIO: {
            var dias_mapa_calor = periodos_mapa_calor;
            heatmapPeriods = dias_mapa_calor;
            heatmapPeriodsHighlight = dame_indices_dias_entre_semana_fechas(dias_mapa_calor);
            break;
        }
        case TIPO_MAPA_CALOR_SEMANAL: {
            var dias_semana_mapa_calor = periodos_mapa_calor;
            heatmapPeriods = dias_semana_mapa_calor;
            heatmapPeriodsHighlight = dame_indices_dias_semana_entre_semana(dias_semana_mapa_calor);
            break;
        }
        case TIPO_MAPA_CALOR_PERSONALIZADO: {
            heatmapPeriods = periodos_mapa_calor;
            heatmapPeriodsHighlight = [];
            break;
        }
    }

    // Subperiodos
    subperiodos_mapa_calor = [
        "0H", "1H", "2H", "3H", "4H", "5H", "6H", "7H",
        "8H", "9H", "10H", "11H", "12H", "13H", "14H", "15H",
        "16H", "17H", "18H", "19H", "20H", "21H", "22H", "23H"];
    heatmapSubperiods = subperiodos_mapa_calor;

    // Mapa de calor
    heatmapDiv = div_grafico;
    heatmapWidth = $("#" + div_grafico).width();
    heatmapReductionScale = 1;
    heatmapColors = escala_colores_mapa_calor;
    heatmapLabelColor = color_etiquetas_mapa_calor;

    // Tiempo de dibujado
    if (mostrar_animacion == true) {
        heatmapDuration = DURACION_ANIMACION_MAPA_CALOR;
    }
    else {
        heatmapDuration = 0;
    }

    // Se dibuja el mapa de calor
    drawHeatmap(datos_mapa_calor, null, true);

    // Menú contextual del mapa de calor
    if (anyadir_menu_contextual == true) {
        var exportacion_valores = exportacion_valores_sensores;
        var opciones_menu_contextual_mapa_calor = [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN];
        if (exportacion_valores == true) {
            opciones_menu_contextual_mapa_calor.push(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES);
        }
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR_WIDGET,
            "opciones": opciones_menu_contextual_mapa_calor};
        anyade_menu_contextual(div_grafico, info_menu_contextual, heatmapTitleText);
    }
}


// Muestra un gráfico de valor analógico (de tipo "reloj")
function muestra_grafico_valor_analogico_reloj_widgets(
    div_grafico_valor_analogico,
    valor, unidad_medida,
    valor_minimo_indicador, valor_maximo_indicador,
    valores_limite_colores, colores) {
    // Se recupera el tamaño de letra en píxeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");

    // Se recuperan el tamaño de letra y la altura de la gráfica
    var altura_grafica = $('#' + div_grafico_valor_analogico).height();
    var anchura_grafica = $('#' + div_grafico_valor_analogico).width();

    var altura_indicador = altura_grafica;
    if (anchura_grafica < (altura_grafica * 2)) {
        altura_indicador = anchura_grafica / 2;
    }

    // Se calcula el radio de la zona interior de colores
    // Nota: Depende de la altura de la aguja (habría que comprobar las proporciones por si el control no "llena" toda la altura)
    var radio_interior_zona_colores = INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_RADIO_INTERIOR_ZONA_COLORES * altura_indicador;
    var radio_exterior_zona_colores = INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_RADIO_EXTERIOR_ZONA_COLORES * altura_indicador;

    // Intervalos de valores de las zonas
    valores_limite_colores.push(valor_maximo_indicador);

    // Parámetros de la gráfica de jqplot
    // - Nota: 'padding: 0' y 'gridPadding todo a 0' son necesarios para quitar espacio en blanco extra de la aguja indicadora generada por jqplot
    var parametros_grafica_jqplot = {
        grid: {
           background: "transparent"
        },
        seriesDefaults: {
            renderer: $.jqplot.MeterGaugeRenderer,
            rendererOptions: {
                label: unidad_medida,
                min: valor_minimo_indicador,
                max: valor_maximo_indicador,
                intervals: valores_limite_colores,
                intervalColors: colores,
                intervalInnerRadius: radio_interior_zona_colores,
                intervalOuterRadius: radio_exterior_zona_colores,
                needleThickness: INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_GROSOR_AGUJA * tamanyo_letra_pixeles,
                ringWidth: INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_ANCHO_ANILLO * tamanyo_letra_pixeles,
                labelHeightAdjust: INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_AJUSTE_ALTURA_ETIQUETA * tamanyo_letra_pixeles,
                ringColor: INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_COLOR_ANILLO_AGUJA_MARCAS,
                tickColor: INDICADOR_WIDGET_VALOR_ANALOGICO_RELOJ_COLOR_TEXTO_MARCAS,
                padding: 0
            }
        },
        gridPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }
    };

    // Parámetros para el formato de los números
    establece_parametros_formato_numeros();

    // Orden de dibujado de los elementos de la gráfica
    establece_orden_dibujado_elementos_grafica();

    // Dibujado de la gráfica
    $.jqplot(div_grafico_valor_analogico, [[valor]], parametros_grafica_jqplot);
}


// Muestra un gráfico de valor analógico (de tipo circular) de widgets
function muestra_grafico_valor_analogico_circular_widgets(
    div_grafico_valor_analogico,
    valor, unidad_medida,
    valor_minimo_indicador, valor_maximo_indicador,
    valores_limite_colores, colores,
    factor_tamanyo_fuente_valor, estilo_fuente_valor, color_fuente_valor,
    mostrar_animacion) {
    // Zonas de colores
    var zonas_colores = [];
    var numero_zonas_colores = valores_limite_colores.length + 1;
    valores_limite_colores.unshift(valor_minimo_indicador);
    valores_limite_colores.push(valor_maximo_indicador);
    for (var i = 0; i < numero_zonas_colores; i++) {
        var zona_color = {
            color: colores[i],
            lo: valores_limite_colores[i],
            hi: valores_limite_colores[i + 1]
        };
        zonas_colores.push(zona_color);
    }

    // Tamaño de letra en píxeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");
    var tamanyo_letra_pixeles_valor = Math.floor(tamanyo_letra_pixeles * factor_tamanyo_fuente_valor);
    var tamanyo_letra_pixeles_unidad_medida = Math.floor(tamanyo_letra_pixeles_valor * INDICADOR_WIDGET_VALOR_ANALOGICO_CIRCULAR_FACTOR_TAMANYO_LETRA_UNIDAD_MEDIDA);

    // Estilo de la fuente de valor
    var peso_fuente_valor = null;
    switch (estilo_fuente_valor) {
        case ESTILO_FUENTE_NORMAL: {
            peso_fuente_valor = "normal";
            break;
        }
        case ESTILO_FUENTE_NEGRITA: {
            peso_fuente_valor = "bold";
            break;
        }
    }

    // Colores del indicador
    var color_borde_indicador = INDICADOR_WIDGET_VALOR_ANALOGICO_CIRCULAR_COLOR_BORDE_INDICADOR;
    var color_fondo_indicador = INDICADOR_WIDGET_VALOR_ANALOGICO_CIRCULAR_COLOR_FONDO_INDICADOR;

    // Duraciones de animaciones
    var duracion_animacion = 0;
    if (mostrar_animacion == true)
    {
        duracion_animacion = DURATION_ANIMACION_GRAFICO_VALOR_ANALOGICO_CIRCULAR;
    }

    // Parámetros del gráfico analógico
    var parametros_grafico_justgage = {
        id: div_grafico_valor_analogico,
        value: valor,
        min: valor_minimo_indicador,
        max: valor_maximo_indicador,
        gaugeWidthScale: INDICADOR_WIDGET_VALOR_ANALOGICO_CIRCULAR_ESCALA_ANCHURA_INDICADOR,
        hideMinMax: true,
        customSectors: zonas_colores,
        textRenderer: callback_valor_justgage,
        hideInnerShadow: true,
        gaugeStroke: color_borde_indicador,
        gaugeColor: color_fondo_indicador,
        relativeGaugeSize: false,
        valueFontFamily: "Arial",
        valueFontSize: tamanyo_letra_pixeles_valor,
        valueFontWeight: peso_fuente_valor,
        valueFontColor: color_fuente_valor,
        valueUnitFontSize: tamanyo_letra_pixeles_unidad_medida,
        startAnimationTime: duracion_animacion,
        refreshAnimationTime: duracion_animacion
    };
    function callback_valor_justgage(val) {
        var valor_justgage = Math.round(valor);
        if (unidad_medida != "") {
            valor_justgage += " " + "\n" + unidad_medida;
        }
        return (valor_justgage);
    }

    // Se crea (y se dibuja el gráfico)
    new JustGage(parametros_grafico_justgage);
}

