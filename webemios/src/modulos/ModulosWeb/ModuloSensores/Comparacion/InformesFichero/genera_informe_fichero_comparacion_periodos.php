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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/util_comparacion_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_informes_comparacion.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_ratio" => $_GET["id_ratio"],
            "clase_sensor" => $_GET["clase_sensor"],
            "id_sensor" => $_GET["id_sensor"],
            "campo" => $_GET["campo"],
            "parametros_extra_campo" => $_GET["parametros_extra_campo"],
            "fecha_inicio_periodo_anterior" => $_GET["fecha_inicio_periodo_anterior"],
            "fecha_inicio_periodo_posterior" => $_GET["fecha_inicio_periodo_posterior"],
            "numero_dias_periodo" => $_GET["numero_dias_periodo"],
            "intervalo_valores" => $_GET["intervalo_valores"],
            "tipo_mapa_calor" => $_GET["tipo_mapa_calor"],
            "horario_semanal" => $_GET["horario_semanal"],
            "exclusion_fechas" => $_GET["exclusion_fechas"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-comparacion-periodos' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-comparacion-periodos'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-comparacion-periodos'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_sensores_comparacion_periodos($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-comparacion-periodos'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_sensores_comparacion_periodos(TIPO_INFORME_FICHERO);

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
        sensores_comparacion_periodos_ver_informe_fichero();
    });
</script>
