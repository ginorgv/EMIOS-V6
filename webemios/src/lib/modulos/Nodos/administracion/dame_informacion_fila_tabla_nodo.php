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
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_NODO, $_POST);

    $tipo_nodo = $_POST['tipo_nodo'];
    $id_nodo = $_POST['id_nodo'];
    $valor_nodo_administrable = $_POST['valor_nodo_administrable'];
    $valor_permitir_adicion_nodos = $_POST['valor_permitir_adicion_nodos'];

    $nodo_administrable = ($valor_nodo_administrable == VALOR_SI);
    $permitir_adicion_nodos = ($valor_permitir_adicion_nodos == VALOR_SI);

    $nodo = dame_nodo($tipo_nodo, $id_nodo);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $nodo->dame_opciones_tabla($nodo_administrable, $permitir_adicion_nodos),
        "numero_columnas" => dame_numero_columnas_tabla_nodos($tipo_nodo),
        "anchuras_columnas" => dame_anchuras_columnas_tabla_nodos($tipo_nodo));
    $fila = TablaDatos::dame_fila(
        $nodo->dame_datos_tabla(),
        $params_fila);

	print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
