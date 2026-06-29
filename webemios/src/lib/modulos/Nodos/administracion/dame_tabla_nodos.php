<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_NODOS, $_POST);

	$tipo_nodo = $_POST["tipo_nodo"];
    $filtro = $_POST["filtro"];
    $parametros_tipo_nodo = $_POST["parametros_tipo_nodo"];
    $tipo_nodo_actualizacion_periodica = $_POST["tipo_nodo_actualizacion_periodica"];

    $id_tabla = "tabla".$tipo_nodo;
    $html = dame_tabla_nodos(
        $tipo_nodo,
        $filtro,
        $parametros_tipo_nodo,
        $tipo_nodo_actualizacion_periodica);

    print(json_encode(array(
        "res" => "OK",
        "id_tabla" => $id_tabla,
        "html" => $html))
    );
?>
