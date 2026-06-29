<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_informes_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $fechas = dame_fechas_inicio_fin_defecto_informe_plantilla_informe($id_plantilla_informe);
    $html_controles_parametros = dame_controles_parametros_informe_plantilla_informe($id_plantilla_informe);
    $html_controles_subtitulos_portadas = dame_controles_subtitulos_portadas_informe_plantilla_informe($id_plantilla_informe);
    $html_controles_titulos = dame_controles_titulos_informe_plantilla_informe($id_plantilla_informe);
    $html_controles_textos = dame_controles_textos_informe_plantilla_informe($id_plantilla_informe);
    $html_controles_imagenes = dame_controles_imagenes_informe_plantilla_informe($id_plantilla_informe);
    $tipo_seleccion_horario_semanal_fechas = dame_tipo_seleccion_horario_semanal_fechas_plantilla_informe($id_plantilla_informe);

    print(json_encode(array(
        "res" => "OK",
        "fecha_inicio" => $fechas["fecha_inicio"],
        "fecha_fin" => $fechas["fecha_fin"],
        "html_controles_parametros" => $html_controles_parametros,
        "html_controles_subtitulos_portadas" => $html_controles_subtitulos_portadas,
        "html_controles_titulos" => $html_controles_titulos,
        "html_controles_textos" => $html_controles_textos,
        "html_controles_imagenes" => $html_controles_imagenes,
        "tipo_seleccion_horario_semanal_fechas" => $tipo_seleccion_horario_semanal_fechas))
    );
?>

