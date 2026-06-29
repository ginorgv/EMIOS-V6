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
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/util_informacion_informes_fichero.php');
        include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_informes_informacion.php');


        // Parámetros del informe
        $parametros_informe = array(
            "clase_actuador" => $_GET["clase_actuador"],
            "destino_accion" => $_GET["destino_accion"],
            "id_destino_accion" => $_GET["id_destino_accion"],
            "origen_acciones" => $_GET["origen_acciones"],
            "clase_sensor" => $_GET["clase_sensor"],
            "id_sensor" => $_GET["id_sensor"],
            "campo" => $_GET["campo"],
            "parametros_extra_campo" => $_GET["parametros_extra_campo"],
            "fecha_inicio" => $_GET["fecha_inicio"],
            "hora_inicio" => $_GET["hora_inicio"],
            "fecha_fin" => $_GET["fecha_fin"],
            "hora_fin" => $_GET["hora_fin"],
            "intervalo_valores" => $_GET["intervalo_valores"],
            "comentarios" => $_GET["comentarios"],
            "horario_semanal" => $_GET["horario_semanal"],
            "exclusion_fechas" => $_GET["exclusion_fechas"],
            "inclusion_fechas" => $_GET["inclusion_fechas"]);

        // Contenido del informe
        $contenido_informe_fichero = "
            <div id='contenedor-informe-fichero-acciones-enviadas' class='container contenedor-informe-fichero'>
                <div id='contenido-informe-fichero-acciones-enviadas'>";
        $paginas_informe_fichero = "";

        // Página de parámetros
        $paginas_informe_fichero .= "
            <div class='pagina-informe-fichero' id='pagina-parametros-informe-fichero-acciones-enviadas'>";
        $paginas_informe_fichero .= dame_html_cabecera_informe_fichero_actuadores_informacion(TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS);
        $paginas_informe_fichero .= dame_html_parametros_tipo_informe_fichero_actuadores_informacion_acciones_enviadas($parametros_informe);
        $paginas_informe_fichero .= "
                <div class='mensaje-aviso-informe-fichero' id='mensaje-aviso-informe-fichero-acciones-enviadas'></div>
                <div class='fin-pagina-informe-fichero'></div>
            </div>";

        // Páginas del informe
        $paginas_informe_fichero .= dame_html_informe_tipo_actuadores_informacion_acciones_enviadas(TIPO_INFORME_FICHERO);

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
        actuadores_informacion_acciones_enviadas_ver_informe_fichero();
    });
</script>
