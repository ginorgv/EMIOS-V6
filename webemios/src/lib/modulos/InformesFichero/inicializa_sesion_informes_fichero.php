<?php
    session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    // Se cargan los idiomas
    $cadenas_idiomas_comun = json_decode(file_get_contents($_SESSION["directorio"].'/comun/rsc/idiomas/idiomas.json'));
    $cadenas_idiomas_web = json_decode(file_get_contents($_SESSION["directorio"].'/rsc/idiomas/idiomas.json'));
    $_SESSION["cadenas_idiomas"] = (object) array_merge((array) $cadenas_idiomas_comun, (array) $cadenas_idiomas_web);

    // Se cargan los parámetros de la red
    $_SESSION["id_red"] = $_GET["id_red"];
    carga_parametros_red($_SESSION["id_red"]);

    // Se recupera el logo PDF y el tema (si existen)
    $info_imagen_logo_pdf = NULL;
    $tema = NULL;
    $tipo_informe = $_GET["tipo_informe"];
    switch ($tipo_informe)
    {
        case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
        {
            $id_plantilla_informe = $_GET["id_plantilla_informe"];
            $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
            $tema = $fila_plantilla_informe["tema"];
            $logo_personalizado = $fila_plantilla_informe["logo_personalizado"];
            if ($logo_personalizado == VALOR_SI)
            {
                $info_imagen_logo_pdf = carga_imagen_base_datos(ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF, $id_plantilla_informe, null);
            }
            break;
        }
    }
    if (($tema !== NULL) && ($tema != TEMA_DEFECTO))
    {
        $_SESSION["tema"] = $tema;
	}
    if ($info_imagen_logo_pdf !== NULL)
    {
        $_SESSION["ruta_logo_pdf"] = $info_imagen_logo_pdf["ruta_fichero_imagen"];
    }

    // Se guarda el usuario interno
    $_SESSION["usuario_interno"] = USUARIO_INTERNO_SERVICIOS;

    // Se carga la información del usuario interno
    carga_informacion_usuario_interno($_GET["id_usuario"], NULL);
?>