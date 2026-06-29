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
$color_tema_fondo_claro = $_SESSION["colores"]["color_tema_fondo_claro"];

// Tamaño de letra (en píxeles)
if (!isset($_SESSION["tamanyo_letra"]))
{
    $_SESSION["tamanyo_letra"] = TAMANYO_LETRA_DEFECTO;
}
$tamanyo_letra = $_SESSION["tamanyo_letra"];

// Color del contenido por defecto
$color_contenido = COLOR_CONTENIDO_DEFECTO;

// Otros tamaños (en píxeles)
$altura_banner = "80";
$altura_logo = "53";
?>


/* Estilos Web comunes */


/* Elementos visibles y ocultos */
.elemento-oculto {
    display: none;
}

.elemento-visible {
    display: ;
}


/* Principal */
body {
    background-color: #DDDDDD;
    font-family: Arial;
    font-size: <?php echo $tamanyo_letra."px"; ?>;
    line-height: 1.3em;
}

#contenedor {
	background-color: <?php echo $color_tema_fondo; ?>;
    height: auto;
    margin: auto;
    width: 98%;
    /* Nota: La anchura mínima es 1024px, pero se deja un margen para la barra de scroll vertical */
    min-width: 960px;
}

#banner {
	background-color: <?php echo $color_tema_oscuro; ?>;
    height: <?php echo $altura_banner."px"; ?>;
}

#logo-web {
    float: left;
    padding-left: 1.2em;
    padding-top: <?php echo (($altura_banner - $altura_logo) / 2)."px"; ?>;
}

#descripcion-usuario {
	color: #CCCCCC;
    font-size: 1em;
    float: right;
    padding-right: 1.5em;
    padding-top: 1.5em;
    text-align: right;
}

#contenido {
	margin-top: 1em;
    width: 100%;
	height: 100%;
    min-height: 20em;
    /* Nota: Para que la altura del padre se ajuste a la altura de los hijos y se apliquen los estilos definidos aquí */
    /* (http://stackoverflow.com/questions/1709442/make-divs-height-expand-with-its-content) */
    overflow: hidden;
}

#pie-pagina {
	background-color: <?php echo $color_tema_oscuro; ?>;
	height: 3em;
	color: #FFFFFF;
	text-align: center;
}

#pie-pagina p {
    padding-top: 1em;
    line-height: 1.2em;
    margin: 0 !important;
}

.texto-fijo-pie-pagina {
    color: #FFFFFF !important;
    text-decoration: none !important;
}

.texto-adicional-pie-pagina {
    color: #CCCCCC;
}


/* Login */
#contenido-login {
	margin-top: 1em;
    width: 100%;
	height: 100%;
    min-height: 450px;
}

#controles-login {
	background-color: <?php echo $color_tema_oscuro; ?>;
	color: #FFFFFF;
	padding-top: 25px;
    padding-bottom: 25px;
	margin-top: 125px;
	font-size: 0.85em;
    width: 450px;
    border-radius: 3px;
}

/* https://github.com/FortAwesome/Font-Awesome/issues/458 */
.iconos-login {
	display: inline-block;
	font-size: 1.5em;
    color: #FFFFFF;
    width: 1.25em;
	text-align: center;
    vertical-align: middle;
}

#controles-login fieldset input {
	margin-bottom: 10px;
	margin-top: 10px;
	height: 1.5em;
	width: 150px;
	font-size: 1em;
	vertical-align: baseline;
    color: <?php echo $color_contenido; ?>;
}

#boton-login {
	width: 135px;
    font-size: 1.3em !important;
    line-height: 1.5em !important;
}


/* Menus y contenido de secciones */
#menu-modulos {
    background-color: <?php echo $color_tema_oscuro; ?>;
    font-size: 1.2em;
    padding-bottom: 0.5em;
    /* Nota: La anchura mínima es 1024px, pero se deja un margen para la barra de scroll vertical */
    min-width: 960px;
}

#menu-modulos a:link, #menu-modulos a:visited, #menu-modulos a:active {
    color: #FFFFFF;
}

.menu-modulos-opciones-modulos {
    max-width: 80%;
}

.menu-modulos-opcion-modulo {
	float: left;
    padding-top: 0.5em;
	padding-left: 1em;
}

.menu-modulos-opcion-modulo a {
	color: #CCCCCC !important;
}

