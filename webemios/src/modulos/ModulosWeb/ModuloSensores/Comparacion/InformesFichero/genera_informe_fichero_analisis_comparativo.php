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
            "campo" => $_GET["campo"],
            "parametros_extra_campo" => $_GET["parametros_extra_campo"],
            "ids_sensores_agregados" => $_GET["ids_sensores_agregados"],
            "id_sensor_destacado" => $_GET["id_sensor_destacado"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "intervalo_valores" => $_GET["intervalo_valores"],
            "tipo_mapa_calor" => $_GET["tipo_mapa_calor"],
            "horario_semanal" => $_GET["horario_semanal"],
            "exclusion_fechas" => $_GET["exclusion_fechas"],
            "inclusion_fechas" => $_GET["inclusion_fechas"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-analisis-comparativo' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-analisis-comparativo'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-analisis-comparativo'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_sensores_analisis_comparativo($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-analisis-comparativo'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_sensores_analisis_comparativo(TIPO_INFORME_FICHERO);

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
        sensores_analisis_comparativo_ver_informe_fichero();
    });
</script>
