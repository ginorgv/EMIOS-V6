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
