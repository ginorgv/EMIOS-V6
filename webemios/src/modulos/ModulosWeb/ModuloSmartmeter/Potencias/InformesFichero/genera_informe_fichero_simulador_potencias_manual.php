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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/InformesFichero/util_potencias_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/util_informes_potencias.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_tarifa" => $_GET["id_tarifa"],
            "ruta_fichero_potencias_maximas" => $_GET["ruta_fichero_potencias_maximas"],
            "nombre_fichero_potencias_maximas" => $_GET["nombre_fichero_potencias_maximas"],
            "potencias_tramos" => $_GET["potencias_tramos"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-simulador-potencias-manual' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-simulador-potencias-manual'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-simulador-potencias-manual'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_smartmeter_potencias(TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_potencias_manual($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-simulador-potencias-manual'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_smartmeter_simulador_potencias_manual(TIPO_INFORME_FICHERO);

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
        smartmeter_simulador_potencias_manual_ver_informe_fichero();
    });
</script>
