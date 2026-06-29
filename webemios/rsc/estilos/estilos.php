<?php
header('content-type:text/css');

session_start();

include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


// Tema (colores)
if (!isset($_SESSION["tema"]))
{
    $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_DEFECTO_OSCURO;
    $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_DEFECTO_INTERMEDIO;
    $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_DEFECTO_CLARO;
    $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_DEFECTO_FONDO;
    $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_DEFECTO_FONDO_CLARO;
}
else
{
    carga_colores_tema_actual();
}
$color_tema_oscuro = $_SESSION["colores"]["color_tema_oscuro"];
$color_tema_intermedio = $_SESSION["colores"]["color_tema_intermedio"];
$color_tema_claro = $_SESSION["colores"]["color_tema_claro"];
$color_tema_fondo = $_SESSION["colores"]["color_tema_fondo"];

// Tamaño de letra (en píxeles)
if (!isset($_SESSION["tamanyo_letra"]))
{
    $_SESSION["tamanyo_letra"] = TAMANYO_LETRA_DEFECTO;
}
$tamanyo_letra = $_SESSION["tamanyo_letra"];

// Color del contenido por defecto
$color_contenido = COLOR_CONTENIDO_DEFECTO;
?>


/* Estilos de Web EMIOS */


/* Descripción de perfil */
#descripcion-perfil {
    padding-left: 1em;
    padding-right: 1em;
}

/* Botones */
.botones-herramientas {
    margin-top: 1.1em;
}

.boton-herramientas {
	float: left;
	text-align: center;
    padding-left: 1em;
    padding-right: 1em;
    margin-bottom: -0.2em;
    margin-top: -1.4em;
}

.boton-seleccion-fichero-administracion, .boton-mostrar-imagen-fichero-administracion {
	margin-bottom: 10px;
}


/* Horarios semanales */
.seleccion-horario-semanal {
	float: left;
}

.nombres-periodos-dias-semana-informes {
    float: left;
    margin-top: 0.25em !important;
    margin-bottom: 0px !important;
    margin-left: 1em;
    width: 12em;
}

.horas-periodos-dias-semana-informes {
    float: left;
	margin-bottom: 0px !important;
    margin-top: 0em !important;
    width: 40%;
}

.nombres-periodos-dias-semana-ventana-modal {
    float: left;
    margin-bottom: 0 !important;
    margin-left: 0em;
    margin-top: 0.25em !important;
    width: 12em;
}

.horas-periodos-dias-semana-ventana-modal {
    float: left;
    margin-bottom: 0 !important;
    margin-top: 0 !important;
    width: 53%;
}

.separacion-fin-horario-semanal-ventana-modal {
    height: 0.75em;
}

.texto-horas-periodos-dias-semana {
    width: 100% !important;
    margin-bottom: 0.25em !important;
}

.margen-superior-primer-dia-semana {
    margin-top: 0.6em;
}

.margen-inferior-ultimo-dia-semana {
    margin-bottom: 0.4em;
}


/* Fechas */
.seleccion-fechas {
	float: left;
}

.nombre-periodos-fechas-informes {
    float: left;
    margin-top: 0.25em !important;
    margin-bottom: 0px !important;
    margin-left: 1em;
    width: 12em;
}

.fechas-periodos-fechas-informes {
    float: left;
	margin-bottom: 0px !important;
    margin-top: 0em !important;
    width: 47%;
}

.nombre-periodos-fechas-ventana-modal {
    float: left;
    margin-bottom: 0 !important;
    margin-left: 0em;
    margin-top: 0.25em !important;
    width: 12em;
}

.fechas-periodos-fechas-ventana-modal {
    float: left;
    margin-bottom: 0 !important;
    margin-top: 0 !important;
    width: 60%;
}

.texto-fechas {
    width: 85% !important;
    margin-bottom: 0.4em !important;
}

.margen-superior-fechas {
    margin-top: 0.6em;
}

.margen-inferior-fechas {
    margin-bottom: 0.2em;
}


/* Parámetros de pie de página */
.parametros-pie-pagina-numeros-paginas {
	float: left;
    text-align: left;
    padding-left: 1em;
    margin-top: 0.2em;
}

.parametros-pie-pagina-numeros-paginas-texto-titulos {
	float: left;
    text-align: left;
    padding-left: 1em;
    margin-top: -0.6em;
}


/* Informes */
.informe {
    margin-top: 0em;
    margin-bottom: 0em;
	margin-left: 1em;
    margin-right: 1em;
	text-align: left;
}


/* Gráficas */
.grafico50 {
    width: calc(50% - 1em);
	float: left;
	margin-top: 1em;
	margin-bottom: 1em;
    margin-left: 1em;
}

.fin-graficos50 {
    clear: both;
}

.grafica100 {
    width: calc(100% - 1em);
    height: 50em;
    float: left;
	margin-top: 1em;
    margin-bottom: 1em;
    margin-left: 0.5em;
    margin-right: 0.5em;
    clear: both;
}

