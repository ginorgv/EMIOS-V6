<?php
	// Inicialización de sesión para los informes fichero
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/inicializa_sesion_informes_fichero.php');
?>

<!DOCTYPE HTML>
<html>
<head>
  	<meta charset="utf-8">
    <link rel="shortcut icon" href="./comun/rsc/imagenes/favicon.ico" />
</head>
<body>
    <?php
        // Fuentes
        include_once($_SESSION["directorio"].'/comun/includes/fuentes_librerias.php');
        include_once($_SESSION["directorio"].'/comun/includes/fuentes_web.php');
        include_once($_SESSION["directorio"].'/includes/fuentes_librerias.php');
        include_once($_SESSION["directorio"].'/includes/fuentes_web.php');

        // Estilos
        include_once($_SESSION["directorio"].'/comun/includes/estilos_librerias.php');
        include_once($_SESSION["directorio"].'/comun/includes/estilos_web.php');
        include_once($_SESSION["directorio"].'/includes/estilos_librerias.php');
        include_once($_SESSION["directorio"].'/includes/estilos_web.php');

        // Includes del informe
        include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/util_eventos_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_informes_eventos.php');


        // Parámetros del informe
        $parametros_informe = array(
            "clase_sensor" => $_GET["clase_sensor"],
            "origen_evento" => $_GET["origen_evento"],
            "id_origen_evento" => $_GET["id_origen_evento"],
            "granularidad_evento" => $_GET["granularidad_evento"],
            "ids_eventos" => $_GET["ids_eventos"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "campo" => $_GET["campo"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-activaciones-eventos' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-activaciones-eventos'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-activaciones-eventos'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_sensores_eventos(TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_sensores_activaciones_eventos($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-activaciones-eventos'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_sensores_activaciones_eventos(TIPO_INFORME_FICHERO);

        // Fin del contenido del informe
        $contenido_informe_fichero .= $paginas_informe_fichero;
        $contenido_informe_fichero .= "
                </div>
            </div>";

        print($contenido_informe_fichero);
    ?>
</body>
</html>

<!-- Recuperación de datos del informe -->
<script class="code" type="text/javascript">
    usuario_interno = true;
    $(document).ready(function() {
        document.title = TLNT.Navegacion.titulo + " (Informe)";
        sensores_activaciones_eventos_ver_informe_fichero();
    });
</script>
