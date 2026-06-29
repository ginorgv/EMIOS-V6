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
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_INSTALACION, $_POST);

    $id_instalacion = $_POST['id_instalacion'];

	$fila_instalacion = dame_fila_instalacion($id_instalacion);
    $instalacion = new Instalacion($fila_instalacion);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $instalacion->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_INSTALACIONES,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_INSTALACIONES)
    );
    $fila = TablaDatos::dame_fila(
        $instalacion->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