.menu-modulos-opcion-salir {
    padding-top: 0.5em;
    padding-right: 1.5em;
    cursor: pointer;
    color: #CCCCCC;
}

.modulo-actual a {
	font-weight: bold;
    color: #FFFFFF !important;
}

/* Nota: El navegador Qt no soporta 'calc' en los estilos, se establece un estilo anterior (que aplica en ese caso) */
#contenedor-menu-secciones {
    float: left;
    /* Nota: Anchura para que quepan los nombres de los módulos y mayoría de secciones */
    width: 13%;
    width: calc(13em);
}

#contenedor-contenido-seccion {
    float: right;
    width: 87%;
    width: calc(100% - 13em);
}

#menu-secciones {
    padding-left: 1em !important;
    padding-bottom: 1em !important;
}

#margen-inferior-menu-secciones {
    height: 1em;
}

#contenido-seccion {
    padding-left: 1em !important;
    padding-right: 1em !important;
}

#menu-secciones a:link, #menu-secciones a:visited, #menu-secciones a:active, #menu-secciones a:hover, #menu-secciones a:focus {
    color: <?php echo $color_contenido; ?> !important;
    text-decoration: none !important;
    cursor: pointer! important;
}

#cabecera-menu-secciones {
    border-radius: 3px 3px 0px 0px;
	background-color: <?php echo $color_tema_oscuro; ?>;
	padding-left: 1em;
	padding-top: 0.3em;
	padding-bottom: 0.15em;
	color: #FFFFFF;
	font-weight: bold;
}

#contenido-menu-secciones {
    padding-top: 0.85em;
    border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
	border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-bottom: 1px solid <?php echo $color_tema_oscuro; ?>;
    border-radius: 0 0 3px 3px;
    min-height: 20em;
}

#menu-secciones nav p {
    margin-top: 0.2em;
    margin-bottom: 0.4em;
	margin-left: 1em;
    margin-right: 1em;
    line-height: 1.2em
}

.seccion-actual {
	font-weight: bold;
}

.contenido-seccion-pantalla-completa {
    width: 100% !important;
}

a:link, a:visited, a:active, a:focus, a {
    text-decoration: none;
    color: <?php echo $color_tema_oscuro; ?>;
    outline-style: none;
    -moz-outline-style: none;
}

.texto-contenido-seccion-error {
    margin: 1em;
}


/* Estilos de tabla-datos */
.tabla-datos, .fila-tabla-datos {
    float: left;
	width: 100%;
}

.titulo-tabla-datos {
    border: solid 1px <?php echo $color_tema_oscuro; ?>;
    box-sizing: border-box;
    border-radius: 3px 3px 0 0;
	background-color: <?php echo $color_tema_oscuro; ?>;
	padding-top: 0.25em;
	padding-bottom: 0.1em;
	color: #FFFFFF;
	font-weight: bold;
	width: 100%;
	height: auto;
	float: left;
    font-size: 1em !important;
}

.titulo-tabla-datos-contenido-oculto {
    border-radius: 3px 3px 3px 3px !important;
}

.contenedor-detalle-tabla-datos .titulo-tabla-datos {
    background-color: <?php echo $color_tema_intermedio; ?> !important;
}

.pestanyas-embebidas .titulo-tabla-datos {
    background-color: <?php echo $color_tema_intermedio; ?> !important;
}

.texto-titulo-tabla-datos {
    padding-left: 1em;
}

/* Nota: Para que se adapte la altura del padre a sus hijos */
/* (http://stackoverflow.com/questions/19354845/div-has-no-height-even-if-it-has-content) */
.fin-tabla-datos {
    clear: both;
}

.opciones-tabla-datos {
	padding-right: 0.8em;
	float: right;
	color: <?php echo $color_tema_oscuro; ?>;
}

.cabecera-tabla-datos {
    border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-bottom: solid 1px <?php echo $color_tema_oscuro; ?>;
    box-sizing: border-box;
	color: <?php echo $color_tema_oscuro; ?>;
	font-weight: bold;
	float: left;
    width: 100%;
	text-align: center;
    padding-top: 0.3em;
    padding-bottom: 0.1em;
}

.cabecera-tabla-datos-sin-opciones {
    width: 100% !important;
}

.cabecera-tabla-datos-con-opciones {
    float: left !important;
    width: 85% !important;
    width: calc(100% - 8em) !important;
}

