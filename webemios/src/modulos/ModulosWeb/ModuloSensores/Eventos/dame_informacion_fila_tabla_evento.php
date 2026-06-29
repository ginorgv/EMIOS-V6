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
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_EVENTO, $_POST);

    $id_evento = $_POST['id_evento'];

	$fila_evento = dame_fila_evento($id_evento);
    $evento = new Evento($fila_evento);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $evento->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVENTOS,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_EVENTOS)
    );
    $fila = TablaDatos::dame_fila(
        $evento->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
