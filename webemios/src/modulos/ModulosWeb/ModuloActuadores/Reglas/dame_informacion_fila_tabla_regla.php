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
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_REGLA, $_POST);

    $id_regla = $_POST['id_regla'];

	$fila_regla = dame_fila_regla($id_regla);
    $regla = new Regla($fila_regla);
    $params_fila = array(
        "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
        "opciones" => $regla->dame_opciones_tabla(),
        "numero_columnas" => NUMERO_COLUMNAS_TABLA_REGLAS,
        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_REGLAS)
    );
    $fila = TablaDatos::dame_fila(
        $regla->dame_datos_tabla(),
        $params_fila);

    print(json_encode(array(
        "res" => "OK",
        "fila" => $fila))
    );
?>
