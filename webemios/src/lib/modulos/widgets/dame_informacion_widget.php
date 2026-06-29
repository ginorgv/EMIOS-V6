<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/CuadriculaWidgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFORMACION_WIDGET, $_POST);

    // Parámetros
    $id_widget = $_POST["id_widget"];
    $numero_widget = $_POST["numero_widget"];
    $minutos_desfase_utc = $_POST["minutos_desfase_utc"];

    // Información del widget y de la pestaña de widgets correspondientes
    $fila_widget = dame_fila_widget($id_widget);
    $nombre = $fila_widget["nombre"];
    $tipo = $fila_widget["tipo"];
    $cadena_parametros_tipo = $fila_widget["parametros_tipo"];
    $id_pestanya = $fila_widget["pestanya"];
    $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);
    $numeros_columnas_filas_widgets = explode(",", $fila_pestanya_widgets["numeros_columnas_filas_widgets"]);

    // Se recuperan el número de columnas de la fila del widget y del propio widget
    $numeros_columnas_widgets = dame_numeros_columnas_widgets_pestanya_widgets($id_pestanya);
    $res_numeros_columnas = CuadriculaWidgets::dame_numeros_columnas_fila_widget_widget(
        $numeros_columnas_filas_widgets,
        $numeros_columnas_widgets,
        $numero_widget);
    $numero_columnas_fila_widget = $res_numeros_columnas["numero_columnas_fila_widget"];
    $numero_columnas_widget = $res_numeros_columnas["numero_columnas_widget"];

    try
    {
        $parametros_tipo = dame_nombres_valores_parametros_tipo_widget($tipo, $cadena_parametros_tipo);
    }
    catch (Exception $e)
    {
        $log = dame_log();
        $log->error("Excepción capturada: ", $e);
    }

    // Se recuperan los datos del widget
    $datos_widget = dame_datos_widget(
        $id_widget,
        $fila_widget,
        $fila_pestanya_widgets,
        $numero_columnas_fila_widget,
        $numero_columnas_widget,
        $minutos_desfase_utc);
    if ($datos_widget["res"] == "ERROR")
    {
        $log = dame_log();
        $log->error("Error al obtener los datos del widget (id: '".$id_widget."', mensaje: '".$datos_widget["msg"]."')");
    }

    // Se devuelve la información del widget
    $info_widget = array(
        "id_pestanya" => $id_pestanya,
        "nombre" => $nombre,
        "tipo" => $tipo,
        "parametros_tipo" => $parametros_tipo,
        "numero_columnas_fila_widget" => $numero_columnas_fila_widget,
        "numero_columnas_widget" => $numero_columnas_widget,
        "datos_widget" => json_encode($datos_widget));

    print(json_encode(array(
        "res" => "OK",
        "info_widget" => $info_widget))
    );
?>
