<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanya_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PESTANYA_WIDGETS, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_pestanya = $_POST['id_pestanya'];
    $modulo = $_POST["modulo"];

    // Se elimina la pestaña de widgets
    $operacion_borrado = "
        DELETE
        FROM pestanyas_widgets
        WHERE
            id = '".$bd_red->_($id_pestanya)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se elimina la imagen de fondo
        elimina_imagen_base_datos(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, $id_pestanya);

        // Se eliminan los widgets de la pestaña
        $operacion_borrado_widgets = "
            DELETE
            FROM widgets
            WHERE
                pestanya = '".$bd_red->_($id_pestanya)."'";
        $res_borrado_widgets = $bd_red->ejecuta_operacion($operacion_borrado_widgets);
        if ($res_borrado_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_widgets."'");
        }

        // Se eliminan las imágenes de los widgets de imágenes
        $operacion_borrado_imagenes_widgets = "
            DELETE
            FROM imagenes
            WHERE
                (origen = '".ORIGEN_IMAGEN_WIDGET_IMAGEN."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(0 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = '".$bd_red->_($id_pestanya)."')";
        $res_borrado_imagenes_widgets = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_widgets);
        if ($res_borrado_imagenes_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_widgets."'");
        }

        $res = "OK";
        $msg = $idiomas->_("Pestaña de widgets eliminada correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado_pestanya."'");
    }

    // Actualiza las posiciones de las pestañas de widgets
    $posicion = actualiza_posiciones_pestanyas_widgets_modulo($modulo);

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
