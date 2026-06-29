<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_LINEA_BASE, $_POST);

    $id_linea_base = $_POST['id_linea_base'];

	$fila_linea_base = dame_fila_linea_base($id_linea_base);
    $linea_base = new LineaBase($fila_linea_base);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $linea_base->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_LINEAS_BASE,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_LINEAS_BASE));
    $fila = TablaDatos::dame_fila(
        $linea_base->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
