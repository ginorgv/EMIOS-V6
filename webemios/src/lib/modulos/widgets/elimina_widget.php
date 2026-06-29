<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_WIDGET, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_widget = $_POST['id_widget'];
    $tipo = $_POST['tipo'];

    $operacion_borrado = "
        DELETE
        FROM widgets
        WHERE
            id = '".$bd_red->_($id_widget)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Widget eliminado correctamente");

        // Acciones a realizar al eliminar un widget
        realiza_acciones_widget_eliminado($id_widget, $tipo);
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un widget
    function realiza_acciones_widget_eliminado($id_widget, $tipo)
    {
        // Acciones a realizar dependiendo del tipo de widget
        switch ($tipo)
        {
            case TIPO_WIDGET_IMAGEN:
            {
                elimina_imagen_base_datos(ORIGEN_IMAGEN_WIDGET_IMAGEN, $id_widget);
                break;
            }
        }
    }
?>
