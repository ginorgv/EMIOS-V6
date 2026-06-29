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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/util_estadistica_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_ratio" => $_GET["id_ratio"],
            "clases_sensores_independientes" => $_GET["clases_sensores_independientes"],
            "ids_sensores_independientes" => $_GET["ids_sensores_independientes"],
            "campos_independientes" => $_GET["campos_independientes"],
            "parametros_extra_campos_independientes" => $_GET["parametros_extra_campos_independientes"],
            "clase_sensor_dependiente" => $_GET["clase_sensor_dependiente"],
            "id_sensor_dependiente" => $_GET["id_sensor_dependiente"],
            "campo_dependiente" => $_GET["campo_dependiente"],
            "parametros_extra_campo_dependiente" => $_GET["parametros_extra_campo_dependiente"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "intervalo_valores" => $_GET["intervalo_valores"],
            "funcion_correlacion" => $_GET["funcion_correlacion"],
            "horario_semanal" => $_GET["horario_semanal"],
            "exclusion_fechas" => $_GET["exclusion_fechas"],
            "inclusion_fechas" => $_GET["inclusion_fechas"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-correlacion' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-correlacion'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-correlacion'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_sensores_estadistica(TIPO_INFORME_SENSORES_CORRELACION);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_sensores_correlacion($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-correlacion'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_sensores_correlacion(TIPO_INFORME_FICHERO);

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
        sensores_correlacion_ver_informe_fichero();
    });
</script>
