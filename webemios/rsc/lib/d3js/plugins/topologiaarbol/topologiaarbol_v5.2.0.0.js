// Topología de árbol


// Constantes
var TOPOLOGIA_ARBOL_COLOR_NODO_ROJO = "ROJO";
var TOPOLOGIA_ARBOL_COLOR_NODO_NARANJA = "NARANJA";
var TOPOLOGIA_ARBOL_COLOR_NODO_VERDE = "VERDE";
var TOPOLOGIA_ARBOL_COLOR_NODO_AZUL = "AZUL";
var TOPOLOGIA_ARBOL_COLOR_NODO_GRIS = "GRIS";

// Colores
var TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_ROJO = "#FF4444";
var TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_NARANJA = "#FFBB00";
var TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_VERDE = "#00AA00";
var TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_AZUL = "#0088FF";
var TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_GRIS = "#A0A0A0";


// Variables de configuración
var divTopologiaArbol = "topologiaArbol",
    numeroNivelesNodosTopologiaArbol = 3,
    alturaNodoFinalTopologiaArbol = 2.5,
    porcentajeAnchuraEtiquetaNodoFinalTopologiaArbol = 30,
    margenTextoNodoTopologiaArbol = 0.8,
    radioCirculoNodoTopologiaArbol = 0.45,
    duracionAnimacionTopologiaArbol = 750;


