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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/util_plantillas_informes_informes_fichero.php');


        // Parámetros del informe
        $parametros_informe = array(
            "id_plantilla_informe" => $_GET["id_plantilla_informe"],
            "ids_parametros" => $_GET["ids_parametros"],
            "valores_parametros" => $_GET["valores_parametros"],
            "ids_elementos_portada" => $_GET["ids_elementos_portada"],
            "ids_elementos_titulo" => $_GET["ids_elementos_titulo"],
            "ids_elementos_texto" => $_GET["ids_elementos_texto"],
            "ids_elementos_notas" => $_GET["ids_elementos_notas"],
            "ids_elementos_imagen" => $_GET["ids_elementos_imagen"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "horario_semanal" => $_GET["horario_semanal"],
            "exclusion_fechas" => $_GET["exclusion_fechas"],
            "inclusion_fechas" => $_GET["inclusion_fechas"],
            "ruta_fichero_parametros_tipo_json" => $_GET["ruta_fichero_parametros_tipo_json"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-informe-plantilla-informe' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-informe-plantilla-informe'>";

        // Página de parámetros (y de errores)
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-informe-plantilla-informe'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_personal_plantillas_informes(TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME, NULL);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_personal_informe_plantilla_informe($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-informe-plantilla-informe'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

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
        personal_informe_plantilla_informe_ver_informe_fichero();
    });
</script>
