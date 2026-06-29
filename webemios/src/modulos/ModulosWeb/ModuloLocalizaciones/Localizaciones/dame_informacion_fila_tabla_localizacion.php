<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/localizaciones/util_ocalizaciones.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_LOCALIZACION, $_POST);

    $id_localizacion = $_POST['id_localizacion'];

    $fila_localizacion = dame_fila_localizacion($id_localizacion);
    $localizacion = new Localizacion($fila_localizacion);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $localizacion->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_LOCALIZACIONES,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_LOCALIZACIONES));
    $fila = TablaDatos::dame_fila(
        $localizacion->dame_datos_tabla(NULL, NULL, NULL),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