// Dibuja la topología en árbol
function dibuja_topologia_arbol(
    div_topologia_arbol,
    numero_niveles_nodos_topologia_arbol,
    numero_niveles_extra_nodos_topologia_arbol,
    nodo_inicial) {
    // Se recupera el tamaño de letra en píxeles
    var tamanyo_letra_pixeles = $.getDefaultPx("#body");

    // Variables de configuración
    var div = div_topologia_arbol;
    var numero_niveles_nodos = numero_niveles_nodos_topologia_arbol;
    var altura_nodo_final = alturaNodoFinalTopologiaArbol * tamanyo_letra_pixeles;
    var anchura_etiqueta_nodo_final = (porcentajeAnchuraEtiquetaNodoFinalTopologiaArbol / 100) * $('#' + div).width();
    var margen_texto_nodo = margenTextoNodoTopologiaArbol * tamanyo_letra_pixeles;
    var radio_circulo_nodo = radioCirculoNodoTopologiaArbol * tamanyo_letra_pixeles;
    var duracion_animacion = duracionAnimacionTopologiaArbol;

    // Variables para dibujar y actualizar el árrbol
    var arbol;
    var svg;
    var numero_nodos = 0;

    // Variables calculadas en la inicialización (utilizadas en función para dibujar el árbol)
    var anchura_texto_nodo_raiz_topologia_arbol;
    var diagonal;

    // Se inicializa la topología del árbol
    inicializa_topologia_arbol(nodo_inicial);
    dibuja_topologia_arbol_nodo(nodo_inicial);

    // Inicializa la topología del árbol
	function inicializa_topologia_arbol(nodo_inicial) {
        // Se establece la altura del arbol
        var numero_nodos_finales = 1;
        if (nodo_inicial.numero_nodos_finales > 0) {
            numero_nodos_finales = nodo_inicial.numero_nodos_finales;
        }
        var altura_topologia_arbol = (numero_nodos_finales * altura_nodo_final);
        $('#' + div).height(altura_topologia_arbol + "px");
		var width = $('#' + div).width();
        var height = $('#' + div).height();

        // Anchura del texto del nodo raíz:
        // - Se añade un texto temporal con el nombre de la red, se recupera su anchura y luego se elimina
        var svg_auxiliar = d3.select("#" + div).append("svg")
			.attr("width", width)
            .attr("height", height);
        var text = svg_auxiliar.append("svg:text")
            .text(nodo_inicial.nombre);
        anchura_texto_nodo_raiz_topologia_arbol = text.node().getBBox().width;
        text.node().parentNode.removeChild(text.node());

        // Se incrementa la anchura del árbol si hay niveles 'extra' en la topología
        var anchura_arbol = $('#' + div).width();
        var separacion_nodos = (anchura_arbol - anchura_texto_nodo_raiz_topologia_arbol - anchura_etiqueta_nodo_final) / numero_niveles_nodos;
        if (numero_niveles_extra_nodos_topologia_arbol > 0) {
            anchura_arbol += (numero_niveles_extra_nodos_topologia_arbol * separacion_nodos);
        }

        // Se añade un espacio adicional para la barra de scroll horizontal
        // (y el mismo espacio arriba para que quede simétrico cuando no haya barra de scroll)
        var altura_arbol = (height + (tamanyo_letra_pixeles * 2));
        $('#' + div).height(altura_arbol + "px");

        // Posición del nodo inicial
        nodo_inicial.x0 = altura_arbol / 2;
		nodo_inicial.y0 = 0;

        // Creación del árbol
        arbol = d3.layout.tree()
            .size([height, width]);

        // Función diagonal
        diagonal = d3.svg.diagonal()
            .projection(function(d) {
                return [d.y, d.x];
            });

        // Se añade el div del tooltip
        var tooltip_id = div + "-" + "tooltip";
        var tooltip_div = "<div id='" + tooltip_id + "' class='tooltip hidden'><span id='value'></span></div>";
        document.getElementById(div).innerHTML = tooltip_div;

        // Se crea el 'svg'
        // Nota: El 'append("g")' es necesario para que funcione en el chrome el "transform" (http://tutorials.jenkov.com/svg/g-element.html)
        svg = d3.select("#" + div).append("svg")
			.attr("width", anchura_arbol)
            .attr("height", altura_arbol)
            .append("g");

        // El texto del primer nodo lo pinta a la izquierda y hay que "desplazar" el canvas (svg))
        var desplazamiento_eje_x = anchura_texto_nodo_raiz_topologia_arbol + margen_texto_nodo;
        var desplazamiento_eje_y = 0;
        svg.attr("transform", "translate(" + desplazamiento_eje_x + "," + desplazamiento_eje_y + ")");
    }


    // Dibuja el arbol a partir de un nodo determinado
    function dibuja_topologia_arbol_nodo(nodo) {
        // Calcular la nueva configuración del árbol
		var nodos = arbol.nodes(nodo_inicial).reverse();
		var links = arbol.links(nodos);

        // Anchura de árbol y separación entre nodos
        var anchura_arbol = $('#' + div).width();
        var separacion_nodos = (anchura_arbol - anchura_texto_nodo_raiz_topologia_arbol - anchura_etiqueta_nodo_final) / numero_niveles_nodos;

		// Ajustar la profundidad de los nodos
		nodos.forEach(function(d) {
			d.y = d.depth * separacion_nodos;
		});

		// Actualizar los nodos
		var node = svg.selectAll("g.node")
			.data(nodos, function(d) {
				return d.id || (d.id = ++numero_nodos);
			});

        // Mostrar u ocultar hijos al pulsar en los nodos de la topología
        function nodo_topologia_arbol_click(d) {
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else {
                d.children = d._children;
                d._children = null;
            }
            dibuja_topologia_arbol_nodo(d);
        }

		// Añadir los nuevos nodos en la posición anterior del padreEnter any new nodes at the parent's previous position
		var nodeEnter = node.enter().append("g")
			.attr("class", "node topologiaarbol-etiqueta-nodo")
			.attr("transform", function(d) {
				return "translate(" + nodo.y0 + "," + nodo.x0 + ")";
			})
			.on("click", nodo_topologia_arbol_click);

        // Identificador del tooltip
        var tooltip_id = div + "-" + "tooltip";

        // Añadir el círculo del nodo con su relleno y el tooltip
		nodeEnter.append("circle")
            .attr("r", 1e-6)
            .attr("class", "topologiaarbol-nodo")
            .style("fill", function(d) {
                var valor_color_nodo = dame_valor_color_nodo_topologia_arbol(d.color_nodo);
                return (valor_color_nodo);
            })
            .on("mouseover", function (d) {
                d3.select("#" + tooltip_id)
                .style("left", d3.event.pageX + "px")
                .style("top", d3.event.pageY + "px")
                .style("opacity", 1)
                .select("#value")
                .text(((d.info_nodo == "") || (d.info_nodo == null)) ? d.nombre: d.info_nodo);
            })
            .on("mouseout", function () {
                d3.select("#" + tooltip_id)
                .style("opacity", 0);
            });

        // Añadir el texto del nodo
        nodeEnter.append("text")
            .attr("x", function(d) {
                return d.children || d._children? -margen_texto_nodo: margen_texto_nodo;
            })
            .attr("dy", ".35em")
            .attr("text-anchor", function(d) {
                return d.children || d._children? "end": "start";
            })
            .text(function(d) {
                return d.nombre;
            })
            .style("fill-opacity", 1e-6);

		// Transición de nodos a su nueva posición
		var nodeUpdate = node.transition()
			.duration(duracion_animacion)
			.attr("transform", function(d) {
				return "translate(" + d.y + "," + d.x + ")";
			});

        nodeUpdate.select("circle")
            .attr("r", radio_circulo_nodo);

        nodeUpdate.select("text")
            .style("fill-opacity", 1);

		// Transición de nodos 'salientes' a la nueva posición del padre
		var nodeExit = node.exit().transition()
			.duration(duracion_animacion)
			.attr("transform", function(d) {
				return "translate(" + nodo.y + "," + nodo.x + ")";
			})
			.remove();

		nodeExit.select("circle")
            .attr("r", 1e-6);

		nodeExit.select("text")
            .style("fill-opacity", 1e-6);

		// Actualizar los enlaces
		var link = svg.selectAll("path.link")
			.data(links, function(d) {
				return d.target.id;
			});

		// Añadir nuevos enlaces en la posición anterior del padre
		link.enter().insert("path", "g")
            .attr("class", "link topologiaarbol-enlace")
            .attr("d", function(d) {
                var o = {
                    x: nodo.x0,
                    y: nodo.y0
                };
                return diagonal({
                    source: o,
                    target: o
                });
            });

		// Transición de enlaces a su nueva posición
		link.transition()
            .duration(duracion_animacion)
            .attr("d", diagonal);

		// Transición de nodos 'salientes' a la nueva posición del padre
		link.exit().transition()
            .duration(duracion_animacion)
            .attr("d", function(d) {
                var o = {
                    x: nodo.x,
                    y: nodo.y
                };
                return diagonal({
                    source: o,
                    target: o
                });
            })
            .remove();

		// Guardar las antiguas posiciones para la transición
		nodos.forEach(function(d) {
			d.x0 = d.x;
			d.y0 = d.y;
		});
	}


    // Dibuja el arbol a partir de un nodo determinado
    function dame_valor_color_nodo_topologia_arbol(color_nodo) {
        var valor_color_nodo = null;
        switch (color_nodo) {
            case TOPOLOGIA_ARBOL_COLOR_NODO_ROJO: {
                valor_color_nodo = TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_ROJO;
                break;
            }
            case TOPOLOGIA_ARBOL_COLOR_NODO_NARANJA: {
                valor_color_nodo =  TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_NARANJA;
                break;
            }
            case TOPOLOGIA_ARBOL_COLOR_NODO_VERDE: {
                valor_color_nodo =  TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_VERDE;
                break;
            }
            case TOPOLOGIA_ARBOL_COLOR_NODO_AZUL: {
                valor_color_nodo =  TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_AZUL;
                break;
            }
            case TOPOLOGIA_ARBOL_COLOR_NODO_GRIS: {
                valor_color_nodo =  TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_GRIS;
                break;
            }
            default: {
                valor_color_nodo =  TOPOLOGIA_ARBOL_VALOR_COLOR_NODO_GRIS;
                break;
            }
        }
        return (valor_color_nodo);
    }
}
