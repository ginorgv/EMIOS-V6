/*
 * Funciones para mostrar gráficos
 *
 */


// Muestra un gráfico de mapa de calor
function muestra_grafico_mapa_calor(
    div_grafico,
    tipo_mapa_calor,
    titulo_mapa_calor,
    periodos_mapa_calor,
    subperiodos_mapa_calor,
    datos_mapa_calor,
    datos_mapa_calor_visibles,
    numero_decimales_mostrados_mapa_calor,
    mostrar_leyendas_mapa_calor,
    escala_colores_mapa_calor,
    altura_maxima_mapa_calor,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Título
    heatmapTitleText = titulo_mapa_calor;

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
            heatmapPeriods = periodos_mapa_calor[0];
            heatmapPeriodsHighLight = periodos_mapa_calor[1];
            break;
        }
    }

    // Subperiodos
    if (subperiodos_mapa_calor == null) {
        heatmapSubperiods = [
            "0H", "1H", "2H", "3H", "4H", "5H", "6H", "7H",
            "8H", "9H", "10H", "11H", "12H", "13H", "14H", "15H",
            "16H", "17H", "18H", "19H", "20H", "21H", "22H", "23H"];
        heatmapSubperiodsHighlight = [9, 10, 11, 12, 13, 14, 15, 16, 17];
    }
    else {
        heatmapSubperiods = subperiodos_mapa_calor[0];
        heatmapSubperiodsHighlight = subperiodos_mapa_calor[1];
        if (heatmapSubperiodsHighlight == null) {
            heatmapSubperiodsHighlight = [];
            for (var i = 0; i < heatmapSubperiods.length; i++) {
                heatmapSubperiodsHighlight.push(i);
            }
        }
    }

    // Mapa de calor
    heatmapDiv = div_grafico;
    heatmapWidth = $("#" + div_grafico).width();
    heatmapReductionScale = 1;
    heatmapColors = escala_colores_mapa_calor;
    heatmapLabelColor = COLOR_ETIQUETAS_MAPA_CALOR;

    // Número de decimales visibles en el mapa de calor
    gridNumberDecimals = numero_decimales_mostrados_mapa_calor;

    // Tiempo de dibujado
    if (mostrar_animacion == true) {
        heatmapDuration = DURACION_ANIMACION_MAPA_CALOR;
    }
    else {
        heatmapDuration = 0;
    }

    // Se dibuja el mapa de calor
    drawHeatmap(
        datos_mapa_calor,
        datos_mapa_calor_visibles,
        mostrar_leyendas_mapa_calor);

    // Ajuste del tamaño del mapa de calor a la altura máxima especificada
    if ((altura_maxima_mapa_calor !== undefined) && (altura_maxima_mapa_calor != null)) {
        var escala_reduccion = dame_escala_reduccion_grafico_altura_maxima(
            heatmapDiv,
            altura_maxima_mapa_calor);
        if (escala_reduccion < 1) {
            heatmapReductionScale = escala_reduccion;
            drawHeatmap(
                datos_mapa_calor,
                datos_mapa_calor_visibles,
                mostrar_leyendas_mapa_calor);
        }
    }

    // Menú contextual del mapa de calor
    if (anyadir_menu_contextual == true) {
        var exportacion_valores = exportacion_valores_sensores;
        var opciones_menu_contextual_mapa_calor = [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN];
        if (exportacion_valores == true) {
            opciones_menu_contextual_mapa_calor.push(OPCION_MENU_CONTEXTUAL_EXPORTAR_VALORES);
        }
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_MAPA_CALOR,
            "opciones": opciones_menu_contextual_mapa_calor};
        anyade_menu_contextual(div_grafico, info_menu_contextual, heatmapTitleText);
    }
}