.pie-tabla-datos {
    border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-bottom: solid 1px <?php echo $color_tema_oscuro; ?>;
    box-sizing: border-box;
    border-radius: 0px 0px 3px 3px;
    color: <?php echo $color_tema_oscuro; ?>;
    font-weight: bold;
	float: left;
	width: 100%;
	text-align: center;
    padding-top: 0.2em;
    padding-bottom: 0.2em;
}

.contenedor-datos-tabla-datos, .contenedor-datos-tabla-datos-sin-margenes {
	border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-bottom: solid 1px <?php echo $color_tema_oscuro; ?>;
    box-sizing: border-box;
	color: <?php echo $color_contenido; ?>;
	float: left;
	width: 100%;
}

.contenedor-datos-tabla-datos-sin-borde-inferior {
    border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    box-sizing: border-box;
	color: <?php echo $color_contenido; ?>;
	float: left;
	width: 100%;
}

.contenedor-datos-tabla-datos-sin-pie {
    border-radius: 0px 0px 3px 3px;
}

.contenedor-fila-tabla-datos {
    border-left: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-right: solid 1px <?php echo $color_tema_oscuro; ?>;
    border-bottom: solid 1px <?php echo $color_tema_claro; ?>;
    box-sizing: border-box;
	color: <?php echo $color_contenido; ?>;
	float: left;
	width: 100%;
}

.contenedor-ultima-fila-tabla-datos {
    border-bottom: solid 1px <?php echo $color_tema_oscuro; ?>;
}

.contenedor-fila-tabla-datos-par {
    background-color: <?php echo $color_tema_fondo_claro; ?>;
}

.contenedor-fila-tabla-datos-impar {
    background-color: <?php echo $color_tema_fondo; ?>;
}

.contenido-fila-tabla-datos {
	float: left;
	width: 100%;
}

.contenedor-datos-fila-tabla-datos-sin-opciones {
    width: 100%;
}

.contenedor-datos-fila-tabla-datos-con-opciones {
    float: left;
    width: 85%;
    width: calc(100% - 8em);
}

.contenedor-opciones-fila-tabla-datos {
    float: right;
    width: 15%;
    width: calc(8em);
}

.contenedor-datos-tabla-datos, .contenedor-datos-tabla-datos-sin-borde-inferior, .contenedor-fila-tabla-datos {
	padding-bottom: 0.2em !important;
    padding-top: 0.4em !important;
}

.contenido-detalle-tabla-datos {
    clear: left;
    padding-left: 1em;
    padding-top: 0.5em;
    padding-bottom: 1em;
    padding-right: 1em;
}

.contenido-detalle-tabla-datos > ul {
    margin-left: 1em !important;
    margin-top: 0em !important;
    margin-bottom: 0px !important;
}

.detalle-tabla-datos {
	float: left;
	width: 100%;
	margin-top: 0.7em;
	margin-bottom: 0.7em
	text-align: left;
	display: none;
}

.dato-tabla-datos {
	float: left;
	text-align: center;
}

columna-lista-tabla-datos {
    padding-left: 0.5em;
    padding-right: 0.5em;
}

.dato-tabla-datos-izda {
    width: 80%;
    float: left;
    text-align: left;
    padding-left: 1em;
}

.dato-tabla-datos input {
	color: <?php echo $color_contenido; ?>;
	margin-top: 10px;
	margin-bottom: 10px;
	height: 12px;
	width: 100px;
	font-size: 12px;
}

.tabla-datos .span2,
.tabla-datos .span3,
.tabla-datos .span4 {
    padding-left: 1em;
}


/* Filtros */
.filtro-texto {
    width: 99% !important;
    width: calc(100% - 1em) !important;
}

.filtro-desplegable {
    width: 100% !important;
}

.filtro-informes {
	width: auto;
    float: left;
    padding-left: 1em;
    padding-right: 1em;
    padding-top: 0.2em;
    padding-bottom: 0.2em;
}

.filtro-informes-solo-botones {
    float: left;
    margin-top: -0.5em;
    padding-bottom: 0.9em;
    padding-left: 1em;
    padding-right: 1em;
    width: auto;
}


/* Estilos de pestanyas-embebidas */
.pestanyas-embebidas {
	padding-right: 1em;
}


/* Anchuras (en porcentajes) */
.anchura13 {
    width: 13% !important;
}

.anchura15 {
    width: 15% !important;
}

.anchura18 {
    width: 18% !important;
}

.anchura22 {
    width: 22% !important;
}