.grafica90 {
    width: calc(100% - 6em);
    height: 50em;
    float: left;
	margin-top: 1em;
    margin-bottom: 1em;
    margin-left: 3em;
    margin-right: 3em;
    clear: both;
}

.grafica100 .jqplot-yaxis-tick {
    width: 7em;
}

.grafica90 .jqplot-yaxis-tick {
    width: 7em;
}

.mapa-calor100 {
	width: 92%;
    margin-left: 4%;
    margin-bottom: 0.25em;
    clear: both;
}

.texto100 {
    width: 100%;
    float: left;
	margin-top: 1em;
	margin-bottom: 1em;
}


/* Textos de información de informes */
.texto-informacion {
	width: 99%;
	float: left;
	margin-top: 1em;
	margin-bottom: 1em;
}


/* Tabla de datos de información */
.informacion-tabla-datos {
	width: 95%;
	float: left;
	padding-left: 1em;
	margin-top: 1em;
	margin-bottom: 1em;
}

.informacion-tabla-datos > ul {
    margin-bottom: 0px !important;
}


/* Controles varios */
.texto-informe-vacio {
    padding-top: 1em;
    padding-bottom: 1em;
    clear: both;
}

.texto-aviso-elemento-informe {
    padding-top: 1em;
    padding-bottom: 1em;
    clear: both;
}

.texto-elemento-no-mostrado-informe {
    padding-top: 1em;
    padding-bottom: 1em;
    height: auto !important;
    clear: both;
}

.texto-contenido-vacio {
    padding: 1em;
}

.input-texto-informes-pequenyo {
	height: 1.2em !important;
	width: 2.5em !important;
	vertical-align: baseline !important;
	text-align: left;
}

.input-texto-informes-mediano {
	height: 1.2em !important;
	width: 5em !important;
	vertical-align: baseline !important;
	text-align: left;
}

.input-texto-informes-grande {
	height: 1.2em !important;
	width: 7.5em !important;
	vertical-align: baseline !important;
	text-align: left;
}

.input-texto-informes-muy-grande {
	height: 1.2em !important;
	width: 20em !important;
	vertical-align: baseline !important;
	text-align: left;
}

.controles-acciones {
    color: <?php echo $color_contenido; ?> !important;
    width: 30em !important;
}

.controles-mensaje {
    font-family: Arial !important;
    font-size: 1em !important;
}

.lista-eventos, .lista-sensores, .lista-tarifas, .lista-apartados, .lista-localizaciones, .textos-informe, .imagenes-informe {
	padding-left: 1em;
}

.accion-predefinida-detalles-tabla {
	width: 100%;
    padding-left: 1em;
    padding-top: 0.7em;
}

.nombre-accion-predefinida-detalles-tabla {
    float: left;
    width: 20em;
}

.imagen-accion-predefinida-detalles-tabla {
    float: left;
    width: 10em;
}

.contenedor-boton-enviar-accion-predefinida-detalles-tabla {
    float: left;
}

.boton-enviar-accion-predefinida-detalles-tabla {
    margin-top: -0.2em;
}

.contenedor-textarea-detalle-tabla-datos {
    margin-right: 1em;
}

.opcion-accion-predefinida {
    margin-top: 0.2em;
    margin-bottom: 0.2em;
}

.nombre-fichero-filtro-informes {
    width: 50%;
}

.nombre-fichero-imagen-plantilla-informe {
    width: 30%;
}

.subtitulo-portada-plantilla-informe {
    width: 98%;
    width: calc(100% - 2em);
}

.titulo-plantilla-informe {
    width: 98%;
    width: calc(100% - 2em);
}


.boton_ayuda_videotutorial {
    font-size: 1.1em;
}

.seleccion-medicion {
    display: table !important;
    font-size: 1em !important;
}

.boton-medicion {
    text-shadow: 0 0 !important;
    font-weight: bold;
}

.lista-posiciones {
    float: left;
    padding-right: 1em;
}

