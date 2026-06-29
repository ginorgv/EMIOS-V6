// Parámetros de configuración de las gráficas de viento
var arcWidth = 5,
    speedMinColor = "moccasin",
    speedMaxColor = "green",
    probMinColor = "lavender",
    probMaxColor = "darkslategray",
    textStyle = {
        "letter-spacing": "1px",
        fill: "#333",
        "font-size": "12px",
        "font-weight": "bold",
        "text-anchor": "middle"
    },
    visWidth = 150,
    centerWidth = 34,
    windroseDiv = "windrose",
    windspeedDiv = "windspeed",
    windroseTitle = "Frequency by direction",
    windspeedTitle = "Average speed by direction",
    cardinalPointsLabels = ["N", "NE", "E", "SE", "S", "SW", "W", "NW"],
    calmText = "calm",
    speedUnitText = "kts",
    probMaxValue = 0.20,
    speedMaxValue = 30,
    transitionDuration = 2500;

// Tipos de gráficas de viento (frecuencia y velocidad)
var WINDROSE_GRAPH = 0,
    WINDSPEED_GRAPH = 1;


// Para dibujar los puntos cardinales
var cardinalPointRotation = 0;
function getCardinalPointRotation() {
    var rotation = cardinalPointRotation;
    cardinalPointRotation += 45;
    return (rotation);
}


//
// Common wind rose code
//


// Function to draw a single arc for the wind rose
// Input: Drawing options object containing
//   width: degrees of width to draw (ie 5 or 15)
//   from: integer, inner radius
//   to: function returning the outer radius
// Output: a function that when called, generates SVG paths.
//   It expects to be called via D3 with data objects from totalsToFrequences()
var arc = function(o) {
    return d3.svg.arc()
        .startAngle(function(d) {return (d.d - o.width) * Math.PI / 180;})
        .endAngle(function(d) {return (d.d + o.width) * Math.PI / 180;})
        .innerRadius(o.from)
        .outerRadius(function(d) {return o.to(d);});
};


//
// Code for data manipulation
//


// Convert a dictionary of {direction: total} to frequencies
// Output is an array of objects with three parameters:
//   d: wind direction
//   p: probability of the wind being in this direction
//   s: average speed of the wind in this direction
function totalsToFrequencies(totals, speeds) {
    var sum = 0;
    // Sum all the values in the dictionary
    for (var dir in totals) {
        sum += totals[dir];
    }

    // Nota: para evitar divisiones por 0? en el caso en el que no haya ningún valor
    if (sum == 0) {
        sum = 1;
    }

    // Build up an object containing frequencies
    var ret = {};
    ret.dirs = [];
    ret.sum = sum;
    for (var dir in totals) {
        var freq = totals[dir] / sum;
        var avgspeed;
        if (totals[dir] > 0) {
            avgspeed = speeds[dir] / totals[dir];
        } else {
            avgspeed = 0;
        }
        if (dir == "null") {
            // winds calm is a special case
            ret.calm = {d: null, p: freq, s: null};
        } else {
            ret.dirs.push({d: parseInt(dir), p: freq, s: avgspeed});
        }
    }
    return ret;
}


// Filter input data, giving back frequencies
function rollupData(data) {
    var totals = {};
    var speeds = {};
    for (var i = 10; i <= 360; i += 10) {
        totals["" + i] = 0;
        speeds["" + i] = 0;
    }
    totals["null"] = 0;
    speeds["null"] = 0;

    for (var key in data) {
        var direction = key;
        // count up all samples with this key
        totals[direction] += data[key][0];
        // add in the average speed * count from this key
        speeds[direction] += data[key][0] * data[key][1];
    }
    return totalsToFrequencies(totals, speeds);
}


//
//  Code for big visualization
//


// Map a wind speed to a color
function speedToColor(d) {
    var speedToColorScale =
        d3.scale.linear()
            .domain([0, speedMaxValue])
            .range([speedMinColor, speedMaxColor])
            .interpolate(d3.interpolateHcl);
    return speedToColorScale(d.s);
}


// Map a wind probability to a color
function probabilityToColor(d) {
    var probabilityToColorScale =
        d3.scale.linear()
            .domain([0, probMaxValue])
            .range([probMinColor, probMaxColor])
            .interpolate(d3.interpolateHcl);
    return probabilityToColorScale(d.p);
}


// Map a wind probability to an outer radius for the chart
function probabilityToRadius(d) {
    var probabilityToRadiusScale =
        d3.scale.linear()
            .domain([0, probMaxValue])
            .range([centerWidth, visWidth - 20])
            .clamp(true);
    return probabilityToRadiusScale(d.p);
}


