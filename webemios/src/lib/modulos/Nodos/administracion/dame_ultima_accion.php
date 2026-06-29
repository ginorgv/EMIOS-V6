<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_DAME_ULTIMA_ACCION, $_POST);

    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];

    // Se recupera la última acción de la tabla correspondiente
    switch ($origen)
    {
        case ORIGEN_ULTIMA_ACCION_PROGRAMACION:
        {
            $tabla = "programaciones";
            break;
        }
        case ORIGEN_ULTIMA_ACCION_GRUPO_ACTUADORES:
        {
            $tabla = "grupos_actuadores";
            break;
        }
        default: {
            throw new Exception("Origen de última acción desconocido");
        }
    }
	$consulta_accion = "
        SELECT
            contenido_ultima_accion,
            valor_ultima_accion,
            hora_ultima_accion
        FROM
            ".$tabla."
        WHERE
            id = '".$bd_red->_($id_origen)."'";
    $res_accion = $bd_red->ejecuta_consulta($consulta_accion);
    if (($res_accion == false) || ($res_accion->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_accion."'");
    }
    $fila_accion = $res_accion->dame_siguiente_fila();
    $contenido_ultima_accion = $fila_accion['contenido_ultima_accion'];
    $valor_ultima_accion = $fila_accion['valor_ultima_accion'];
    $cadena_fecha_hora_ultima_accion_base_datos_utc = $fila_accion['hora_ultima_accion'];
    if ($contenido_ultima_accion === NULL)
    {
        $contenido_ultima_accion = "";
    }

    print(json_encode(array(
        "res" => "OK",
        "contenido_ultima_accion" => $contenido_ultima_accion,
        "valor_ultima_accion" => $valor_ultima_accion,
        "fecha_hora_ultima_accion" => $cadena_fecha_hora_ultima_accion_base_datos_utc))
    );
?>
