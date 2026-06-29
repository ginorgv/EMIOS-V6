<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModulosProyectos/LineasBase/LineaBase.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_VARIABLES_LINEA_BASE, $_POST);

    // Parámetros
    $id_linea_base = $_POST['id_linea_base'];

    // Se crea la línea base y se recupera la tabla de variables
    $params = array("id" => $id_linea_base);
    $linea_base = new LineaBase($params);

	$res = "OK";
	$html = $linea_base->dame_tabla_variables();

    print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
