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
	include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_HISTORICO_PROCESADO, $_POST);

    $id_historico_procesado = $_POST['id_historico_procesado'];

	$fila_historico_procesado = dame_fila_historico_procesado($id_historico_procesado);
    $historico_procesado = new HistoricoProcesado($fila_historico_procesado);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_HISTORICO_PROCESADO,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_HISTORICO_PROCESADO));
    $fila = TablaDatos::dame_fila(
        $historico_procesado->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
