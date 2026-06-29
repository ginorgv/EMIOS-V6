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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/GrupoTarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_GRUPO_TARIFAS, $_POST);

    $medicion = $_POST["medicion"];
    $id_grupo_tarifas = $_POST["id_grupo_tarifas"];

    $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
    $fila_grupo_tarifas = dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);
	$grupo_tarifas = new GrupoTarifas($fila_grupo_tarifas);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $grupo_tarifas->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_GRUPOS_TARIFAS,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_GRUPOS_TARIFAS));
    $info_tabla = $grupo_tarifas->dame_info_tabla($medicion);
    $datos_tabla = $info_tabla["datos"];
    $fila = TablaDatos::dame_fila(
        $datos_tabla,
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
