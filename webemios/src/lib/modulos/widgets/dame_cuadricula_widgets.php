<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CUADRICULA_WIDGETS, $_POST);

    // Parámetros
    $id_pestanya = $_POST["id_pestanya"];
    $cuadricula_widgets = dame_cuadricula_widgets($id_pestanya);

	print(json_encode(array(
        "res" => "OK",
        "html_cuadricula_widgets" => $cuadricula_widgets->dame_cuadricula($_POST["id_pestanya"]),
        "ids_widgets" => $cuadricula_widgets->dame_ids_widgets(),
        "numeros_columnas_widgets" => $cuadricula_widgets->dame_numeros_columnas_widgets(),
        "numeros_columnas_filas_widgets" => $cuadricula_widgets->numeros_columnas_filas_widgets,
        "ajustar_altura_widgets" => $cuadricula_widgets->ajustar_altura_widgets,
        "cadena_parametros_apariencia_pestanya" => $cuadricula_widgets->cadena_parametros_apariencia_pestanya,
        "cadena_parametros_apariencia_widgets" => $cuadricula_widgets->cadena_parametros_apariencia_widgets,
        "cadena_parametros_opciones_pantalla_completa" => $cuadricula_widgets->cadena_parametros_opciones_pantalla_completa,
        "imagen_fondo" => $cuadricula_widgets->parametros_apariencia_pestanya["imagen_fondo"],
        "mostrar_cabecera" => $cuadricula_widgets->parametros_apariencia_pestanya["mostrar_cabecera"],
        "mostrar_hora_cabecera" => $cuadricula_widgets->parametros_apariencia_pestanya["mostrar_hora_cabecera"],
        "mostrar_fecha_cabecera" => $cuadricula_widgets->parametros_apariencia_pestanya["mostrar_fecha_cabecera"]))
    );
?>
