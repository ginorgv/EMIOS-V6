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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/InformesFichero/util_caudales_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/util_informes_caudales.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_tarifa" => $_GET["id_tarifa"],
            "ruta_fichero_caudales_maximos" => $_GET["ruta_fichero_caudales_maximos"],
            "nombre_fichero_caudales_maximos" => $_GET["nombre_fichero_caudales_maximos"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-optimizador-caudales-manual' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-optimizador-caudales-manual'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-optimizador-caudales-manual'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_smartmeter_caudales(TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_smartmeter_optimizador_caudales_manual($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-optimizador-caudales-manual'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_smartmeter_optimizador_caudales_manual(TIPO_INFORME_FICHERO);

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
        smartmeter_optimizador_caudales_manual_ver_informe_fichero();
    });
</script>