.anchura30 {
    width: 30% !important;
}

.anchura45 {
    width: 45% !important;
}

.anchura80 {
    width: 80% !important;
}

.anchura90 {
    width: 90% !important;
}

.anchura100 {
    width: 100% !important;
}


/* Selectores */
.selector-fecha {
	width: 6em !important;
	font-size: 10px !important;
	vertical-align: baseline !important;
    background-color: #FFFFFF !important;
}

.selector-hora {
	width: 3em !important;
	font-size: 10px !important;
	vertical-align: baseline !important;
    background-color: #FFFFFF !important;
}


/* Varios */
.alineado-texto {
	margin-top: -0.2em !important;
}

.sin-margen {
    padding-left: 0px !important;
}

.centrado {
	text-align: center;
}

.alineado-izda {
	text-align: left;
}

.desplegable-simple {
	display: block;
    float: left;
    padding-left: 1em;
    padding-right: 1em;
    padding-top: 0.2em;
    padding-bottom: 0.2em;
    min-height: 4em;
}

.desplegable-simple-sin-etiqueta {
	display: block;
    float: left;
    padding-left: 1em;
    padding-right: 1em;
    min-height: 3em;
    margin-top: -0.25em;
}

.desplegable-simple-sin-margen-superior {
	display: block;
    float: left;
    padding-left: 1em;
    padding-right: 1em;
    margin-top: -0.5em;
    padding-bottom: 0.2em;
    min-height: 4em;
}

.margenes-verticales {
	margin-top: 0.3em;
	margin-bottom: 0.3em;
}

.margen-superior-pequenyo {
	margin-top: 0.3em;
}

.margen-superior {
	margin-top: 0.8em;
}

.margen-inferior {
	margin-bottom: 0.8em;
}

.boton-formulario {
    font-size: 0.95em !important;
    line-height: 1.5em !important;
	margin-top: 1.4em !important;
}

#input-form-progs {
	font-size: 10px;
	color: <?php echo $color_contenido; ?>;
	vertical-align: sub;
}

select {
	color: <?php echo $color_contenido; ?>;
	font-size: 1em;
	width: 150px;
	height: 2em;
	vertical-align: baseline;
}

.middle {
	margin-top: 0.8em;
	margin-bottom: 0.8em;
}

.clickable, .boton-tabla-datos, .boton-pantalla-completa, .datepicker, .timepicker, .monthdaypicker {
	cursor: pointer !important;
}

.boton-pantalla-completa {
    padding-top: 0.5em;
}

.cadena_fecha {
    white-space: nowrap;
}

.iconos-dato {
    white-space: nowrap;
}

.icono-escalable {
    width: 1em;
    height: 1em;
}

.boton-formulario-ultima-fila {
    margin-top: -1.2em !important;
    padding-bottom: 0.8em !important;
}

.boton-filtro-ultima-fila {
    margin-top: -0.5em !important;
}

