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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_PROYECTO, $_POST);

    $id_proyecto = $_POST['id_proyecto'];

	$fila_proyecto = dame_fila_proyecto($id_proyecto);
    $proyecto = new Proyecto($fila_proyecto);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $proyecto->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_PROYECTOS,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_PROYECTOS));
    $fila = TablaDatos::dame_fila(
        $proyecto->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
