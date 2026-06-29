/* Inicio fichero: 'rsc/lib/d3js/plugins/heatmap/heatmap.php'*/

<?php
header('content-type:text/css');

session_start();

include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');

// Tamaño de letra
$tamanyo_letra = TAMANYO_LETRA_DEFECTO;
?>


/* Estilos de 'heatmap' */

.heatmap-title {
    line-height: 2em;
    text-anchor: middle;
    fill: #777;
}

.periodLabel {
    text-anchor: end;
}

.subperiodLabel {
    text-anchor: middle;
}

.legend {
}

rect.bordered {
    stroke: #E6E6E6;
}

text.mono {
    font-size: 1em;
    fill: #aaa;
}

text.axis {
    fill-opacity: 0.6;
}

text.axis-highlight {
    fill-opacity: 1;
}

div.tooltip {
    position: absolute;
    text-align: center;
    font-size: 1.1em;
    width: auto;
    height: auto;
    padding: 0.1em;
    background: #DDDDDD;
    border: 1px solid grey;
    pointer-events: none;
    color: #666;
}

/* Fin fichero: 'rsc/lib/d3js/plugins/heatmap/heatmap.php'*/

/* Inicio fichero: 'rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol.php'*/

<?php
header('content-type:text/css');

session_start();

include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');

// Tamaño de letra
$tamanyo_letra = TAMANYO_LETRA_DEFECTO;
?>


/* Estilos de topología */
.topologiaarbol {
    height: 100%;
    margin-bottom: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-top: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-left: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-right: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    padding-left: 0;
    overflow-x: auto;
    overflow-y: hidden;
}

.topologiaarbol-nodo {
	cursor: pointer;
}

.topologiaarbol-nodo-verde {
	stroke: #00AA00;
	cursor: pointer;
}

.topologiaarbol-nodo-naranja {
	stroke: #FFBB00;
	cursor: pointer;
}

.topologiaarbol-nodo-rojo {
	stroke: #FF4444;
	cursor: pointer;
}

.topologiaarbol-nodo-azul {
	stroke: #0088FF;
	cursor: pointer;
}

.topologiaarbol-nodo-gris {
	stroke: #909090;
	cursor: pointer;
}

.topologiaarbol-etiqueta-nodo {
	font-size: <?php echo ($tamanyo_letra * 0.9)."px"; ?>;
}

.topologiaarbol-enlace {
	fill: none;
	stroke: #d5d5d5;
	stroke-width: 1.5px;
}

/* Fin fichero: 'rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol.php'*/

/* Inicio fichero: 'rsc/lib/d3js/plugins/windhistory/windhistory.php'*/

<?php
header('content-type:text/css');

session_start();

include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');

// Tamaño de letra
$tamanyo_letra = TAMANYO_LETRA_DEFECTO;
?>


/* Estilos de 'windhistory' */

.calmpercentage {
    font-size: 18px;
    text-anchor: middle;
    fill: rgb(0, 0, 0);
}

.calmcaption {
    font-size: 14px;
    text-anchor: middle;
    fill: rgb(0, 0, 0);
}

.windcaption {
    font-size: <?php echo ($tamanyo_letra * 1.2)."px"; ?>;
    text-anchor: middle;
    fill: rgb(0, 0, 0);
}

.caption {
    font-size: <?php echo ($tamanyo_letra * 1.5)."px"; ?>;
}

.axes {
    fill: none;
    stroke: #AAAAAA;
    stroke-width: 0.5px;
}

g.labels {
    fill: #444444;
    font-size: <?php echo $tamanyo_letra."px"; ?>;
    letter-spacing: <?php echo ($tamanyo_letra * 0.1)."px"; ?>;
}

.arcs {
    stroke: #000000;
    stroke-width: 0.5px;
}

.arctext text {
    font-size: <?php echo ($tamanyo_letra * 0.75)."px"; ?>;
}

g.tickmarks {
    font-size: <?php echo ($tamanyo_letra * 0.8)."px"; ?>;
    fill: #888888;
}

div.tooltip {
  position: absolute;
  text-align: center;
  font-size: 1.1em;
  width: auto;
  height: auto;
  padding: 0.1em;
  background: #DDDDDD;
  border: 1px solid grey;
  pointer-events: none;
  color: #666;
}

/* Fin fichero: 'rsc/lib/d3js/plugins/windhistory/windhistory.php'*/

