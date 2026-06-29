//
// Visualización de mapa de calor (por periodo y subperiodo)
//


// Configuration variables
var heatmapDiv = "heatmap",
    heatmapTitleText = "",
    heatmapTitleColor = "#555555",
    heatmapLabelColor = "#090909",
    heatmapWidth = 1000,
    heatmapLeftOffset = 0,
    heatmapDuration = 2500,
    heatmapPeriods = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
    heatmapPeriodsHighlight = [0, 1, 2, 3, 4],
    heatmapSubperiods = ["0H", "1H", "2H", "3H", "4H", "5H", "6H", "7H", "8H", "9H", "10H", "11H", "12H", "13H", "14H", "15H", "16H", "17H", "18H", "19H", "20H", "21H", "22H", "23H"],
    heatmapSubperiodsHighlight = [9, 10, 11, 12, 13, 14, 15, 16, 17],
    // Nota: El número de intervalos mostrados depende del número de colores definido en 'heatmapColors' (máximo 9)
    heatmapColors = ["#6baed6", "#9ecae1", "#c6dbef", "#deebf7", "#ffeda0", "#fed976", "#feb24c", "#fd8d3c", "#fc4e2a"], // ColorBrewer (escala de colores)
    // Escala de reducción del mapa de calor
    heatmapReductionScale = 1,
    // Número de decimales mostrados en el mapa de calor
    gridNumberDecimals = null;