// Map a wind speed to an outer radius for the chart
function speedToRadius(d) {
    var speedToRadiusScale =
        d3.scale.linear()
            .domain([0, speedMaxValue])
            .range([centerWidth, visWidth - 20])
            .clamp(true);
    return speedToRadiusScale(d.s);
}


// Options for drawing the complex arc chart
var windroseArcOptions = {
    width: arcWidth,
    from: centerWidth,
    to: probabilityToRadius
};
var windspeedArcOptions = {
    width: arcWidth,
    from: centerWidth,
    to: speedToRadius
};


// Draw a complete wind rose visualization, including axes and center text
function drawComplexArcs(parent, plotData, colorFunc, complexArcOptions) {
    // Draw the main wind rose arcs
    parent.append("svg:g").attr("class", "arcs")
        .selectAll("path")
        .data(plotData.dirs)
        .enter()
      .append("svg:path")
        .attr("d", arc(complexArcOptions))
        .style("fill", colorFunc)
        .attr("transform", "translate(" + visWidth + "," + visWidth + ")");

    // Add the calm wind probability in the center
    var cw = parent.append("svg:g").attr("class", "calmwind")
        .selectAll("text")
        .data([plotData.calm.p])
        .enter();
    cw.append("svg:text")
        .attr("transform", "translate(" + visWidth + ", " + visWidth + ")")
        .attr("class", "calmpercentage")
        .text(function(d) {return Math.round(d * 100) + " %";});
    cw.append("svg:text")
        .attr("transform", "translate(" + visWidth + ", " + (visWidth + 14) + ")")
        .attr("class", "calmcaption")
        .text(calmText);
}


//
// Visualización de nuevos diagramas
//


// Top level function to draw diagrams
function makeWindVis(json_wind_data) {
    drawBigWindrose(json_wind_data, WINDROSE_GRAPH);
    drawBigWindrose(json_wind_data, WINDSPEED_GRAPH);
}


// Draw a big wind rose, for the visualization
function drawBigWindrose(windroseData, type) {
    // Various visualization size parameters
    var width = visWidth * 2;
    var height = visWidth * 2;
    var radius = Math.min(width, height) / 2;
    var padding = visWidth / 10;

    var container = "";
    var captionText = "";
    if (type == WINDROSE_GRAPH) {
        container = "#" + windroseDiv;
        captionText = windroseTitle;
        var tooltip_id = windroseDiv + "-" + "tooltip";
        var tooltip_div = "<div id='" + tooltip_id + "' class='tooltip hidden'><span id='value'></span></div>";
        document.getElementById(windroseDiv).innerHTML = tooltip_div;
    } else {
        container = "#" + windspeedDiv;
        captionText = windspeedTitle;
        var tooltip_id = windspeedDiv + "-" + "tooltip";
        var tooltip_div = "<div id='" + tooltip_id + "' class='tooltip hidden'><span id='value'></span></div>";
        document.getElementById(windspeedDiv).innerHTML = tooltip_div;
    }

    // Tamaño de letra en pixels
    var pixels_font_size = $.getDefaultPx("#body");

    // The main SVG visualization element
    var vis = d3.select(container)
        .append("svg:svg")
        .attr("width", width + "px").attr("height", (height + (pixels_font_size * 2)) + "px");

    // Nota: se redefinen estas funciones
    var probabilityToRadiusScale =
        d3.scale.linear()
            .domain([0, probMaxValue])
            .range([centerWidth, visWidth - 20])
            .clamp(true);

    var speedToRadiusScale =
        d3.scale.linear()
            .domain([0, speedMaxValue])
            .range([centerWidth, visWidth - 20])
            .clamp(true);

    // Set up axes: circles whose radius represents probability or speed
    if (type == WINDROSE_GRAPH) {
        var probTickmarks = 0.05;
        if (probMaxValue > 0.20) {
            probTickmarks = 0.1;
        }

        var ticks = d3.range(0, probMaxValue + 0.001, 0.05);
        var tickmarks = d3.range(probTickmarks, probMaxValue - 0.05 + 0.001, probTickmarks);
        var radiusFunction = probabilityToRadiusScale;
        var tickLabel = function(d) {return "" + (d * 100).toFixed(0) + " %";};
    } else {
        var speedTickmarks = null;
        if (speedMaxValue > 50000) {
            speedTickmarks = 10000;
        }
        else if (speedMaxValue > 10000) {
            speedTickmarks = 2000;
        }
        else if (speedMaxValue > 5000) {
            speedTickmarks = 1000;
        }
        else if (speedMaxValue > 1000) {
            speedTickmarks = 200;
        }
        else if (speedMaxValue > 500) {
            speedTickmarks = 100;
        }
        else if (speedMaxValue > 200) {
            speedTickmarks = 50;
        }
        else if (speedMaxValue > 100) {
            speedTickmarks = 20;
        }
        else {
            speedTickmarks = 10;
        }

        var ticks = d3.range(0, speedMaxValue + 0.1, 10);
        var tickmarks = d3.range(speedTickmarks, speedMaxValue - 5 + 0.1, speedTickmarks);
        var radiusFunction = speedToRadiusScale;
        var tickLabel = function(d) {return "" + Math.round(d) + " " + speedUnitText;};
    }

    // Circles representing chart ticks
    vis.append("svg:g")
        .attr("class", "axes")
      .selectAll("circle")
        .data(ticks)
      .enter().append("svg:circle")
        .attr("cx", radius).attr("cy", radius)
        .attr("r", radiusFunction);

    // Text representing chart tickmarks
    vis.append("svg:g").attr("class", "tickmarks")
        .selectAll("text")
        .data(tickmarks)
      .enter().append("svg:text")
        .text(tickLabel)
        .attr("dy", "-2px")
        .attr("transform", function(d) {
            var y = visWidth - radiusFunction(d);
            return "translate(" + radius + "," + y + ")";});

    // Labels: cardinal points
    var marginCardinalPoints = 3 + (visWidth - 175) / 10;
    cardinalPointRotation = 0;
    vis.append("svg:g")
      .attr("class", "labels")
      .selectAll("text")
        .data(cardinalPointsLabels)
      .enter().append("svg:text")
        .attr("dy", "-" + marginCardinalPoints + "px")
        .attr("transform", function(d) {
            return "translate(" + radius + ", " + padding + ") rotate(" + getCardinalPointRotation() + ", 0, " + (radius - padding) + ")";})
        .text(function(dir) {return dir;});

    var rollup = rollupData(windroseData);

    if (type == WINDROSE_GRAPH) {
        drawComplexArcs(vis, rollup, speedToColor, windroseArcOptions);
    } else {
        drawComplexArcs(vis, rollup, probabilityToColor, windspeedArcOptions);
    }
    vis.append("svg:text")
       .text(captionText)
       .attr("class", "windcaption")
       .attr("transform", "translate(" + width / 2 + ", " + (height + (pixels_font_size * 1.5)) + ")");
}


