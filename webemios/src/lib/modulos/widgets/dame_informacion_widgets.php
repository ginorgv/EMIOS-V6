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
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFORMACION_WIDGETS, $_POST);

    // Parámetros
    $id_pestanya = $_POST["id_pestanya"];
    $ids_widgets = $_POST["ids_widgets"];
    $numeros_columnas_widgets = $_POST["numeros_columnas_widgets"];
    $numeros_columnas_filas_widgets = $_POST["numeros_columnas_filas_widgets"];
    $minutos_desfase_utc = $_POST["minutos_desfase_utc"];

    // Variables
    $nombres = array();
    $tipos = array();
    $parametros_tipos = array();
    $datos_widgets = array();

    // Se recupera la fila de la pestanya de widgets
    $fila_pestanya_widgets = dame_fila_pestanya_widgets($id_pestanya);

    // Se recupera la información de cada uno de los widgets
    $info_widgets = array();
    $numero_widget = 1;
    foreach ($ids_widgets as $id_widget)
    {
        $fila_widget = dame_fila_widget($id_widget);
        $nombre = $fila_widget["nombre"];
        $tipo = $fila_widget["tipo"];
        $cadena_parametros_tipo = $fila_widget["parametros_tipo"];

        // Se recuperan el número de columnas de la fila del widget y del propio widget
        $res_numeros_columnas = CuadriculaWidgets::dame_numeros_columnas_fila_widget_widget(
            $numeros_columnas_filas_widgets,
            $numeros_columnas_widgets,
            $numero_widget);
        $numero_columnas_fila_widget = $res_numeros_columnas["numero_columnas_fila_widget"];
        $numero_columnas_widget = $res_numeros_columnas["numero_columnas_widget"];

        // Se recuperan los parámetros del tipo de widget
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

        // Se añade la información del widget
        $info_widget = array(
            "id_pestanya" => $id_pestanya,
            "nombre" => $nombre,
            "tipo" => $tipo,
            "parametros_tipo" => $parametros_tipo,
            "numero_columnas_fila_widget" => $numero_columnas_fila_widget,
            "numero_columnas_widget" => $numero_columnas_widget,
            "datos_widget" => json_encode($datos_widget));
        array_push($info_widgets, $info_widget);

        // Se incrementa el número de widget
        $numero_widget++;
    }

    // Se devuelve la información de los widgets
	print(json_encode(array(
        "res" => "OK",
        "info_widgets" => $info_widgets))
    );
?>
