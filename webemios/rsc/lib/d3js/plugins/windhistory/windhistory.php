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