//
// Actualización de diagramas con nuevos datos
//


// Update all diagrams
function updateWindVisDiagrams(json_wind_data) {
    updateBigWindrose(json_wind_data, WINDROSE_GRAPH);
    updateBigWindrose(json_wind_data, WINDSPEED_GRAPH);
}


// Update a specific digram to new data
function updateBigWindrose(windroseData, type) {
    var container = "";
    if (type == WINDROSE_GRAPH) {
        container = "#" + windroseDiv;
    } else {
        container = "#" + windspeedDiv;
    }

    var vis = d3.select(container).select("svg");
    var rollup = rollupData(windroseData);

    if (type == WINDROSE_GRAPH) {
        updateComplexArcs(vis, rollup, speedToColor, windroseArcOptions, type);
    } else {
        updateComplexArcs(vis, rollup, probabilityToColor, windspeedArcOptions, type);
    }
}


// Update drawn arcs, etc to the newly data
function updateComplexArcs(parent, plotData, colorFunc, complexArcOptions, type) {
    // Update the arcs' shape and color
    parent.select("g.arcs").selectAll("path")
        .data(plotData.dirs)
        .transition().duration(transitionDuration)
        .style("fill", colorFunc)
        .attr("d", arc(complexArcOptions));

    // Update the arcs' title tooltip
    if (type == WINDROSE_GRAPH) {
        var tooltip_id = windroseDiv + "-" + "tooltip";
    } else {
        var tooltip_id = windspeedDiv + "-" + "tooltip";
    }

    parent.select("g.arcs").selectAll("path")
        .on("mouseover", function (d) {
            d3.select("#" + tooltip_id)
            .style("left", d3.event.pageX + "px")
            .style("top", d3.event.pageY + "px")
            .style("opacity", 1)
            .select("#value")
            .text(d.d + " \u00b0: " + (100 * d.p).toFixed(1) + " %, " + d.s.toFixed(0) + " " + speedUnitText)
        })
        .on("mouseout", function () {
            d3.select("#" + tooltip_id)
            .style("opacity", 0);
        });

    // Update the calm wind probability in the center
    parent.select("g.calmwind").select("text")
        .data([plotData.calm.p])
        .text(function(d) {return Math.round(d * 100) + " %";});
}