// Muestra gráficos de viento
// (http://windhistory.com/station.html?KQUK)
function muestra_graficos_viento(
    div_grafico_frecuencia_viento,
    div_grafico_velocidad_viento,
    datos_viento,
    max_frecuencia,
    max_velocidad_media,
    mostrar_animacion,
    anyadir_menu_contextual) {
    // Se establecen los parámetros de los gráficos
    visWidth = $("#" + div_grafico_frecuencia_viento).width() / 2 - MARGEN_GRAFICAS_VIENTO;
    if (visWidth > MAX_RADIO_GRAFICAS_VIENTO) {
        visWidth = MAX_RADIO_GRAFICAS_VIENTO;
    }
    windroseDiv = div_grafico_frecuencia_viento;
    windspeedDiv = div_grafico_velocidad_viento;

    probMaxValue = max_frecuencia + MARGEN_CIRCULOS_GRAFICA_FRECUENCIA_VIENTO;
    speedMaxValue = max_velocidad_media + MARGEN_CIRCULOS_GRAFICA_VELOCIDAD_VIENTO;
    if (probMaxValue < MIN_CIRCULOS_GRAFICA_FRECUENCIA_VIENTO) {
        probMaxValue = MIN_CIRCULOS_GRAFICA_FRECUENCIA_VIENTO;
    }
    if (speedMaxValue < MIN_CIRCULOS_GRAFICA_VELOCIDAD_VIENTO) {
        speedMaxValue = MIN_CIRCULOS_GRAFICA_VELOCIDAD_VIENTO;
    }
    speedMaxValue = Math.ceil(speedMaxValue / REDONDEO_CIRCULOS_GRAFICAS_VIENTO) * REDONDEO_CIRCULOS_GRAFICAS_VIENTO;
    probMaxValue = Math.ceil(probMaxValue * 100 / REDONDEO_CIRCULOS_GRAFICAS_VIENTO) * REDONDEO_CIRCULOS_GRAFICAS_VIENTO / 100;

    speedMinColor = "lightblue";
    speedMaxColor = "darkblue";
    probMinColor = "lightgreen";
    probMaxColor = "darkgreen";

    windroseTitle = TLNT.Idiomas._("Frecuencia por dirección");
    windspeedTitle = TLNT.Idiomas._("Velocidad media por dirección");
    calmText = TLNT.Idiomas._("calma");
    speedUnitText = unidad_medida_velocidad;
    cardinalPointsLabels = [
        TLNT.Idiomas._("N"),
        TLNT.Idiomas._("NE"),
        TLNT.Idiomas._("E"),
        TLNT.Idiomas._("SE"),
        TLNT.Idiomas._("S"),
        TLNT.Idiomas._("SO"),
        TLNT.Idiomas._("O"),
        TLNT.Idiomas._("NO")
    ];

    // Tiempo de dibujado
    if (mostrar_animacion == true) {
        transitionDuration = DURACION_ANIMACION_GRAFICOS_VIENTO;
    }
    else {
        transitionDuration = 0;
    }

    // Se muestran los gráficos
    // (primero sin datos y luego se actualizan para que se muestre la animación)
    makeWindVis([]);
    updateWindVisDiagrams(datos_viento);

    // Menús contextuales
    if (anyadir_menu_contextual == true) {
        var info_menu_contextual = {
            "tipo_origen": TIPO_ORIGEN_MENU_CONTEXTUAL_SVG_GRAFICA_VIENTO,
            "opciones": [OPCION_MENU_CONTEXTUAL_GUARDAR_IMAGEN]};
        anyade_menu_contextual(div_grafico_frecuencia_viento, info_menu_contextual, windroseTitle);
        anyade_menu_contextual(div_grafico_velocidad_viento, info_menu_contextual, windspeedTitle);
    }
}


function dame_filas_valores_mapa_calor(id_mapa_calor) {
    var valores_xml = $("#" + id_mapa_calor + " .valores_xml").attr("valores");
    if (valores_xml === undefined) {
        return (null);
    }

    var elemento_doc_valores = $.parseXML(valores_xml);
    var doc_valores = $(elemento_doc_valores);

    var filas_valores = [];
    var texto = "";

    var fila_cabecera = [];
    var elemento_columnas = doc_valores.find('columnas');
    elemento_columnas.find('nombre').each(function() {
        texto = unescapeHtmlXml($(this).text());
        fila_cabecera.push(texto);
    });
    filas_valores.push(fila_cabecera);

    doc_valores.find('fila').each(function() {
        var fila_valores = [];
        var elemento_fila = $(this);
        elemento_fila.find('valor').each(function() {
            texto = unescapeHtmlXml($(this).text());
            fila_valores.push(texto);
        });
        filas_valores.push(fila_valores);
    });
    return (filas_valores);
}
