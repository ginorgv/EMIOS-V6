<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    $idiomas = new Idiomas();
	$bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $nombre_sensor = $_POST['nombre_sensor'];

    // Se borra la fila de información de valores pendientes de borrado del sensor
    // (o bien se mantienen los datos o se borran al añadir / modificarel sensor, pero ya no se borrarán automáticamente)
    $operacion_borrado_informacion_valores_pendientes_borrado_sensor = "
        DELETE
        FROM informacion_valores_pendientes_borrado
        WHERE
            (red = '".$_SESSION["id_red"]."')
            AND (sensor = '".$bd_datos->_($nombre_sensor)."')";
    $res_borrado_informacion_valores_pendientes_borrado_sensor = $bd_datos->ejecuta_operacion($operacion_borrado_informacion_valores_pendientes_borrado_sensor);
    if ($res_borrado_informacion_valores_pendientes_borrado_sensor == false)
    {
        throw new Exception("Error en la operación: '".$operacion_borrado_informacion_valores_pendientes_borrado_sensor."'");
    }

    print(json_encode(array(
        "res" => OK,
        "msg" => ""))
    );
?>
