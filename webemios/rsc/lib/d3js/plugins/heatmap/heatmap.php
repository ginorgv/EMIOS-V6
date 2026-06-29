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
