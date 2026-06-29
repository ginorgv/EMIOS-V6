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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/util_compra_energia_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_informes_compra_energia.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_sensor" => $_GET["id_sensor"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "fecha_inicio_perfil_horario" => $_GET["fecha_inicio_perfil_horario"],
            "fecha_fin_perfil_horario" => $_GET["fecha_fin_perfil_horario"],
            "tipo_perfil_horario" => $_GET["tipo_perfil_horario"],
            "agrupaciones_dias_semana" => $_GET["agrupaciones_dias_semana"],
            "exclusion_fechas" => $_GET["exclusion_fechas"],);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-prevision-compra-energia' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-prevision-compra-energia'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-prevision-compra-energia'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_smartmeter_prevision_compra_energia($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-prevision-compra-energia'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_smartmeter_prevision_compra_energia(TIPO_INFORME_FICHERO);

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
        smartmeter_prevision_compra_energia_ver_informe_fichero();
    });
</script>
