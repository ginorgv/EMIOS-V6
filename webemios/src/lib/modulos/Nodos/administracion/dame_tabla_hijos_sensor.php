<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_HIJOS_SENSOR, $_POST);

	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_sensor = $_POST['id_sensor'];

    // Se recupera información del sensor
    $fila_sensor = dame_fila_sensor($id_sensor);
    $tipo = $fila_sensor["tipo"];
    $parametros_tipo = $fila_sensor["parametros_tipo"];
    $administrable = $fila_sensor["administrable"];

    // Se crea el nodo sensor y se recupera la tabla de sensores hijos
    $sensor = Nodo::crea_nodo($id_sensor, TIPO_NODO_SENSOR);
    $tabla_hijos = $sensor->dame_tabla_hijos(
        $tipo,
        $parametros_tipo,
        $administrable);

	$res = "OK";
	$html = $tabla_hijos;

    print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