// Dibuja un mapa de calor
function drawHeatmap(
    heatmapData,
    heatmapVisibleData,
    showLegends) {
    $("#" + heatmapDiv).html("");

    // Datos visibles del mapa de calor
    var heatmapCustomVisibleData = null;
    if (heatmapVisibleData != null) {
        heatmapCustomVisibleData = true;
    }
    else {
        heatmapCustomVisibleData = false;
        heatmapVisibleData = heatmapData;
    }

    // Tamaño de letra y asignación de variables de configuración
    var pixels_font_size = $.getDefaultPx("#" + heatmapDiv);
    var heatmap_width = heatmapWidth;
    var margin_period_value = pixels_font_size * 2;

    // Aplicación de escala de reducción
    if (heatmapReductionScale < 1) {
        pixels_font_size *= heatmapReductionScale;
        heatmap_width *= heatmapReductionScale;
        margin_period_value *= heatmapReductionScale;
    }

    // Márgenes y tamaños de elementos
    var top_margin = pixels_font_size * 4;
    var title_font_size = pixels_font_size * 1.17;
    var numbers_font_size = pixels_font_size * 0.9;
    var subperiod_height = pixels_font_size * 1.5;
    var legend_texts_height = pixels_font_size * 1.5;
    var period_width = pixels_font_size * 7;

    // Si no hay título se elimina el margen para el mismo
    if (heatmapTitleText == "") {
        top_margin = 0;
        title_font_size = 0;
    }

    // Tamaño de cada uno de los 'cuadrados' del mapa de calor, altura y anchura de las leyendas
    var grid_size = (heatmap_width - period_width - margin_period_value) / heatmapSubperiods.length;
    if (showLegends == true) {
        var heatmap_height = grid_size * (heatmapPeriods.length + 0.5);
        var legend_element_width = Math.round(grid_size * heatmapSubperiods.length / 9);
    }
    else {
        var heatmap_height = grid_size * (heatmapPeriods.length - 0.8);
    }

    // Comprobación para ver si se dibujan los elementos de texto
    var show_periods = true;
    var show_subperiods = true;
    var show_grid_values = true;
    var show_legend_texts = true;
    if (grid_size < (pixels_font_size * 1)) {
        show_periods = false;
    }
    if (grid_size < (pixels_font_size * 2)) {
        show_subperiods = false;
        show_grid_values = false;
    }
    if (grid_size < (pixels_font_size * 1.5)) {
        show_legend_texts = false;
    }
    if (show_periods == false) {
        period_width = 0;
        margin_period_value = 0;
    }
    if (show_subperiods == false) {
        subperiod_height = 0;
    }
    if (show_legend_texts == false) {
        legend_texts_height = 0;
    }

    // Recálculo del tamaño de cada uno de los 'cuadrados' del mapa de calor, altura y anchura de las leyendas
    // (una vez ya comprobados los elementos que se van a mostrar)
    grid_size = (heatmap_width - period_width - margin_period_value) / heatmapSubperiods.length;
    if (showLegends == true) {
        heatmap_height = grid_size * (heatmapPeriods.length + 0.5);
        legend_element_width = Math.round(grid_size * heatmapSubperiods.length / 9);
    }
    else {
        heatmap_height = grid_size * (heatmapPeriods.length - 0.8);
    }

    // Valores máximo y minimo del mapa de calor y escala de colores
    var min_data_value = d3.min(heatmapData, function (d) {return (parseFloat(d.value));});
    var max_data_value = d3.max(heatmapData, function (d) {return (parseFloat(d.value));});
    var color_scale = d3.scale.quantile()
        .domain([
            min_data_value,
            max_data_value])
        .range(heatmapColors);

    // Tooltip y datos de los valores en el mapa de calor (para la exportación de valores)
    var tooltip_id = heatmapDiv + "-" + "tooltip";
    var tooltip_div = "<div id='" + tooltip_id + "' class='tooltip hidden'><span id='value'></span></div>";
    var xmldata_div = "<div class='valores_xml' valores='' hidden=''></div>";
    document.getElementById(heatmapDiv).innerHTML = tooltip_div + xmldata_div;

    // Establecimiento de altura y anchura del 'svg' del mapa de calor y traslación vertical y horizontal
    var vertical_translation = top_margin + subperiod_height;
    var horizontal_translation = (heatmapWidth - heatmap_width) / 2;
    var div_width = heatmap_width + horizontal_translation;
    var svg = d3.select("#" + heatmapDiv).append("svg")
        .attr("width", div_width)
        .attr("height", heatmap_height + top_margin + (grid_size * 0.5) + subperiod_height + legend_texts_height)
        .append("g")
        .attr("transform", "translate(" + horizontal_translation + ", " + vertical_translation + ")");

    // Título del mapa de calor
    if (heatmapTitleText != "") {
        var title_offset_y = null;
        if (show_subperiods == true) {
            title_offset_y = -(title_font_size * 2);
        }
        else {
            title_offset_y = -(title_font_size * 1);
        }
        if (heatmapReductionScale < 1) {
            title_offset_y *= 1.2;
        }
        svg.append("text")
            .attr("font-size", title_font_size + "px")
            .attr("x", (heatmap_width / 2))
            .attr("y", title_offset_y)
            .attr("text-anchor", "middle")
            .attr("class", "heatmap-title")
            .style("fill", heatmapTitleColor)
            .text(heatmapTitleText);
    }

    // Etiquetas de periodos (días)
    if (show_periods == true) {
        var y_translation = grid_size / 2 + numbers_font_size / 2;
        svg.selectAll(".periodLabel")
            .data(heatmapPeriods)
            .enter().append("text")
                .text(function (d) { return d; })
                .attr("font-size", numbers_font_size + "px")
                .attr("x", 0)
                .attr("y", function (d, i) {return i * grid_size;})
                .style("text-anchor", "end")
                .attr("transform", "translate(" + period_width + ", " + y_translation + ")")
                .style("fill", heatmapLabelColor)
                .attr("class", function (d, i) {return ((heatmapPeriodsHighlight.indexOf(i) > -1)? "periodLabel axis-highlight": "periodLabel axis");});
    }

    // Etiquetas de subperiodos (horas)
    if (show_subperiods == true) {
        var subperiodLabel_offset_y = 6;
        if (heatmapReductionScale < 1) {
            subperiodLabel_offset_y *= heatmapReductionScale;
        }
        svg.selectAll(".subperiodLabel")
            .data(heatmapSubperiods)
            .enter().append("text")
                .text(function(d) {return d;})
                .attr("font-size", numbers_font_size + "px")
                .attr("x", function(d, i) {return i * grid_size + period_width + margin_period_value;})
                .attr("y", 0)
                .style("text-anchor", "middle")
                .attr("transform", "translate(" + grid_size / 2 + ", -" + subperiodLabel_offset_y + ")")
                .style("fill", heatmapLabelColor)
                .attr("class", function(d, i) {return ((heatmapSubperiodsHighlight.indexOf(i) > -1)? "subperiodLabel axis axis-highlight": "subperiodLabel axis");});
    }

    // Dibujado del mapa de calor
    // (si no hay animación se dibujan los cuadrados ya con el color correspondiente - para mejorar el rendimiento)
    var subperiod_round_size = 4;
    var subperiod_stroke_width = 2;
    if (heatmapReductionScale < 1) {
        subperiod_round_size *= heatmapReductionScale;
        subperiod_stroke_width *= heatmapReductionScale;
    }
    var heatmap = null;
    if (heatmapDuration > 0) {
        if (showLegends == true) {
            heatmap = svg.selectAll(".subperiod")
                .data(heatmapData)
                .enter().append("rect")
                    .attr("font-size", numbers_font_size + "px")
                    .attr("x", function(d) {return (d.subperiod) * grid_size + period_width + margin_period_value;})
                    .attr("y", function(d) {return (d.period - 1) * grid_size;})
                    .attr("rx", subperiod_round_size)
                    .attr("ry", subperiod_round_size)
                    .attr("class", "bordered")
                    .attr("width", grid_size)
                    .attr("height", grid_size)
                    .attr("stroke-width", subperiod_stroke_width)
                    .style("fill", heatmapColors[0])
                    .on("mouseover", function (d) {
                        d3.select("#" + tooltip_id)
                        .style("left", d3.event.pageX + "px")
                        .style("top", d3.event.pageY + "px")
                        .style("opacity", 1)
                        .select("#value")
                        .text(formatea_numero(parseFloat(d.value), 2));
                    })
                    .on("mouseout", function () {
                        d3.select("#" + tooltip_id)
                        .style("opacity", 0);
                    });
        }
        else {
            heatmap = svg.selectAll(".subperiod")
                .data(heatmapData)
                .enter().append("rect")
                    .attr("font-size", numbers_font_size + "px")
                    .attr("x", function(d) {return (d.subperiod) * grid_size + period_width + margin_period_value;})
                    .attr("y", function(d) {return (d.period - 1) * grid_size;})
                    .attr("rx", subperiod_round_size)
                    .attr("ry", subperiod_round_size)
                    .attr("class", "bordered")
                    .attr("width", grid_size)
                    .attr("height", grid_size)
                    .attr("stroke-width", subperiod_stroke_width)
                    .style("fill", heatmapColors[0]);
        }
        heatmap.transition().duration(heatmapDuration)
            .style("fill", function(d) {return color_scale(parseFloat(d.value));});
    }
    else {
        if (showLegends == true) {
            heatmap = svg.selectAll(".subperiod")
                .data(heatmapData)
                .enter().append("rect")
                    .attr("font-size", numbers_font_size + "px")
                    .attr("x", function(d) {return (d.subperiod) * grid_size + period_width + margin_period_value;})
                    .attr("y", function(d) {return (d.period - 1) * grid_size;})
                    .attr("rx", subperiod_round_size)
                    .attr("ry", subperiod_round_size)
                    .attr("class", "bordered")
                    .attr("width", grid_size)
                    .attr("height", grid_size)
                    .attr("stroke-width", subperiod_stroke_width)
                    .style("fill", function(d) {return color_scale(parseFloat(d.value));})
                    .on("mouseover", function (d) {
                        d3.select("#" + tooltip_id)
                        .style("left", d3.event.pageX + "px")
                        .style("top", d3.event.pageY + "px")
                        .style("opacity", 1)
                        .select("#value")
                        .text(formatea_numero(parseFloat(d.value), 2));
                    })
                    .on("mouseout", function () {
                        d3.select("#" + tooltip_id)
                        .style("opacity", 0);
                    });
        }
        else {
            heatmap = svg.selectAll(".subperiod")
                .data(heatmapData)
                .enter().append("rect")
                    .attr("font-size", numbers_font_size + "px")
                    .attr("x", function(d) {return (d.subperiod) * grid_size + period_width + margin_period_value;})
                    .attr("y", function(d) {return (d.period - 1) * grid_size;})
                    .attr("rx", subperiod_round_size)
                    .attr("ry", subperiod_round_size)
                    .attr("class", "bordered")
                    .attr("width", grid_size)
                    .attr("height", grid_size)
                    .attr("stroke-width", subperiod_stroke_width)
                    .style("fill", function(d) {return color_scale(parseFloat(d.value));});
        }
    }

    // Valores en el mapa de calor
    show_grid_values = ((show_grid_values == true) && (heatmapReductionScale > 0.9));
    if (show_grid_values == true) {
        if (gridNumberDecimals == null) {
            gridNumberDecimals = 0;
            if (parseInt(max_data_value - min_data_value) < heatmapColors.length) {
                gridNumberDecimals = 2;
            }
        }
        var grid_numbers_font_size = numbers_font_size * 0.8;
        svg.selectAll(".subperiod")
            .data(heatmapVisibleData)
            .enter().append("text")
                .attr("font-size", grid_numbers_font_size + "px")
                .attr("x", function(d) {return (d.subperiod) * grid_size + grid_size / 2 + period_width + margin_period_value;})
                .attr("y", function(d) {return (d.period - 1) * grid_size + grid_size / 2 + grid_numbers_font_size / 2;})
                .style("text-anchor", "middle")
                .text(function(d) {
                    if (heatmapCustomVisibleData == true) {
                        return (d.value);
                    }
                    else {
                        return (gridValueNumberFormatHeatmap(parseFloat(d.value), gridNumberDecimals));
                    }
                })
                .on("mouseover", function (d) {
                    d3.select("#" + tooltip_id)
                    .style("left", d3.event.pageX + "px")
                    .style("top", d3.event.pageY + "px")
                    .style("opacity", 1)
                    .select("#value")
                    .text(formatea_numero(parseFloat(d.value), 2));
                })
                .on("mouseout", function () {
                    d3.select("#" + tooltip_id)
                    .style("opacity", 0);
                });
    }

    // Leyendas
    if (showLegends == true) {
        var legend = svg.selectAll(".legend")
            .data([min_data_value].concat(color_scale.quantiles()), function(d) {return d;})
            .enter().append("g")
            .style("fill", heatmapLabelColor)
            .attr("class", "legend");
        legend.append("rect")
            .attr("x", function(d, i) {return legend_element_width * i + period_width + margin_period_value;})
            .attr("y", heatmap_height)
            .attr("width", legend_element_width)
            .attr("height", grid_size / 2)
            .style("fill", function(d, i) {
                // Nota: Si los valores iniciales y finales son iguales, se pintan los valores con el color más 'caliente',
                // cuando en la escala aparece el más 'frío', se cambia la escala de valores para que salga el color más caliente
                if (min_data_value == max_data_value) {
                    return (heatmapColors[heatmapColors.length - 1]);
                }
                else {
                    return (heatmapColors[i]);
                }
            });
        if (show_legend_texts == true) {
            legend.append("text")
                .text(function(d) {return ">=" + formatea_numero_limite_digitos(d, 2, true);})
                .attr("font-size", numbers_font_size + "px")
                .attr("x", function(d, i) {return legend_element_width * i + period_width + margin_period_value;})
                .attr("y", heatmap_height + (grid_size / 2) + (pixels_font_size * 1.2));
        }
    }

    // Se añaden los datos XML en el div correspondiente:
    // - Formato de datos (heatmapVisibleData):
    //     [
    //       {
    //          "period": XXX,
    //          "subperiod": XXX,
    //          "value": XXX
    //       },
    //       ...
    //     ]
    var numero_valores = heatmapVisibleData.length;
    var valores_xml = null;
    if (numero_valores > 0) {
        valores_xml = "<valores>";
        valores_xml += "<columnas>";
        valores_xml += "<nombre></nombre>";
        var numero_columnas = heatmapSubperiods.length;
        for (var i = 0; i < numero_columnas; i++) {
            valores_xml += "<nombre>" + escapeHtmlXml(heatmapSubperiods[i].toString()) + "</nombre>";
        }
        valores_xml += "</columnas>";
        var numero_periodo = null;
        var numero_subperiodo = null;
        var numero_filas = heatmapPeriods.length;
        var numero_valor = 0;
        var elemento_valor = null;
        for (var i = 0; i < numero_filas; i++) {
            numero_periodo = (i + 1);
            valores_xml += "<fila>";
            valores_xml += "<valor>" + escapeHtmlXml(heatmapPeriods[i].toString()) + "</valor>";
            for (var j = 0; j < numero_columnas; j++) {
                elemento_valor = null;
                if (numero_valor >= numero_valores) {
                    break;
                }
                else {
                    // Se cambia la forma de obtener el valor (solo cogía datos a partir del primer día elegido)
                    // Ahora se busca dentro del contenido el elemento que corresponde

                    //elemento_valor = heatmapVisibleData[numero_valor];

                    // CODIGO EN ES6, mucho más eficiente para cuando seamos capaces de usarlo
                    //elemento_valor = heatmapVisibleData.find(elemento_valor => elemento_valor.period == numero_periodo && elemento_valor.subperiod == j);
                    for (iterador_mapa_calor_datos = 0; iterador_mapa_calor_datos < numero_valores; iterador_mapa_calor_datos++){
                        elemento_valor = heatmapVisibleData[iterador_mapa_calor_datos];
                        if ((elemento_valor.period == numero_periodo) && (elemento_valor.subperiod == j)){
                            break;
                        }
                    }
                }
                numero_subperiodo = j;
                if ((elemento_valor["period"] == numero_periodo) &&
                    (elemento_valor["subperiod"] == numero_subperiodo)) {
                    valores_xml += "<valor>" + escapeHtmlXml(elemento_valor["value"].toString()) + "</valor>";
                    numero_valor += 1;
                }
                else {
                    valores_xml += "<valor></valor>";
                }
            }
            valores_xml += "</fila>";
        }
        valores_xml += "</valores>";
    }
    $("#" + heatmapDiv + " .valores_xml").attr('valores', valores_xml);
}


// Función de formateado de número de valor en mapa de calor
function gridValueNumberFormatHeatmap(value, decimals) {
    var formatted_value = null;
    if (value < 10000) {
        if (decimals > 0) {
            if (value < 100) {
                formatted_value = formatea_numero(value, decimals);
            }
            else {
                formatted_value = Math.round(value);
            }
        }
        else {
            formatted_value = Math.round(value);
        }
    }
    else {
        if (value < 1000000) {
            formatted_value = Math.round(value / 1000) + "m";
        }
        else {
            formatted_value = Math.round(value / 1000000) + "M";
        }
    }
    return (formatted_value);
}