.btn-medicion-seleccionada {
    color: #ffffff !important;
    background-color: #939393 !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_claro; ?>), to(<?php echo $color_tema_claro; ?>)) !important;
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -o-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: linear-gradient(to bottom, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-medicion-seleccionada:focus {
    color: #ffffff !important;
    background-color: #939393 !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_claro; ?>), to(<?php echo $color_tema_claro; ?>)) !important;
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -o-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: linear-gradient(to bottom, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-medicion-seleccionada:hover {
    color: #ffffff !important;
    background-color: #333333 !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_intermedio; ?>, <?php echo $color_tema_intermedio; ?>) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_intermedio; ?>), to(<?php echo $color_tema_intermedio; ?>)) !important;
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_intermedio; ?>, <?php echo $color_tema_intermedio; ?>) !important;
    background-image: -o-linear-gradient(top, <?php echo $color_tema_intermedio; ?>, <?php echo $color_tema_intermedio; ?>) !important;
    background-image: linear-gradient(to bottom, <?php echo $color_tema_intermedio; ?>, <?php echo $color_tema_intermedio; ?>) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-medicion-seleccionada:active {
    color: #ffffff !important;
    background-color: #333333 !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_oscuro; ?>), to(<?php echo $color_tema_oscuro; ?>)) !important;
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>) !important;
    background-image: -o-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>) !important;
    background-image: linear-gradient(to bottom, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-medicion-no-seleccionada {
    color: #ffffff !important;
    background-color: #AAAAAA !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, #AAAAAA, #AAAAAA) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#AAAAAA), to(#AAAAAA)) !important;
    background-image: -webkit-linear-gradient(top, #AAAAAA, #AAAAAA) !important;
    background-image: -o-linear-gradient(top, #AAAAAA, #AAAAAA) !important;
    background-image: linear-gradient(to bottom, #AAAAAA, #AAAAAA) !important;
    border-color: rgba(128, 128, 128, 1) rgba(128, 128, 128, 1) rgba(128, 128, 128, 1) !important;
}


/* Mapa */
.mapa {
	width: 100%;
    border-radius: 0px 0px 3px 3px;
}

.mapa-vacio {
    height: 2em;
}

.localizador-mapa {
	width: 100%;
	height: 325px;
	float: left;
	border: 1px solid #DDDDDD;
    box-sizing: border-box;
    border-radius: 3px;
}

.texto-mapa-vacio {
    height: 100%;
    margin-bottom: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-top: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-left: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    margin-right: <?php echo ($tamanyo_letra * 1.0)."px"; ?>;
    padding-left: 0;
}

.leaflet-container {
    font-size: 1em !important;
    background-color: white;
}

.leaflet-control-layers input {
	vertical-align: baseline;
}

.leaflet-control-layers-overlays label {
	font-size: 1em !important;
    padding-top: 0.2em !important;
}

.leaflet-control-layers label {
    margin-bottom: 0px !important;
}

.leaflet-popup-content {
    width: 32em !important;
}

.leaflet-control-layers-list {
    margin-bottom: 5px !important;
}

.contenedor-botones-tooltip-mapa {
    align: left;
}

.boton-tooltip-mapa {
	text-align: center;
    margin-left: 0em;
    margin-right: 1.5em;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    min-width: 3em;
}


/* Menú contextual */
.menu-contextual {
    display: none;
    z-index: 1000;
    position: absolute;
    /* Nota: Para que no se muestren los puntos de listas */
    overflow: hidden;
    border: 1px solid #979797;
    white-space: nowrap;
    font-family: Arial;
    font-size: 1em;
    color: #606060;
    box-shadow: #909090 0.15em 0.15em 0.10em 0.10em;
    padding: 0.1em;
    background-color: #F0F0F0;
    margin: 0;
}

.menu-contextual li {
    padding: 0.5em 1em;
    margin-left: 0 !important;
    cursor: pointer;
}

.menu-contextual li:hover {
    background-color: #909090;
}


/* Estilos de contenido de secciones vacías (sólo mensaje) */
.contenedor-seccion-vacia .datos {
	min-height: 3em;
}

.mensaje-seccion-vacia {
	text-align: left;
    margin-top: 0.4em;
    margin-left: -0.4em;
    margin-bottom: 0.4em;
}


/* Estilos de cuadrícula de widgets */
.texto-cuadricula-widgets-vacia {
    margin: 1em;
}

.tabla-cuadricula-widgets {
	width: 100%;
    table-layout: fixed;
}

.contenido-cuadricula-widgets {
    margin: 0.5em;
    /* Nota: Para que la altura del padre se ajuste a la altura de los hijos y se apliquen los estilos definidos aquí */
    /* (http://stackoverflow.com/questions/1709442/make-divs-height-expand-with-its-content) */
    overflow: hidden;
}


/* Estilos de widgets */

.cabecera-pestanya-widgets {
    text-align: center;
}

.hora-cabecera-pestanya-widgets {
    font-size: 2em;
    padding-top: 0.5em;
}
.fecha-cabecera-pestanya-widgets {
    font-size: 1em;
    padding-top: 0.5em;
}

.titulo-cabecera-pestanya-widgets {
    font-size: 1.5em;
    padding-top: 1em;
}

.titulo-fila-widgets {
    padding-left: 0.5em;
    padding-top: 0.5em;
}

.widget {
    float: left;
    border-radius: 3px;
    width: 100%;
}

.contenedor-widget {
    padding: 0.5em;
}

.titulo-widget {
	width: 100%;
    line-height: 1.1em;
    /* Nota: Para que la altura del padre se ajuste a la altura de los hijos y se apliquen los estilos definidos aquí */
    /* (http://stackoverflow.com/questions/1709442/make-divs-height-expand-with-its-content) */
    overflow: hidden;
}

.texto-titulo-widget-con-opciones {
    float: left;
    padding-left: 1em;
    padding-top: 0.3em;
    padding-bottom: 0.25em;
    width: calc(100% - 9em);
}

.texto-titulo-widget-sin-opciones {
    text-align: center;
    padding-bottom: 0.2em;
}

.contenedor-opciones-titulo-widget {
    float: right;
    padding-top: 0.3em;
    padding-bottom: 0.25em;
    width: calc(8em);
}

.opciones-titulo-widget {
	padding-right: 0.8em;
	float: right;
}

.contenido-widget {
    position: relative;
    float: left;
    width: 100%;
    border-radius: 0px 0px 3px 3px;
    display: table;
    z-index: 1;
    background-clip: padding-box;
}

.icono-widget {
  position: absolute;
  /* Nota: Si se pone 'z-index' a 0 no se pueden pulsar los botones del widget */
  z-index: -1;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
}

.texto-error-widget {
    font-size: 1.75em;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.mensaje-error-widget {
    font-size: 1.25em;
    margin-bottom: 0.5em;
}

.contenedor-imagen-widget {
    margin: 1em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.altura-contenido-widget-columnas-5 {
    height: 11em;
}
.altura-contenido-widget-columnas-4 {
    height: 13em;
}
.altura-contenido-widget-columnas-3 {
    height: 15em;
}
.altura-contenido-widget-columnas-2 {
    height: 17em;
}
.altura-contenido-widget-columnas-1 {
    height: 19em;
}

/* http://www.dev-metal.com/center-div-vertically-horizontally-modern-method-without-fixed-size/ */
.contenido-widget-contenedor-centrado {
    display: table-cell;
    text-align: center;
    vertical-align: middle;
    /* Nota: width 100% es necesario porque si solo hay un 'svg' (gauge-meter), no ocupa el 100% */
    width: 100%;
}

.contenido-widget-contenido-centrado {
    width: 100%;
}

.texto-grande-widget-valor-digital-sensor {
    font-size: 3em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-texto-grande-widget-valor-digital-columnas-5 {
    font-size: 2em;
}
.tamanyo-fuente-texto-grande-widget-valor-digital-columnas-4 {
    font-size: 2.5em;
}
.tamanyo-fuente-texto-grande-widget-valor-digital-columnas-3 {
    font-size: 3em;
}
.tamanyo-fuente-texto-grande-widget-valor-digital-columnas-2 {
    font-size: 4em;
}
.tamanyo-fuente-texto-grande-widget-valor-digital-columnas-1 {
    font-size: 6em;
}

/* Nota: El tamanyo del texto 'pequeño' es relativo al tamaño del texto grande */
.texto-pequenyo-widget-valor-digital-sensor {
    font-size: 0.6em;
    line-height: 0.6em;
}

.fecha-hora-widget-valor-digital-sensor {
    font-size: 1.1em;
    margin-bottom: 0.3em;
}

.texto-sin-valores-widget-valor-digital-sensor {
    font-size: 1.75em;
    margin-top: 0.3em;
    margin-bottom: 0.3em;
}

.indicador-widget-valor-analogico-sensor-reloj-con-valor-digital {
    width: 100%;
    text-align: left !important; /* Sin este 'align' al exportar a imagen salía mal */
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.altura-indicador-widget-reloj-con-valor-digital-columnas-5 {
    height: 6.5em;
}
.altura-indicador-widget-reloj-con-valor-digital-columnas-4 {
    height: 8.5em;
}
.altura-indicador-widget-reloj-con-valor-digital-columnas-3 {
    height: 10.5em;
}
.altura-indicador-widget-reloj-con-valor-digital-columnas-2 {
    height: 12.5em;
}
.altura-indicador-widget-reloj-con-valor-digital-columnas-1 {
    height: 14.5em;
}

.indicador-widget-valor-analogico-sensor-reloj-sin-valor-digital {
    width: 100%;
    margin-top: 0.2em;
    text-align: left !important; /* Sin este 'align' al exportar a imagen salía mal */
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.altura-indicador-widget-reloj-sin-valor-digital-columnas-5 {
    height: 8.5em;
}
.altura-indicador-widget-reloj-sin-valor-digital-columnas-4 {
    height: 10.5em;
}
.altura-indicador-widget-reloj-sin-valor-digital-columnas-3 {
    height: 12.5em;
}
.altura-indicador-widget-reloj-sin-valor-digital-columnas-2 {
    height: 14.5em;
}
.altura-indicador-widget-reloj-sin-valor-digital-columnas-1 {
    height: 16.5em;
}

.indicador-widget-valor-analogico-sensor-circular {
    width: 100%;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula */
.altura-indicador-widget-circular-columnas-5 {
    height: 7.5em;
}
.altura-indicador-widget-circular-columnas-4 {
    height: 7.5em;
}
.altura-indicador-widget-circular-columnas-3 {
    height: 9.5em;
}
.altura-indicador-widget-circular-columnas-2 {
    height: 11.5em;
}
.altura-indicador-widget-circular-columnas-1 {
    height: 13.5em;
}

.valor-unidad-widget-valor-analogico-sensor-reloj {
    font-size: 1.8em;
    line-height: 1em;
    margin-top: 0.2em;
    /* Nota: Para que el texto del título no ocupe varías líneas y se descuadre la cuadrícula */
    overflow: hidden;
    white-space: nowrap;
}

.fecha-hora-widget-valor-analogico-sensor {
    font-size: 1.1em;
    margin-top: 0.2em;
    margin-bottom: 0.3em;
}

.grafica-widgets-graficas {
    width: 98%;
    width: calc(100% - 2em);
    margin-left: 1em;
    margin-right: 1em;
    margin-top: 0.3em;
    text-align: left !important;
}

.grafica-widgets-graficas-tarta {
    width: 98%;
    width: calc(100% - 2em);
    margin-left: 1em;
    margin-right: 1em;
    text-align: left !important;
}

.mapa-calor-widget-mapa-calor {
    width: 98%;
    width: calc(100% - 2em);
    margin-left: 1em;
    margin-right: 1em;
    margin-top: 1em;
    text-align: left !important;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.altura-grafica-widgets-columnas-5 {
    height: 8.5em;
}
.altura-grafica-widgets-columnas-4 {
    height: 10.5em;
}
.altura-grafica-widgets-columnas-3 {
    height: 12.5em;
}
.altura-grafica-widgets-columnas-2 {
    height: 14.5em;
}
.altura-grafica-widgets-columnas-1 {
    height: 16.5em;
}

.altura-grafica-tarta-widgets-columnas-5 {
    height: 10.5em;
}
.altura-grafica-tarta-widgets-columnas-4 {
    height: 12.5em;
}
.altura-grafica-tarta-widgets-columnas-3 {
    height: 14.5em;
}
.altura-grafica-tarta-widgets-columnas-2 {
    height: 16.5em;
}
.altura-grafica-tarta-widgets-columnas-1 {
    height: 18.5em;
}

.fechas-horas-widgets-graficas, .fechas-horas-widget-mapa-calor {
    font-size: 1.1em;
    margin-top: 0.2em;
    margin-bottom: 0.3em;
}

.fecha-hora-widgets-valores, .fecha-hora-widgets-graficas {
    display: inline-block;
    margin-left: 0.25em;
    margin-right: 0.25em;
}

.texto-mensaje-widgets {
    font-size: 1.75em;
    margin-top: 0.3em;
    margin-bottom: 0.2em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-5 {
    font-size: 2em;
}
.tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-4 {
    font-size: 2.5em;
}
.tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-3 {
    font-size: 3em;
}
.tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-2 {
    font-size: 4em;
}
.tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-1 {
    font-size: 6em;
}

/* Nota: El tamanyo del texto de unidad de medida es relativo al tamaño del texto de evolución de valores */
.unidad-medida-widget-evolucion-valores-comparacion-periodos-sensor {
    font-size: 0.6em;
    line-height: 0.6em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-5 {
    font-size: 1.3em;
}
.tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-4 {
    font-size: 1.6em;
}
.tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-3 {
    font-size: 2em;
}
.tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-2 {
    font-size: 2.6em;
}
.tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-1 {
    font-size: 4em;
}

.texto-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor {
    line-height: 1em;
    margin-bottom: 0.1em;
    margin-top: 0.1em;
}

.texto-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor {
    line-height: 1em;
    margin-bottom: 0.1em;
    margin-top: 0.1em;
}

.fecha-hora-widget-evolucion-valores-comparacion-periodos-sensor {
    font-size: 1.1em;
    margin-bottom: 0.3em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-5 {
    font-size: 2em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}
.tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-4 {
    font-size: 2.5em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}
.tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-3 {
    font-size: 3em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}
.tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-2 {
    font-size: 4em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}
.tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-1 {
    font-size: 6em;
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.1em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-5 {
    font-size: 1.3em;
}
.tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-4 {
    font-size: 1.6em;
}
.tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-3 {
    font-size: 2em;
}
.tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-2 {
    font-size: 2.6em;
}
.tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-1 {
    font-size: 4em;
}

.texto-nombre-estado-widget-informacion-actuador {
    line-height: 1em;
    margin-top: 0.1em;
    margin-bottom: 0.2em;
}

.fecha-hora-widget-informacion-actuador {
    font-size: 1.1em;
    margin-bottom: 0.3em;
}

.texto-sin-ultima-accion-widget-informacion-actuador {
    font-size: 1.75em;
    margin-top: 0.3em;
    margin-bottom: 0.3em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-5 {
    font-size: 2em;
}
.tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-4 {
    font-size: 2.5em;
}
.tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-3 {
    font-size: 3em;
}
.tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-2 {
    font-size: 4em;
}
.tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-1 {
    font-size: 6em;
}

/* Nota: Altura dependiente de la configuración de la cuadrícula de widgets */
.tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-5 {
    font-size: 1.3em;
}
.tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-4 {
    font-size: 1.6em;
}
.tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-3 {
    font-size: 2em;
}
.tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-2 {
    font-size: 2.6em;
}
.tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-1 {
    font-size: 4em;
}

.texto-avance-proyecto-widget-informacion-proyecto {
    line-height: 1em;
    margin-bottom: 0.1em;
    margin-top: 0.1em;
}

.texto-estado-proyecto-widget-informacion-proyecto {
    line-height: 1em;
    margin-bottom: 0.1em;
    margin-top: 0.1em;
}

.fecha-hora-widget-informacion-proyecto {
    font-size: 1.1em;
    margin-bottom: 0.3em;
}

.texto-sin-datos-widget-informacion-proyecto {
    font-size: 1.75em;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.contenedor-botones-widget {
    text-align: center;
    margin-bottom: 0.5em;
}


/* Estilos de varios informes */
.texto-grande-portada-informe {
    text-align: center;
    font-weight: bold;
    font-size: 3em;
    line-height: 1.3em;
    color: <?php echo $color_tema_oscuro; ?>
}

.texto-mediano-portada-informe {
	text-align: center;
    font-size: 2em;
    line-height: 1.5em;
    color: <?php echo $color_tema_intermedio; ?>
}

.texto-pequenyo-portada-informe {
	text-align: center;
    font-size: 1.5em;
    line-height: 1.5em;
    color: #555555;
}

.contenedor-titulo-informe {
    padding-bottom: 1em;
    padding-top: 0.5em;
}

.titulo-informe {
    font-weight: bold;
    font-size: 1.3em;
    color: <?php echo $color_tema_intermedio; ?>;
    font-family: Arial;
    margin-top: 0.5em;
    padding-bottom: 2px;
    border-bottom: 0.06cm solid <?php echo $color_tema_intermedio; ?>;
}

.titulo-texto-informe {
    color: <?php echo $color_tema_oscuro; ?>;
    font-weight: bold;
}

.contador-caracteres-texto-informe {
    float: right;
}

.contenedor-texto-informe {
    float: left;
    width: 99%;
    width: calc(100% - 1em);
    margin-top: 0.5em;
    margin-bottom: 1em;
}

.contenedor-texto-informe-sin-margen-superior {
    float: left;
    width: 99%;
    width: calc(100% - 1em);
    margin-bottom: 1em;
}

.area-texto-informe {
    text-align: justify;
    font-family: Arial;
    width: 100%;
    margin-bottom: 0;
    clear: both;
}

.controles-textos-informe {
    margin-right: 1em;
}

.area-entrada-texto-detalles-informe {
    text-align: justify;
    font-family: Arial;
    width: 99%;
}

.apartado-informe {
    clear: both;
}

.tabla-parametros {
    width: 100%;
    text-align: left;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.contenedor-imagen-informe {
    width: 99%;
    width: calc(100% - 1em);
    margin-top: 1em;
    margin-bottom: 1em;
    text-align: center;
}

.imagen-informe {
    outline: 1px solid #777777;
    outline-offset: -1px;
}

/* Estilos para informes en ficheros */
.pagina-informe-fichero {
    background-color: white;
    font-size: 1.2em;
    page-break-after: always;
}

.fin-pagina-informe-fichero {
    clear: both;
}

.salto-pagina-informe-fichero {
    clear: both;
    page-break-after: always;
}

.no-divisible-informe-fichero {
    page-break-inside: avoid;
}

.titulo-informe-fichero {
    font-weight: bold;
    font-size: 1.3em;
    color: <?php echo $color_tema_intermedio; ?>;
    font-family: Arial;
    margin-top: 20px;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 0.06cm solid <?php echo $color_tema_intermedio; ?>;
}

.tabla-parametros-informe-fichero {
    width: 100%;
    text-align: left;
    font-family: arial;
    margin-top: 1em;
}

.tabla-parametros-informe-fichero td {
    padding-top: 0.4em;
    padding-bottom: 0.4em;
}

.tabla-cabecera-informe-fichero {
    width: 100%;
    padding-bottom: -9px;
    border-bottom: 4px solid <?php echo $color_tema_oscuro; ?>;
    vertical-align: bottom;
    font-family: arial;
    font-size: 26pt;
    color: #338C26;
}

.cabecera-informe-fichero {
    padding-bottom: 20px;
}

.logo-cabecera-informe-fichero {
    width: 25%;
    padding-bottom: 5px;
}

.imagen-logo-cabecera-informe-fichero {
    height: 70px !important;
    max-width: None !important;
}

.titulo-cabecera-informe-fichero {
    width: 75%;
    text-align: right;
    vertical-align: bottom;
    padding-bottom: 8px;
    color: <?php echo $color_tema_oscuro; ?>;
}

.texto-titulo-cabecera-informe-fichero {
    font-weight: bold;
    line-height: 1em;
    vertical-align: bottom !important;
}

.titulo-parametro-informe-fichero {
    width: 25%;
}

.contenido-parametro-informe-fichero {
    width: 75%;
}

.mensaje-aviso-informe-fichero {
    padding: 0.4em;
    font-family: Arial;
    text-align: left;
    margin-top: 2.5em;
    margin-bottom: 1em;
}

/* Nota: Para que se adapte la altura del padre a sus hijos */
/* (http://stackoverflow.com/questions/19354845/div-has-no-height-even-if-it-has-content) */
.fin-elemento-plantilla-informe {
    clear: both;
}

.contenedor-informe-fichero {
	background-color: white;
    height: auto;
    margin: auto;
    width: 1100px;
    min-width: 1100px;
}

/* Nota: Para que se 'separen' las palabras 'largas' en varias líneas */
/* Nota: Se establece la anchura al '99%' porque al '100%' en algunos casos se cortaba la última letra */
/* (https://stackoverflow.com/questions/13819390/css-word-wrap-not-working-as-expected) */
.texto-informe-informe-fichero {
    width: 99%;
    text-align: justify;
    page-break-inside: avoid;
    font-size: 1.15em;
    line-height: 1.2em;
    word-break: break-word;
}

.grafica100-informe-fichero {
    width: 98%;
    height: 50em;
    float: left;
	margin-top: 1%;
    margin-bottom: 1%;
    /* Nota: Para que quepa la fecha del eje Y */
    margin-left: 1%;
    margin-right: 1%;
    /* Para evitar que se corte la gráfica al final de una página */
    page-break-inside: avoid;
    clear: both;
}

.grafica90-informe-fichero {
    width: 88%;
    height: 50em;
    float: left;
	margin-top: 1%;
    margin-bottom: 1%;
    /* Nota: Para que quepa el valor del eje Y */
    margin-left: 6%;
    margin-right: 6%;
    /* Para evitar que se corte la gráfica al final de una página */
    page-break-inside: avoid;
    clear: both;
}

.grafica100-informe-fichero .jqplot-yaxis-tick {
    width: 7em;
}

.grafica90-informe-fichero .jqplot-yaxis-tick {
    width: 7em;
}

.grafico50-informe-fichero {
	width: 48%;
    float: left;
	margin-top: 1%;
	margin-bottom: 1%;
    margin-left: 1%;
    margin-right: 1%;
    text-align: center;
    page-break-inside: avoid;
}

.fin-graficos50-informe-fichero {
    clear: both;
}

.tabla-datos100-informe-fichero {
	float: left;
    width: 100%;
	margin-top: 1%;
	text-align: left;
}

.titulo-tabla-datos100-informe-fichero {
	float: left;
	width: 95%;
	margin-top: 1%;
	text-align: center;
    font-size: 1.2em;
    color: #555555;
}

.mapa-calor100-informe-fichero {
	width: 98%;
    margin-left: 0 !important
    margin-bottom: 0.25em;
    clear: both;
}

.texto100-informe-fichero {
    width: 98%;
    float: left;
	margin-top: 1%;
    margin-bottom: 1%;
    margin-left: 1%;
    margin-right: 1%;
}


/* Estilos de informes varios de ficheros */
.texto-grande-portada-informe-informe-fichero-con-salto-pagina {
    text-align: center;
    font-weight: bold;
    font-size: 3em;
    line-height: 1.5em;
    color: <?php echo $color_tema_oscuro; ?>;
    margin-top: 400px;
}
.texto-grande-portada-informe-informe-fichero-sin-salto-pagina {
    text-align: center;
    font-weight: bold;
    font-size: 3em;
    line-height: 1.5em;
    color: <?php echo $color_tema_oscuro; ?>;
    margin-top: 200px;
}

.texto-mediano-portada-informe-informe-fichero {
	text-align: center;
    font-size: 2em;
    line-height: 1.5em;
    color: <?php echo $color_tema_intermedio; ?>
}

.texto-pequenyo-portada-informe-informe-fichero {
	text-align: center;
    font-size: 1.5em;
    line-height: 1.5em;
    color: #555555;
}


/* Estilos 'extra' de tabla datos */
.tabla-datos100 {
    float: left;
    width: 100%;
	margin-top: 1em;
}

.titulo-tabla-datos100 {
	float: left;
	width: 95%;
	margin-top: 1em;
	text-align: center;
    font-size: 1.2em;
    color: #555555;
}

.boton-herramientas-detalle-tabla-datos {
	float: left;
	text-align: center;
    padding-left: 1em;
    padding-right: 1em;
    padding-bottom: 1em;
}

.boton-herramientas-detalle-tabla-datos > .btn {
    min-width: 5em;
}


/* Varios */

.boton-contenido-seccion {
	float: left;
	text-align: center;
    padding-right: 1em;
    padding-bottom: 1em;
}

.boton-contenido-seccion > .btn {
    min-width: 5em;
}

.cadena-fecha {
    white-space: nowrap;
}

.color-nodo-visible-localizaciones-hijas {
    color: #0088FF;
}

.color-estado-equipo-ok {
    color: #00AA00;
}

.color-estado-equipo-error {
    color: #FF4444;
}

.color-estado-equipo-pendiente {
    color: #FFBB00;
}

.contador-caracteres-textarea {
    font-weight: normal;
    font-size: 0.80em;
    opacity: 0.75;
}

.texto-parametros-extra-campo {
    width: 99% !important;
    width: calc(100% - 1em) !important;
}


/* Estilos de librerías externas */

/* Jqplot */
td.jqplot-table-legend-label {
    text-align: left;
}

.jqplot-table-legend-swatch, .jqplot-table-legend-label {
    padding-top: 0px !important;
}

table.jqplot-table-legend {
    width: max-content;
}

.jqplot-highlighter-tooltip, .jqplot-canvasOverlay-tooltip {
    border: 1px solid #bfbfbf;
    font-size: 1em;
    white-space: nowrap;
    background: rgba(230, 230, 230, 0.9);
    padding: 1px;
    text-align: left;
}

.jqplot-cursor-tooltip {
    border: 1px solid #bfbfbf;
    background: rgba(230, 230, 230, 0.9);
    padding: 1px;
    text-align: left;
    margin-top: 6px;
    margin-right: 5px;
}

div.jqplot-table-legend-swatch {
    border-width: 5px !important;
}

.jqplot-pie-series.jqplot-data-label {
    color: #000000;
}


/* Chosen */
.chosen-select {
    font-size: 1em !important;
    width: 100% !important;
    margin-bottom: 0.6em !important;
}

.chosen-select-administracion {
    font-size: 1em !important;
    width: 30em !important;
    margin-bottom: 1em !important;
}

.chosen-container .chosen-results li.highlighted {
    background-color: <?php echo $color_tema_oscuro; ?>; !important;
    background-image: linear-gradient(<?php echo $color_tema_claro; ?> 20%, <?php echo $color_tema_claro; ?> 90%) !important;
    color: #fff !important;
}

.chosen-container-single .chosen-single {
    background: #fff !important;
    color: <?php echo $color_contenido; ?> !important;
    font-size: 1em !important;
    height: 2.1em !important;
    line-height: 2em !important;
    box-shadow: none !important;
}

.chosen-container .chosen-results {
    max-height: 12em !important;
    color: <?php echo $color_contenido; ?> !important;
}

.chosen-container .chosen-results li {
    line-height: 1.3em !important;
}

.chosen-disabled .chosen-single {
    color: #000000 !important;
    background: #efefef none repeat scroll 0 0 !important;
    border: 1px solid #ccc !important;
}

.chosen-container-active .chosen-single {
    border: 1px solid #aaa !important;
    box-shadow: none !important;
}

.chosen-container-single .chosen-search input[type="text"] {
    height: 1.75em !important;
}

.contenedor-topologiaarbol {
    /* Nota: Para 'contrarrestar' el 'padding' de la tabla de datos y que queden los márgenes más ajustados */
    /* (igual en el mapa que en la topología cuando no hay datos) */
    margin-bottom: -0.2em;
    margin-top : -0.4em;
}

.grafico-topologiaarbol {
    padding-top: 2em;
}


/* Multiselect2side */
.ms2side__header {
	background-color: <?php echo $color_tema_fondo; ?> !important;
    height: 2.2em !important;
    width: 50% !important;
}

.ms2side__header input {
    width: 60% !important;
}

.ms2side__header a {
    background-color: <?php echo $color_tema_fondo; ?> !important;
    font-size: 0.75em !important;
    height: 1.5em !important;
}

.ms2side__select {
    width: 40% !important;
    margin-bottom: 0.2em;
}

.ms2side__div select {
    width: 100% !important;
}

.ms2side__options, .ms2side__updown {
    font-size: 1.2em;
    width: 3em !important;
}

.ms2side__options p, .ms2side__updown p {
    height: 1.1em !important;
    border: 1px solid #CCCCCC !important;
    border-radius: 3px;
    cursor: pointer !important;
    margin-top: 0px !important;
    margin-bottom: 4px !important;
}

.ms2side__options p.ms2side__hide, .ms2side__updown p.ms2side__hide {
	border: 1px solid #BBBBBB !important;
}

/* (Nota: No funciona en 'Internet Explorer' (sí en Chrome, FireFox y Edge)) */
.ms2side__select option {
	overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}