.elemento-seleccionable {
  -webkit-touch-callout: text !important; /* iOS Safari */
    -webkit-user-select: text !important; /* Safari */
     -khtml-user-select: text !important; /* Konqueror HTML */
       -moz-user-select: text !important; /* Firefox */
        -ms-user-select: text !important; /* Internet Explorer/Edge */
            user-select: text !important; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

.elemento-no-seleccionable {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

/* Varios UI */
div.ui-datepicker {
	font-size: 0.8em;
}

div.ui-tooltip {
	font-size: 1em;
}

.ui-autocomplete * {
	font-size: 0.8em;
}

.ui-tabs-anchor {
	font-size: 1em;
}

.ui-widget-content {
	font-size: 1.2em;
}

details summary::-webkit-details-marker {
	display: none;
}


/* http://stackoverflow.com/questions/17323224/bootstrap-changing-dropdown-menus-active-link-color */
.dropdown-menu > .active > a,
.dropdown-menu > .active > a:hover,
.dropdown-menu > .active > a:focus {
	color: #ffffff;
	text-decoration: none;
	outline: 0;
	background-color: #018115;
	background-image: -moz-linear-gradient(top, #7FE481, #018115);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#7FE481), to(#018115));
	background-image: -webkit-linear-gradient(top, #7FE481, #018115);
	background-image: -o-linear-gradient(top, #7FE481, #018115);
	background-image: linear-gradient(to bottom, #7FE481, #018115);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff7FE481', endColorstr='#ff018115', GradientType=0);
}


/* Ventanas modales */
.modal {
    width: 800px !important;
    margin-left: -400px !important;
}

.modal-header {
    background-color: <?php echo $color_tema_oscuro; ?>;
    border-left: solid 1px #CCCCCC;
    border-right: solid 1px #CCCCCC;
    border-top: solid 1px #CCCCCC;
    border-radius: 6px 6px 0 0;
}

.modal-header h3 {
    color: #FFFFFF;
    font-size: 1.5em;
    line-height: 1.5em !important;
}

.modal-body {
	max-height: 510px !important;
    background-color: <?php echo $color_tema_fondo; ?> !important;
}

.modal-footer {
    background-color: #DDDDDD;
}

.modal-footer .btn {
    font-size: 1.2em !important;
    line-height: 1.5em !important;
    margin-left: 0.5em !important;
}

.close {
    color: #CCCCCC !important;
    font-size: 1.5em !important;
    line-height: 1em !important;
    opacity: 0.4 !important;
}

.close:hover,
.close:focus {
    opacity: 0.6 !important;
}

.ayuda-ventana-modal {
    font-size: 1.1em;
    float: right;
    cursor: pointer;
    margin-right: 0.5em;
    margin-top: 0.45em;
    display: none;
}


/* Clases de la ventana modal de administración */
.select-administracion {
	width: 30em;
	font-size: 1em !important;
}

.input-administracion {
	width: 30em;
	font-size: 1em !important;
}

.input-administracion-50-izda {
	width: 14em;
    margin-right: 0.75em;
	font-size: 1em !important;
}

.input-administracion-50-dcha {
	width: 14em;
	font-size: 1em !important;
}

.input-administracion-33-izda, .input-administracion-33-centro {
	width: 9.6em;
    margin-right: 0.75em;
	font-size: 1em !important;
}

.input-administracion-33-dcha {
	width: 9.6em;
	font-size: 1em !important;
}

.slider-administracion {
	width: 30em;
	font-size: 1em !important;
    margin-bottom: 0.5em;
}

.selector-fechas-administracion {
	width: 30em;
	font-size: 1em !important;
}

.selector-fechas-administracion-50-izda {
	width: 14em;
    margin-right: 0.75em;
	font-size: 1em !important;
}

.selector-fechas-administracion-50-dcha {
	width: 14em;
	font-size: 1em !important;
}

.selector-color-administracion {
    height: 2em !important;
}

.titulo-campo-administracion {
	color: <?php echo $color_tema_oscuro; ?>;
	font-weight: bold;
}


/* Colores */
.color-tema-oscuro {
	color: <?php echo $color_tema_oscuro; ?>;
}

.color-verde {
	color: #3D9F36;
}

.color-rojo {
	color: #DA4F49;
}

.color-naranja {
	color: #E65F00;
}

.color-gris {
	color: #444444;
}

.color-gris-claro {
	color: #777777;
}

.color-gris-muy-claro {
	color: #BBBBBB;
}

.color-azul {
	color: #2F8AD3;
}

.color-blanco {
	color: #FFFFFF;
}


/* Controles varios */
.error {
    color: #A00 !important;
}

.data-check-failed {
    background-color: #EE8779 !important;
}

.mostrar-todos-elementos-y {
    overflow-y: visible !important
}

.mostrar-barra-desplazamiento-y {
    overflow-y: auto !important
}

li {
    line-height: 1.5em;
    margin-left: 1em;
}

.row-fluid [class^="span"] {
    min-height: 10px !important;
}

input[type="text"], input[type="password"] {
    font-size: 1em !important;
    height: 1.3em !important;
    line-height: 1.3em !important;
    color: <?php echo $color_contenido; ?> !important;
}

textarea {
    font-size: 1em !important;
    line-height: 1.3em !important;
    color: <?php echo $color_contenido; ?> !important;
}

.icono-ayuda {
    font-size: 1.6em;
}

.boton-ayuda-select-administracion {
    position: relative;
    top: 0.2em;
}

.boton-ayuda-texto-informe {
    position: relative;
    top: 0.2em;
}


/* Estilos de librerías externas */


/* Alertas de jQuery Alert */
#popup_container {
	font-size: 1em;
    max-width: 70vw !important;
    min-width: 40vw !important;
}

#popup_title {
    font-size: 1.2em;
    border-left: solid 1px #CCCCCC;
    border-right: solid 1px #CCCCCC;
    border-top: solid 1px #CCCCCC;
}

#popup_message {
    font-size: 1em;
}

#popup_ok {
    font-size: 1.2em !important;
    line-height: 1.5em !important;
}

#popup_cancel {
    font-size: 1.2em !important;
    line-height: 1.5em !important;
}

.jHelp_tarifa_cierre {
    max-width: 70vw !important;
    min-width: 40vw !important;
    height: 90vh !important;
    position: fixed;
    top: 15%;
}

.jHelp_tarifa_cierre_content {
    width: 98% !important;
    min-height: 85% !important;
    height: 85% !important;
    padding-right: 0px !important;
}

.jInfo_popup #popup_title {
	background-color: #3D9F36 !important;
}

.jAlert_popup #popup_title {
	background-color: #DA4F49 !important;
}

.jConfirm_popup #popup_title {
	background-color: #3D9F36 !important;
}
.jConfirm_popup #popup_ok {
	background-color: #3D9F36;
}

#popup_prompt_input {
    height: 2em !important;
}


/* Datepicker */
.datepicker table tr td.today,
.datepicker table tr td.today:hover,
.datepicker table tr td.today.disabled,
.datepicker table tr td.today.disabled:hover {
    background-color: #CCCCCC !important;
    background-image: -moz-linear-gradient(top, #CCCCCC, #CCCCCC);
    background-image: -ms-linear-gradient(top, #CCCCCC, #CCCCCC);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#CCCCCC), to(#CCCCCC));
    background-image: -webkit-linear-gradient(top, #CCCCCC, #CCCCCC);
    background-image: -o-linear-gradient(top, #CCCCCC, #CCCCCC);
    background-image: linear-gradient(to_bottom, #CCCCCC, #CCCCCC) !important;
}

.datepicker table tr td.active,
.datepicker table tr td.active:hover,
.datepicker table tr td.active.disabled,
.datepicker table tr td.active.disabled:hover {
    color: #FFFFFF !important;
    background-color: <?php echo $color_tema_oscuro; ?> !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>);
    background-image: -ms-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_oscuro; ?>), to(<?php echo $color_tema_oscuro; ?>));
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>);
    background-image: -o-linear-gradient(top, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>);
    background-image: linear-gradient(to bottom, <?php echo $color_tema_oscuro; ?>, <?php echo $color_tema_oscuro; ?>) !important;
}

.datepicker thead tr:first-child th,
.datepicker tfoot tr:first-child th {
    background-color: <?php echo $color_tema_claro; ?> !important;
}

.datepicker thead tr:first-child th:hover,
.datepicker tfoot tr:first-child th:hover {
    background-color: <?php echo $color_tema_intermedio; ?> !important;
}

.datepicker table tr td.today:hover,
.datepicker table tr td.today:hover:hover,
.datepicker table tr td.today.disabled:hover,
.datepicker table tr td.today.disabled:hover:hover,
.datepicker table tr td.today:active,
.datepicker table tr td.today:hover:active,
.datepicker table tr td.today.disabled:active,
.datepicker table tr td.today.disabled:hover:active,
.datepicker table tr td.today.active,
.datepicker table tr td.today:hover.active,
.datepicker table tr td.today.disabled.active,
.datepicker table tr td.today.disabled:hover.active,
.datepicker table tr td.today.disabled,
.datepicker table tr td.today:hover.disabled,
.datepicker table tr td.today.disabled.disabled,
.datepicker table tr td.today.disabled:hover.disabled,
.datepicker table tr td.today[disabled],
.datepicker table tr td.today:hover[disabled],
.datepicker table tr td.today.disabled[disabled],
.datepicker table tr td.today.disabled:hover[disabled] {
    background-color: #CCCCCC !important;
}


/* Bootstrap */
.tab-content {
    /* Nota: Para que no se muestre la barra de scroll horizontal (aunque el contenido no supere el 100 %) */
    overflow: visible !important;
}

.btn {
    font-size: 1em !important;
    line-height: 1.7em !important;
}

.btn-success {
    color: #ffffff !important;
    background-color: #939393 !important;
    background-image: -moz-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $color_tema_claro; ?>), to(<?php echo $color_tema_claro; ?>)) !important;
    background-image: -webkit-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: -o-linear-gradient(top, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    background-image: linear-gradient(to bottom, <?php echo $color_tema_claro; ?>, <?php echo $color_tema_claro; ?>) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-success:focus {
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

.btn-success:hover {
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

.btn-success:active {
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

.btn-success.disabled,
.btn-success[disabled] {
    color: #ffffff !important;
    background-color: #AAAAAA !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, ##AAAAAA, ##AAAAAA) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(##AAAAAA), to(##AAAAAA)) !important;
    background-image: -webkit-linear-gradient(top, ##AAAAAA, ##AAAAAA) !important;
    background-image: -o-linear-gradient(top, ##AAAAAA, ##AAAAAA) !important;
    background-image: linear-gradient(to bottom, #AAAAAA, #AAAAAA) !important;
    border-color: rgba(128, 128, 128, 1) rgba(128, 128, 128, 1) rgba(128, 128, 128, 1) !important;
}

.btn-danger,
.btn-danger:hover,
.btn-danger:focus,
.btn-danger:active,
.btn-danger.active,
.btn-danger.disabled,
.btn-danger[disabled] {
    color: #ffffff !important;
    background-color: #bd362f !important;
    background-image: -moz-linear-gradient(top, #bd362f, #bd362f) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#bd362f), to(#bd362f)) !important;
    background-image: -webkit-linear-gradient(top, #bd362f, #bd362f) !important;
    background-image: -o-linear-gradient(top, #bd362f, #bd362f) !important;
    background-image: linear-gradient(to bottom, #bd362f, #bd362f) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-danger:active,
.btn-danger.active {
    color: #ffffff !important;
    background-color: #942a25 !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, #942a25, #942a25) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#942a25), to(#942a25)) !important;
    background-image: -webkit-linear-gradient(top, #942a25, #942a25) !important;
    background-image: -o-linear-gradient(top, #942a25, #942a25) !important;
    background-image: linear-gradient(to bottom, #942a25, #942a25) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-info,
.btn-info:hover,
.btn-info:focus,
.btn-info:active,
.btn-info.active,
.btn-info.disabled,
.btn-info[disabled] {
    color: #ffffff !important;
    background-color: #51a351 !important;
    background-image: -moz-linear-gradient(top, #51a351, #51a351) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#51a351), to(#51a351)) !important;
    background-image: -webkit-linear-gradient(top, #51a351, #51a351) !important;
    background-image: -o-linear-gradient(top, #51a351, #51a351) !important;
    background-image: linear-gradient(to bottom, #51a351, #51a351) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.btn-info:active,
.btn-info.active {
    color: #ffffff !important;
    background-color: #408140 !important;
    background-position: 0 0px !important;
    background-image: -moz-linear-gradient(top, #408140, #408140) !important;
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#408140), to(#408140)) !important;
    background-image: -webkit-linear-gradient(top, #408140, #408140) !important;
    background-image: -o-linear-gradient(top, #408140, #408140) !important;
    background-image: linear-gradient(to bottom, #408140, #408140) !important;
    border-color: rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) rgba(0, 0, 0, 0.25) !important;
}

.nav-tabs {
    border-bottom: 1px solid #ccc !important;
}

.nav-tabs > li {
    margin-left: 0px !important;
}

.nav-tabs > li > a {
    border: 1px solid #ccc !important;
    font-size: 1em !important;
    line-height: 1.5em !important;
    margin-left: 0px !important;
    padding-left: 1em !important;
    padding-right: 1em !important;
}

.nav-tabs > li > a:hover,
.nav-tabs > li > a:focus {
    color: #ffffff !important;
    border-color: <?php echo $color_tema_claro; ?> <?php echo $color_tema_claro; ?> <?php echo $color_tema_claro; ?> !important;
    background-color: <?php echo $color_tema_claro; ?> !important;
    font-size: 1em !important;
    line-height: 1.5em !important;
    margin-left: 0px !important;
    cursor: pointer !important;
}

.nav-tabs > .active > a,
.nav-tabs > .active > a:hover,
.nav-tabs > .active > a:focus {
    border-color: <?php echo $color_tema_oscuro; ?> <?php echo $color_tema_oscuro; ?> <?php echo $color_tema_oscuro; ?> !important;
    background-color: <?php echo $color_tema_oscuro; ?> !important;
    font-size: 1em !important;
    line-height: 1.5em !important;
    cursor: default !important;
}

.bootstrap-timepicker-widget.dropdown-menu.open {
    top: 31px;
}


/* jquery-alert */
#popup_panel {
	margin-top: 1em !important;
    margin-bottom: 1em !important;
}
